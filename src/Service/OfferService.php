<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offer;

class OfferService{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function GetMyOffers(int $userId, int $page) : Array
    {
        $repo = $this->em->getRepository(Offer::class);
        
        $qb = $repo->createQueryBuilder('g')
            ->andWhere('g.user = :id')
            ->setParameter('id', $userId)
            ->orderBy('g.createdAt', 'DESC');

        $qb2 = clone $qb;
        
        $count = $this->GetCount($qb);
        
        $itemsPerPage = 5;
        $lastPage = ceil($count / $itemsPerPage);

        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;
        $offers = [];
        
        if($count > 0)
        {
            $offers = $this->getPage($qb2, $page, $itemsPerPage);
        }
        
        return [$offers, $count, $lastPage];
    }
    private function GetPage($qb, int $page, int $itemsPerPage)
    {
        return $qb
            ->setFirstResult($itemsPerPage * ($page-1))
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();
    }

    private function GetCount($qb)
    {
        return $qb
            ->select('count(g.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
   
}