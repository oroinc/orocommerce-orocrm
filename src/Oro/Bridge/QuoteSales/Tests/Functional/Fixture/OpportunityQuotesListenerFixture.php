<?php
namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\CustomerBundle\Entity\Customer as CommerceCustomer;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SalesBundle\Entity\B2bCustomer;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\SalesBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WebsiteBundle\Entity\Website;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OpportunityQuotesListenerFixture extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var Organization */
    protected $organization;

    /** @var ObjectManager */
    protected $em;

    /** @var CustomerUser */
    protected $customer;

    /** @var B2bCustomer */
    protected $b2bCustomer;

    /** @var Website */
    protected $website;

    /** @var User */
    protected $user;

    /** @var AccountCustomerManager */
    protected $accountCustomerManager;

    public function createOpportunity()
    {
        foreach ($this->opportunityProvider() as $data) {
            $status = $this->getStatus($data['status']);
            $customer = $data['customer'];
            $opportunity = new Opportunity();
            $opportunity->setName(sprintf('Opportunity name_%d', mt_rand(1, 10)));
            $opportunity->setStatus($status);
            $opportunity->setOrganization($this->organization);
            $opportunity->setCustomerAssociation($customer);

            $this->em->persist($opportunity);
            $this->em->flush();

            if (array_key_exists('create_quote', $data) && $data['create_quote']) {
                $this->createQuote($opportunity);
            }

            $this->setReference($data['reference_name'], $opportunity);
        }
    }

    /**
     * @return Customer
     */
    protected function createCustomer()
    {
        $customer = new CommerceCustomer();
        $customer->setName('Default customer');

        $this->em->persist($customer);
        $this->em->flush();

        return $this->accountCustomerManager->getAccountCustomerByTarget($customer);
    }

    /**
     * @return Customer
     */
    protected function createB2bCustomer()
    {
        $customer = new B2bCustomer();
        $customer->setAccount($this->getReference(CreateDefaultAccountFixture::DEFAULT_ACCOUNT_REF));
        $customer->setName('B2BCustomer');
        $customer->setOrganization($this->organization);

        $this->em->persist($customer);
        $this->em->flush();

        return $this->accountCustomerManager->getAccountCustomerByTarget($customer);
    }

    /**
     * @param string $statusId
     * @return AbstractEnumValue
     */
    protected function getStatus($statusId)
    {
        $className = ExtendHelper::buildEnumValueClassName(Opportunity::INTERNAL_STATUS_CODE);

        return $this->em->getRepository($className)->find(ExtendHelper::buildEnumValueId($statusId));
    }

    /**
     * @param Opportunity $opportunity
     */
    protected function createQuote(Opportunity $opportunity)
    {
        $customerUsers = array_merge([null], $this->customer->getUsers()->getValues());
        /* @var $customerUser CustomerUser */
        $customerUser = $customerUsers[mt_rand(0, count($customerUsers) - 1)];

        $validUntil = new \DateTime('now');
        $addDays = sprintf('+%s days', mt_rand(10, 100));
        $validUntil->modify($addDays);
        $poNumber = 'CA' . mt_rand(1000, 9999) . 'USD';
        $quote = new Quote();
        $quote
            ->setOwner($this->user)
            ->setOrganization($this->organization)
            ->setValidUntil($validUntil)
            ->setCustomerUser($customerUser)
            ->setCustomer($this->customer)
            ->setShipUntil(new \DateTime('+10 day'))
            ->setPoNumber($poNumber)
            ->setWebsite($this->website)
            ->setOpportunity($opportunity);

        $this->em->persist($quote);
        $this->em->flush();
    }

    /**
     * @return array
     */
    protected function opportunityProvider()
    {
        return [
            [
                'status'            => 'in_progress',
                'customer'          => $this->createCustomer(),
                'create_quote'      => true,
                'reference_name'    => 'opportunity'
            ],
            [
                'status'            => 'in_progress',
                'customer'          => $this->createB2bCustomer(),
                'reference_name'    => 'opportunity_won_b2b'
            ],
            [
                'status'            => 'lost',
                'customer'          => $this->createCustomer(),
                'reference_name'    => 'opportunity_won'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->accountCustomerManager = $this->container->get('oro_sales.manager.account_customer');

        $role = $manager->getRepository('OroUserBundle:Role')
                        ->findOneBy(['role' => 'ROLE_ADMINISTRATOR']);
        $this->user = $manager->getRepository('OroUserBundle:Role')->getFirstMatchedUser($role);
        $this->organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $this->website = $manager->getRepository('OroWebsiteBundle:Website')->findOneBy(['name' => 'Default']);
        $this->customer = $manager->getRepository('OroCustomerBundle:Customer')->findOneBy([]);

        $this->em = $manager;
        $this->createOpportunity();
    }
}
