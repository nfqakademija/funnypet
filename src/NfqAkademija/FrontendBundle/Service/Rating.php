<?php

namespace NfqAkademija\FrontendBundle\Service;

use Doctrine\ORM\EntityManager;
use NfqAkademija\FrontendBundle\Entity\Photo;
use NfqAkademija\UserBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;
use NfqAkademija\FrontendBundle\Entity\Rating as RatingEntity;

class Rating
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
     * @var DisplayPhoto
     */
    private $displayPhoto;

    /**
     * @param EntityManager $doctrine
     * @param SecurityContext $securityContext
     * @param DisplayPhoto $displayPhoto
     */
    public function __construct(EntityManager $doctrine, SecurityContext $securityContext, $displayPhoto)
    {
        $this->entityManager = $doctrine;
        $this->user = $securityContext->getToken()->getUser();
        $this->displayPhoto = $displayPhoto;
    }

    /**
     * Check if user can rate photo
     *
     * @param $ratingValue
     * @param $photoId
     * @return array
     */
    public function ratePhoto($ratingValue, $photoId)
    {
        if ($this->user instanceof User) {
            $photo = $this->getPhoto($photoId);
            if ($photo instanceof Photo) {
                $rating = $this->getRating($photo, $this->user);
                if (!$rating instanceof RatingEntity) {
                    $this->setRating($photo, $this->user, $ratingValue);
                    return array("success" => "Nuotrauka sėkmingai įvertinta.");
                } else {
                    return array("error" => "Jūs jau balsavote.");
                }
            } else {
                return array("error" => "Tokios nuotraukos nėra.");
            }
        } else {
            return array("error" => "Jūs esate neprisijungęs.");
        }
    }

    /**
     * Get photo by photo id
     *
     * @param integer $photoId
     * @return Photo
     */
    private function getPhoto($photoId)
    {
        return $this->displayPhoto->getPhoto($photoId);
    }

    /**
     * Get rating if user already rate
     *
     * @param Photo $photo
     * @param User $user
     * @return RatingEntity
     */
    private function getRating(Photo $photo, User $user)
    {
        return $this->entityManager
                    ->getRepository("NfqAkademijaFrontendBundle:Rating")
                    ->findOneBy(array("photo" => $photo, "user" => $user));
    }

    /**
     * Save rating to db
     *
     * @param Photo $photo
     * @param User $user
     * @param integer $ratingValue
     */
    private function setRating(Photo $photo, User $user, $ratingValue)
    {
        $rating = new RatingEntity();
        $rating->setUser($user)
               ->setPhoto($photo)
               ->setRating($ratingValue);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();
    }
}
