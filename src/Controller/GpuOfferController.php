<?php

namespace App\Controller;


use App\Entity\GpuOffer;
use App\Form\GpuOfferType;
use App\Service\GpuOfferService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GpuOfferController extends AbstractController
{
    private EntityManagerInterface $em;
    private GpuOfferService $gpuOfferService;

    public function __construct(EntityManagerInterface $em, GpuOfferService $gpuOfferService)
    {
        $this->em = $em;
        $this->gpuOfferService = $gpuOfferService;
    }

    #[Route('/karty-graficzne', name: 'app_gpu_offer')]
    public function index(Request $request): Response
    {
        $pageParam = $request->query->get('page');
        $orderParam = $request->query->get('orderby');
        $priceFromParam = (float) $request->query->get('price_from');
        $priceToParam = (float) $request->query->get('price_to');
        $manufacturerParam = $request->query->all('manufacturer');
        $orderArr = explode('_', $orderParam);
  
        if($orderParam == null)
        {
            $orderArr[0] = 'createdAt';
            $orderArr[1] = 'desc';
        }
        $page = $pageParam == null ? "1" : $pageParam;

        $result = $this->gpuOfferService->getOffers($page, $manufacturerParam, $priceToParam, $priceFromParam, $orderArr[0], $orderArr[1]);
        $offers = $result[0];
        //$count = $result[1];
        $lastPage = $result[2];
 
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
