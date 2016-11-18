<?php

namespace Oro\Bridge\CustomerAccount\Form\Type;

use Symfony\Component\Form\AbstractType;

class CustomerAccountConfigChoice extends AbstractType
{
    const NAME = 'oro_config_customer_account_choice';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }
}
