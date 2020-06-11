<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of BaseController
 *
 * @author gurjeet
 */
class BaseController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController {

    public function bodyFilter($body) {
        $logoScroller = $this->getLogoScroller();
        $array = array(
            '[[LOGOSLIDER]]' => $logoScroller,
        );
        return strtr($body, $array);
    }

    public function getLogoScroller() {
        $settings = $this->getSetting();
        $em = $this->getDoctrine()->getManager();
        $albumId = $settings['logo_album_id'];
        if ($albumId == null || $albumId == "" || $albumId == "0") {
            return '/* album id not given */';
        } else {
            $album = $em->getRepository(\App\Entity\Album::class)->findOneBy(['id' => $albumId]);
            if ($album) {
                $images = $em->getRepository(\App\Entity\PhotoGallery::class)->findBy(['album' => $album->getId()]);
                $content = $this->renderView('business/logoslider.html.twig', ['images' => $images]);
            } else {
                return '/* album ' . $albumId . ' not found */';
            }
        }
        return $content;
    }

    //put your code here
    public function myDate() {
        return new \DateTime("now", new \DateTimeZone('UTC'));
    }

    public function dummyText() {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sem felis, gravida in ullamcorper at, gravida sed tortor. Integer ut nisi tortor. Maecenas maximus ut lorem ut suscipit. Integer mollis auctor quam quis sollicitudin. Nullam pretium semper interdum. In consectetur maximus tristique. Fusce elementum libero nunc, at lacinia massa mollis pharetra. Suspendisse potenti. Vivamus dolor justo, lacinia sed massa ut, iaculis consectetur massa. Donec sit amet dignissim orci. Nam erat arcu, ultricies sed pulvinar malesuada, luctus a tortor.<br>
Praesent vehicula iaculis volutpat. Sed vel elit accumsan, fringilla orci quis, semper mi. Proin egestas quam a erat congue, at dignissim massa aliquet. Etiam hendrerit, risus sed faucibus dictum, ex nisl egestas sem, vitae commodo orci libero sit amet nisl. Sed in vulputate turpis. Vestibulum nec pellentesque leo, lacinia mollis nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque facilisis urna euismod, fringilla magna in, malesuada quam.<br>

Praesent quis convallis eros. Vivamus eu ipsum sed velit viverra varius non eu magna. Aliquam accumsan turpis sed nisi consectetur, quis consectetur sapien maximus. Praesent rhoncus mauris nisl, sed euismod nisl aliquet congue. Sed elementum nibh ligula, a efficitur diam tempor et. Morbi elementum odio lectus. Aenean tellus eros, volutpat ut rutrum sit amet, sollicitudin ut nunc. Praesent finibus nisl a dui faucibus vehicula non non ante.<br>

Praesent pulvinar est non urna eleifend, et eleifend mauris blandit. Sed commodo, mauris quis iaculis suscipit, dui lectus porttitor nisl, nec molestie metus urna id diam. Integer et convallis nulla. Etiam ultricies ipsum eget consectetur volutpat. Nulla facilisi. In bibendum, turpis in mattis volutpat, erat tellus tempor urna, in aliquam arcu ex mollis ipsum. Aliquam rhoncus pretium ligula id elementum. Sed vel ultricies ex. Nullam vel lacinia arcu. Phasellus in lacus eget sem aliquam faucibus ac at odio. Nam semper pharetra ante, nec maximus nibh mollis vel. Aliquam convallis elit sed est ullamcorper lobortis. Donec porta mi ante, eu pulvinar lectus imperdiet in. Proin diam diam, volutpat sit amet malesuada non, tristique ac metus.<br>

Quisque volutpat ipsum sed turpis semper finibus. Donec ornare est vitae risus rutrum placerat. Etiam euismod dui augue, et pellentesque felis ornare id. Aliquam ac lorem vitae est aliquam gravida. Nam ultricies massa at metus congue, ac posuere odio aliquam. Integer tempor bibendum nibh et ornare. Donec nec leo convallis metus tempus ultricies. Morbi egestas purus ac tortor aliquet, quis egestas diam venenatis. Donec vel eleifend felis. Donec vehicula mi sed est elementum tincidunt. In in sapien condimentum, auctor tortor vestibulum, fermentum mauris. Nam non ullamcorper orci, et congue odio. Pellentesque tincidunt eros ac efficitur bibendum. Suspendisse pharetra bibendum pellentesque.<br>';
    }

    public function getSlug($string) {

        $string = trim(strtolower($string));
        $string = str_replace("&", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace("--", "-", $string);
        $string = str_replace("-- ", "-", $string);
        $string = preg_replace("/[^a-z0-9-.]+/", "", $string);
        $string = str_replace("--", "-", $string);
        return $string;
    }

    public function getSetting() {
        $em = $this->getDoctrine()->getManager();
        $set = $em->getRepository('App:Setting')->findAll();
        $array = array();
        foreach ($set as $k => $v) {
            $array[$v->getOpt()] = $v->getData();
        }
        return $array;
    }

    function imageResize($string, $type, $size) {
        $new_w = $size;
        $new_h = $size;
        //resize
        $src_img = imagecreatefromstring($string);

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
        if ($type == 'image/png') {
            imagepng($dst_img);
        }
        if ($type == 'image/jpg' || $type == 'image/jpeg') {
            imagejpeg($dst_img);
        }
    }

    function imageResizeAndSave($file, $to, $size) {
        $new_w = $size;
        $new_h = $size;
        //resize
        $mimeType = mime_content_type($file);
        if ($mimeType == 'image/jpeg' || $mimeType == 'image/jpg') {
            $src_img = imagecreatefromjpeg($file);
        }
        if ($mimeType == 'image/png') {
            $src_img = imagecreatefrompng($file);
        }
        if ($mimeType == 'image/gif') {
            $src_img = imagecreatefromgif($file);
        }

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
        if ($mimeType == 'image/png') {
            imagepng($dst_img, $to);
        }
        if ($mimeType == 'image/jpg' || $mimeType == 'image/jpeg') {
            imagejpeg($dst_img, $to, 75);
        }
        if ($mimeType == 'image/gif') {
            imagejpeg($dst_img, $to);
        }
    }

    public function getTmpPath() {
        return '../tmp';
    }

    public function redirectToReffer($request) {
        $url = $request->headers->get('referer');
        return $this->redirect($url);
    }

}
