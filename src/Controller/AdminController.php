<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends BaseController {

    /**
     * @Route("/myadmin", name="myadmin_index", methods="GET")
     */
    public function indexAction() {
        return $this->render('admin/index.html.twig', []);
    }

    /**
     * @Route("/myadmin/enquiry", name="myadmin_enquiry", methods="GET")
     */
    public function enquiryAction() {
        $em = $this->getDoctrine()->getManager();
        //$pages = $em->getRepository('App:Enquiry')->findAll();
        $query = $em->createQuery(
                'SELECT E
                            FROM App:Enquiry E
                            ORDER BY E.createdOn DESC'
        );

        $enquirys = $query->getResult();
        return $this->render('admin/enquiryList.html.twig', ['enquirys' => $enquirys]);
    }

    /**
     * @Route("/myadmin/enquiry/{id}/view", name="myadmin_enquiry_view", methods="GET")
     */
    public function enquiryViewAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $enquiry = $em->getRepository('App:Enquiry')->findOneBy(['id' => $id]);
        $custom = json_decode($enquiry->getCustomFields());
        $enquiry->setIsReaded(true);
        $em->persist($enquiry);
        $em->flush();
        return $this->render('admin/enquiryView.html.twig', ['enquiry' => $enquiry, 'custom' => $custom]);
    }

    /**
     * @Route("/myadmin/page/index", name="myadmin_page_index", methods="GET")
     */
    public function pageIndexAction() {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('App:Page')->findAll();
//        $query = $em->createQuery(
//                        'SELECT p.id, p.title, p.name, p.body, m.name
//                            FROM App:Page p
//                            JOIN App:Menu m WITH p.menuId = m.id 
//                            ORDER BY p.name ASC'
//                );
        //$pages = $query->getResult();
        return $this->render('admin/page/index.html.twig', ['pages' => $pages]);
    }

    /**
     * @Route("/myadmin/settings", name="myadmin_settings", methods={"GET","POST"})
     */
    public function settingsAction() {
        $session = new Session;
        $em = $this->getDoctrine()->getManager();
        //$domainEntity = $em->getRepository('App:Domain')->findAll();
        if (isset($_POST['btnSave'])) {
            $opt = $_POST['opt'];
            foreach ($opt as $k => $v) {
                $s = $em->getRepository('App:Setting')->find($k);
                $s->setData(trim($v));
                $em->persist($s);
                $em->flush();
            }
            return $this->redirectToRoute('myadmin_settings');
        }
        $settings = $em->getRepository('App:Setting')->findAll();
        $array = [];
        foreach ($settings as $v) {

            $options = $v->getOptions();
            //print_r(json_decode($options));

            $array[] = array(
                'id' => $v->getId(),
                'opt' => $v->getOpt(),
                'type' => $v->getType(),
                'data' => $v->getData(),
                'options' => json_decode($options),
            );
        }

        return $this->render('admin/setting.html.twig', array(
                    'settings' => $array,
        ));
    }

}
