<?php

namespace App\Controller;

use App\Entity\Enquiry;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        //$core = $this->get('core_service');
        // replace this example code with whatever you need
        $em = $this->getDoctrine()->getManager();
        $settings = $this->getSetting();
        if ($settings['default_home_sections'] == 'yes') {
            $sections = $em->getRepository('App:Section')->findAll();
        } else {
            $sections = null;
        }
        $scroller = $em->getRepository('App:Scroller')->findAll();
        $home = $em->getRepository('App:Page')->findOneBy(['name' => 'Home']);
        $body = $this->bodyFilter($home->getBody());
        $response = $this->render('business/index.html.twig', ['sections' => $sections, 'scroller' => $scroller, 'home' => $home, 'body' => $body]);

        return $this->etagResponse($response, $request, true);
    }

    /**
     * @Route("/scroller/{id}/{name}", name="scroller_image")
     */
    public function scrollImgAction(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $em = $this->getDoctrine()->getManager();
        $s = $em->getRepository('App:Scroller')->findOneBy(['id' => $id]);
        $data = '';
        while (!feof($s->getFiledata())) {
            $data .= fread($s->getFiledata(), 1024);
        }
        rewind($s->getFiledata());

        $response = new Response($data, Response::HTTP_OK, array('content-type' => $s->getFiletype()));
        return $this->etagResponse($response, $request, true);
    }

    /**
     * @Route("/myadmin/photoG/{id}/photo/{name}", name="pg_image")
     */
    public function pgImgAction(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $em = $this->getDoctrine()->getManager();
        $s = $em->getRepository('App:Pg')->findOneBy(['id' => $id]);
        $data = '';
        while (!feof($s->getData())) {
            $data .= fread($s->getData(), 1024);
        }
        rewind($s->getData());

        return new Response($data, Response::HTTP_OK, array('content-type' => $s->getType()));
    }

    /**
     * @Route("/photoG/{id}/photo/{name}", name="pg_image2")
     */
    public function pgImg2Action(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $em = $this->getDoctrine()->getManager();
        $s = $em->getRepository('App:Pg')->findOneBy(['id' => $id]);
        $data = '';
        while (!feof($s->getData())) {
            $data .= fread($s->getData(), 1024);
        }
        rewind($s->getData());
        $response = new Response($data, 200, ['content-type' => $s->getType()]);
        return $this->etagResponse($response, $request, true);
    }

    /**
     * @Route("/pg/{id}/{size}/{name}", name="pg_image_thumb")
     */
    public function pgImgThumbAction(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $size = $request->get('size');
        $em = $this->getDoctrine()->getManager();
        $s = $em->getRepository('App:Pg')->findOneBy(['id' => $id]);
        $data = '';
        while (!feof($s->getData())) {
            $data .= fread($s->getData(), 1024);
        }
        rewind($s->getData());
        $new_w = $size;
        $new_h = $size;
        $type = $s->getType();
        //resize
        $src_img = imagecreatefromstring($data);

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $old_y * ($new_h / $old_x);
        }
        if ($old_x < $old_y) {
            $thumb_w = $old_x * ($new_w / $old_y);
            $thumb_h = $new_h;
        }
        if ($old_x == $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_h;
        }

        $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        //header('Content-Type:' . $type);
        ob_start();

        if ($type == 'image/png') {
            imagepng($dst_img);
        } elseif ($type == 'image/jpg' || $type == 'image/jpeg') {
            imagejpeg($dst_img);
        }
        $content = ob_get_contents();
        ob_end_clean();
        $response = new Response($content, 200, ['content-type' => $type]);

        return $this->etagResponse($response, $request, true);
    }

    /**
     * @Route("/css/style.css", name="css")
     */
    public function cssAction(Request $request) {
        //header("Content-Type: text/html");
        $css = $this->renderView('business/style.css', []);
        $response = new Response($css, Response::HTTP_OK, array('content-type' => 'text/css'));
        return $this->etagResponse($response, $request, true);
    }

    /**
     * @Route("/image_list.js", name="html_image_list")
     */
    public function imageListAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $pg = $em->getRepository('App:Pg')->findAll();
        $js = $this->renderView('admin/pg/pg.js.twig', array('pg' => $pg));
        return new Response($js, 200, array('content-type' => 'text/javascript'));
    }

    public function navAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $menus = $em->getRepository('App:Menu')->findAll();
        $nav = array();
        $productMenuId = $this->getSetting()['product_manu_id'];
        foreach ($menus as $k => $v) {
            $page = $em->getRepository('App:Page')->findBy(array('menu' => $v->getId()));
            $nav[$k]['menu'] = $v;
            $pCount = count($page);
            $nav[$k]['count'] = $pCount;
            if ($v->getId() == $productMenuId) {
                $nav[$k]['productmenu'] = true;
                $pCount = 0;
            } else {
                $nav[$k]['productmenu'] = false;
            }

            if ($pCount == 0) {
                $nav[$k]['sub'] = FALSE;
                $nav[$k]['pages'] = null;
            } elseif ($pCount == 1) {
                $nav[$k]['pages'] = $page;
                $nav[$k]['sub'] = FALSE;
            } else {
                $nav[$k]['pages'] = $page;
                $nav[$k]['sub'] = TRUE;
            }
        }

        $procat = $em->getRepository('App:ProCat')->findBy(['parent' => '0']);
        $pcat = array();
        foreach ($procat as $k => $v) {
            $subcat = $em->getRepository('App:ProCat')->findBy(['parent' => $v->getId()]);
            $pcat[$k]['cat'] = $v;
            if (count($subcat) > 0) {
                $pcat[$k]['sub'] = $subcat;
            } else {
                $pcat[$k]['sub'] = null;
            }
        }



        return $this->render('business/nav.html.twig', ['menus' => $nav, 'cat' => $pcat]);
    }

    /**
     * @Route("/enquirysent.html", name="enquiry_sent", methods="GET")
     */
    public function enquirySentAction(Request $request) {
        return $this->render('business/enquirySent.html.twig');
    }

    /**
     * @Route("/enquiryform.html", name="enquiry", methods={"GET","POST"})
     */
    public function enquiryAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $enquiry = new Enquiry();
        $fixFields = array('Name', 'Email');
        $moreFields = explode(',', $this->getSetting()['enquiry_form_fields']);

        $formBuilder = $this->createFormBuilder($enquiry);
        $formBuilder->setAction($this->generateUrl('enquiry'));
        foreach ($fixFields as $v) {
            if ($v == 'Email') {
                $formBuilder->add($v, EmailType::class, array('mapped' => false, 'label' => $v));
            } else {
                $formBuilder->add($v, TextType::class, array('mapped' => false, 'label' => $v));
            }
        }
        foreach ($moreFields as $v) {
            $formBuilder->add($v, TextType::class, array('mapped' => false, 'label' => ucwords($v)));
        }
        $formBuilder->add('Message', TextareaType::class, array('mapped' => false, 'label' => ucwords('Message')));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $customFields = array();
            foreach ($moreFields as $v) {
                $customFields[] = array('kay' => $v, 'value' => $form[$v]->getData());
            }
            $enquiry->setCreatedOn($this->myDate())
                    ->setEmail($form['Email']->getData())
                    ->setName($form['Name']->getData())
                    ->setQuery($form['Message']->getData())
                    ->setCustomFields(json_encode($customFields))
                    ->setIsReaded(false);
            $em->persist($enquiry);
            $em->flush();
            return $this->redirectToRoute('enquiry_sent');
        }

        return $this->render('business/enquiry.html.twig', array('form' => $form->createView()));
    }

    public function info() {
        /*
         * ssh thecom@m.theluminars.com -p 299
         * 654321@@@tttTTT
         * DB INFO
         * User: thecom_cms 
         * Database: thecom_cms
         * PASS: nIZdTMSVimaQL3kMRD
         */
    }

}
