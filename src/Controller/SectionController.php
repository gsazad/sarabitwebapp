<?php

namespace App\Controller;

use App\Entity\Section;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Section controller.
 *
 */
class SectionController extends BaseController {
    /**
     * Lists all section entities.
     *
     */

    /**
     * @Route("/myadmin/section/", name="myadmin_section_index", methods="GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $sections = $em->getRepository('App:Section')->findAll();

        return $this->render('section/index.html.twig', array(
                    'sections' => $sections,
        ));
    }

    /**
     * @Route("//section/{id}/{size}/{name}", name="section_image", methods={"GET","POST"})
     */
    public function imageShowAction(Request $request) {
        $id = $request->get('id');
        $size = $request->get('size');
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('App:Section')->findOneBy(['id' => $id]);
        $fileData = '';
        while (!feof($section->getFileData())) {
            $fileData .= fread($section->getFileData(), 1024);
        }
        rewind($section->getFileData());
        header("Content-Type: " . $section->getFileType());
        echo $this->imageResize($fileData, $section->getFileType(), $size);
        die;
    }

    /**
     * @Route("/myadmin/section/{id}/image/new", name="myadmin_section_image_new", methods={"GET","POST"})
     */
    public function newImageAction(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('App:Section')->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($section)
                        ->add('fileData', FileType::class, array('data_class' => null, 'attr' => array('accept' => ".png,.jpg,.jpeg"), 'label' => 'Select Image'))->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $to = $this->getTmpPath() . '/newSection';
            $em = $this->getDoctrine()->getManager();
            $section = $em->getRepository('App:Section')->findOneBy(['id' => $id]);
            $file = $_FILES['form'];
            $fileName = $file['name']['fileData'];
            $fileType = $file['type']['fileData'];
            $fileSize = $file['size']['fileData'];
            $fileTmpName = $file['tmp_name']['fileData'];
            $to = $this->getTmpPath() . '/newSection' . $fileName;
            $this->imageResizeAndSave($fileTmpName, $to, 512);
            $section->setFileData(file_get_contents($to));
            $section->setFileType($fileType);
            $section->setFileName($this->getSlug($fileName));
            $em->persist($section);
            $em->flush();
            @unlink($to);
            return $this->redirectToRoute('myadmin_section_index');
        }


        return $this->render('section/newImage.html.twig', array(
                    'section' => $section,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new section entity.
     *
     */

    /**
     * @Route("/myadmin/section/new", name="myadmin_section_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $section = new Section();
        $form = $this->createForm('App\Form\SectionType', $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();

            return $this->redirectToRoute('myadmin_section_show', array('id' => $section->getId()));
        }

        return $this->render('section/new.html.twig', array(
                    'section' => $section,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a section entity.
     *
     */

    /**
     * @Route("/myadmin/section/{id}/show", name="myadmin_section_show", methods="GET")
     */
    public function showAction(Section $section) {
        $deleteForm = $this->createDeleteForm($section);

        return $this->render('section/show.html.twig', array(
                    'section' => $section,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing section entity.
     *
     */

    /**
     * @Route("/myadmin/section/{id}/edit", name="myadmin_section_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Section $section) {
        $deleteForm = $this->createDeleteForm($section);
        $editForm = $this->createForm('App\Form\SectionType', $section);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_section_edit', array('id' => $section->getId()));
        }

        return $this->render('section/edit.html.twig', array(
                    'section' => $section,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a section entity.
     *
     */

    /**
     * @Route("/myadmin/section/{id}/delete", name="myadmin_section_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, Section $section) {
        $form = $this->createDeleteForm($section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($section);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_section_index');
    }

    /**
     * Creates a form to delete a section entity.
     *
     * @param Section $section The section entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Section $section) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_section_delete', array('id' => $section->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
