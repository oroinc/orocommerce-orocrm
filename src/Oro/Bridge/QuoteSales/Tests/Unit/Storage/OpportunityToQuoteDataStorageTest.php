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

    #[\Override]
    protected function setUp(): void
    {
        $this->dataContainerStub = new DataStorageStub();
        $this->storage = new OpportunityToQuoteDataStorage($this->dataContainerStub);
    }

    public function testSaveToStorage()
    {
        $testOpportunity = new OpportunityStub(500);
        $opportunityCustomer = new CustomerStub();
        $commerceCustomer = new CustomerStub(300);

        $opportunityCustomer->setCustomerTarget($commerceCustomer);
        $testOpportunity->setCustomerAssociation($opportunityCustomer);

        $this->storage->saveToStorage($testOpportunity);

        $data = $this->dataContainerStub->get();

        $this->assertNotEmpty($data);
        $this->assertNotEmpty($data['entity_data']);
        $this->assertEquals(500, $data['entity_data']['opportunity']);
        $this->assertEquals(300, $data['entity_data']['customer']);
    }
}
