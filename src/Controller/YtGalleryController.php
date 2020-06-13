<?php

namespace App\Controller;

use App\Entity\YtGallery;
use App\Form\YtGalleryType;
use App\Repository\YtGalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/myadmin/yt/gallery")
 */
class YtGalleryController extends AbstractController
{
    /**
     * @Route("/", name="yt_gallery_index", methods={"GET"})
     */
    public function index(YtGalleryRepository $ytGalleryRepository): Response
    {
        return $this->render('yt_gallery/index.html.twig', [
            'yt_galleries' => $ytGalleryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="yt_gallery_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ytGallery = new YtGallery();
        $form = $this->createForm(YtGalleryType::class, $ytGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ytGallery);
            $entityManager->flush();

            return $this->redirectToRoute('yt_gallery_index');
        }

        return $this->render('yt_gallery/new.html.twig', [
            'yt_gallery' => $ytGallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="yt_gallery_show", methods={"GET"})
     */
    public function show(YtGallery $ytGallery): Response
    {
        return $this->render('yt_gallery/show.html.twig', [
            'yt_gallery' => $ytGallery,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="yt_gallery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, YtGallery $ytGallery): Response
    {
        $form = $this->createForm(YtGalleryType::class, $ytGallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('yt_gallery_index');
        }

        return $this->render('yt_gallery/edit.html.twig', [
            'yt_gallery' => $ytGallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="yt_gallery_delete", methods={"DELETE"})
     */
    public function delete(Request $request, YtGallery $ytGallery): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ytGallery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ytGallery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('yt_gallery_index');
    }
}
