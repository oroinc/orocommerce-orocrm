<?php

namespace Oro\Bridge\CustomerAccount\Async;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

class ReassingCustomerProducer
{
    const TOPIC = Topics::REASSIGN_CUSTOMER_ACCOUNT;

    /**
     * @var MessageProducerInterface
     */
    protected $producer;

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * ReassingCustomerProducer constructor.
     *
     * @param ConfigManager $configManager
     * @param MessageProducerInterface $producer
     */
    public function __construct(
        ConfigManager $configManager,
        MessageProducerInterface $producer
    ) {
        $this->configManager = $configManager;
        $this->producer = $producer;
    }

    /**
     * @param $assignType string
     */
    public function produce($assignType)
    {
        $this->producer->send(self::TOPIC, [
            'type' => $assignType
        ]);
    }
}
