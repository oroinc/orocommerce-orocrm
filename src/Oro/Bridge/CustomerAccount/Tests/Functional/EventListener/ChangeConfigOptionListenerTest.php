<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Bridge\CustomerAccount\EventListener\ChangeConfigOptionListener;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Bundle\MessageQueueBundle\Test\Functional\MessageQueueExtension;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class ChangeConfigOptionListenerTest extends WebTestCase
{
    use MessageQueueExtension;

    protected function setUp(): void
    {
        $this->initClient();
    }

    public function testShouldSendMessageIfOptionChanged(): void
    {
        $service = $this->getService();
        $event = new ConfigUpdateEvent([
            'oro_customer_account_bridge.customer_account_settings' => [
                'old' => 'root',
                'new' => 'each'
            ]
        ]);
        $service->onConfigUpdate($event);
        self::assertMessageSent(ReassignCustomerAccountTopic::getName());

        $event = new ConfigUpdateEvent([
            'oro_customer_account_bridge.customer_account_settings' => [
                'old' => 'each',
                'new' => 'root'
            ]
        ]);
        self::assertMessageSent(ReassignCustomerAccountTopic::getName());
        $service->onConfigUpdate($event);
    }

    public function testShouldNotSendMessageIfOptionNotChanged(): void
    {
        $service = $this->getService();
        $event = new ConfigUpdateEvent([]);

        $service->onConfigUpdate($event);

        self::assertMessagesEmpty(ReassignCustomerAccountTopic::getName());
    }

    private function getService(): ChangeConfigOptionListener
    {
        return self::getContainer()->get('oro_customer_account.form.event_listener.change_config_option');
    }
}
