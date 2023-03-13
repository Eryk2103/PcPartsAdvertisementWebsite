<?php

namespace App\Controller;

use App\Service\OfferService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        //Currently their is one category so redirect to it
        //return $this->render('home/index.html.twig', []);
        return $this->redirectToRoute('app_gpu_offer');
    }

    #[Route('/moje-oferty', name: 'app_home_my_offers')]
    public function MyOffers(Request $request, UserInterface $user): Response
    {
        $pageParam = $request->query->get('page');
        $page = $pageParam == null ? "1" : $pageParam;
        
        $result = $this->offerService->GetMyOffers($user, $page);

        return $this->render('home/my-offers.html.twig', [
            'offers' => $result[0],
            'lastPage' => $result[2],
            'currentPage' => $page,
        ]);
    }
}
