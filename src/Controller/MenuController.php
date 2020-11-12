<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Menu controller.
 *
 */
class MenuController extends BaseController {
    /**
     * Lists all menu entities.
     *
     */

    /**
     * @Route("/myadmin/menu/", name="myadmin_menu_index", methods="GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $menus = $em->getRepository('App:Menu')->findBy(['parent' => null]);

        $array = array();
        foreach ($menus as $k => $v) {
            $array[$k]['menu'] = $v;
            $pages = $em->getRepository('App:Page')->findBy(['menu' => $v->getId()]);
            $array[$k]['count'] = count($pages);
        }

        return $this->render('admin/menu/index.html.twig', array(
                    'menus' => $array,
        ));
    }

    /**
     * Creates a new menu entity.
     *
     */

    /**
     * @Route("/myadmin/menu/new", name="myadmin_menu_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $menu = new Menu();
        $form = $this->createForm('App\Form\MenuType', $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            return $this->redirectToRoute('myadmin_menu_show', array('id' => $menu->getId()));
        }

        return $this->render('admin/menu/new.html.twig', array(
                    'menu' => $menu,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a menu entity.
     *
     */

    /**
     * @Route("/myadmin/menu/{id}/show", name="myadmin_menu_show", methods="GET")
     */
    public function showAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $deleteForm = $this->createDeleteForm($menu);

        return $this->render('admin/menu/show.html.twig', array(
                    'menu' => $menu,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing menu entity.
     *
     */

    /**
     * @Route("/myadmin/menu/{id}/edit", name="myadmin_menu_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $deleteForm = $this->createDeleteForm($menu);
        $editForm = $this->createForm('App\Form\MenuType', $menu);
        $editForm->handleRequest($request);
        $pages = $em->getRepository('App:Page')->findBy(['menu' => $menu->getId()]);
        $count = count($pages);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_menu_edit', array('id' => $menu->getId()));
        }

        return $this->render('admin/menu/edit.html.twig', array(
                    'menu' => $menu,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'count' => $count,
        ));
    }

    /**
     * @Route("/myadmin/menu/add/child/{id}", name="myadmin_menu_add_child_menu", methods={"GET","POST"})
     */
    public function addChildMenu(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setParent($parent);
            $em->persist($menu);
            $em->flush();

            return $this->redirectToRoute('myadmin_menu_child_list', array('id' => $id));
        }

        return $this->render('admin/menu/new.html.twig', array(
                    'menu' => $menu,
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/myadmin/menu/child/{id}/list", name="myadmin_menu_child_list", methods={"GET","POST"})
     */
    public function menuChildList(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $childMenu = $em->getRepository(Menu::class)->findBy(['parent' => $id]);

        return $this->render('admin/menu/childList.html.twig', ['childMenu' => $childMenu, 'parentId' => $id]);
    }

    /**
     * @Route("/myadmin/menu/child/{id}/image/upload", name="myadmin_menu_child_image_upload", methods={"GET","POST"})
     */
    public function menu_child_image_upload(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $form = $this->createFormBuilder($menu)
                ->setAction($this->generateUrl('myadmin_menu_child_list', ['id' => $id]))
                ->add('upload', FileType::class, ['mapped' => false])
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['upload']->getData();
            $fileinfo = $this->getUploadedFileInfo($file);
            $filedata = file_get_contents($fileinfo['realPath']);
            $filedata = $this->imageResize($filedata, $fileinfo['mimetype'], 600);
            $menu->setFileData($filedata)
                    ->setFileType($fileinfo['mimetype'])
                    ->setFileName($fileinfo['filename'])

            ;
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('myadmin_menu_child_list', ['id' => $id]);
        }
        return $this->render('admin/menu/imageUpload.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Deletes a menu entity.
     *
     */

    /**
     * @Route("/myadmin/menu/{id}/delete", name="myadmin_menu_delete", methods="DELETE")
     */
    public function deleteAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $form = $this->createDeleteForm($menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_menu_index');
    }

    /**
     * Creates a form to delete a menu entity.
     *
     * @param Menu $menu The menu entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Menu $menu) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_menu_delete', array('id' => $menu->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
