<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DisplayHandlerController extends Controller
{
    public function indexAction()
    {
        $display_photo = $this->container->get('display_photo');

        return $this->render(
            'NfqAkademijaFrontendBundle:DisplayHandler:index.html.twig',
            array("photos" => $display_photo->getDashboardPhotos())
        );
    }

    public function ajaxScrollAction($start)
    {
        $display_photo = $this->container->get('display_photo');

        return $this->render(
            'NfqAkademijaFrontendBundle:DisplayHandler:scroll.html.twig',
            array("photos" => $display_photo->getDashboardPhotos($start))
        );
    }

    public function ajaxRateLikeAction($photo_id)
    {
        $rating = $this->container->get('rating');
        return new JsonResponse($rating->ratePhoto(1, $photo_id));
    }

    public function ajaxRateDislikeAction($photo_id)
    {
        $rating = $this->container->get('rating');
        return new JsonResponse($rating->ratePhoto(-1, $photo_id));
    }
}
