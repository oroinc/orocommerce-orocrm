<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManager;
use Oro\Bridge\CustomerAccount\EventListener\OrganizationPersistListener;
use Oro\Bridge\CustomerAccount\Migrations\Data\ORM\CommerceChannel;
use Oro\Bundle\ChannelBundle\Builder\BuilderFactory;
use Oro\Bundle\ChannelBundle\Builder\ChannelObjectBuilder;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Provider\SettingsProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\Repository\OrganizationRepository;

class OrganizationPersistListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var OrganizationPersistListener */
    private $listener;

    /** @var DoctrineHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $doctrineHelper;

    /** @var BuilderFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $builderFactory;

    protected function setUp()
    {
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->builderFactory = $this->createMock(BuilderFactory::class);
        $this->listener = new OrganizationPersistListener($this->doctrineHelper, $this->builderFactory);
    }

    public function testPostPersist()
    {
        $organization = new Organization();

        $orgRepository = $this->createMock(OrganizationRepository::class);
        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepositoryForClass')
            ->with(Organization::class)
            ->willReturn($orgRepository);
        $orgRepository->expects($this->once())
            ->method('getFirst')
            ->willReturn($organization);

        $channel = new Channel();

        /** @var $manager EntityManager|\PHPUnit_Framework_MockObject_MockObject */
        $manager = $this->createMock(EntityManager::class);

        /** @var $settingsProvider SettingsProvider|\PHPUnit_Framework_MockObject_MockObject */
        $settingsProvider = $this->createMock(SettingsProvider::class);

        $channelObjectBuilder = new ChannelObjectBuilder($manager, $settingsProvider, $channel);

        $this->builderFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($channelObjectBuilder);

        $settingsProvider->expects($this->once())
            ->method('getEntitiesByChannelType')
            ->with(CommerceChannel::COMMERCE_CHANNEL_TYPE)
            ->willReturn([\stdClass::class]);

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityManager')
            ->with(Channel::class)
            ->willReturn($manager);

        $manager->expects($this->once())->method('persist')->with($this->callback(
            function ($class) {
                $this->assertInstanceOf(Channel::class, $class);
                $this->assertEquals('Commerce channel', $class->getName());
                $this->assertEquals(CommerceChannel::COMMERCE_CHANNEL_TYPE, $class->getChannelType());
                return true;
            }
        ));

        $this->listener->prePersist($organization);
    }
}
