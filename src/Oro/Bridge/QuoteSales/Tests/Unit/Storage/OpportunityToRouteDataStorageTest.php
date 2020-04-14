<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\Storage;

use Oro\Bridge\QuoteSales\Storage\OpportunityToRouteDataStorage;
use Oro\Bridge\QuoteSales\Tests\Unit\Fixture\DataStorageStub;
use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;
use Oro\Bundle\SalesBundle\Tests\Unit\Fixture\OpportunityStub;

class OpportunityToRouteDataStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OpportunityToRouteDataStorage
     */
    private $storage;

    /**
     * @var DataStorageInterface
     */
    private $dataContainerStub;

    protected function setUp(): void
    {
        $this->dataContainerStub = new DataStorageStub();
        $this->storage = new OpportunityToRouteDataStorage($this->dataContainerStub);
    }

    public function testSaveToStorage()
    {
        $testOpportunity = new OpportunityStub();
        $this->storage->saveToStorage($testOpportunity);

        $data = $this->dataContainerStub->get();

        $this->assertNotEmpty($data);
        $this->assertEquals('oro_sales_opportunity_view', $data['route']);
    }
}
