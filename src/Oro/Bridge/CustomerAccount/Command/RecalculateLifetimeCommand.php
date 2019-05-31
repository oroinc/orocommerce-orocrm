<?php

namespace Oro\Bridge\CustomerAccount\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\ChannelBundle\Command\RecalculateLifetimeCommand as AbstractRecalculateLifetimeCommand;
use Oro\Bundle\ChannelBundle\Provider\SettingsProvider;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;

/**
 * Perform re-calculation of lifetime values for commerce customers.
 */
class RecalculateLifetimeCommand extends AbstractRecalculateLifetimeCommand
{
    /** @var string */
    protected static $defaultName = 'oro:commerce:lifetime:recalculate';

    /** @var LifetimeProcessor */
    private $lifetimeProcessor;

    /**
     * @param ManagerRegistry $registry
     * @param SettingsProvider $settingsProvider
     * @param LifetimeProcessor $lifetimeProcessor
     */
    public function __construct(
        ManagerRegistry $registry,
        SettingsProvider $settingsProvider,
        LifetimeProcessor $lifetimeProcessor
    ) {
        parent::__construct($registry, $settingsProvider);

        $this->lifetimeProcessor = $lifetimeProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setDescription('Perform re-calculation of lifetime values for commerce customers.');
    }

    /**
     * {@inheritdoc}
     */
    protected function getChannelType()
    {
        return 'commerce';
    }

    /**
     * @param EntityManager $em
     * @param Customer $customer
     *
     * @return float
     */
    protected function calculateCustomerLifetime(EntityManager $em, $customer)
    {
        return $this->lifetimeProcessor->calculateLifetimeValue($customer);
    }

    /**
     * @param EntityManager $em
     * @param string        $customerClass
     * @param string        $channelType
     *
     * @return QueryBuilder
     */
    protected function getCustomersQueryBuilder(EntityManager $em, $customerClass, $channelType)
    {
        return $em->getRepository('OroCustomerBundle:Customer')->createQueryBuilder('customer')
            ->select(sprintf('customer.%s as customer_id', 'id'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCustomerClass($channelSettings)
    {
        return 'Oro\Bundle\CustomerBundle\Entity\Customer';
    }
}
