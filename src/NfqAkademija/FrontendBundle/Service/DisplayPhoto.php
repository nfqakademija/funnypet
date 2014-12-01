<?php

namespace NfqAkademija\FrontendBundle\Service;

use Doctrine\ORM\EntityManager;
use NfqAkademija\UserBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;

class DisplayPhoto
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @param EntityManager $doctrine
     * @param SecurityContext $securityContext
     */
    public function __construct(EntityManager $doctrine, SecurityContext $securityContext)
    {
        $this->entityManager = $doctrine;
        $this->user = $securityContext->getToken()->getUser();
    }

    /**
     * Get photos array
     * Used in dashboard and infinity scroll
     *
     * @param int $start
     * @return array|\NfqAkademija\FrontendBundle\Entity\Photo[]
     */
    public function getDashboardPhotos($start = 0)
    {
        $photos = $this->getPhotos($start);

        $this->setCurrentUserPhotoRating(
            $photos,
            $this->getCurrentUserPhotoRating(
                $this->getPhotoIds($photos)
            )
        );

        return $photos;
    }

    /**
     * Get photos
     *
     * @param $start
     * @return array|\NfqAkademija\FrontendBundle\Entity\Photo[]
     */
    private function getPhotos($start)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->findOrderByCreatedDate($start);
    }

    /**
     * Get photos id array
     *
     * @param $photos
     * @return array
     */
    private function getPhotoIds($photos)
    {
        $photoIds = array();

        if (is_array($photos)) {
            foreach ($photos as $photo) {
                /* @var \NfqAkademija\FrontendBundle\Entity\Photo $photo */
                $photoIds[] = $photo->getId();
            }
        }

        return $photoIds;
    }

    /**
     * Get current user photos rating array
     *
     * @param array $photoIds
     * @return array|\NfqAkademija\FrontendBundle\Entity\Rating[]|null
     */
    private function getCurrentUserPhotoRating(array $photoIds)
    {
        if ($this->user instanceof User and !empty($photoIds)) {
            return $this->entityManager
                        ->getRepository("NfqAkademijaFrontendBundle:Rating")
                        ->findBy(array("photoId" => $photoIds, "userId" => $this->user->getId()));
        }

        return null;
    }

    /**
     * Set current user rating entity to photo entity
     *
     * @param $photos
     * @param $userRatings
     */
    private function setCurrentUserPhotoRating($photos, $userRatings)
    {
        if ($userRatings) {

            $ratingArray = array();
            foreach ($userRatings as $userRating) {
                /* @var \NfqAkademija\FrontendBundle\Entity\Rating $userRating */
                $ratingArray[$userRating->getPhotoId()] = $userRating;
            }

            foreach ($photos as $photo) {
                /* @var \NfqAkademija\FrontendBundle\Entity\Photo $photo */
                if (array_key_exists($photo->getId(), $ratingArray)) {
                    $photo->setCurrentUserRating($ratingArray[$photo->getId()]);
                }
            }
        }
    }

    /**
     * Get photo object
     *
     * @param integer $photoId
     * @return \NfqAkademija\FrontendBundle\Entity\Photo
     */
    public function getPhoto($photoId)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Photo")
                    ->find($photoId);
    }
}
