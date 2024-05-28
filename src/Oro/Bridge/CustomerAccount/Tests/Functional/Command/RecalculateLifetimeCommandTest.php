<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Command;

use Oro\Bridge\CustomerAccount\Command\RecalculateLifetimeCommand;
use Oro\Bundle\ChannelBundle\Tests\Functional\Command\AbstractRecalculateLifetimeCommandTest;
use Oro\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrders;

class RecalculateLifetimeCommandTest extends AbstractRecalculateLifetimeCommandTest
{
    protected function setUp(): void
    {
        $this->markTestSkipped('will be unskipped in BAP-22632');

        parent::setUp();
        $this->loadFixtures([LoadOrders::class]);
    }

    protected function getCommandName(): string
    {
        return RecalculateLifetimeCommand::getDefaultName();
    }
}
