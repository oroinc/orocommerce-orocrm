<?php

namespace Oro\Bridge\CustomerSales\Provider\Customer;

use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Provider\Customer\CustomerIconProviderInterface;
use Oro\Bundle\UIBundle\Model\Image;

class CustomerIconProvider implements CustomerIconProviderInterface
{
    const CUSTOMER_ICON_FILE = 'bundles/orocustomersalesbridge/img/customer-logo.png';

    /**
     * {@inheritdoc}
     */
    public function getIcon($entity)
    {
        if (!$entity instanceof Customer) {
            return null;
        }

        return new Image(Image::TYPE_FILE_PATH, ['path' => '/' . self::CUSTOMER_ICON_FILE]);
    }
}
