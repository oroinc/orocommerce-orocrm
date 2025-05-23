<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\EntityExtendBundle\Entity\EnumOptionInterface;
use Oro\Bundle\TaxBundle\Entity\CustomerTaxCode;
use Oro\Bundle\TaxBundle\Tests\Functional\Controller\CustomerControllerTest as BaseAccountControllerTest;

class TaxAccountControllerTest extends BaseAccountControllerTest
{
    #[\Override]
    protected function getFixtureList(): array
    {
        $values = parent::getFixtureList();
        $values[] = LoadAccount::class;

        return $values;
    }

    #[\Override]
    protected function getFormValues(
        string $name,
        Customer $parent,
        CustomerGroup $group,
        EnumOptionInterface $internalRating,
        CustomerTaxCode $customerTaxCode
    ): array {
        $values = parent::getFormValues($name, $parent, $group, $internalRating, $customerTaxCode);
        $values['oro_customer_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)
            ->getId();

        return $values;
    }
}
