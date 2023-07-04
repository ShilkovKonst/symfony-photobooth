<?php

namespace App\Controller;

use App\Entity\User;
use Stripe\StripeClient;
use App\Entity\Reservation;
use App\Entity\ReservedDates;
use App\Service\StripeService;
use App\Service\ZipCodesService;
use App\Form\CreateReservationType;
use App\Service\ReservedDatesService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MachinesReservationsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MakeReservationController extends AbstractController
{

    // private $stripe;

    // public function __construct()
    // {
    //     $this->stripe = new StripeClient($_ENV["STRIPE_SECRET_KEY"]);
    // }
    #[Route('/choose-plan', name: 'app_choose_plan')]
    public function index(StripeService $stripe): Response
    {
        return $this->render('reservation/index.html.twig', [
            'plans' => $stripe->getPlans(),
        ]);
    }

    #[Route('/make-reservation/{planId}', name: 'app_make_reservation')]
    public function createReservation(
        $planId,
        ZipCodesService $zipCodes,
        ReservedDatesService $reservedDates,
        Request $request,
        MachinesReservationsService $machineChecker,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user  */
        $user = $this->getUser();
        // if (empty($this->stripe->customers->all([
        //     'email' => $user->getEmail(),
        // ])->data)) {
        //     dd('customer no', $this->stripe->customers->all(['email' => $user->getEmail()]));
        // } else {
        //     dd('customer yes', $this->stripe->customers->all(['email' => $user->getEmail()]));
        // }

        $reservation = new Reservation;
        $reservationDates = new ReservedDates;
        $form = $this->createForm(CreateReservationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $machine = $machineChecker->checkMachinesDates($form->get('eventDate')->getData());
            if ($machine == null) {
                $this->addFlash('danger', "There is no machine for the date " . $form->get('eventDate')->getData());
                return $this->redirectToRoute('app_make_reservation', [
                    'planId' => $planId,
                ]);
            }
            $eventDate = \DateTimeImmutable::createFromFormat('Y-m-d', $form->get('eventDate')->getData());

            $reservation->setEventDate($eventDate);
            $reservation->setUser($user);
            $reservation->setEventType($form->get('eventType')->getData());
            if ($form->get('eventType')->getData() == 'Autre') {
                $reservation->setAddEventType($form->get('addEventType')->getData());
            }
            $reservation->setEventZip(explode(' | ', $form->get('eventZip')->getData())[0]);
            $reservation->setEventCity(explode(' | ', $form->get('eventZip')->getData())[1]);
            $reservation->setEventAddress($form->get('eventAddress')->getData());
            if ($form->get('eventAddressAddInfo')->getData() !== '') {
                $reservation->setEventAddressAddInfo($form->get('eventAddressAddInfo')->getData());
            }
            $reservation->setEventPlan(explode(' | ', $form->get('eventPlan')->getData())[0]);
            $reservation->setIsTermsAccepted($form->get('agreeTerms')->getData());
            $reservation->setMachine($machine);
            $reservationDates->setReservation($reservation);
            $reservationDates->setMachine($machine);
            $reservationDates->setDates([
                date_format($eventDate->modify('-1 days'), 'Y-m-d'),
                date_format($eventDate, 'Y-m-d'),
                date_format($eventDate->modify('+1 days'), 'Y-m-d')
            ]);

            $entityManager->persist($reservation);
            $entityManager->persist($reservationDates);
            $entityManager->flush();

            $this->addFlash('success', "Reservation est crée.");
            return $this->redirectToRoute('app_main');
        }

        return $this->render('reservation/make_reservation_form.html.twig', [
            'zipCodes' => $zipCodes->getAllCodesPostaux(),
            'reservedDates' => json_encode($reservedDates->getActualDates()),
            'minDate' => $reservedDates->getMinDate(),
            'chosen_plan' => $planId,
            'types' => ['Mariage', 'Anniversaire', 'Soirée', 'Autre'],
            'reservationForm' => $form->createView(),
        ]);
    }
}
