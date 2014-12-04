<?php

namespace NfqAkademija\FrontendBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use NfqAkademija\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @return array|\NfqAkademija\FrontendBundle\Entity\Photo[]|void
     */
    public function getDashboardPhotos(Request $request, $start = 0)
    {
        if ($request->get("q")) {
            $photos = $this->searchPhotos($request, $start);
        } else {
            $photos = $this->getPhotos($start);
        }

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

    /**
     * Search tags for search auto complete
     *
     * @param Request $request
     * @return array
     */
    public function searchTags(Request $request)
    {
        $return = array();

        $tags = $this->entityManager
                     ->getRepository("NfqAkademijaFrontendBundle:Tag")
                     ->findTagsByTerm(trim($request->get('term')));

        foreach ($tags as $tag) {
            $return[] = array(
                "id" => $tag->getName(),
                "label" => $tag->getName(),
                "value" => $tag->getName()
            );
        }

        return $return;
    }

    /**
     * Find photos by search term
     *
     * @param Request $request
     * @param $start
     * @return array
     */
    public function searchPhotos(Request $request, $start)
    {
        $tags_ids = array();

        $tags = $this->entityManager
            ->getRepository("NfqAkademijaFrontendBundle:Tag")
            ->findBy(array("name" => $this->getSearchTags(urldecode($request->get('q')))));

        foreach ($tags as $tag) {
            $tags_ids[] = $tag->getId();
        }

        if (!empty($tags_ids)) {
            $sql = "
            SELECT
              p.id AS id_photo,
              p.title,
              p.file_name,
              p.created_date,
              p.rating,
              t.id AS id_tag,
              t.name
            FROM photo p
              LEFT JOIN photos_tags pt ON p.id = pt.photo_id AND pt.tag_id IN (".implode(",", $tags_ids).")
              LEFT JOIN tag t ON t.id = pt.tag_id
            GROUP BY p.id
            ORDER BY
              COUNT(pt.photo_id) DESC,
              p.created_date DESC
            LIMIT :start, 12
            ";

            $rsm = new ResultSetMapping();
            $rsm->addEntityResult('NfqAkademijaFrontendBundle:Photo', 'p');
            $rsm->addFieldResult('p', 'id_photo', 'id');
            $rsm->addFieldResult('p', 'file_name', 'fileName');
            $rsm->addFieldResult('p', 'title', 'title');
            $rsm->addFieldResult('p', 'created_date', 'createdDate');
            $rsm->addFieldResult('p', 'rating', 'rating');
            $rsm->addJoinedEntityResult('NfqAkademijaFrontendBundle:Tag', 't', 'p', 'tags');
            $rsm->addFieldResult('t', 'id_tag', 'id');
            $rsm->addFieldResult('t', 'name', 'name');

            $result =  $this->entityManager
                ->createNativeQuery($sql, $rsm)
                ->setParameter("start", (int) $start)
                ->getResult();

            //Reset entity that will be displayed all tags
            foreach ($result as $photo) {
                $this->entityManager->refresh($photo);
            }

            return $result;
        }

        return array();
    }

    /**
     * Convert tags from string to array
     *
     * @param string $tags Tags string. Separate all tags by comma
     * @return array
     */
    private function getSearchTags($tags)
    {
        $tag_array = array();
        if ($tags) {
            foreach (explode(",", $tags) as $tag) {
                $tag_array[] = trim($tag);
            }
        }

        return array_unique($tag_array);
    }
}
