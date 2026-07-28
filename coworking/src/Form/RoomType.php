<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('capacity', IntegerType::class)
            ->add('equipment', ChoiceType::class, [
                'choices' => [
                    'TV' => 'tv',
                    'Pizarra' => 'whiteboard',
                    'Proyector' => 'projector',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('hourlyRate', MoneyType::class, [
                'currency' => 'EUR',
                'divisor' => 1,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
