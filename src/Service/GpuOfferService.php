<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GpuOffer;

class GpuOfferService{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function GetOffers(int $page, ?Array $manufacturers = null, ?Float $priceTo = null ,?Float $priceFrom = null, string $orderField = "createdAt", string $orderDirection = "DESC") : Array
    {
        $repo = $this->em->getRepository(GpuOffer::class);
        $qb = $repo->createQueryBuilder('g')
            ->join('g.offer', 'o')
            ->orderBy('o.' . $orderField, $orderDirection);
        if($manufacturers)
        {
            $qb -> andWhere('g.manufacturer in (:m)')
                ->setParameter('m', $manufacturers);
            
        }
        if($priceFrom)
        {
            $qb -> andWhere('o.price >= :priceFrom')
            ->setParameter('priceFrom', $priceFrom);
        }
        if($priceTo)
        {
            $qb -> andWhere('o.price <= :priceTo')
            ->setParameter('priceTo', $priceTo);
        }
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