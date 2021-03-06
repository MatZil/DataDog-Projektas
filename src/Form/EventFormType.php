<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', null, [
                'help' => 'Up to 60 characters long',
                'attr' => ['autofocus' => null],
            ])
            ->add('intro', null, [
                'help' => 'Up to 100 characters long',
            ])
            ->add('description', null, [
                'help' => 'Minimum of 15 characters',
            ])
            ->add('date', DateTimeType::class, [
                'years' => range(date('Y'), date('Y') + 10),
                'date_format' => 'yyyy-MM-dd h:min',
                'label' => 'Date and time'
            ])
            ->add('location')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('price', MoneyType::class)
            ->add('photo', null, [
                'label' => 'Event photo',
                'help' => '200x200px to 900x900px size',
                'data_class' => null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
