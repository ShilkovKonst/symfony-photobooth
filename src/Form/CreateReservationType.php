<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CreateReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventType', TextType::class)
            ->add('addEventType', TextType::class)
            ->add('isTermsAccepted', CheckboxType::class)
            ->add('eventCity', TextType::class)
            ->add('eventAddress', TextType::class)
            ->add(
                'eventAddressAddInfo',
                TextType::class,
                [
                    'required' => false
                ]
            )
            ->add('eventPlan');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
