<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\EventListener;

use Doctrine\ORM\EntityManager;
use Oro\Bridge\CustomerAccount\Async\Topics;
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

    public function testShouldSendMessageIfOptionChanged()
    {
        $service = $this->getService();
        $event = new ConfigUpdateEvent([
            'oro_customer_account_bridge.customer_account_settings' => [
                'old' => 'root',
                'new' => 'each'
            ]
        ]);
        $service->onConfigUpdate($event);
        $this->assertMessageSent(Topics::REASSIGN_CUSTOMER_ACCOUNT);

        $event = new ConfigUpdateEvent([
            'oro_customer_account_bridge.customer_account_settings' => [
                'old' => 'each',
                'new' => 'root'
            ]
        ]);
        $this->assertMessageSent(Topics::REASSIGN_CUSTOMER_ACCOUNT);
        $service->onConfigUpdate($event);
    }

    public function testShouldNotSendMessageIfOptionNotChanged()
    {
        $service = $this->getService();
        $event = new ConfigUpdateEvent([]);
        $this->assertMessagesEmpty(Topics::REASSIGN_CUSTOMER_ACCOUNT);
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return ChangeConfigOptionListener
     */
    protected function getService()
    {
        return $this->getContainer()->get('oro_customer_account.form.event_listener.change_config_option');
    }
}
