<?php

namespace CedricZiel\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PostRepository extends EntityRepository
{
    /**
     * @return Query
     */
    public function findAllPublishedOrderedByDateQuery()
    {
        $qb = $this->createQueryBuilder('p');

        $and = $qb->expr()->andX();
        $and->add($qb->expr()->eq('p.published', true));
        $and->add($qb->expr()->lte('p.publishedAt', ':now'));

        return $qb
            ->where($and)
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime())
            ->getQuery();
    }
}
