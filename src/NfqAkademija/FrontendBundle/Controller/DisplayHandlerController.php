<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DisplayHandlerController extends Controller
{
    public function indexAction()
    {
        return $this->render('NfqAkademijaFrontendBundle:DisplayHandler:index.html.twig', array());
    }
}
