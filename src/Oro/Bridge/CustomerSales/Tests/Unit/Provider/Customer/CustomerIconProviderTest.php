<?php

namespace Oro\Bridge\CustomerSales\Tests\Unit\Provider\Customer;

use Oro\Bridge\CustomerSales\Provider\Customer\CustomerIconProvider;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\UIBundle\Model\Image;

class CustomerIconProviderTest extends \PHPUnit\Framework\TestCase
{
    private CustomerIconProvider $customerIconProvider;

    protected function setUp(): void
    {
        $this->customerIconProvider = new CustomerIconProvider();
    }

    public function testShouldReturnIconForCustomer()
    {
        $icon = $this->customerIconProvider->getIcon(new Customer());
        $this->assertEquals(
            new Image(Image::TYPE_FILE_PATH, ['path' => '/' . CustomerIconProvider::CUSTOMER_ICON_FILE]),
            $icon
        );
    }

    public function testShouldReturnNullForOtherEntities()
    {
        $icon = $this->customerIconProvider->getIcon(new \stdClass());
        $this->assertNull($icon);
    }
}
