<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;

use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\CustomerBundle\Entity\AccountGroup;
use Oro\Bundle\TaxBundle\Entity\AccountTaxCode;
use Oro\Bundle\TaxBundle\Tests\Functional\Controller\AccountControllerTest as BaseAccountControllerTest;

/**
 * @dbIsolation
 */
class TaxAccountControllerTest extends BaseAccountControllerTest
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
     * @param AccountTaxCode $accountTaxCode
     *
     * @return array
     */
    protected function getFormValues(
        $name,
        Customer $parent,
        AccountGroup $group,
        AbstractEnumValue $internalRating,
        AccountTaxCode $accountTaxCode
    ) {
        $values = parent::getFormValues($name, $parent, $group, $internalRating, $accountTaxCode);
        $values['oro_account_type[account]'] = $this->getReference(LoadAccount::ACCOUNT)->getId();

        return $values;
    }
}
