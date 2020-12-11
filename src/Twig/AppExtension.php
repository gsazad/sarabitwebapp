<?php

namespace App\Twig;

use App\Entity\BossBlock;
use App\Entity\PageSection;
use App\Entity\PageSectionImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension {

    protected $container;
    public $em;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container) {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getDoctrine() {

        return $this->container->get('doctrine')->getManager();
    }

    public function getFilters(): array {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('slug', [$this, 'getSlug']),
            new TwigFilter('slug4sms', [$this, 'getSlug4sms']),
            new TwigFilter('md5', [$this, 'md5']),
            new TwigFilter('getuserinfo', [$this, 'getuserinfo']),
            new TwigFilter('getHtml', [$this, 'getHtml']),
            new TwigFilter('getYtId', [$this, 'getYtId']),
            new TwigFilter('die', [$this, 'doDie']),
        ];
    }

    public function getFunctions(): array {
        return array(
            new TwigFunction('getcats', array($this, 'getCats')),
            new TwigFunction('getinput', array($this, 'getInput')),
            new TwigFunction('getsetting', array($this, 'getSetting')),
            new TwigFunction('getGrapeHead', array($this, 'getGrapeHead')),
            new TwigFunction('strPos', array($this, 'strPos')),
            new TwigFunction('getPageSectionImages', array($this, 'getPageSectionImages')),
            new TwigFunction('hextoRGB', array($this, 'hextoRGB')),
            new TwigFunction('getPageFont', array($this, 'getPageFont')),
        );
    }

    public function hextoRGB($hex) {
        $hex = str_replace('#', '', $hex);
        $split = str_split($hex, 2);
        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);
        return array(
            'r' => $r,
            'g' => $g,
            'b' => $b,
        );
    }

    public function getPageSectionImages(PageSection $section) {
        $em = $this->em;
        $images = $em->getRepository(PageSectionImages::class)->findBy(['pageSection' => $section->getId()], ['rank' => 'ASC']);
        return $images;
    }

    public function strPos($string, $word) {
        if (strpos($string, $word) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getYtId($url) {
        $url = explode('?v=', $url);
        return $url[1];
    }

    public function getPageFont($pageSections) {
        $array = [];
        foreach ($pageSections as $p) {
            if ($p->getTitleFont() && $p->getContentFont()) {
                $titleFontId = $p->getTitleFont()->getId();
                $contentFontId = $p->getContentFont()->getId();
                if ($p->getTitleFont()->getName() != 'default') {
                    $array[$titleFontId] = $p->getTitleFont();
                }
                if ($p->getContentFont()->getName() != 'default') {
                    $array[$contentFontId] = $p->getContentFont();
                }
            }
        }
        return $array;
    }

    public function getGrapeHead() {
        $em = $this->em;
        $header = $em->getRepository(BossBlock::class)->findOneBy(['name' => 'header']);
        return $header;
    }

    public function doDie($html) {
        die('die');
    }

    public function getSetting() {
        $em = $this->getDoctrine();
        $set = $em->getRepository('App:Setting')->findAll();
        $array = array();
        foreach ($set as $k => $v) {
            $array[$v->getOpt()] = $v->getData();
        }
        return $array;
    }

    public function getHtml($html) {
        $html = str_replace('<table', "<table class='table table-bordered'", $html);
        $html = strip_tags($html, '<table><td><tr><th><b><strong><i><u><font><ul><ol><li>');
        return $html;
    }

    public function getInput($type, $name, $value) {
        return "<input type='$type' value='$value' name='$name'>";
    }

    public function getSlug4sms($string) {
        $string = trim(ucwords($string));
        $string = str_replace(" ", "_", $string);
        $string = str_replace("__", "_", $string);
        $string = str_replace("__ ", "_", $string);
        $string = preg_replace("/[^A-Za-z0-9-]+/", "", $string);
        $string = str_replace("__", "_", $string);
        return $string;
    }

    public function getSlug($string) {

        $string = trim(strtolower($string));
        $string = str_replace("&", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace(" ", "-", $string);
        $string = str_replace("--", "-", $string);
        $string = str_replace("-- ", "-", $string);
        $string = preg_replace("/[^a-z0-9-]+/", "", $string);
        $string = str_replace("--", "-", $string);
        return $string;
    }

    public function getUserInfo($id) {
        return $this->container->get('aicservice')->getUserInfo($id);
    }

    public function getCats() {
        return $this->container->get('aicservice')->getCatsServices();
    }

    public function md5($string) {
        return md5($string);
    }

    public function getName() {
        return 'core_extension';
    }

}
