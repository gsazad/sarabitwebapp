<?php

namespace App\Controller;

use App\Entity\ProCat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use UI\Controls\Form;

/**
 * Procat controller.
 *
 */
class ProCatController extends BaseController {
    /**
     * Lists all proCat entities.
     *
     */

    /**
     * @Route("/myadmin/procat/", name="myadmin_procat_index", methods={"GET","POST"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $proCatNew = new Procat();
        $form = $this->createFormBuilder($proCatNew)
                        ->add('name')
                        ->add('parent', ChoiceType::class, array('choices' => $this->getMasterCat()))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proCatNew);
            $em->flush();

            return $this->redirectToRoute('myadmin_procat_index');
        }

        $proCats = $em->getRepository('App:ProCat')->findBy(['parent' => 0]);

        $cats = array();
        foreach ($proCats as $v) {
            $cats[] = array(
                'id' => $v->getId(),
                'name' => $v->getName(),
                'parent' => $v->getParent(),
                'isSub' => $this->hasSub($v->getId()),
                'sub' => $this->getSub($v->getId())
            );
        }

        return $this->render('procat/index.html.twig', array(
                    'proCats' => $cats,
                    'form' => $form->createView(),
                    'proCat' => $proCatNew,
        ));
    }

    public function hasSub($id) {
        $em = $this->getDoctrine()->getManager();
        $proCats = $em->getRepository('App:ProCat')->findBy(['parent' => $id]);
        if ($proCats) {
            return true;
        } else {
            return false;
        }
    }

    public function getSub($id) {
        $em = $this->getDoctrine()->getManager();
        return $proCats = $em->getRepository('App:ProCat')->findBy(['parent' => $id]);
    }

    /**
     * Creates a new proCat entity.
     *
     */

    /**
     * @Route("/myadmin/procat/new", name="myadmin_procat_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $proCat = new Procat();
        //$form = $this->createForm('App\Form\ProCatType', $proCat);
        $form = $this->createFormBuilder($proCat)
                        ->add('name')
                        ->add('parent', ChoiceType::class, array('choices' => $this->getMasterCat()))->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proCat);
            $em->flush();

            return $this->redirectToRoute('myadmin_procat_show', array('id' => $proCat->getId()));
        }

        return $this->render('procat/new.html.twig', array(
                    'proCat' => $proCat,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proCat entity.
     *
     */

    /**
     * @Route("/myadmin/procat/{id}/show", name="myadmin_procat_show", methods="GET")
     */
    public function showAction(ProCat $proCat) {
        $deleteForm = $this->createDeleteForm($proCat);

        return $this->render('procat/show.html.twig', array(
                    'proCat' => $proCat,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proCat entity.
     *
     */

    /**
     * @Route("/myadmin/procat/{id}/edit", name="myadmin_procat_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, ProCat $proCat) {
        $deleteForm = $this->createDeleteForm($proCat);
        $editForm = $this->createFormBuilder($proCat)
                        ->add('name')
                        ->add('parent', ChoiceType::class, array('choices' => $this->getMasterCat()))->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_procat_edit', array('id' => $proCat->getId()));
        }

        return $this->render('procat/edit.html.twig', array(
                    'proCat' => $proCat,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proCat entity.
     *
     */

    /**
     * @Route("/myadmin/procat/{id}/delete", name="myadmin_procat_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, ProCat $proCat) {
        $form = $this->createDeleteForm($proCat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proCat);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_procat_index');
    }

    /**
     * Creates a form to delete a proCat entity.
     *
     * @param ProCat $proCat The proCat entity
     *
     * @return Form The form
     */
    private function createDeleteForm(ProCat $proCat) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_procat_delete', array('id' => $proCat->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    public function getMasterCat() {
        $master = array();
        $em = $this->getDoctrine()->getManager();
        $master['Master'] = 0;
        $procat = $em->getRepository('App:ProCat')->findBy(['parent' => '0']);
        foreach ($procat as $v) {
            $master[$v->getName()] = $v->getId();
        }
        return $master;
    }

}
