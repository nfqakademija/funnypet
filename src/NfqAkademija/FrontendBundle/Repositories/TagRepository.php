<?php

namespace NfqAkademija\FrontendBundle\Repositories;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * Search tags by tag part
     *
     * @param string $term
     * @return array
     */
    public function findTagsByTerm($term)
    {
        return  $this->getEntityManager()
                     ->getRepository("NfqAkademijaFrontendBundle:Tag")
                     ->createQueryBuilder("t")
                     ->where("t.name LIKE :name")
                     ->setParameter("name", "%".$term."%")
                     ->setMaxResults(10)
                     ->getQuery()
                     ->getResult();
    }
}
