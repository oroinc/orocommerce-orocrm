<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\Stub;

use Oro\Bundle\ContactUsBundle\Entity\ContactRequest;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;

/**
 * Stub required to mock extended entity methods
 */
class ContactRequestStub extends ContactRequest
{
    /**
     * @var CustomerUser
     */
    protected $customerUser;

    public function setCustomerUser(CustomerUser $customerUser)
    {
        $this->customerUser = $customerUser;
    }
}
