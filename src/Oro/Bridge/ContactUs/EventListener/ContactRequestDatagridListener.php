<?php

namespace Oro\Bridge\ContactUs\EventListener;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;

class ContactRequestDatagridListener
{
    const CUSTOMER_USER_COLUMN = 'customerUserName';

    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        $config->addColumn(
            self::CUSTOMER_USER_COLUMN,
            ['label' => 'oro.contactus.contactrequest.customer_user.label'],
            'CONCAT(customerUser.firstName, \' \', customerUser.lastName) as customerUserName',
            ['data_name' => self::CUSTOMER_USER_COLUMN],
            [
                'type' => 'string',
                'data_name' => self::CUSTOMER_USER_COLUMN,
            ]
        );

        $query = $config->getOrmQuery();
        $query->addLeftJoin($query->getRootAlias().'.customer_user', 'customerUser');
    }
}
