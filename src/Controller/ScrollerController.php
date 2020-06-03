<?php

namespace App\Controller;

use App\Entity\Scroller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Scroller controller.
 *
 */
class ScrollerController extends BaseController {
    /**
     * Lists all scroller entities.
     *
     */

    /**
     * @Route("/myadmin/scroller/", name="myadmin_scroller_index", methods="GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $scrollers = $em->getRepository('App:Scroller')->findAll();

        return $this->render('scroller/index.html.twig', array(
                    'scrollers' => $scrollers,
        ));
    }

    /**
     * Creates a new scroller entity.
     *
     */

    /**
     * @Route("/myadmin/scroller/new", name="myadmin_scroller_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $scroller = new Scroller();
        $form = $this->createForm('App\Form\ScrollerType', $scroller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $_FILES['appbundle_scroller'];
//            print_r($file['tmp_name']['filedata']);
//            die;
            $destination = $this->getTmpPath() . '/newScrollet' . $file['name']['filedata'];
            $this->imageResizeAndSave($file['tmp_name']['filedata'], $destination, 1500);
            $scroller->setFiletype($file['type']['filedata'])
                    ->setFiledata(file_get_contents($destination));
            $em->persist($scroller);
            $em->flush();
            @unlink($destination);

            return $this->redirectToRoute('myadmin_scroller_show', array('id' => $scroller->getId()));
        }

        return $this->render('scroller/new.html.twig', array(
                    'scroller' => $scroller,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a scroller entity.
     *
     */

    /**
     * @Route("/myadmin/scroller/{id}/show", name="myadmin_scroller_show", methods="GET")
     */
    public function showAction(Scroller $scroller) {
        $deleteForm = $this->createDeleteForm($scroller);

        return $this->render('scroller/show.html.twig', array(
                    'scroller' => $scroller,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing scroller entity.
     *
     */

    /**
     * @Route("/myadmin/scroller/{id}/edit", name="myadmin_scroller_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Scroller $scroller) {
        $deleteForm = $this->createDeleteForm($scroller);
        //$editForm = $this->createForm('App\Form\ScrollerType', $scroller);
        $editForm = $this->createFormBuilder($scroller)
                        ->add('header')
                        ->add('description')
                        ->add('url')->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_scroller_edit', array('id' => $scroller->getId()));
        }

        return $this->render('scroller/edit.html.twig', array(
                    'scroller' => $scroller,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/myadmin/scroller/{id}/image", name="myadmin_scroller_image", methods={"GET","POST"})
     */
    public function editImageAction(Request $request) {

        $tmpPath = $this->getTmpPath() . '/';
        $tmpNamePrefix = date(time());


        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $scroller = $em->getRepository('App:Scroller')->findOneBy(['id' => $id]);

        $editForm = $this->createFormBuilder($scroller)
                        ->add('filedata', FileType::class, array('data_class' => null, 'label' => 'Select Image', 'required' => false,
                            'attr' => array('accept' => ".png,.jpg,.jpeg")
                        ))->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $scroller = $em->getRepository('App:Scroller')->findOneBy(['id' => $id]);
            $file = $_FILES['form'];
            $destination = $tmpPath . $tmpNamePrefix . $file['name']['filedata'];
            $fileTmpName = $file['tmp_name']['filedata'];
            $this->imageResizeAndSave($fileTmpName, $destination, 1500);
            $scroller->setFiletype($file['type']['filedata'])
                    ->setFiledata(file_get_contents($destination));
            $em->persist($scroller);
            $em->flush();
            @unlink($destination);


            return $this->redirectToRoute('myadmin_scroller_index', array('id' => $scroller->getId()));
        }

        return $this->render('scroller/image.html.twig', array(
                    'scroller' => $scroller,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a scroller entity.
     *
     */

    /**
     * @Route("/myadmin/scroller/{id}/delete", name="myadmin_scroller_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, Scroller $scroller) {
        $form = $this->createDeleteForm($scroller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($scroller);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_scroller_index');
    }

    /**
     * Creates a form to delete a scroller entity.
     *
     * @param Scroller $scroller The scroller entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Scroller $scroller) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_scroller_delete', array('id' => $scroller->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
