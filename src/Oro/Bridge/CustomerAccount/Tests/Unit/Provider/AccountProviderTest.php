<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bridge\CustomerAccount\Provider\Customer\AccountProvider;
use Oro\Bridge\CustomerAccount\Tests\Unit\Fixtures\Customer;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SalesBundle\Entity\Customer as SaleCustomer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UserBundle\Entity\User;

class AccountProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var AccountProvider */
    private $provider;

    /** @var ConfigManager|\PHPUnit\Framework\MockObject\MockObject */
    private $configManager;

    /** @var ManagerRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $doctrine;

    /** @var AccountCustomerManager|\PHPUnit\Framework\MockObject\MockObject */
    private $customerManager;

    #[\Override]
    protected function setUp(): void
    {
        $this->configManager = self::createMock(ConfigManager::class);
        $this->doctrine = self::createMock(ManagerRegistry::class);
        $this->customerManager = self::createMock(AccountCustomerManager::class);

        $this->provider = new AccountProvider($this->configManager, $this->doctrine, $this->customerManager);
    }

    public function testGetAccountWithRootAccount(): void
    {
        $this->assertConfigManager();

        $customer = new Customer();
        $childCustomer = new Customer();
        $childCustomer->setParent($customer);

        $account = new Account();
        $saleCustomer = new SaleCustomer();
        $saleCustomer->setAccount($account);

        $this->customerManager
            ->expects(self::once())
            ->method('getAccountCustomerByTarget')
            ->with($customer, false)
            ->willReturn($saleCustomer);

        self::assertSame($account, $this->provider->getAccount($childCustomer));
    }

    public function testGetAccountWithEachAccount(): void
    {
        $this->assertConfigManager(false);

        $user = new User();
        $organization = new Organization();
        $customer = new Customer();
        $childCustomer = new Customer();
        $childCustomer->setName('Child name');
        $childCustomer->setParent($customer);
        $childCustomer->setOwner($user);
        $childCustomer->setOrganization($organization);
        $childCustomer->setDataChannel(new Channel());

        $account = $this->provider->getAccount($childCustomer);

        self::assertSame('Child name', $account->getName());
        self::assertSame($organization, $account->getOrganization());
        self::assertSame($user, $account->getOwner());
    }

    private function assertConfigManager(bool $userRootAccount = true): void
    {
        $this->configManager
            ->expects(self::any())
            ->method('get')
            ->with('oro_customer_account_bridge.customer_account_settings')
            ->willReturn($userRootAccount ? 'root' : 'each');
    }
}
