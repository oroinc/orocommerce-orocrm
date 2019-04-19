<?php

namespace Oro\Bridge\CustomerAccount\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\ChannelBundle\Builder\BuilderFactory;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

/**
 * Loads default commerce channel for existing organization which have such channel type
 */
class LoadDefaultChannelForExistingOrganizations extends AbstractDefaultChannelDataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var BuilderFactory $builderFactory */
        $builderFactory = $this->container->get('oro_channel.builder.factory');

        $allOrganizations = $manager->getRepository(Organization::class)->findAll();
        $channelRepository = $manager->getRepository(Channel::class);
        $channelsToSave = [];

        foreach ($allOrganizations as $organization) {
            $existingChannel = $channelRepository->findOneBy([
                'owner' => $organization,
                'channelType' => CommerceChannel::COMMERCE_CHANNEL_TYPE
            ]);

            if ($existingChannel) {
                continue;
            }

            $channel = $builderFactory
                ->createBuilder()
                ->setChannelType(CommerceChannel::COMMERCE_CHANNEL_TYPE)
                ->setStatus(Channel::STATUS_ACTIVE)
                ->setEntities()
                ->setOwner($organization)
                ->getChannel();

            $manager->persist($channel);
            $channelsToSave[] = $channel;
        }

        if ($channelsToSave) {
            $manager->flush();
        }
    }
}
