<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Service\StripeService;
use App\Service\ZipCodesService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CreateReservationType extends AbstractType
{
    private $stripe;
    private $plans;
    private $zips;

    public function __construct(StripeService $stripe, ZipCodesService $zips)
    {
        $this->stripe = $stripe;
        $this->plans = array_map(function ($plan) {
            return [$plan['product']['name'] => $plan['id'] . ' | ' . $plan['unit_amount'] . ' | ' . $plan['product']['name']];
        }, $this->stripe->getPlans());
        $this->zips = $zips->getAllCodesPostaux();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventPlan', ChoiceType::class, [
                'expanded' => true,
                'choices' => $this->plans,
            ])
            ->add('eventDate', TextType::class)
            ->add('eventZip', ChoiceType::class, [
                'choices' => $this->zips,
                ])
            ->add('eventAddress', TextType::class)
            ->add('eventAddressAddInfo', TextType::class, ['required' => false,])
            ->add('eventType', ChoiceType::class, [
                'expanded' => true,
                'choices' => ['0' => 'Mariage', '1' => 'Anniversaire', '2' => 'Soirée', '3' => 'Autre'],
            ])
            ->add('addEventType', TextType::class, ['required' => false,])
            ->add('agreeTerms', CheckboxType::class, [
                // 'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Reservation::class,
        ]);
    }
}