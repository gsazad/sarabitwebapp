<?php

namespace App\Controller;

use App\Entity\GrapeBlock;
use App\Form\GrapeBlockType;
use App\Repository\GrapeBlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/myadmin/grape/block")
 */
class GrapeBlockController extends AbstractController
{
    /**
     * @Route("/", name="grape_block_index", methods={"GET"})
     */
    public function index(GrapeBlockRepository $grapeBlockRepository): Response
    {
        return $this->render('grape_block/index.html.twig', [
            'grape_blocks' => $grapeBlockRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="grape_block_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $grapeBlock = new GrapeBlock();
        $form = $this->createForm(GrapeBlockType::class, $grapeBlock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($grapeBlock);
            $entityManager->flush();

            return $this->redirectToRoute('grape_block_index');
        }

        return $this->render('grape_block/new.html.twig', [
            'grape_block' => $grapeBlock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="grape_block_show", methods={"GET"})
     */
    public function show(GrapeBlock $grapeBlock): Response
    {
        return $this->render('grape_block/show.html.twig', [
            'grape_block' => $grapeBlock,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="grape_block_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GrapeBlock $grapeBlock): Response
    {
        $form = $this->createForm(GrapeBlockType::class, $grapeBlock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('grape_block_index');
        }

        return $this->render('grape_block/edit.html.twig', [
            'grape_block' => $grapeBlock,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="grape_block_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GrapeBlock $grapeBlock): Response
    {
        if ($this->isCsrfTokenValid('delete'.$grapeBlock->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($grapeBlock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('grape_block_index');
    }
}
