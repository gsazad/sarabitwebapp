<?php

namespace App\Controller;

use App\Entity\PhotoGallery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Photogallery controller.
 *
 */
class PhotoGalleryController extends BaseController {
    /**
     * Lists all photoGallery entities.
     *
     */

    /**
     * @Route("/myadmin/photogallery/{albumId}/index", name="myadmin_photogallery_index", methods="GET")
     */
    public function indexAction(Request $request) {
        $albumId = $request->get('albumId');
        $em = $this->getDoctrine()->getManager();

        $photoGalleries = $em->getRepository('App:PhotoGallery')->findBy(['album' => $albumId]);
        $album = $em->getRepository('App:Album')->findOneBy(['id' => $albumId]);

        return $this->render('photogallery/index.html.twig', array(
                    'photoGalleries' => $photoGalleries,
                    'albumId' => $albumId,
                    'album' => $album,
        ));
    }

    /**
     * Creates a new photoGallery entity.
     *
     */

    /**
     * @Route("/myadmin/photogallery/{albumId}/new", name="myadmin_photogallery_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $albumId = $request->get('albumId');
        $photoGallery = new Photogallery();
        $form = $this->createForm(\App\Form\PhotoGalleryType::class, $photoGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $album = $em->getRepository('App:Album')->findOneBy(['id' => $albumId]);
            $photoGallery = new Photogallery();
            $file = $_FILES['appbundle_photogallery'];
            $fileName = $file['name']['fileData'];
            $fileType = $file['type']['fileData'];
            $fileTmpName = $file['tmp_name']['fileData'];
            
            $data = $this->imageResize(file_get_contents($fileTmpName), $fileType, 800);
            
//            $to = $this->getTmpPath() . '/Photo' . $fileName;
//            $this->imageResizeAndSave($fileTmpName, $to, 800);
            
            
            $photoGallery->setTitle($form['title']->getData());
            $photoGallery->setDescription($form['description']->getData());
            $photoGallery->setAlbum($album);
            $photoGallery->setCreatedOn($this->mydate());
            $photoGallery->setFileName($this->getSlug($fileName));
            $photoGallery->setFileType($fileType);
            $photoGallery->setFileData($data);


            $em = $this->getDoctrine()->getManager();
            $em->persist($photoGallery);
            $em->flush();
            @unlink($to);

            return $this->redirectToRoute('myadmin_photogallery_index', array('albumId' => $albumId));
        }

        return $this->render('photogallery/new.html.twig', array(
                    'photoGallery' => $photoGallery,
                    'form' => $form->createView(),
                    'albumId' => $albumId
        ));
    }

    /**
     * @Route("/myadmin/photogallery/{id}/edit/image/{albumId}/album", name="myadmin_photogallery_edit_image", methods={"GET","POST"})
     */
    public function editImageAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $imageId = $request->get('id');
        $albumId = $request->get('albumId');
        $pg = $em->getRepository('App:PhotoGallery')->findOneBy(['id' => $imageId, 'album' => $albumId]);
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('myadmin_photogallery_edit_image', ['id' => $imageId, 'albumId' => $albumId]))
                ->setMethod('POST')
                ->add('data', FileType::class, array('label' => 'Select Image', 'attr' => array('accept' => ".png,.jpg,.jpeg")))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $_FILES['form'];
//            print_r($file);
//            die;
            $fileType = $file['type']['data'];
            $pg->setFileType($file['type']['data'])
                    ->setFileData(file_get_contents($file['tmp_name']['data']));

            $em->persist($pg);
            $em->flush();
            $this->addFlash(
                    'success', '<b>Success!</b><br> Image Saved...'
            );

            return $this->redirectToRoute('myadmin_photogallery_index', array('albumId' => $pg->getAlbum()->getId()));
        }
        return $this->render('photogallery/editImage.html.twig', array('form' => $form->createView()));
    }

    /**
     * Finds and displays a photoGallery entity.
     *
     */

    /**
     * @Route("/myadmin/photogallery/{id}/show", name="myadmin_photogallery_show", methods="GET")
     */
    public function showAction(PhotoGallery $photoGallery) {
        $deleteForm = $this->createDeleteForm($photoGallery);

        return $this->render('photogallery/show.html.twig', array(
                    'photoGallery' => $photoGallery,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing photoGallery entity.
     *
     */

    /**
     * @Route("/myadmin/photogallery/{id}/edit", name="myadmin_photogallery_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, PhotoGallery $photoGallery) {
        $deleteForm = $this->createDeleteForm($photoGallery);
        $editForm = $this->createFormBuilder($photoGallery)->add('title')
                        ->add('description')
                        ->add('album', EntityType::class, array(
                            'class' => 'App:Album',
                            'choice_label' => 'name',
                        ))->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_photogallery_edit', array('id' => $photoGallery->getId()));
        }

        return $this->render('photogallery/edit.html.twig', array(
                    'photoGallery' => $photoGallery,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a photoGallery entity.
     *
     */

    /**
     * @Route("/myadmin/photogallery/{id}/delete", name="myadmin_photogallery_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, PhotoGallery $photoGallery) {
        $form = $this->createDeleteForm($photoGallery);
        $albumId = $photoGallery->getAlbumId();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($photoGallery);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_photogallery_index', array('albumId' => $albumId));
    }

    /**
     * Creates a form to delete a photoGallery entity.
     *
     * @param PhotoGallery $photoGallery The photoGallery entity
     *
     * @return Form The form
     */
    private function createDeleteForm(PhotoGallery $photoGallery) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_photogallery_delete', array('id' => $photoGallery->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("//photogallery/thumbnails/{id}/{size}/{name}", name="photogallery_thumb")
     */
    public function photoGalleryThumbAction(Request $request) {
        $id = $request->get('id');
        $name = $request->get('name');
        $size = $request->get('size');
        $em = $this->getDoctrine()->getManager();
        $s = $em->getRepository('App:PhotoGallery')->findOneBy(['id' => $id]);
        $data = '';
        while (!feof($s->getFileData())) {
            $data .= fread($s->getFileData(), 1024);
        }
        rewind($s->getFileData());
        $new_w = $size;
        $new_h = $size;
        $type = $s->getFileType();
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
        header('Content-Type:' . $type);
        if ($type == 'image/png') {
            imagepng($dst_img);
        } elseif ($type == 'image/jpg' || $type == 'image/jpeg') {
            imagejpeg($dst_img);
        }
        die;

        //resize
    }

}
