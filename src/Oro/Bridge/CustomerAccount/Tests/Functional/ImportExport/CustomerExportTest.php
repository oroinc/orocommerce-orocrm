<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\ImportExport;

use Oro\Bundle\CustomerBundle\Tests\Functional\ImportExport\CustomerExportTest as BaseCustomerExportTest;

/**
 * @dbIsolationPerTest
 */
class CustomerExportTest extends BaseCustomerExportTest
{
    /**
     * {@inheritdoc}
     */
    protected function getContains()
    {
        return array_merge(
            parent::getContains(),
            [
                'Account',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getNotContains()
    {
        return array_merge(
            parent::getNotContains(),
            [
                'Lifetime',
                'Channel Name',
                'Previous Account',
            ]
        );
    }
}
