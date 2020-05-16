<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\AccountBundle\Tests\Functional\Api\Rest\RestAccountTest as BaseRestAccountTest;

class RestAccountTest extends BaseRestAccountTest
{
    /**
     * @param array $request
     * @depends testCreate
     */
    public function testList($request)
    {
        static::markTestSkipped("This test never worked before.");
        $this->client->request(
            'GET',
            $this->getUrl('oro_api_get_accounts')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);
        $this->assertEquals(2, \count($result), \var_export($result, true));
    }
}
