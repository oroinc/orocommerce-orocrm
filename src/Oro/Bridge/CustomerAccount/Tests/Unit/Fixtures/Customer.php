<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Customer as BaseCustomer;

class Customer extends BaseCustomer
{
    /** @var Account */
    protected $account;

    /** @var Account */
    protected $previousAccount;

    /** @var int */
    protected $lifetime;

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount(Account $account = null)
    {
        $this->account = $account;
    }

    public function getPreviousAccount()
    {
        return $this->previousAccount;
    }

    public function setPreviousAccount(Account $account = null)
    {
        $this->previousAccount = $account;
    }

    /**
     * @param float $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @return float
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }
}
