<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => fn(Room $room) => sprintf('%s (cap: %d, €%.2f/h)', $room->getName(), $room->getCapacity(), $room->getHourlyRate()),
            ])
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start',
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'End',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
