<?php

namespace Oro\Bridge\QuoteSales\Tests\Unit\Fixture;

use Oro\Bundle\ProductBundle\Storage\DataStorageInterface;

class DataStorageStub implements DataStorageInterface
{
    private array $data = [];

    #[\Override]
    public function set(array $data): void
    {
        $this->data = $data;
    }

    #[\Override]
    public function get(): array
    {
        return $this->data;
    }

    #[\Override]
    public function remove(): void
    {
        // This method added just to implement interface
    }
}
