<?php

namespace App\Controller;

use App\Entity\GrapeBlock;
use App\Entity\Page;
use App\Entity\PageSection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Page controller.
 *
 */
class PageController extends BaseController {

    /**
     * Lists all page entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('App:Page')->findAll();


        return $this->render('page/index.html.twig', array(
                    'pages' => $pages,
        ));
    }

    /**
     * @Route("/myadmin/page.new.html", name="myadmin_page_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $page = new Page();
        $form = $this->createForm('App\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $menu = $form['menu']->getData();
            $page->setTitle($form['title']->getData());
            $page->setName($form['name']->getData());
            $page->setUrl(trim($form['url']->getData()));
            $page->setTarget($form['target']->getData());
            $page->setKeywords(strtolower($form['keywords']->getData()));
            $page->setDescription($form['description']->getData());
            $page->setMenu($menu);
            $page->setIsHome(0);
            $page->setRank(0);
            $page->setBody($form['body']->getData())
                    ->setCss(' ');
            $em->persist($page);
            $em->flush();

            return $this->redirectToRoute('myadmin_page_edit', array('id' => $page->getId()));
        }

        return $this->render('admin/page/new.html.twig', array(
                    'page' => $page,
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/myadmin/page/{id}/delete", name="myadmin_page_delete", methods="GET")
     */
    public function deleteAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('App:Page')->findOneBy(['id' => $id]);
        if ($page) {
            $em->remove($page);
            //$em->persist($page);
            $em->flush();
        }
        return $this->redirectToRoute('myadmin_page_index');
    }

    /**
     * Finds and displays a page entity.
     *
     */

    /**
     * @Route("/p.{id}.{slug}.html", name="page_show", methods="GET")
     */
    public function showAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id]);
        $PageSections = $em->getRepository(PageSection::class)->findBy(['page' => $page->getId()], ['rank' => 'ASC']);

        $body = $this->bodyFilter($page->getBody());
        $response = $this->render('business/page.html.twig', array(
            'page' => $page,
            'body' => $body,
            'pageSection' => $PageSections
        ));

        return $this->etagResponse($response, $request);
    }

    /**
     * Displays a form to edit an existing page entity.
     *
     */

    /**
     * @Route("/myadmin/open/page_{id}.html", name="myadmin_page_open", methods={"GET","POST"})
     */
    public function openAction(Request $request, DataTableFactory $dtf) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id]);
        $datatable = $dtf->create()
                        ->add('rank', NumberColumn::class, ['label' => 'Rank'])
                        ->add('title', TextColumn::class, ['label' => 'Title'])
                        ->add('type', TextColumn::class, ['label' => 'Type'])
                        ->add('action', TextColumn::class, ['label' => 'Edit', 'render' => function($c, $v) {
                                $editUrl = $this->generateUrl('myadmin_page_section_edit', ['id' => $v->getId()]);
                                $html = "<div class='btn-group'>"
                                        . "<a href='javascript:void(0)' class='btn btn-sm btn-primary aic-show-large-modal' data-href='$editUrl'>Edit</a>"
                                ;
                                if ($v->getType() != 'scroller') {
                                    $iconEditUrl = $this->generateUrl('myadmin_page_section_icon_edit', ['id' => $v->getId()]);
                                    $html .= "<a href='javascript:void(0)' class='btn btn-sm btn-success aic-show-large-modal' data-href='$iconEditUrl'>Edit Icon</a>";
                                }
                                if ($v->getType() == 'image-right' || $v->getType() == 'image-left' || $v->getType() == 'banner') {
                                    $imageEditUrl = $this->generateUrl('myadmin_page_section_image_edit', ['id' => $v->getId()]);
                                    $html .= "<a href='javascript:void(0)' class='btn btn-sm btn-info aic-show-large-modal' data-href='$imageEditUrl'>Edit Image</a>";
                                }
                                if ($v->getType() == 'scroller') {
                                    $imagesUrl = $this->generateUrl('myadmin_page_section_images', ['id' => $v->getId()]);
                                    $html .= "<a class='btn btn-sm btn-info' href='$imagesUrl' >Images</a>";
                                }
                                $pageSettingEditUrl = $this->generateUrl('myadmin_page_setting_edit', ['id' => $v->getId()]);
                                $html .= "<a data-href='$pageSettingEditUrl' href='javascript:void(0)' class='btn btn-danger btn-sm aic-show-large-modal'>SettingEdit</a>";
                                $deleteUrl = $this->generateUrl('myadmin_page_section_delete', ['id' => $v->getId()]);
                                $html .= "<a href='$deleteUrl' class='btn btn-danger btn-sm'>Delete</a>";
                                $html .= "</div>";
                                return $html;
                            }])
                        ->addOrderBy('rank', DataTable::SORT_ASCENDING)
                        ->createAdapter(ORMAdapter::class, [
                            'entity' => PageSection::class,
                            'query' => function (QueryBuilder $builder) use ($page) {
                                $builder
                                ->select('p')
                                ->from(PageSection::class, 'p')
                                ->where('p.page = :page')
                                ->setParameter('page', $page->getId())
                                ;
                            },
                        ])->handleRequest($request);

        if ($datatable->isCallback()) {
            return $datatable->getResponse();
        }
        return $this->render('admin/page/open.html.twig', [
                    'page' => $page,
                    'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/myadmin/page/section/reorder", name="myadmin_page_section_reorder", methods={"GET","POST"})
     */
    public function pageSectionReorder(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->get('data');
//        print_r($data);
        foreach ($data as $d) {
            if ($d) {
                if ($d['new']) {
                    $row = $em->getRepository(PageSection::class)->findOneBy(['page' => $request->get('id'), 'rank' => $d['old']]);
                    if ($row) {
                        print_r($d);
                        $row->setRank($d['new']);
                        $em->persist($row);
                    }
                }
            }
        }
        $em->flush();
        return new JsonResponse(['true']);
    }

    /**
     * @Route("/myadmin/page/section/edit/{id}/image/edit", name="myadmin_page_section_image_edit", methods={"GET","POST"})
     */
    public function pageSectionImageEdit(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository(PageSection::class)->findOneBy(['id' => $request->get('id')]);
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl("myadmin_page_section_image_edit", ['id' => $request->get('id')]))
                ->add('imageData', FileType::class)
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $_FILES['form'];
            $fileName = $file['name']['imageData'];
            $fileType = $file['type']['imageData'];
            $fileTmpName = $file['tmp_name']['imageData'];
            $section->setImageData(file_get_contents($fileTmpName))
                    ->setImageFileName($fileName)
                    ->setImageFileType($fileType);
            $em->persist($section);
            $em->flush();
            return $this->redirectToRoute('myadmin_page_open', ['id' => $section->getPage()->getId()]);
        }
        return $this->render('admin/page/pageSectionEdit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/myadmin/page/section/edit/{id}/icon/edit", name="myadmin_page_section_icon_edit", methods={"GET","POST"})
     */
    public function pageSectionIconEdit(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository(PageSection::class)->findOneBy(['id' => $request->get('id')]);
        $form = $this->createFormBuilder($section)
                ->setAction($this->generateUrl("myadmin_page_section_icon_edit", ['id' => $request->get('id')]))
                ->add('headerIcon', TextType::class, ['required' => false])
                ->add('headerIconColor', ColorType::class)
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($section);
            $em->flush();
            return $this->redirectToRoute('myadmin_page_open', ['id' => $section->getPage()->getId()]);
        }
        return $this->render('admin/page/pageSectionEdit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/myadmin/page/section/edit/{id}", name="myadmin_page_section_edit", methods={"GET","POST"})
     */
    public function pageSectionEdit(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $pageSection = $em->getRepository(PageSection::class)->findOneBy(['id' => $id]);
        $form = $this->createFormBuilder($pageSection)
                ->setAction($this->generateUrl('myadmin_page_section_edit', ['id' => $id]))
                ->add('title')
                ->add('content')
                ->add('type', TextType::class, ['disabled' => true])
                ->add('backgroundColor', ColorType::class)
                ->add('titleColor', ColorType::class)
                ->add('textColor', ColorType::class)
                ->add('alignContent', ChoiceType::class, ['data' => $pageSection->getAlignContent(), 'choices' => ['center' => 'center', 'none' => 'none']])
                ->add('alignTitle', ChoiceType::class, ['data' => $pageSection->getAlignTitle(), 'choices' => ['center' => 'center', 'none' => 'none']])
                ->add('backgroundContainment', ChoiceType::class, ['choices' => ['container' => 'container', 'container-fluid' => 'container-fluid']])
                ->add('contentContainment', ChoiceType::class, ['choices' => ['container' => 'container', 'container-fluid' => 'container-fluid']])
        ;
        if ($pageSection->getType() == 'youtube-section') {
            $form = $form->add('youtubeUrls', \Symfony\Component\Form\Extension\Core\Type\UrlType::class);
        }
        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

//            $pageSection = new PageSection();

            $em->persist($pageSection);
            $em->flush();
            return $this->redirectToRoute('myadmin_page_open', ['id' => $pageSection->getPage()->getId()]);
        }
        return $this->render('admin/page/pageSectionEdit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/myadmin/page/setting/edit/{id}", name="myadmin_page_setting_edit", methods={"GET","POST"})
     */
    public function pageSettingEdit(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $pageSection = $em->getRepository(PageSection::class)->findOneBy(['id' => $id]);
        $form = $this->createFormBuilder($pageSection)
                ->setAction($this->generateUrl('myadmin_page_setting_edit', ['id' => $id]))
                ->add('contentPaddingBottom')
                ->add('contentPaddingTop')
                ->add('contentPaddingLeft')
                ->add('contentPaddingRight')
                ->add('titleAnimation', ChoiceType::class, [
                    'choices' => [
                        "none" => "none",
                        "uk-animation-slide-left" => "uk-animation-slide-left",
                        "uk-animation-slide-right" => "uk-animation-slide-right",
                        "uk-animation-fade" => "uk-animation-fade",
                    ],
                ])
                ->add('contentAnimation', ChoiceType::class, [
            'choices' => [
                "none" => "none",
                "uk-animation-slide-left" => "uk-animation-slide-left",
                "uk-animation-slide-right" => "uk-animation-slide-right",
                "uk-animation-fade" => "uk-animation-fade",
            ],
                ])
        ;

        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

//            $pageSection = new PageSection();

            $em->persist($pageSection);
            $em->flush();
            return $this->redirectToRoute('myadmin_page_open', ['id' => $pageSection->getPage()->getId()]);
        }
        return $this->render('admin/page/pageSectionEdit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/myadmin/new/section_{id}.html", name="myadmin_page_new_section", methods={"GET","POST"})
     */
    public function newSection(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id]);
        $lastPage = $em->getRepository(PageSection::class)->findOneBy(['page' => $page->getId()], ['rank' => 'desc']);
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('myadmin_page_new_section', ['id' => $id]))
                ->add('title', TextType::class)
                ->add('type', ChoiceType::class, ['choices' => [
                        'simple' => 'simple',
                        'image-right' => 'image-right',
                        'image-left' => 'image-left',
                        'scroller' => 'scroller',
                        'scroller' => 'scroller',
                        'banner' => 'banner',
                        'youtube-section' => 'youtube-section',
            ]])
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $section = new PageSection();
                $section->setTitle($form['title']->getData());
                if ($form['type']->getData() == 'simple') {
                    $section->setContent($this->getLongLerom());
                }
                if ($lastPage) {
                    $rank = $lastPage->getRank() + 1;
                } else {
                    $rank = 1;
                }
                $section->setRank($rank)
                        ->setPage($page)
                        ->setType($form['type']->getData())
                        ->setAlignContent('center')
                        ->setAlignTitle('center')
                        ->setBackgroundContainment('container')
                        ->setContentContainment('container')
                        ->setContent($this->getSmallLerom())
                        ->setBackgroundColor('#ffffff')
                        ->setTitleColor('#000000')
                        ->setTextColor('#000000')
                        ->setContentPaddingBottom(0)
                        ->setContentPaddingTop(0)
                        ->setContentPaddingLeft(0)
                        ->setContentPaddingRight(0)
                ;
                $em->persist($section);
                $em->flush();
                return $this->redirectToRoute('myadmin_page_open', ['id' => $page->getId()]);
            }
        }
        return $this->render('admin/page/newSection.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/myadmin/page.edit.html", name="myadmin_page_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request) {
        //$deleteForm = $this->createDeleteForm($page);
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $request->get('id')]);
        // $editForm = $this->createForm('App\Form\PageType', $page);
        $formBuilder = $this->createFormBuilder($page);
        if ($page->getIsHome() == TRUE) {
            $formBuilder->add('name', TextType::class, ['attr' => ['readonly' => 'readonly']])
                    ->add('menu', EntityType::class, array(
                        'class' => 'App:Menu',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('M')
                                    ->where('M.name = :name')
                                    ->setParameter('name', 'Home')
                                    ->orderBy('M.name', 'ASC');
                        },
                        'choice_label' => 'name',
            ));
        } else {
            $formBuilder->add('name')
                    ->add('menu', EntityType::class, array(
                        'class' => 'App:Menu',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('M')
                                    ->where('M.name != :name')
                                    ->setParameter('name', 'Home')
                                    ->orderBy('M.name', 'ASC');
                        },
                        'choice_label' => function($c) {
                            if ($c->getParent()) {
                                return $c->getName() . " -- " . $c->getParent()->getName();
                            } else {
                                return $c->getName();
                            }
                        },
            ));
        }

        $formBuilder->add('title')
                ->add('keywords', TextType::class)
                ->add('description', TextType::class)
                ->add('body', TextareaType::class, array('attr' => array('novalidate' => 'novalidate')))
                ->add('url', TextType::class, array('required' => false))
                ->add('target', ChoiceType::class, array('choices' => array('Open in Same Window' => '_top', 'Open In New Window' => '_blank')))
                ->add('rank');
        $editForm = $formBuilder->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //$this->getDoctrine()->getManager()->flush();
            $em = $this->getDoctrine()->getManager();
            if ($page->getIsHome() == TRUE) {
                $page->setName('Home');
            }
            $em->persist($page);
            $em->flush();
            $this->addFlash(
                    'success', '<b>Success!</b><br> Content Saved...'
            );
            return $this->redirectToRoute('myadmin_page_edit', array('id' => $page->getId()));
        }

        return $this->render('admin/page/edit.html.twig', array(
                    'page' => $page,
                    'edit_form' => $editForm->createView(),
//
        ));
    }

    /**
     * @Route("/myadmin/pagebuilder/save", name="myadmin_page_edit_grape_save", methods={"GET","POST"})
     */
    public function grapesSaveAction(Request $request) {
        $id = $request->get('id');
        $html = $request->get('html');
        $css = $request->get('css');
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id]);
        $page->setBody($html)
                ->setCss($css);
        $em->persist($page);
        $em->flush();
        echo 'done';
        die;
    }

    /**
     * @Route("/myadmin/pagebuilder/page.edit.html", name="myadmin_page_edit_grape", methods={"GET","POST"})
     */
    public function grapesAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $request->get('id')]);
        $blocks = $em->getRepository(GrapeBlock::class)->findAll();
        $imageAssets = file_get_contents($this->generateUrl('image_assets_json', [], UrlGeneratorInterface::ABSOLUTE_URL));
        return $this->render('admin/page/grapes.html.twig', ['blocks' => $blocks, 'page' => $page, 'imageAssets' => $imageAssets]);
    }

    /**
     * @Route("/myadmin/page.edit2.html", name="myadmin_page_edit2", methods={"GET","POST"})
     */
    public function edit2Action(Request $request) {

        //$deleteForm = $this->createDeleteForm($page);
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $request->get('id')]);
        // $editForm = $this->createForm('App\Form\PageType', $page);
        $formBuilder = $this->createFormBuilder($page);
        if ($page->getIsHome() == TRUE) {
            $formBuilder->add('name', TextType::class, ['attr' => ['readonly' => 'readonly']])
                    ->add('menu', EntityType::class, array(
                        'class' => 'App:Menu',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('M')
                                    ->where('M.name = :name')
                                    ->setParameter('name', 'Home')
                                    ->orderBy('M.name', 'ASC');
                        },
                        'choice_label' => 'name',
            ));
        } else {
            $formBuilder->add('name')
                    ->add('menu', EntityType::class, array(
                        'class' => 'App:Menu',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('M')
                                    ->where('M.name != :name')
                                    ->setParameter('name', 'Home')
                                    ->orderBy('M.name', 'ASC');
                        },
                        'choice_label' => 'name',
            ));
        }

        $formBuilder
                ->add('body', TextareaType::class, array('attr' => array('novalidate' => 'novalidate')))
        ;
        $editForm = $formBuilder->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //$this->getDoctrine()->getManager()->flush();
            $em = $this->getDoctrine()->getManager();
            if ($page->getIsHome() == TRUE) {
                $page->setName('Home');
            }
            $em->persist($page);
            $em->flush();
            $this->addFlash(
                    'success', '<b>Success!</b><br> Content Saved...'
            );
            return $this->redirectToRoute('myadmin_page_edit2', array('id' => $page->getId()));
        }

        return $this->render('admin/page/edit2.html.twig', array(
                    'page' => $page,
                    'edit_form' => $editForm->createView(),
//
        ));
    }

    /**
     * Creates a form to delete a page entity.
     *
     * @param Page $page The page entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Page $page) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('page_delete', array('id' => $page->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("/myadmin/page/section/delete/{id}", name="myadmin_page_section_delete", methods={"GET","POST"})
     */
    public function pageSectionDelete(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $pageSection = $em->getRepository(PageSection::class)->findOneBy(['id' => $id]);
        $pageSectionImages = $em->getRepository(\App\Entity\PageSectionImages::class)->findBy(['pageSection' => $pageSection->getId()]);
        $pageId = $pageSection->getPage()->getId();
        if ($pageSectionImages) {
            foreach ($pageSectionImages as $psi) {
                $pageSectionImage = $em->getRepository(\App\Entity\PageSectionImages::class)->findOneBy(['id' => $psi->getId()]);
                $em->remove($pageSectionImage);
            }
            $em->flush();
        }
        $em->remove($pageSection);
        $em->flush();
        $pageSections = $em->getRepository(PageSection::class)->findBy(['page' => $pageId], ['rank' => 'DESC']);
        $x = 1;
        foreach ($pageSections as $pss) {
            $ps = $em->getRepository(PageSection::class)->findOneBy(['id' => $pss->getId()]);
            $ps->setRank($x);
            $em->persist($ps);
            $x++;
        }
        $em->flush();
        return $this->redirectToRoute('myadmin_page_open', ['id' => $pageId]);
    }

}
