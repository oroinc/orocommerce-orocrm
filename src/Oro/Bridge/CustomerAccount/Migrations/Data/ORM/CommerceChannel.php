<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Loads "commerce" default channel.
 */
class CommerceChannel extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public const COMMERCE_CHANNEL_TYPE = 'commerce';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $channel = $this->container->get('oro_channel.builder.factory')
            ->createBuilder()
            ->setChannelType(self::COMMERCE_CHANNEL_TYPE)
            ->setStatus(Channel::STATUS_ACTIVE)
            ->setEntities()
            ->getChannel();
        $this->setReference('commerce_channel', $channel);
        $manager->persist($channel);
        $manager->flush();
    }
}
