<?php

namespace App\Controller;

use DateTime;
use App\Service\StripeService;
use App\Service\ZipCodesService;
use App\Form\CreateReservationType;
use App\Service\ReservedDatesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MakeReservationController extends AbstractController
{
    #[Route('/choose-plan', name: 'app_choose_plan')]
    public function index(StripeService $stripe): Response
    {
        return $this->render('reservation/index.html.twig', [
            'plans' => $stripe->getPlans(),
        ]);
    }

    #[Route('/make-reservation/{planId}', name: 'app_make_reservation')]
    public function createReservation($planId, ZipCodesService $zipCodes, ReservedDatesService $reservedDates, Request $request): Response
    {
        $form = $this->createForm(CreateReservationType::class);
        $form->handleRequest($request);

        return $this->render('reservation/make_reservation_form.html.twig', [
            'zipCodes' => $zipCodes->getAllCodesPostaux(),
            'reservedDates' => json_encode($reservedDates->getActualDates()),
            'minDate' => $reservedDates->getMinDate(),
            'chosen_plan' => $planId,
            'types' => ['Mariage', 'Anniversaire', 'Soirée', 'Autre'],
            'reservationForm' => $form->createView(),
        ]);
    }

    #[Route('/make-reservation/submitted', name: 'app_make_reservation_submitted')]
    public function ddZipAndCity(Request $request): Response
    {
        dd($request->request->get('eventDate'));
        $date = strtotime($request->request->get('eventDate'));
        $outputZipRaw = $request->request->get('eventZip');
        $outputZip = explode(' ', $outputZipRaw);
        $zip = $outputZip[0];
        $city = $outputZip[2];
        dd($zip, $city, $date);
        return $this->render(
            'main/index.html.twig'

        );
    }
}
