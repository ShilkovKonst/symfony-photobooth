<?php

namespace App\Service;

use App\Entity\User;
use Stripe\StripeClient;
use App\Entity\Reservation;

class StripeService
{
    private $stripe;
    private $plans;

    public function __construct()
    {
        $this->stripe = new StripeClient($_ENV["STRIPE_SECRET_KEY"]);
        $this->plans = $this->stripe->prices->all(['expand' => ['data.product']])->data;
    }

    public function getPlans()
    {
        return $this->plans;
    }

    public function getOnePlan($id)
    {
        return $this->stripe->prices->retrieve($id, ['expand' => ['product']]);
    }

    public function createSession(Reservation $reservation, User $user)
    {
        /** @var Reservation $reservation */
        $customer = $this->checkCustomer($user);
        $session = $this->stripe->checkout->sessions->create([
            'success_url' => $_ENV['BASE_URL'] . '/choose-plan',
            'cancel_url' => $_ENV['BASE_URL'] . '/',
            'line_items' => [
                [
                    'price' => $reservation->getEventPlan(),
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'customer' => $customer->id,
            'locale' => 'fr',
            'metadata' => [
                'reservation_id' => $reservation->getId(),
            ],
            'automatic_tax' => [
                'enabled' => true
            ],
            'invoice_creation' => [
                'enabled' => true,
                'invoice_data' => [
                    'custom_fields' => [
                        [
                            'name' => "Type d'évenement",
                            'value' => $reservation->getEventType() == 'Autre' ? $reservation->getAddEventType() : $reservation->getEventType(),
                        ], [
                            'name' => "Date d'évenement",
                            'value' => $reservation->getEventDate()->format('Y-m-d'),
                        ], [
                            'name' => "Lieu d'évenement",
                            'value' => $reservation->getEventZip() . ', ' . $reservation->getEventCity(),
                        ], [
                            'name' => "Adresse d'évenement",
                            'value' => $reservation->getEventAddress(),
                        ]
                    ],
                    'rendering_options' => [
                        'amount_tax_display' => 'include_inclusive_tax'
                    ],
                    'metadata' => [
                        'reservation_id' => $reservation->getId(),
                    ]
                ]
            ]
        ]);
        dd($session);
        return $session->url;
    }

    private function checkCustomer($user)
    {
        /** @var User $user */
        $customersList = $this->stripe->customers->all([
            'email' => $user->getEmail(),
        ])->data;
        if (empty($customersList)) {
            $customer = $this->stripe->customers->create([
                "email" => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                "phone" => $user->getMobTel(),
                'address' => [
                    'city' => $user->getCity(),
                    'country' => 'FR',
                    'line1' => $user->getAddress(),
                    'postal_code' => $user->getZipCode()
                ],
                'metadata' => [
                    'user_id' => $user->getId(),
                ]
            ]);
        } else {
            $customer = $customersList[0];
        }

        return $customer;
    }
}
