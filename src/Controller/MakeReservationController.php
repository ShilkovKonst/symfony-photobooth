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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MakeReservationController extends AbstractController
{
    private $session;

    public function __construct(private RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

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
        ReservedDatesService $reservedDates,
        Request $request,
        MachinesReservationsService $machineChecker,
        StripeService $stripeService
    ): Response {
        /** @var User $user  */
        $user = $this->getUser();
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
            $this->session->set('reservation', $reservation);
            $this->session->set('reservationDates', $reservationDates);
            // $entityManager->persist($reservation);
            // $entityManager->persist($reservationDates);
            // $entityManager->flush();
            // $this->addFlash('success', "Reservation est crée.");
            return $this->redirect($stripeService->createSession($reservation, $user)->url);
        }

        return $this->render('reservation/make_reservation_form.html.twig', [
            'reservedDates' => json_encode($reservedDates->getActualDates()),
            'minDate' => $reservedDates->getMinDate(),
            'chosen_plan' => $planId,
            'types' => ['Mariage', 'Anniversaire', 'Soirée', 'Autre'],
            'reservationForm' => $form->createView(),
        ]);
    }

    #[Route('/reservation-success', name: 'app_reservation_success')]
    public function createReservationSuccess(
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var Reservation $reservation */
        $reservation = $this->session->get('reservation');
        $reservationDates = $this->session->get('reservationDates');

        $reservation->isIsPaid(true);
        $entityManager->persist($reservation);
        $entityManager->persist($reservationDates);
        $entityManager->flush();

        $this->session->remove('reservation');
        $this->session->remove('reservationDates');

        $this->addFlash('success', "Reservation est crée.");
        return $this->render('reservation/reservation_success.html.twig', [
            'reservation' => $reservation
        ]);
    }
}
