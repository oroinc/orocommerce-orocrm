<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Command;

use Oro\Bundle\ChannelBundle\Tests\Functional\Command\AbstractRecalculateLifetimeCommandTest;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;

class RecalculateLifetimeCommandTest extends AbstractRecalculateLifetimeCommandTest
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([LoadOrders::class]);
    }

    #[\Override]
    protected function getCommandName(): string
    {
        return 'oro:commerce:lifetime:recalculate';
    }
}
