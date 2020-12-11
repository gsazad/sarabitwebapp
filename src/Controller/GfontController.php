<?php

namespace App\Controller;

use App\Entity\Gfont;
use App\Form\GfontType;
use App\Repository\GfontRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;

/**
 * @Route("/myadmin/gfont")
 */
class GfontController extends AbstractController {

    /**
     * @Route("/", name="gfont_index", methods={"GET"})
     */
    public function index(GfontRepository $gfontRepository): Response {
        return $this->render('gfont/index.html.twig', [
                    'gfonts' => $gfontRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="gfont_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response {
        $gfont = new Gfont();
        $form = $this->createForm(GfontType::class, $gfont);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form['name']->getData() == 'default') {

                $form['name']->addError(new FormError($form['name']->getData() . 'is not valid'));
            }
            if ($form['familyName']->getData() == 'default') {

                $form['familyName']->addError(new FormError($form['familyName']->getData() . 'is not valid'));
            }
            if ($form['url']->getData() == 'default') {
                $form['url']->addError(new FormError($form['url']->getData() . 'is not valid'));
            }
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($gfont);
                $entityManager->flush();

                return $this->redirectToRoute('gfont_index');
            }
        }
        return $this->render('gfont/new.html.twig', [
                    'gfont' => $gfont,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gfont_show", methods={"GET"})
     */
    public function show(Gfont $gfont): Response {
        return $this->render('gfont/show.html.twig', [
                    'gfont' => $gfont,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gfont_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Gfont $gfont): Response {
        $form = $this->createForm(GfontType::class, $gfont);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gfont_index');
        }

        return $this->render('gfont/edit.html.twig', [
                    'gfont' => $gfont,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gfont_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Gfont $gfont): Response {
        if ($this->isCsrfTokenValid('delete' . $gfont->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gfont);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gfont_index');
    }

}
