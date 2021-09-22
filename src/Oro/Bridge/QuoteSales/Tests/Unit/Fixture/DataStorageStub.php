<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\Fixture;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;

class DataStorageStub implements DataStorageInterface
{
    private array $data = [];

    public function set(array $data): void
    {
        $this->data = $data;
    }

    public function get(): array
    {
        return $this->data;
    }

    public function remove(): void
    {
        // This method added just to implement interface
    }
}
