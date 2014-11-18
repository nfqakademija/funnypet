<?php

namespace NfqAkademija\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('NfqAkademijaUserBundle:Admin:index.html.twig', array());
    }
}
