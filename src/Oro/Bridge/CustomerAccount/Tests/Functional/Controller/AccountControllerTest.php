<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Tests\Functional\Controller\CustomerControllerTest as BaseAccountControllerTest;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;

class AccountControllerTest extends BaseAccountControllerTest
{
    /**
     * @return array
     */
    protected function getFixtureList()
    {
        $values = parent::getFixtureList();
        $values[] = 'Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount';

        return $values;
    }

    /**
     * @param $name
     * @param Customer $parent
     * @param CustomerGroup $group
     * @param AbstractEnumValue $internalRating
     * @return array
     */
    protected function prepareFormValues(
        $name,
        Customer $parent,
        CustomerGroup $group,
        AbstractEnumValue $internalRating
    ) {
        $values = parent::prepareFormValues($name, $parent, $group, $internalRating);
        $values['oro_customer_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)->getId(
        );

        return $values;
    }
}
