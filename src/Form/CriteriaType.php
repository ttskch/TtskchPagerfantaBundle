<?php

namespace Ttskch\PagerfantaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ttskch\PagerfantaBundle\Entity\Criteria;

class CriteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder
            ->add('page', HiddenType::class)
            ->add('limit', HiddenType::class)
            ->add('sort', HiddenType::class)
            ->add('direction', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Criteria::class,
        ]);
    }
}
