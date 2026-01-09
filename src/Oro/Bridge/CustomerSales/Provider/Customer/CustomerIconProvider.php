<?php

namespace Oro\Bridge\CustomerSales\Provider\Customer;

use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Provider\Customer\CustomerIconProviderInterface;
use Oro\Bundle\UIBundle\Model\Image;

/**
 * Provides customer icon for display in the sales context.
 *
 * Implements the customer icon provider interface to supply a branded customer icon
 * that is displayed in sales-related views and interfaces. Returns null for non-customer
 * entities to allow other providers to handle them.
 */
class CustomerIconProvider implements CustomerIconProviderInterface
{
    public const CUSTOMER_ICON_FILE = 'bundles/orocustomersalesbridge/img/customer-logo.png';

    #[\Override]
    public function getIcon($entity)
    {
        if (!$entity instanceof Customer) {
            return null;
        }

        return new Image(Image::TYPE_FILE_PATH, ['path' => '/' . self::CUSTOMER_ICON_FILE]);
    }
}
