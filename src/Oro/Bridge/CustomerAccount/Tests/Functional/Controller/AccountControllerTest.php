<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Tests\Functional\Controller\CustomerControllerTest;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;

class AccountControllerTest extends CustomerControllerTest
{
    /**
     * {@inheritDoc}
     */
    protected function getFixtureList(): array
    {
        return array_merge(parent::getFixtureList(), [LoadAccount::class]);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareFormValues(
        string $name,
        Customer $parent,
        CustomerGroup $group,
        AbstractEnumValue $internalRating
    ): array {
        $values = parent::prepareFormValues($name, $parent, $group, $internalRating);
        $values['oro_customer_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)
            ->getId();

        return $values;
    }
}
