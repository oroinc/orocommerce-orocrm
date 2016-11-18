<?php

namespace Oro\Bridge\CustomerAccount\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\CustomerBundle\Form\Type\AccountType;

class CustomerExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return AccountType::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'account',
            'oro_account_select',
            [
                'required' => true,
                'label'    => 'oro.account.entity_label'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => 'Oro\\Bundle\\CustomerBundle\\Entity\\Account',
            'validation_groups' => ['Default', 'Integration'],
        ]);
    }
}
