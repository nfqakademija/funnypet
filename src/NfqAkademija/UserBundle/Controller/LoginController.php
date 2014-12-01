<?php

namespace NfqAkademija\UserBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends ConnectController
{
    public function loginAction(Request $request)
    {
        $result = parent::connectAction($request);

        if ($result instanceof Response) {
            return new RedirectResponse("/");
        }
    }
}