<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'help' => 'Up to 60 characters long'
            ])
            ->add('intro', null, [
                'help' => 'Up to 100 characters long'
            ])
            ->add('description', null, [
                'help' => 'Minimum of 15 characters'
            ])
            ->add('date')
            ->add('location')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('price', MoneyType::class, [
                'data' => '0'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Event photo',
                'help' => '200x200px to 900x900px size',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}