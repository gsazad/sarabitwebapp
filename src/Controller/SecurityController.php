<?php

namespace App\Controller;

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//use Symfony\Component\Security\Core;

class SecurityController extends BaseController {

    /**
     * @Route("/myadmin/logout", name="myadmin_logout")
     */
    public function logout() {
        return true;
    }

    /**
     * @Route("/myadmin/login.html", name="myadmin_login_page")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils) {
        $session = $request->getSession();
// get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
// last username entered by the user
        $lastUsername = $authUtils->getLastUsername();
        return $this->render(
                        'admin/login.html.twig', array(
// last username entered by the user
                    'last_username' => $lastUsername,
                    'error' => $error,
                        )
        );
    }

}
