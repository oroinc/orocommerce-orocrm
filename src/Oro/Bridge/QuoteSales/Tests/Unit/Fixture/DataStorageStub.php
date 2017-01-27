<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\Fixture;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;

class DataStorageStub implements DataStorageInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @inheritDoc
     */
    public function set(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->data;
    }

    public function remove()
    {
        // This method added just to implement interface
    }
}
