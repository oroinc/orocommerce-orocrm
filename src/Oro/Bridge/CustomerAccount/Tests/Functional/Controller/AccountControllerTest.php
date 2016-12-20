<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;

use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\CustomerBundle\Entity\AccountGroup;
use Oro\Bundle\CustomerBundle\Tests\Functional\Controller\AccountControllerTest as BaseAccountControllerTest;

/**
 * @dbIsolation
 */
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
     * @param AccountGroup $group
     * @param AbstractEnumValue $internalRating
     * @return array
     */
    protected function prepareFormValues(
        $name,
        Customer $parent,
        AccountGroup $group,
        AbstractEnumValue $internalRating
    ) {
        $values = parent::prepareFormValues($name, $parent, $group, $internalRating);
        $values['oro_account_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)->getId(
        );

        return $values;
    }
}
