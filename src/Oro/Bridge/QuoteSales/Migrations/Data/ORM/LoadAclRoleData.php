<?php

namespace Oro\Bridge\QuoteSales\Migrations\Data\ORM;

use Oro\Bundle\SecurityBundle\Migrations\Data\ORM\AbstractLoadAclData;

/**
 * Data fixture that loads ACL role permissions for quote-sales workflows.
 *
 * Loads access control list role definitions from the workflows configuration file,
 * establishing the necessary permissions for users to interact with quote-related
 * sales workflows and operations.
 */
class LoadAclRoleData extends AbstractLoadAclData
{
    #[\Override]
    protected function getDataPath()
    {
        return '@OroQuoteSalesBridgeBundle/Migrations/Data/ORM/Roles/workflows.yml';
    }
}
