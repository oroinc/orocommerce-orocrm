<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bridge\CustomerAccount\Tests\Functional\DataFixtures\LoadAccount;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\TaxBundle\Entity\CustomerTaxCode;
use Oro\Bundle\TaxBundle\Tests\Functional\Controller\CustomerControllerTest as BaseAccountControllerTest;

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
     * @param CustomerTaxCode $customerTaxCode
     *
     * @return array
     */
    protected function getFormValues(
        $name,
        Customer $parent,
        CustomerGroup $group,
        AbstractEnumValue $internalRating,
        CustomerTaxCode $customerTaxCode
    ) {
        $values = parent::getFormValues($name, $parent, $group, $internalRating, $customerTaxCode);
        $values['oro_customer_type[customer_association_account]'] = $this->getReference(LoadAccount::ACCOUNT_1)->getId(
        );

        return $values;
    }
}
