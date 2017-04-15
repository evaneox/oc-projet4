<?php

namespace Louvre\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Louvre\ShopBundle\Form\Type\VisitorType;

class TicketOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entryDate', DateType::class, array(
                'widget'        => 'single_text',
                'html5'         => false,
                'label'         => false,
                'required'      => true,
                'attr'          => ['readonly'  => 'readonly'],
                'error_bubbling'=> true
            ))
            ->add('fullDay', ChoiceType::class, array(
                'label'         => false,
                'choices'       => ['step_one.form.entry_date.full' => 1, 'step_one.form.entry_date.half' => 0],
                'expanded'      => true,
                'multiple'      => false,
                'required'      => true,
                'attr'          => ['name'  => 'fullDayContainer']
            ))
            ->add('visitors', CollectionType::class, array(
                'entry_type'    => VisitorType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'label'         => false,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Louvre\ShopBundle\Entity\TicketOrder'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'louvre_shopbundle_ticketorder';
    }


}
