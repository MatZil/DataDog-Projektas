<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'data_class' => null
            ])
            ->add('intro', null, [
                'help' => 'Up to 100 characters long',
                'data_class' => null
            ])
            ->add('description', null, [
                'help' => 'Minimum of 15 characters',
                'data_class' => null
            ])
            ->add('date')
            ->add('location')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'data_class' => null
            ])
            ->add('price', MoneyType::class, [
                'data_class' => null
            ])
            ->add('photo', null, [
                'label' => 'Event photo',
                'help' => '200x200px to 900x900px size',
                'data_class' => null
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'eventPhoto' => null
        ]);
    }
}