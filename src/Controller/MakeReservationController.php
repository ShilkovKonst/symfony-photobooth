<?php

namespace App\Controller;

use DateTime;
use Stripe\StripeClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MakeReservationController extends AbstractController
{
    #[Route('/choose-plan', name: 'app_choose_plan')]
    public function index(): Response
    {
        $stripe = new StripeClient($_ENV["STRIPE_SECRET_KEY"]);
        $plans = $stripe->prices->all(['expand' => ['data.product']]);
        // dd($plans->data);
        return $this->render('reservation/index.html.twig', [
            'plans' => $plans->data,
        ]);
    }

    #[Route('/make-reservation', name: 'app_make_reservation')]
    public function createReservation(): Response
    {
        $currentDate = new DateTime();
        $minDate = $currentDate->modify('+2 days');
        $client = HttpClient::create();
        $url = 'https://geo.api.gouv.fr/communes?codeRegion=11&fields=codesPostaux';
        try {
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
                    foreach ($codesPostaux as $index) {
                        $result[] = [
                            'codesPostaux' => [$index],
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
            $finalData = array_merge(...$modifiedData);
            // dd(json_encode($finalData, true));

            return $this->render('reservation/make_reservation_form.html.twig', [
                'zipCodes' => $finalData,
                'minDate' => $minDate
            ]);
        } catch (TransportExceptionInterface $e) {
            $errorMessage = $e->getMessage();
            // dd($errorMessage);
        }
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
    }
}
