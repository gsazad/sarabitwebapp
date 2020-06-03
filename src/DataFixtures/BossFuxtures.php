<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Menu;
use App\Entity\Page;
use App\Entity\Section;
use App\Entity\Setting;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BossFuxtures extends Fixture {

    public function load(ObjectManager $manager) {
        $dateNow = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
        $u = new Admin();
        $u->setName('Gurjeet Singh');
        $u->setUsername('singh@gurjeet.co.in');
        $u->setPassword(sha1('s'));
        $u->setIsActive(1);
        $u->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'));
        $manager->persist($u);
        $manager->flush();

        $fonts = array(
            "'Rajdhani', sans-serif",
            "'Yantramanav', sans-serif",
            "'Kalam', cursive",
            "'Khand', sans-serif",
            "'Mukta Mahee', sans-serif",
            "'Baloo Paaji', cursive",
            "'Noto Sans', sans-serif",
            "'Hind', sans-serif",
            "'Ubuntu', sans-serif",
            "'Anton', sans-serif",
        );
        $settings = array(
            'url' => array('type' => 'text', 'data' => '#', 'options' => ''),
            'email_address' => array('type' => 'email', 'data' => 'singh@gurjeet.co.in', 'options' => ''),
            'header_line_1' => array('type' => 'text', 'data' => 'Arash Info Corporation', 'options' => ''),
            'header_line_2' => array('type' => 'text', 'data' => 'Call: 9814790299, Email: info@arashinfo.com', 'options' => ''),
            'enquiry_form_fields' => array('type' => 'text', 'data' => 'mobile,address,city', 'options' => ''),
            'headers' => array('type' => 'textarea', 'data' => '', 'options' => ''),
            'company_logo' => array('type' => 'text', 'data' => '', 'options' => ''),
            'navbar_bg_color' => array('type' => 'color', 'data' => '', 'options' => ''),
            'navbar_text_color' => array('type' => 'color', 'data' => '', 'options' => ''),
            'navbar_font' => array('type' => 'select', 'data' => '', 'options' => json_encode($fonts)),
            'product_manu_id' => array('type' => 'number', 'data' => '', 'options' => ''),
        );

        foreach ($settings as $k => $v) {
            $s = new Setting();
            $s->setOpt($k);
            $s->setType($v['type']);
            $s->setData($v['data']);
            $s->setOptions($v['options']);
            $manager->persist($s);
            $manager->flush();
        }

        $menus = array(
            'Home' => array('Home'),
            'About Us' => array('About Us'),
            'Our Services' => array('Service One', 'Service Two'),
            'Contact Us' => array('Contact Us'),
        );

        foreach ($menus as $k => $v) {
            $m = new Menu();
            $m->setName($k);
            $m->setLocation('top');
            $m->setRank(0);
            $manager->persist($m);

            foreach ($v as $kk => $vv) {
                $p = new Page();
                $p->setName($vv);
                $p->setMenu($m);
                $p->setTitle($vv);
                $p->setTarget('_top');
                $p->setKeywords($vv);
                $p->setDescription($vv);
                $p->setBody($vv);
                $p->setRank(0);
                if ($vv == 'Home') {
                    $p->setIsHome(TRUE);
                } else {
                    $p->setIsHome(FALSE);
                }
                $manager->persist($p);
            }
            $manager->flush();
        }


        $arr = array(
            'Section One' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt pretium tincidunt. Pellentesque finibus turpis sit amet nunc pretium, id aliquam ipsum lacinia.',
            'Section Two' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt pretium tincidunt. Pellentesque finibus turpis sit amet nunc pretium, id aliquam ipsum lacinia.',
            'Section Three' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt pretium tincidunt. Pellentesque finibus turpis sit amet nunc pretium, id aliquam ipsum lacinia.',
        );

        foreach ($arr as $k => $v) {
            $section = new Section();
            $section->setHeader($k);
            $section->setBody($v);
            $section->setRank('1');
            $section->setUrl('#');
            $section->setFileData('');
            $section->setFileType('image/png');
            $section->setFileName('icon.png');

            $manager->persist($section);
            $manager->flush();
        }

        $manager->flush();
    }

}
