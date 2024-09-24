<?php

namespace Oro\Bridge\QuoteSales\Migrations\Data\ORM;

use Oro\Bundle\SecurityBundle\Migrations\Data\ORM\AbstractLoadAclData;

class LoadAclRoleData extends AbstractLoadAclData
{
    #[\Override]
    protected function getDataPath()
    {
        return '@OroQuoteSalesBridgeBundle/Migrations/Data/ORM/Roles/workflows.yml';
    }
}
