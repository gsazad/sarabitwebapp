<?php

namespace App\Controller;

use App\Entity\Pg;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PgController
 *
 * @author gurjeet
 */
class PgController extends BaseController {

    /**
     * @Route("/myadmin/photo_gallery/index", name="myadmin_photo_gallery")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $pg = $em->getRepository('App:Pg')->findAll();
        return $this->render('admin/pg/pg.html.twig', array('pg' => $pg));
    }

    /**
     * @Route("/myadmin/photo_gallery/{id}/image/delete", name="myadmin_photo_gallery_delete", methods={"GET","POST"})
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $imageId = $request->get('id');
        $pg = $em->find('App:Pg', $imageId);
        $em->remove($pg);
        $em->flush();
        return $this->redirectToRoute('myadmin_photo_gallery', array());
    }

    /**
     * @Route("/myadmin/photo_gallery/new", name="myadmin_photo_gallery_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $pg = new Pg();
        $form = $this->createFormBuilder($pg)
                ->setAction($this->generateUrl('myadmin_photo_gallery_new'))
                ->setMethod('POST')
                ->add('name')
                ->add('data', FileType::class, array('label' => 'Select Image', 'attr' => array('accept' => ".png,.jpg,.jpeg")))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $_FILES['form'];
//            print_r($file);
//            die;
            $fileType = $file['type']['data'];
            $fileName = '';
            if ($fileType == 'image/png') {
                $fileName = $this->getSlug($form['name']->getData()) . date('d-m-y') . '.png';
            } elseif ($fileType == 'image/jpeg' || $fileType == 'image/jpg') {
                $fileName = $this->getSlug($form['name']->getData()) . date('d-m-y') . '.jpg';
            } else {
                $fileName = $this->getSlug($file['name']['data']);
            }
            $pg->setType($file['type']['data'])
                    ->setData(file_get_contents($file['tmp_name']['data']))
                    ->setCreatedOn($this->myDate())
                    ->setName($form['name']->getData())
                    ->setFileName($fileName);
            $em->persist($pg);
            $em->flush();
            $this->addFlash(
                    'success', '<b>Success!</b><br> Image Saved...'
            );

            return $this->redirectToRoute('myadmin_photo_gallery', array());
        }
        return $this->render('admin/pg/new.html.twig', array('form' => $form->createView()));
    }

}
