<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

/**
 * Commerce channel ORM data fixture.
 * Provides logic for lifetime value updating and filling channel to entity.
 */
class CommerceChannelData extends AbstractDefaultChannelDataFixture
{
    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [CreateAccountEntities::class, CommerceChannel::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Channel $channel */
        $channel = $this->getReference('commerce_channel');
        $entities = $channel->getEntities();
        $shouldBeCreated = false;
        foreach ($entities as $entity) {
            $shouldBeCreated |= $this->getRowCount($manager, $entity);
            if ($shouldBeCreated) {
                break;
            }
        }

        if ($shouldBeCreated) {
            $manager->persist($channel);
            $manager->flush();
            // fill channel to all existing entities
            foreach ($entities as $entity) {
                $this->fillChannelToEntity($manager, $channel, $entity);
            }
            $this->updateLifetimeForAccounts($manager, $channel);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function fillChannelToEntity(
        ObjectManager $manager,
        Channel $channel,
        string $entityClass,
        array $additionalParameters = []
    ): void {
        /** @var QueryBuilder $qb */
        $qb = $manager->createQueryBuilder()
            ->update($entityClass, 'e')
            ->set('e.dataChannel', $channel->getId())
            ->where('e.dataChannel IS NULL');
        if (!empty($additionalParameters)) {
            foreach ($additionalParameters as $parameterName => $value) {
                $qb
                    ->andWhere(sprintf('e.%s = :%s', $parameterName, $parameterName))
                    ->setParameter($parameterName, $value);
            }
        }
        $qb->getQuery()->execute();
    }

    /**
     * {@inheritDoc}
     */
    protected function updateLifetime(
        ObjectManager $manager,
        array $accountIds,
        Channel $channel,
        string $customerIdentity,
        string $lifetimeFieldName
    ): void {
        $customerMetadata = $manager->getClassMetadata($customerIdentity);
        $lifetimeColumnName = $customerMetadata->getColumnName($lifetimeFieldName);
        $field = AccountCustomerManager::getCustomerTargetField($customerIdentity);

        $manager->getConnection()->executeStatement(
            'UPDATE orocrm_channel_lifetime_hist SET status = :status'
            . ' WHERE data_channel_id = :channel_id AND account_id IN (:account_ids)',
            ['status' => false, 'channel_id' => $channel->getId(), 'account_ids' => $accountIds],
            ['status' => Types::BOOLEAN, 'channel_id' => Types::INTEGER, 'account_ids' => Connection::PARAM_INT_ARRAY]
        );
        $manager->getConnection()->executeStatement(
            'INSERT INTO orocrm_channel_lifetime_hist'
            . ' (account_id, data_channel_id, status, amount, created_at)'
            . sprintf(
                ' SELECT ca.account_id AS hist_account_id, e.dataChannel_id AS hist_data_channel_id,'
                . ' ca.account_id > 0 as hist_status, SUM(COALESCE(e.%s, 0)) AS hist_amount,'
                . ' TIMESTAMP :created_at AS hist_created_at',
                $lifetimeColumnName
            )
            . sprintf(' FROM %s AS e', $customerMetadata->getTableName())
            . sprintf(' JOIN orocrm_sales_customer AS ca ON ca.%s_id = e.id', $field)
            . ' WHERE e.dataChannel_id = :channel_id AND ca.account_id IN (:account_ids)'
            . ' GROUP BY hist_account_id, hist_data_channel_id, hist_status, hist_created_at',
            [
                'created_at' => new \DateTime(null, new \DateTimeZone('UTC')),
                'channel_id' => $channel->getId(),
                'account_ids' => $accountIds
            ],
            [
                'created_at' => Types::DATETIME_MUTABLE,
                'channel_id' => Types::INTEGER,
                'account_ids' => Connection::PARAM_INT_ARRAY
            ]
        );
    }
}
