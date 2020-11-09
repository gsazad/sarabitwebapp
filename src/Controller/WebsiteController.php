<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\Album;
use App\Entity\YtGallery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of WebsiteController
 *
 * @author gurjeet
 */
class WebsiteController extends BaseController {

    /**
     * @Route("/yt/{id}/video", name="yt_video")
     */
    public function ytvideo(Request $request) {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $yt = $em->getRepository(YtGallery::class)->findOneBy(['id' => $id]);
        return $this->render('business/ytVideo.html.twig', ['yt' => $yt]);
    }

    /**
     * @Route("/yt", name="yt_index")
     */
    public function ytindex() {
        $em = $this->getDoctrine()->getManager();
        $yt = $em->getRepository(YtGallery::class)->findBy([], ['id' => 'DESC']);
        return $this->render('business/ytIndex.html.twig', ['yt' => $yt]);
    }

    /**
     * @Route("/album", name="album_index")
     */
    public function albumAction() {
        
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository(Album::class)->findAll();
       
        $array = array();
        foreach ($albums as $v) {
            $array[] = array(
                'name' => $v->getName(),
                'id' => $v->getId(),
                'max' => $this->getMax($v->getId())
            );
        }

        return $this->render('business/album.html.twig', array(
                    'albums' => $array
        ));
    }

    /**
     * @Route("/photogallery/{id}.{slug}.html", name="photo_gallery")
     */
    public function photoGalleryAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $gallery = $em->getRepository('App:PhotoGallery')->findBy(array('album' => $id));
        $album = $em->getRepository('App:Album')->findOneBy(array('id' => $id));
        return $this->render('business/photoGallery.html.twig', array(
                    'gallery' => $gallery,
                    'album' => $album,
        ));
    }

    /**
     * @Route("/photogallery/view/{id}.{slug}.html", name="photo_gallery_view")
     */
    public function photoViewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $photo = $em->getRepository('App:PhotoGallery')->findOneBy(array('id' => $id));
        //$album = $em->getRepository('App:Album')->findOneBy(array('id' => $id));
        return $this->render('business/photo.html.twig', array(
                    'photo' => $photo,
        ));
    }

    public function getMax($albumId) {
       
        $em = $this->getDoctrine()->getManager();
        $lastId = $em->createQueryBuilder()
                ->select('p')
                ->from('App:PhotoGallery', 'p')
                ->where('p.album = :id')
                ->orderBy('p.id', 'DESC')
                ->setParameter('id', $albumId)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        return $lastId;
    }
    /**
     * @Route("/products/{catId}/list/{slug}", name="product_list", methods="GET")
     */
    public function productListAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $catId = $request->get('catId');
        $cat = $em->getRepository('App:ProCat')->findOneBy(['id' => $catId]);
        $products = $em->getRepository('App:Product')->findBy(['categoryId' => $catId]);

        return $this->render('business/productList.html.twig', array('cat' => $cat, 'products' => $products));
    }

    /**
     * @Route("/products/{id}/show/{slug}", name="product_show", methods="GET")
     */
    public function productShowAction(Request $request) {
        $reffer = $this->redirectToReffer($request);
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $product = $em->getRepository('App:Product')->findOneBy(['id' => $id]);

        return $this->render('business/productShow.html.twig', array('product' => $product, 'reffer' => $reffer));
    }

}
