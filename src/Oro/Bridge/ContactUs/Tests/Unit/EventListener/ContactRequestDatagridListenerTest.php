<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\EventListener;

use Oro\Bridge\ContactUs\EventListener\ContactRequestDatagridListener;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmQueryConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;

class ContactRequestDatagridListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContactRequestDatagridListener */
    private $listener;

    protected function setUp(): void
    {
        $this->listener = new ContactRequestDatagridListener();
    }

    public function testOnBuildBefore()
    {
        $config = $this->createMock(DatagridConfiguration::class);
        $config->expects($this->once())
            ->method('addColumn')
            ->with(
                'customerUserName',
                ['label' => 'oro.contactus.contactrequest.customer_user.label'],
                'CONCAT(customerUser.firstName, \' \', customerUser.lastName) as customerUserName',
                ['data_name' => 'customerUserName'],
                [
                    'type' => 'string',
                    'data_name' => 'customerUserName',
                ]
            );
        $query = $this->createMock(OrmQueryConfiguration::class);
        $query->expects($this->once())
            ->method('getRootAlias')
            ->willReturn('contactRequest');
        $query->expects($this->once())
            ->method('addLeftJoin')
            ->with('contactRequest.customer_user', 'customerUser');
        $config->expects($this->once())
            ->method('getOrmQuery')
            ->willReturn($query);
        $event = $this->createMock(BuildBefore::class);
        $event->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $this->listener->onBuildBefore($event);
    }
}
