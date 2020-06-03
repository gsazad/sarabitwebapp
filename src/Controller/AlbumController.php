<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Album controller.
 *
 */
class AlbumController extends BaseController {
    /**
     * Lists all album entities.
     *
     */

    /**
     * @Route("/myadmin/album/", name="myadmin_album_index", methods="GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $albums = $em->getRepository('App:Album')->findAll();

        return $this->render('album/index.html.twig', array(
                    'albums' => $albums,
        ));
    }

    /**
     * Creates a new album entity.
     *
     */

    /**
     * @Route("/myadmin/album/new", name="myadmin_album_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $album = new Album();
        $form = $this->createForm('App\Form\AlbumType', $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();

            return $this->redirectToRoute('myadmin_album_show', array('id' => $album->getId()));
        }

        return $this->render('album/new.html.twig', array(
                    'album' => $album,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a album entity.
     *
     */

    /**
     * @Route("/myadmin/album/{id}/show", name="myadmin_album_show", methods="GET")
     */
    public function showAction(Album $album) {
        $deleteForm = $this->createDeleteForm($album);

        return $this->render('album/show.html.twig', array(
                    'album' => $album,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing album entity.
     *
     */

    /**
     * @Route("/myadmin/album/{id}/edit", name="myadmin_album_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Album $album) {
        $deleteForm = $this->createDeleteForm($album);
        $editForm = $this->createForm('App\Form\AlbumType', $album);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_album_edit', array('id' => $album->getId()));
        }

        return $this->render('album/edit.html.twig', array(
                    'album' => $album,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a album entity.
     *
     */

    /**
     * @Route("/myadmin/album/{id}/delete", name="myadmin_album_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, Album $album) {
        $form = $this->createDeleteForm($album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($album);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_album_index');
    }

    /**
     * Creates a form to delete a album entity.
     *
     * @param Album $album The album entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Album $album) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_album_delete', array('id' => $album->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
