<?php

namespace Louvre\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VisitorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr'          => ['placeholder' => 'step_one.form.name.placeholder'],
                'required'      => true,
                'label'         => 'step_one.form.name.label',
            ))
            ->add('surname', TextType::class, array(
                'attr'          => ['placeholder' => 'step_one.form.surname.placeholder'],
                'required'      => true,
                'label'         => 'step_one.form.surname.label',
            ))
            ->add('birthday', BirthdayType::class, array(
                'label'         => 'step_one.form.birthday.label',
                'years'         => range(1917,2017),
            ))
            ->add('country', CountryType::class, array(
                'label'         => 'step_one.form.country.label',
                'preferred_choices' => [
                    'FR'
                ]
            ))
            ->add('reduced', CheckboxType::class, array(
                'label'         => 'step_one.form.reduced.label',
                'required'      => false,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\ShopBundle\Entity\Visitor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'louvre_shopbundle_visitor';
    }


}
