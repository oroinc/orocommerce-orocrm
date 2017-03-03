<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\ImportExport;

use Oro\Bundle\CustomerBundle\Tests\Functional\ImportExport\CustomerExportTest as BaseCustomerExportTest;

/**
 * @dbIsolationPerTest
 */
class CustomerExportTest extends BaseCustomerExportTest
{
    public function testExport()
    {
        parent::testExport();

        $this->assertContains('Account', $this->fileContent);
        $this->assertNotContains('Lifetime', $this->fileContent);
        $this->assertNotContains('Channel Name', $this->fileContent);
        $this->assertNotContains('Previous Account', $this->fileContent);
    }
}
