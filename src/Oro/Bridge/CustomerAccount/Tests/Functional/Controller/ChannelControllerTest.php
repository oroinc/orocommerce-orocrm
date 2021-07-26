<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller;

use Oro\Bundle\ChannelBundle\Tests\Functional\Controller\ChannelControllerTest as BaseChannelControllerTest;

class ChannelControllerTest extends BaseChannelControllerTest
{
    public function gridProvider()
    {
        $data = parent::gridProvider();
        $data['Channel grid'][0]['expectedResultCount'] = 2;

        return $data;
    }
}
