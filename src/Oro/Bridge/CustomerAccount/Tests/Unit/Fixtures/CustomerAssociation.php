<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures;

use Oro\Bundle\SalesBundle\Entity\Customer as BaseCustomer;

class CustomerAssociation extends BaseCustomer
{
    /** @var object */
    protected $target;

    /**
     * @param $target
     *
     * @return $this
     */
    public function setCustomerTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return object
     */
    public function getCustomerTarget()
    {
        return $this->target;
    }
}
