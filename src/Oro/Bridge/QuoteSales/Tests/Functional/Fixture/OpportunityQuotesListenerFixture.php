<?php
namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Entity\Customer as CommerceCustomer;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SalesBundle\Entity\B2bCustomer;
use Oro\Bundle\SalesBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\WebsiteBundle\Entity\Website;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OpportunityQuotesListenerFixture extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ObjectManager */
    protected $em;

    /** @var AccountCustomerManager */
    protected $accountCustomerManager;

    /**
     * @return Customer
     */
    protected function createCommerceCustomer()
    {
        $customer = new CommerceCustomer();
        $customer->setName('Default customer');

        $this->em->persist($customer);
        $this->em->flush();

        $this->setReference('customer', $customer);
        return $this->accountCustomerManager->getAccountCustomerByTarget($customer);
    }

    /**
     * @param Organization $organization
     * @return Customer
     */
    protected function createB2bCustomer(Organization $organization)
    {
        $customer = new B2bCustomer();
        $customer->setAccount($this->getReference(CreateDefaultAccountFixture::DEFAULT_ACCOUNT_REF));
        $customer->setName('B2BCustomer');
        $customer->setOrganization($organization);

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

    protected function createQuote(Opportunity $opportunity, Website $website)
    {
        $customer = $opportunity->getCustomerAssociation()->getTarget();

        $quote = new Quote();
        $quote
            ->setOrganization($opportunity->getOrganization())
            ->setCustomerUser(null)
            ->setCustomer($customer)
            ->setPoNumber('CA1000USD')
            ->setWebsite($website)
            ->setOpportunity($opportunity);

        $this->em->persist($quote);
        $this->em->flush();
    }

    protected function createOpportunity(Organization $organization, Website $website)
    {
        $customer = $this->createCommerceCustomer();
        $opportunityData = [
            [
                'status'            => 'in_progress',
                'customer'          => $customer,
                'create_quote'      => true,
                'reference_name'    => 'opportunity'
            ],
            [
                'status'            => 'in_progress',
                'customer'          => $this->createB2bCustomer($organization),
                'reference_name'    => 'opportunity_won_b2b'
            ],
            [
                'status'            => 'lost',
                'customer'          => $customer,
                'reference_name'    => 'opportunity_won'
            ]
        ];

        foreach ($opportunityData as $data) {
            $status = $this->getStatus($data['status']);
            $customer = $data['customer'];
            $opportunity = new Opportunity();
            $opportunity->setName(sprintf('Opportunity name_%d', mt_rand(1, 10)));
            $opportunity->setStatus($status);
            $opportunity->setOrganization($organization);
            $opportunity->setCustomerAssociation($customer);

            $this->em->persist($opportunity);
            $this->em->flush();

            if (array_key_exists('create_quote', $data) && $data['create_quote']) {
                $this->createQuote($opportunity, $website);
            }

            $this->setReference($data['reference_name'], $opportunity);
        }
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;
        $this->accountCustomerManager = $this->container->get('oro_sales.manager.account_customer');

        $this->createOpportunity(
            $manager->getRepository('OroOrganizationBundle:Organization')->getFirst(),
            $manager->getRepository('OroWebsiteBundle:Website')->findOneBy(['name' => 'Default'])
        );
    }
}
