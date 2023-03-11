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
        return $this->render('home/index.html.twig', []);
    }

    #[Route('/dodaj-ogloszenie', name: 'app_home_create_offer')]
    public function Create(Request $request, UserInterface $user): Response
    {
        if($request->isMethod('post'))
        {
            $selectValue = $request->request->get('select');
            if($selectValue == 0)
            {

                return $this->redirectToRoute('app_gpu_offer_create');
            }


        }
        return $this->render('home/form-category-select.html.twig');
    }
    #[Route('/moje-oferty', name: 'app_home_my_offers')]
    public function MyOffers(Request $request, UserInterface $user): Response
    {
        $pageParam = $request->query->get('page');
        $page = $pageParam == null ? "1" : $pageParam;
        
        $id = $user->getId();
        $result = $this->offerService->GetMyOffers($page, $id);

        return $this->render('home/my-offers.html.twig', [
            'offers' => $result[0],
            'lastPage' => $result[2],
            'currentPage' => $page,
        ]);
    }
}
