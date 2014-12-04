<?php

namespace NfqAkademija\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DisplayHandlerController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $display_photo = $this->container->get('display_photo');

        return $this->render(
            'NfqAkademijaFrontendBundle:DisplayHandler:index.html.twig',
            array("photos" => $display_photo->getDashboardPhotos($request))
        );
    }

    /**
     * Display photos. Used by infinity scroll, search
     *
     * @param $start
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxPhotoDisplayAction($start, Request $request)
    {
        $display_photo = $this->container->get('display_photo');

        return $this->render(
            'NfqAkademijaFrontendBundle:DisplayHandler:scroll.html.twig',
            array("photos" => $display_photo->getDashboardPhotos($request, $start))
        );
    }

    /**
     * Rate photo
     *
     * @param $photo_id
     * @return JsonResponse
     */
    public function ajaxRateLikeAction($photo_id)
    {
        $rating = $this->container->get('rating');
        return new JsonResponse($rating->ratePhoto(1, $photo_id));
    }

    /**
     * Rate photo
     *
     * @param $photo_id
     * @return JsonResponse
     */
    public function ajaxRateDislikeAction($photo_id)
    {
        $rating = $this->container->get('rating');
        return new JsonResponse($rating->ratePhoto(-1, $photo_id));
    }

    /**
     * Find tags by search auto complete
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxSearchTagsAction(Request $request)
    {
        $display_photo = $this->container->get('display_photo');
        return new JsonResponse($display_photo->searchTags($request));
    }
}
