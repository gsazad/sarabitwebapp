<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Product controller.
 *
 */
class ProductController extends BaseController {
    /**
     * Lists all product entities.
     *
     */

    /**
     * @Route("/myadmin/product/{catId}/index", name="myadmin_product_index", methods="GET")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $catId = $request->get('catId');
        $cat = $em->getRepository('App:ProCat')->findOneBy(['id' => $catId]);
        $products = $em->getRepository('App:Product')->findBy(['categoryId' => $catId]);

        return $this->render('product/index.html.twig', array(
                    'products' => $products,
                    'cat' => $cat
        ));
    }

    /**
     * @Route("/myadmin/product/{catId}/image/{id}/edit", name="myadmin_product_image_edit", methods={"GET","POST"})
     */
    public function imageEditAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $catId = $request->get('catId');
        $id = $request->get('id');
        $product = $em->getRepository('App:Product')->findOneBy(['id' => $id]);
        $form = $this->createFormBuilder($product)
                        ->setAction($this->generateUrl('myadmin_product_image_edit', array('catId' => $catId, 'id' => $id)))
                        ->add('filedata', FileType::class, array('mapped' => false, 'label' => 'Select JPG', 'attr' => array('accept' => '.jpg,.jpeg')))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $_FILES['form'];
            $fileName = $file['name']['filedata'];
            $fileType = $file['type']['filedata'];
            $fileTmpPath = $file['tmp_name']['filedata'];
            $to = $this->getTmpPath() . '/proPhoto' . $fileName;
            $this->imageResizeAndSave($fileTmpPath, $to, 800);
            $product->setFileData(file_get_contents($to));
            $product->setFileName($fileName);
            $product->setFileType($fileType);
            $em->persist($product);
            $em->flush();
            @unlink($to);
            $this->addFlash('success', 'Product Photo Updated..');

            return $this->redirectToRoute('myadmin_product_index', array('catId' => $catId));
        }

        return $this->render('product/image.html.twig', ['form' => $form->createView(), 'product' => $product]);
    }

    /**
     * @Route("//productimage/{id}/{size}/{name}", name="product_image", methods={"GET","POST"})
     */
    public function imageShowAction(Request $request) {
        $id = $request->get('id');
        $size = $request->get('size');
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('App:Product')->findOneBy(['id' => $id]);
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
     * Creates a new product entity.
     *
     */

    /**
     * @Route("/myadmin/product/{catId}/new", name="myadmin_product_new", methods={"GET","POST"})
     */
    public function newAction(Request $request) {
        $product = new Product();
        $em = $this->getDoctrine()->getManager();
        $catId = $request->get('catId');
        $cat = $em->getRepository('App:ProCat')->findOneBy(['id' => $catId]);
        $form = $this->createFormBuilder($product)->add('name')
                ->add('description')
                ->add('keywords')
                ->add('body')
                ->add('filedata', FileType::class, array('label' => 'Select Product Photo', 'attr' => array('accept' => '.jpg,.jpeg')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $_FILES['form'];
            $fileName = $file['name']['filedata'];
            $fileType = $file['type']['filedata'];
            $fileTmpPath = $file['tmp_name']['filedata'];
            $to = $this->getTmpPath() . '/proPhoto' . $fileName;
            $this->imageResizeAndSave($fileTmpPath, $to, 800);

            $em = $this->getDoctrine()->getManager();
            $product = new Product();
            $product->setProcat($cat);
            $product->setName($form['name']->getData());
            $product->setDescription($form['description']->getData());
            $product->setBody($form['body']->getData());
            $product->setKeywords(strtolower($form['keywords']->getData()));
            $product->setFileData(file_get_contents($to));
            $product->setFileName($fileName);
            $product->setFileType($fileType);
            $em->persist($product);
            $em->flush();
            @unlink($to);

            return $this->redirectToRoute('myadmin_product_index', array('catId' => $catId));
        }

        return $this->render('product/new.html.twig', array(
                    'product' => $product,
                    'form' => $form->createView(),
                    'cat' => $cat,
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     */

    /**
     * @Route("/myadmin/product/{id}/show", name="myadmin_product_show", methods="GET")
     */
    public function showAction(Product $product) {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('product/show.html.twig', array(
                    'product' => $product,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     */

    /**
     * @Route("/myadmin/product/{id}/edit", name="myadmin_product_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Product $product) {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createFormBuilder($product)->add('name')
                ->add('description')
                ->add('keywords')
                ->add('body')
                ->getForm();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('myadmin_product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
                    'product' => $product,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     */

    /**
     * @Route("/myadmin/product/{id}/delete", name="myadmin_product_delete", methods="DELETE")
     */
    public function deleteAction(Request $request, Product $product) {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('myadmin_procat_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Product $product) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('myadmin_product_delete', array('id' => $product->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    private function getSub($id) {
        $master = array();
        $em = $this->getDoctrine()->getManager();
        $proCats = $em->getRepository('App:ProCat')->findBy(['parent' => $id]);
        foreach ($proCats as $v) {
            $master[] = array('key' => $v->getId(), 'value' => $v->getName());
        }
        return $master;
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

    /**
     * @Route("/myadmin/product/cat/select", name="myadmin_product_cat_select", methods="GET")
     */
    public function catSelectAction() {
        $master = array();
        $em = $this->getDoctrine()->getManager();
        $procat = $em->getRepository('App:ProCat')->findBy(['parent' => '0']);
        foreach ($procat as $v) {
            $master[] = array('key' => $v->getId(), 'value' => $v->getName(), 'isSub' => $this->hasSub($v->getId()), 'sub' => $this->getSub($v->getId()));
        }
        return $this->render('product/select.html.twig', ['selects' => $master]);
    }

}
