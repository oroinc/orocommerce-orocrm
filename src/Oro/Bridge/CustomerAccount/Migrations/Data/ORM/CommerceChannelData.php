<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;

class CommerceChannelData extends AbstractDefaultChannelDataFixture
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        $dependencies = parent::getDependencies() + [
            'Oro\Bridge\CustomerAccount\Migrations\Data\ORM\CreateAccountEntities',
            'Oro\Bridge\CustomerAccount\Migrations\Data\ORM\CommerceChannel'
        ];

        return $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var Channel $channel */
        $channel = $this->getReference('commerce_channel');
        $entities = $channel->getEntities();
        $shouldBeCreated = false;
        foreach ($entities as $entity) {
            $shouldBeCreated |= $this->getRowCount($entity);
            if ($shouldBeCreated) {
                break;
            }
        }

        if ($shouldBeCreated) {
            $this->em->persist($channel);
            $this->em->flush();
            // fill channel to all existing entities
            foreach ($entities as $entity) {
                $this->fillChannelToEntity($channel, $entity);
            }
            $this->updateLifetimeForAccounts($channel);
        }
    }

    /**
     * @param Channel $channel
     * @param string  $entity
     * @param array   $additionalParameters
     */
    protected function fillChannelToEntity(Channel $channel, $entity, $additionalParameters = [])
    {
        /** @var QueryBuilder $qb */
        $qb = $this->em->createQueryBuilder()
            ->update($entity, 'e')
            ->set('e.dataChannel', $channel->getId())
            ->where('e.dataChannel IS NULL');
        if (!empty($additionalParameters)) {
            foreach ($additionalParameters as $parameterName => $value) {
                $qb->andWhere(
                    sprintf(
                        'e.%s = :%s',
                        $parameterName,
                        $parameterName
                    )
                )->setParameter($parameterName, $value);
            }
        }
        $qb->getQuery()->execute();
    }
}
