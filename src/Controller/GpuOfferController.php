<?php

namespace App\Controller;


use App\Entity\GpuOffer;
use App\Form\GpuOfferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function index(): Response
    {
        $repo = $this->em->getRepository(GpuOffer::class);
        $offers = $repo->findAll();
        return $this->render('gpu_offer/index.html.twig', [
            'offers' => $offers,
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
