<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Oro\Bridge\CustomerAccount\Migrations\Schema\v1_5\OroAccountInheritanceTarget;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ActivityListBundle\Helper\ActivityInheritanceTargetsHelper;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OroAccountInheritanceTargetTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->connection = $this->createMock(Connection::class);
        $this->targetHelper = $this->createMock(ActivityInheritanceTargetsHelper::class);
        $this->extendExtension = $this->createMock(ExtendExtension::class);
        $this->schema = $this->createMock(Schema::class);
        $this->queryBag = $this->createMock(QueryBag::class);
        $this->migration = new OroAccountInheritanceTarget();
        $this->migration->setConnection($this->connection);
        $this->migration->setContainer($this->container);
        $this->migration->setExtendExtension($this->extendExtension);
    }

    public function testUpdateShowOnPageStringValue()
    {
        $inheritanceTargets = [
            'someKey' => [
                'target' => 'SomeClass',
                'path' => [
                    'join' => 'Oro\Bundle\SalesBundle\Entity\Customer'
                ]
            ]
        ];

        $this->container->expects(self::once())
            ->method('get')
            ->willReturn($this->targetHelper);

        $this->targetHelper->expects(self::once())
            ->method('getInheritanceTargets')
            ->with(Account::class)
            ->willReturn($inheritanceTargets);

        $this->extendExtension->expects(self::once())
            ->method('getEntityClassByTableName')
            ->with('oro_order')
            ->willReturn('SomeClass');

        $phpData = [
            'activity' => [
                'show_on_page' => 'Oro\Bundle\ActivityBundle\EntityConfig\ActivityScope::VIEW_PAGE',
                'state' => 'Active'
            ],
        ];

        $expectedData = [
            'activity' => [
                'show_on_page' => 1,
                'state' => 'Active'
            ],
        ];

        $dbData = base64_encode(serialize($phpData));
        $this->connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([['id' => 1, 'data' => $dbData]]);

        $this->connection->expects(self::once())
            ->method('convertToPHPValue')
            ->with($dbData, 'array')
            ->willReturn($phpData);

        $this->connection->expects(self::once())
            ->method('executeStatement')
            ->with(
                'UPDATE oro_entity_config SET data = :data WHERE id = :id',
                ['data' => $expectedData, 'id' => 1],
                ['id' => 'integer', 'data' => 'array']
            )
            ->willReturn(1);

        $this->migration->up($this->schema, $this->queryBag);
    }
}
