<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;

use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
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
     * @param CustomerGroup $group
     * @param AbstractEnumValue $internalRating
     * @param AccountTaxCode $accountTaxCode
     *
     * @return array
     */
    protected function getFormValues(
        $name,
        Customer $parent,
        CustomerGroup $group,
        AbstractEnumValue $internalRating,
        AccountTaxCode $accountTaxCode
    ) {
        $values = parent::getFormValues($name, $parent, $group, $internalRating, $accountTaxCode);
        $values['oro_account_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)->getId(
        );

        return $values;
    }
}
