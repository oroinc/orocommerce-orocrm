<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\CustomerBundle\Entity\Customer as BaseCustomer;

class Customer extends BaseCustomer
{
    /** @var Account */
    protected $account;

    /** @var Account */
    protected $previousAccount;

    /** @var int */
    protected $lifetime;

    /** @var Channel */
    protected $dataChannel;

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

    public function getDataChannel()
    {
        return $this->dataChannel;
    }

    public function setDataChannel(Channel $dataChannel): void
    {
        $this->dataChannel = $dataChannel;
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
