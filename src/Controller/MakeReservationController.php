<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Stripe\StripeClient;
use App\Repository\ReservedDatesRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MakeReservationController extends AbstractController
{
    private $stripe;
    private $plans;

    public function __construct()
    {
        $this->stripe = new StripeClient($_ENV["STRIPE_SECRET_KEY"]);
        $this->plans = $this->stripe->prices->all(['expand' => ['data.product']]);
    }
    #[Route('/choose-plan', name: 'app_choose_plan')]
    public function index(): Response
    {
        // $plans = $this->stripe->prices->all(['expand' => ['data.product']]);
        // dd($plans->data);
        return $this->render('reservation/index.html.twig', [
            'plans' => $this->plans->data,
        ]);
    }

    #[Route('/make-reservation/{planId}', name: 'app_make_reservation')]
    public function createReservation($planId, ReservedDatesRepository $reservedDatesRepo): Response
    {
        $currentDate = new DateTime();
        $currentDate->setTimezone(new DateTimeZone('Europe/Paris'));
        $minDate = $currentDate->modify('+2 days')->format('Y-m-d');
        $reservedDatesRaw = $reservedDatesRepo->findAll();
        $reservedDates = array_map(function ($date) {
            return $date->getDates();      
        }, $reservedDatesRaw);
        $reservedDates = array_merge(...$reservedDates);
        $actualDates = array_filter($reservedDates, function ($item) use ($minDate) { return $item >= $minDate; });
        // dd(array_merge($reservedDates), $actualDates, $minDate, $currentDate);

        return $this->render('reservation/make_reservation_form.html.twig', [
            'zipCodes' => $this->getAllCodesPostaux(),
            'reservedDates' => json_encode($actualDates),
            'minDate' => $minDate,
            'plans' => $this->plans,
            'chosen_plan' => $planId,
            'types' => ['Mariage', 'Anniversaire', 'Soirée', 'Autre']
        ]);
    }

    #[Route('/make-reservation/submitted', name: 'app_make_reservation_submitted')]
    public function ddZipAndCity(Request $request): Response
    {
        $date = $request->request->get('eventDate');
        $outputZipRaw = $request->request->get('eventZip');
        $outputZip = explode(' ', $outputZipRaw);
        $zip = $outputZip[0];
        $city = $outputZip[2];
        dd($zip, $city, $date);
        return $this->render(
            'main/index.html.twig'

        );
    }

    private function getAllCodesPostaux(): array
    {
        $client = HttpClient::create();
        $url = 'https://geo.api.gouv.fr/communes?codeRegion=11&fields=codesPostaux';
        $response = $client->request('GET', $url);
        $dataRaw = $response->getContent();
        $data = json_decode($dataRaw, true);
        // $zipCodesRaw=array_column($data, 'codesPostaux');
        // $zipCodes=array_merge(...$zipCodesRaw);
        $modifiedData = array_map(function ($item) {
            $codesPostaux = $item['codesPostaux'];
            $nom = $item['nom'];
            // Создание новых объектов для каждого индекса
            $result = [];
            if (count($codesPostaux) > 1) {
                foreach ($codesPostaux as $codePostal) {
                    $result[] = [
                        'codesPostaux' => [$codePostal],
                        'nom' => $nom
                    ];
                }
            } else {
                $result[] = [
                    'codesPostaux' => [$codesPostaux[0]],
                    'nom' => $nom
                ];
            }
            return $result;
        }, $data);

        return array_merge(...$modifiedData);
    }
}
