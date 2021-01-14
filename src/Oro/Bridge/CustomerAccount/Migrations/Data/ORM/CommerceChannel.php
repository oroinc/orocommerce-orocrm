<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ChannelBundle\Builder\BuilderFactory;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;

class CommerceChannel extends AbstractDefaultChannelDataFixture
{
    const COMMERCE_CHANNEL_TYPE = 'commerce';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var BuilderFactory $builderFactory */
        $builderFactory = $this->container->get('oro_channel.builder.factory');
        $channel        = $builderFactory
            ->createBuilder()
            ->setChannelType(self::COMMERCE_CHANNEL_TYPE)
            ->setStatus(Channel::STATUS_ACTIVE)
            ->setEntities()
            ->getChannel();

        $this->em->persist($channel);
        $this->em->flush();

        $this->setReference('commerce_channel', $channel);
    }
}
