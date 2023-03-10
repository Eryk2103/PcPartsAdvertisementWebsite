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
    
    public function GetOffers(int $page, ?Array $model = null, ?Array $brand = null, ?Array $manufacturers = null, ?Float $priceTo = null ,?Float $priceFrom = null, string $orderField = "createdAt", string $orderDirection = "DESC") : Array
    {
        
        $repo = $this->em->getRepository(GpuOffer::class);
        $qb = $repo->createQueryBuilder('g')
            ->join('g.offer', 'o')
            ->orderBy('o.' . $orderField, $orderDirection);

        if($model)
        {
            $qb -> andWhere('g.model in (:mo)')
                ->setParameter('mo', $model);
        }
        if($manufacturers)
        {
            $qb -> andWhere('g.manufacturer in (:m)')
                ->setParameter('m', $manufacturers);
        }
        if($brand)
        {
            $qb -> andWhere('g.brand in (:b)')
                ->setParameter('b', $brand);
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
    public function GetBrands()
    {
        return ["MSI", "ASUS", "AMD", "NVIDIA", "PALIT", "INNE"];
    }
    public function GetManufacturers()
    {
        return ["NVIDIA", "AMD", "INNE"];
    }
    public function GetModels()
    {
        return ["RTX 3050", "RTX 3060", "RTX 3070", "RTX 3080", "RX 7900 XTX", "INNE"];
    }
    public function GetOffer(int $id)
    {
        $repo = $this->em->getRepository(GpuOffer::class);
        $qb = $repo->createQueryBuilder('g')
            ->join('g.offer', 'o')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
        return $qb;
    }
    public function delete(int $id)
    {
        $repo = $this->em->getRepository(GpuOffer::class);
        $offer = $repo->find($id);
        $this->em->remove($offer);
        $this->em->flush();
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