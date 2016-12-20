<?php

namespace Oro\Bridge\CustomerAccount\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\ChannelBundle\Command\RecalculateLifetimeCommand as AbstractRecalculateLifetimeCommand;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

class RecalculateLifetimeCommand extends AbstractRecalculateLifetimeCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this
            ->setName('oro:commerce:lifetime:recalculate')
            ->setDescription('Perform re-calculation of lifetime values for commerce customers.');
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
        $lftProcessor = $this->getContainer()->get('oro_customer_account.manager.lifetime_processor');

        return $lftProcessor->calculateLifetimeValue($customer);
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
        return $em->getRepository('OroCustomerBundle:Account')->createQueryBuilder('customer')
            ->select(sprintf('customer.%s as customer_id', 'id'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCustomerClass($channelSettings)
    {
        return 'Oro\Bundle\CustomerBundle\Entity\Account';
    }
}
