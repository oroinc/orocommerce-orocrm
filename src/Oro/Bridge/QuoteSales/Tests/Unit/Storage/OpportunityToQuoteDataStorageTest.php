<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\Storage;

use Oro\Bridge\QuoteSales\Storage\OpportunityToQuoteDataStorage;
use Oro\Bridge\QuoteSales\Tests\Unit\Fixture\DataStorageStub;
use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;
use Oro\Bundle\SalesBundle\Tests\Unit\Fixture\CustomerStub;
use Oro\Bundle\SalesBundle\Tests\Unit\Fixture\OpportunityStub;

class OpportunityToQuoteDataStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OpportunityToQuoteDataStorage
     */
    private $storage;

    /**
     * @var DataStorageInterface
     */
    private $dataContainerStub;

    protected function setUp(): void
    {
        $this->dataContainerStub = new DataStorageStub();
        $this->storage = new OpportunityToQuoteDataStorage($this->dataContainerStub);
    }

    public function testSaveToStorage()
    {
        $testOpportunity = new OpportunityStub('test_opportunity');
        $opportunityCustomer = new CustomerStub();
        $commerceCustomer = new CustomerStub('test_customer');

        $opportunityCustomer->setCustomerTarget($commerceCustomer);
        $testOpportunity->setCustomerAssociation($opportunityCustomer);

        $this->storage->saveToStorage($testOpportunity);

        $data = $this->dataContainerStub->get();

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data['entity_data']);
        $this->assertEquals('test_opportunity', $data['entity_data']['opportunity']);
        $this->assertEquals('test_customer', $data['entity_data']['customer']);
    }
}
