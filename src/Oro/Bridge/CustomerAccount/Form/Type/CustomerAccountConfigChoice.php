<?php

namespace Oro\Bridge\CustomerAccount\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Form type for selecting customer account configuration options.
 *
 * Extends the Symfony ChoiceType to provide a specialized form field for choosing
 * customer account configuration settings in the system configuration interface.
 */
class CustomerAccountConfigChoice extends AbstractType
{
    public const NAME = 'oro_config_customer_account_choice';

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
