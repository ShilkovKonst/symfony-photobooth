<?php

namespace App\Service;

use Stripe\StripeClient;

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
}