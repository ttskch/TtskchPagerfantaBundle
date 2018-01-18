<?php

namespace App\Form;

use App\Criteria\UserCriteria;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ttskch\PagerfantaBundle\Form\CriteriaType;

class UserSearchType extends CriteriaType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('query', SearchType::class, [
                'attr' => [
                    'placeholder' => 'Search for ...',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserCriteria::class,
        ]);
    }
}
