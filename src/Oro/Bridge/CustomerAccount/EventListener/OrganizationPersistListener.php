<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\ORM\NoResultException;
use Oro\Bridge\CustomerAccount\Migrations\Data\ORM\CommerceChannel;
use Oro\Bundle\ChannelBundle\Builder\BuilderFactory;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

/**
 * Creates default Commerce channel for each new organization created
 */
class OrganizationPersistListener
{
    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var BuilderFactory */
    private $builderFactory;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param BuilderFactory $builderFactory
     */
    public function __construct(DoctrineHelper $doctrineHelper, BuilderFactory $builderFactory)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->builderFactory = $builderFactory;
    }
    /**
     * @param Organization $organization
     */
    public function prePersist(Organization $organization)
    {
        try {
            $defaultOrganization = $this->doctrineHelper
            ->getEntityRepositoryForClass(Organization::class)
            ->getFirst();
        } catch (NoResultException $exception) {
            // @ignoredException
            // Do not create channel for default organization (if no any in db) because it will loaded by fixtures
            return;
        }

        $em = $this->doctrineHelper->getEntityManager(Channel::class);
        $channel        = $this->builderFactory
            ->createBuilder()
            ->setChannelType(CommerceChannel::COMMERCE_CHANNEL_TYPE)
            ->setStatus(Channel::STATUS_ACTIVE)
            ->setEntities()
            ->setOwner($organization)
            ->getChannel();

        $em->persist($channel);
    }
}
