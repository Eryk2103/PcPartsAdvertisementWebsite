<?php

namespace App\Controller;


use App\Entity\GpuOffer;
use App\Form\GpuOfferType;
use App\Service\GpuOfferService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        $brandParam = $request->query->all('brand');
        $orderArr = explode('_', $orderParam);
        $modelParam =  $request->query->all('model');
        if($orderParam == null)
        {
            $orderArr[0] = 'createdAt';
            $orderArr[1] = 'desc';
        }
        $page = $pageParam == null ? "1" : $pageParam;

        $result = $this->gpuOfferService->getOffers($page, $modelParam, $brandParam, $manufacturerParam, $priceToParam, $priceFromParam, $orderArr[0], $orderArr[1]);
        
        $offers = $result[0];
        //$count = $result[1];
        $lastPage = $result[2];     $brands = $this->gpuOfferService->GetBrands();
        $manufacturers = $this->gpuOfferService->GetManufacturers();
        $models = $this->gpuOfferService->GetModels();

        return $this->render('gpu_offer/index.html.twig', [
            'offers' => $offers,
            'lastPage' => $lastPage,
            'currentPage' => $page,
            'orderSelected' => $orderParam,
            'priceFrom' => $priceFromParam,
            'priceTo' => $priceToParam,
            'manufacturers' => $manufacturers,
            'brands' => $brands,
            'models' => $models,
            'maufacturersSelected' => $manufacturerParam,
            'brandSelected' => $brandParam,
            'modelsSelected' => $modelParam
        ]);

    }
    #[Route('dodaj-ogloszenie/karty-graficzne', name: 'app_gpu_offer_create')]
    public function Create(Request $request, UserInterface $user, SluggerInterface $slugger) : Response
    {
        $offer = new GpuOffer();
        $form = $this->createForm(GpuOfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('offer')->get('img')->getData();
            $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$img->guessExtension();

            try{
                $img->move($this->getParameter("uploads_dir"), $newFilename);

            }
            catch(FileException $e){

            }

            $offer2 = $offer->getOffer();
            $offer2->setUser($user);
            $offer2->setImg($newFilename);
            $offer = $offer->setOffer($offer2);
            $this->em->persist($offer);
            $this->em->flush();

            return $this->redirectToRoute('app_gpu_offer');

        }
        return $this->render('gpu_offer/create.html.twig', [
            'offerForm' => $form->createView(),
        ]);
    }
    #[Route('karty-graficzne/{id}', name: 'app_gpu_offer_detail',  requirements: ['id' => '\d+'])]
    public function Detail(int $id) : Response
    {
        $offer = $this->gpuOfferService->GetOffer($id);
        if($offer)
        {
            return $this->render('gpu_offer/detail.html.twig', [
                'offer' => $offer
            ]);
        }
        return $this->redirectToRoute('app_gpu_offer');
    }
    #[Route('karty-graficzne/usun/{id}', name: 'app_gpu_offer_delete',  requirements: ['id' => '\d+'])]
    public function Delete(int $id) : Response
    {
        $this->gpuOfferService->delete($id);
        return $this->redirectToRoute('app_gpu_offer');
    }
}
