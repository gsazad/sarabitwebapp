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
            'logo_album_id' => array('type' => 'number', 'data' => '', 'options' => ''),
            'default_home_sections' => array('type' => 'select', 'data' => 'no', 'options' => json_encode(['yes', 'no'])),
            'home_header' => array('type' => 'select', 'data' => 'slider', 'options' => json_encode(['slider'])),
            'nav_type' => array('type' => 'select', 'data' => 'fixed-top', 'options' => json_encode(['default','fixed-top'])),
            'submenu_type' => array('type' => 'select', 'data' => 'mega', 'options' => json_encode(['default','mega'])),
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
            'Home' => array('Home' => '<p>Home</p>'),
            'About Us' => array('About Us' => '<p>Home</p>'),
            'Our Services' => array('Service One' => '<p>Home</p>', 'Service Two' => '<p>Home</p>'),
            'Contact Us' => array('Contact Us' => '<p>Home</p>'),
        );

        foreach ($menus as $k => $v) {
            $m = new Menu();
            $m->setName($k);
            $m->setLocation('top');
            $m->setRank(0);
            $manager->persist($m);

            foreach ($v as $kk => $vv) {
                $p = new Page();
                $p->setName($kk);
                $p->setMenu($m);
                $p->setTitle($kk);
                $p->setTarget('_top');
                $p->setKeywords($vv);
                $p->setDescription($vv);
                $p->setBody($vv);
                $p->setCss(' ');
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

//        Grapes Fixture
        $grapes = new \App\Entity\GrapeBlock();
        $grapes->setLabel('Card With Image 3')
                ->setAttributes("{class: 'fa fa-address-card'}")
                ->setName("cardImage3")
                ->setContent('<div class="row"> <div class="col-sm-4"> <div class="card" style=""> <img src="..." class="card-img-top" alt="..."> <div class="card-body"> <h5 class="card-title">Card title</h5> <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card\'s content.</p> <a href="#" class="btn btn-primary">Go somewhere</a> </div> </div> </div> <div class="col-sm-4"> <div class="card" style=""> <img src="..." class="card-img-top" alt="..."> <div class="card-body"> <h5 class="card-title">Card title</h5> <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card\'s content.</p> <a href="#" class="btn btn-primary">Go somewhere</a> </div> </div> </div> <div class="col-sm-4"> <div class="card" style=""> <img src="..." class="card-img-top" alt="..."> <div class="card-body"> <h5 class="card-title">Card title</h5> <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card\'s content.</p> <a href="#" class="btn btn-primary">Go somewhere</a> </div> </div> </div> </div>');
        $manager->persist($grapes);
        $grapes = new \App\Entity\GrapeBlock();
        $grapes->setLabel('Jumbotron')
                ->setAttributes("{class: 'fa fa-anchor'}")
                ->setName("jumbotron")
                ->setContent('<div class="jumbotron">
  <h1 class="display-4">Hello, world!</h1>
  <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
  <hr class="my-4">
  <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
  <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
</div>');
        $manager->persist($grapes);
        $grapes = new \App\Entity\GrapeBlock();
        $grapes->setLabel('Info Cards 3')
                ->setAttributes("{class: 'fa fa-info'}")
                ->setName("infocard3")
                ->setContent('<div class="row">
<div class="col-sm">
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      <h4 class="my-0 font-weight-normal">Free</h4>
    </div>
    <div class="card-body">
      <h1 class="card-title pricing-card-title">$0 <small class="text-muted">/ mo</small></h1>
      <ul class="list-unstyled mt-3 mb-4">
        <li>10 users included</li>
        <li>2 GB of storage</li>
        <li>Email support</li>
        <li>Help center access</li>
      </ul>
      <button type="button" class="btn btn-lg btn-block btn-outline-primary">Sign up for free</button>
    </div>
  </div>
  </div>
  <div class="col-sm">
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      <h4 class="my-0 font-weight-normal">Pro</h4>
    </div>
    <div class="card-body">
      <h1 class="card-title pricing-card-title">$15 <small class="text-muted">/ mo</small></h1>
      <ul class="list-unstyled mt-3 mb-4">
        <li>20 users included</li>
        <li>10 GB of storage</li>
        <li>Priority email support</li>
        <li>Help center access</li>
      </ul>
      <button type="button" class="btn btn-lg btn-block btn-primary">Get started</button>
    </div>
  </div>
    </div>
  <div class="col-sm">
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      <h4 class="my-0 font-weight-normal">Enterprise</h4>
    </div>
    <div class="card-body">
      <h1 class="card-title pricing-card-title">$29 <small class="text-muted">/ mo</small></h1>
      <ul class="list-unstyled mt-3 mb-4">
        <li>30 users included</li>
        <li>15 GB of storage</li>
        <li>Phone and email support</li>
        <li>Help center access</li>
      </ul>
      <button type="button" class="btn btn-lg btn-block btn-primary">Contact us</button>
    </div>
  </div>
  </div>
  </div>');
        $manager->persist($grapes);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('sectionCenter')
                ->setAttributes("{class: 'fa fa-puzzle-piece'}")
                ->setLabel('Section Center')
                ->setContent('<section class="mt-2 mb-2 pt-5 pb-5 text-center bg-light">
                <div class="pr-5 pl-5">
                    <h2>Ready To Use Secitons</h2>
                    <p>Build your page section by section! We have created multiple options for you to put together and customise into pixel perfect pages. From headers to footers, you will be able to choose the best combination for your project.</p>
                    <a href="#" class="btn btn-primary" style="border-radius: 100px">Click Here</a>
                </div>
            </section>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('sectionLeft')
                ->setAttributes("{class: 'fa fa-puzzle-piece'}")
                ->setLabel('Section Left')
                ->setContent('<section class="mt-2 mb-2 pt-5 pb-5 text-left bg-light">
                <div class="pr-5 pl-5">
                    <h2>Ready To Use Secitons</h2>
                    <p>Build your page section by section! We have created multiple options for you to put together and customise into pixel perfect pages. From headers to footers, you will be able to choose the best combination for your project.</p>
                    <a href="#" class="btn btn-primary" style="border-radius: 100px">Click Here</a>
                </div>
            </section>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('sectionRight')
                ->setAttributes("{class: 'fa fa-puzzle-piece'}")
                ->setLabel('Section Right')
                ->setContent('<section class="mt-2 mb-2 pt-5 pb-5 text-right bg-light">
                <div class="pr-5 pl-5">
                    <h2>Ready To Use Secitons</h2>
                    <p>Build your page section by section! We have created multiple options for you to put together and customise into pixel perfect pages. From headers to footers, you will be able to choose the best combination for your project.</p>
                    <a href="#" class="btn btn-primary" style="border-radius: 100px">Click Here</a>
                </div>
            </section>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('sectionWithImageLeft')
                ->setAttributes("{class: 'fa fa-id-card-o'}")
                ->setLabel('Section With Image Left')
                ->setContent('<section class="mt-2 mb-2 text-left bg-light">
                <div class="row">
                    <div class="col-sm-4" >
                        <div style="min-height: 150px;height: 100%;background-image: url(http://127.0.0.1/bossnew/public/photoG/6/photo/dv06-06-20.jpg); background-size: cover;background-position: center"></div>
                    </div>
                    <div class="col-sm-8 p-4">
                        <div class="pr-5 pl-5">
                            <h2>Ready To Use Secitons</h2>
                            <p>Build your page section by section! We have created multiple options for you to put together and customise into pixel perfect pages. From headers to footers, you will be able to choose the best combination for your project.</p>
                            <a href="#" class="btn btn-primary" style="border-radius: 100px">Click Here</a>
                        </div>
                    </div>
                </div>
            </section>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('sectionWithImageRight')
                ->setAttributes("{class: 'fa fa-id-card-o'}")
                ->setLabel('Section With Image Right')
                ->setContent('<section class="mt-2 mb-2 text-left bg-light">
                <div class="row">
                    <div class="col-sm-8 p-4">
                        <div class="pr-5 pl-5">
                            <h2>Ready To Use Secitons</h2>
                            <p>Build your page section by section! We have created multiple options for you to put together and customise into pixel perfect pages. From headers to footers, you will be able to choose the best combination for your project.</p>
                            <a href="#" class="btn btn-primary" style="border-radius: 100px">Click Here</a>
                        </div>
                    </div>
                    <div class="col-sm-4" >
                        <div style="min-height: 150px;height: 100%;background-image: url(http://127.0.0.1/bossnew/public/photoG/6/photo/dv06-06-20.jpg); background-size: cover;background-position: center"></div>
                    </div>
                </div>
            </section>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('homebanner')
                ->setAttributes("{class: 'fa fa-home'}")
                ->setLabel('Home Page Banner')
                ->setContent(' <div class="mr-0 ml-0 bg-dark" style="min-height: 500px;background-size: cover;background-repeat: no-repeat;">
            <div class="container">
                <div class="row" style="padding-top: 150px">
                    <div class="col-sm">
                        <h1>Ready To Use</h1>
                        <p>Home page banner with background image</p>
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
        </div>');
        $manager->persist($grape);
        $grape = new \App\Entity\GrapeBlock();
        $grape->setName('logoslider')
                ->setAttributes("{class: 'fa fa-slideshare'}")
                ->setLabel('Logo Slider')
                ->setContent('<div> <h2 class="text-center">Partners</h2> <div data-gjs-editable="false"> [[LOGOSLIDER]] </div> </div>');
        $manager->persist($grape);
//        End
//           Boss Blocks Start
        $block = new \App\Entity\BossBlock();
        $block->setName('header')
                ->setBody('<p>header Block</p>')
                ->setCss('');
        $manager->persist($block);
//        End


        $manager->flush();
    }

}
