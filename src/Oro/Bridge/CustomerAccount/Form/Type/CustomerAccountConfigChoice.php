<?php

namespace Oro\Bridge\CustomerAccount\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerAccountConfigChoice extends AbstractType
{
    const NAME = 'oro_config_customer_account_choice';

    #[\Override]
    public function getBlockPrefix(): string
    {
        return self::NAME;
    }

    #[\Override]
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
