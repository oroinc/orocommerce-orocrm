<?php

declare(strict_types=1);

namespace Oro\Bridge\CustomerAccount\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bridge\CustomerAccount\Manager\LifetimeProcessor;
use Oro\Bundle\ChannelBundle\Command\RecalculateLifetimeCommand as AbstractRecalculateLifetimeCommand;
use Oro\Bundle\ChannelBundle\Provider\SettingsProvider;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;

/**
 * Recalculates lifetime value of eCommerce customers.
 */
class RecalculateLifetimeCommand extends AbstractRecalculateLifetimeCommand
{
    /** @var string */
    protected static $defaultName = 'oro:commerce:lifetime:recalculate';

    private LifetimeProcessor $lifetimeProcessor;

    public function __construct(
        ManagerRegistry $registry,
        SettingsProvider $settingsProvider,
        LifetimeProcessor $lifetimeProcessor
    ) {
        parent::__construct($registry, $settingsProvider);

        $this->lifetimeProcessor = $lifetimeProcessor;
    }

    #[\Override]
    public function configure()
    {
        parent::configure();

        $this->setDescription('Recalculates lifetime value of eCommerce customers.')
            ->addUsage('--force');
    }

    #[\Override]
    protected function getChannelType(): string
    {
        return 'commerce';
    }

    /**
     * @param EntityManager $em
     * @param Customer $customer
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[\Override]
    protected function calculateCustomerLifetime(EntityManager $em, object $customer): float
    {
        return $this->lifetimeProcessor->calculateLifetimeValue($customer);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function getCustomersQueryBuilder(
        EntityManager $em,
        string $customerClass,
        string $channelType
    ): QueryBuilder {
        return $em->getRepository(Customer::class)->createQueryBuilder('customer')
            ->select(sprintf('customer.%s as customer_id', 'id'));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function getCustomerClass($channelSettings): string
    {
        return Customer::class;
    }
}
