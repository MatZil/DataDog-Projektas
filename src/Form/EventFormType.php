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
            ->add('title')
            ->add('intro')
            ->add('description')
            ->add('date')
            ->add('location')
            ->add('fk_Category', EntityType::class, [
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('price', MoneyType::class)
            ->add('photo', FileType::class, [
                'label' => 'Event photo',
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