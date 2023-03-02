<?php

namespace App\Controller;


use App\Entity\GpuOffer;
use App\Form\GpuOfferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GpuOfferController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/karty-graficzne', name: 'app_gpu_offer')]
    public function index(Request $request): Response
    {
        //params
        $pageParam = $request->query->get('page');
        $orderParam = $request->query->get('orderby');
        $priceFromParam = $request->query->get('price_from');
        $priceToParam = $request->query->get('price_to');
        $manufacturerParam = $request->query->all('manufacturer');
        $orderArr = explode('_', $orderParam);

        if($orderParam == null)
        {
            $orderArr[0] = 'createdAt';
            $orderArr[1] = 'desc';
        }
        
        //query builder
        $repo = $this->em->getRepository(GpuOffer::class);
        $qb = $repo->createQueryBuilder('g')
            ->join('g.offer', 'o')
            ->orderBy('o.' . $orderArr[0], $orderArr[1]);
        if($manufacturerParam)
        {
           
            $qb -> andWhere('g.manufacturer in (:m)')
                ->setParameter('m', $manufacturerParam);
            
        }
        if($priceFromParam)
        {
            $qb -> andWhere('o.price >= :priceFrom')
            ->setParameter('priceFrom', $priceFromParam);
        }
        if($priceToParam)
        {
            $qb -> andWhere('o.price <= :priceTo')
            ->setParameter('priceTo', $priceToParam);
        }

        //query builder for getting number of rows
        $qbCount = $repo->createQueryBuilder('g')
            ->join('g.offer', 'o')
            ->orderBy('o.' . $orderArr[0], $orderArr[1]);

            if($manufacturerParam)
            {
               
                $qbCount -> andWhere('g.manufacturer in (:m)')
                    ->setParameter('m', $manufacturerParam);
                
            }
            if($priceFromParam)
            {
                $qbCount -> andWhere('o.price >= :priceFrom')
                ->setParameter('priceFrom', $priceFromParam);
            }
            if($priceToParam)
            {
                $qbCount -> andWhere('o.price <= :priceTo')
                ->setParameter('priceTo', $priceToParam);
            }    

        $count = $qbCount
            ->select('count(g.id)')
            ->getQuery()
            ->getSingleScalarResult();

        //pagination
        $page = $pageParam == null ? "1" : $pageParam;
        $itemsPerPage = 5;
        $lastPage = ceil($count / $itemsPerPage);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $lastPage ? $lastPage : $page;

        $offers = $qb
            ->setFirstResult($itemsPerPage * ($page-1))
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();

        return $this->render('gpu_offer/index.html.twig', [
            'offers' => $offers,
            'lastPage' => $lastPage,
            'currentPage' => $page,
            'orderSelected' => $orderParam
        ]);

    }
    #[Route('dodaj-ogloszenie/karty-graficzne', name: 'app_gpu_offer_create')]
    public function Create(Request $request) : Response
    {
        $offer = new GpuOffer();
        $form = $this->createForm(GpuOfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($offer);
            $this->em->flush();

            return $this->redirectToRoute('app_gpu_offer');

        }
        return $this->render('gpu_offer/create.html.twig', [
            'offerForm' => $form->createView(),
        ]);
    }

}
