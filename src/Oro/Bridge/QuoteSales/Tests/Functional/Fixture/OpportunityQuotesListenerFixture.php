<?php

namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Entity\Customer as CommerceCustomer;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SalesBundle\Entity\B2bCustomer;
use Oro\Bundle\SalesBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Oro\Bundle\TestFrameworkBundle\Tests\Functional\DataFixtures\LoadOrganization;
use Oro\Bundle\WebsiteBundle\Entity\Website;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OpportunityQuotesListenerFixture extends AbstractFixture implements
    ContainerAwareInterface,
    DependentFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [LoadOrganization::class];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->createOpportunity(
            $manager,
            $this->getReference(LoadOrganization::ORGANIZATION),
            $manager->getRepository(Website::class)->findOneBy(['name' => 'Default'])
        );
    }

    private function createOpportunity(ObjectManager $manager, Organization $organization, Website $website): void
    {
        $customer = $this->createCommerceCustomer($manager);
        $opportunityData = [
            [
                'status'            => 'in_progress',
                'customer'          => $customer,
                'create_quote'      => true,
                'reference_name'    => 'opportunity'
            ],
            [
                'status'            => 'in_progress',
                'customer'          => $this->createB2bCustomer($manager, $organization),
                'reference_name'    => 'opportunity_won_b2b'
            ],
            [
                'status'            => 'lost',
                'customer'          => $customer,
                'reference_name'    => 'opportunity_won'
            ]
        ];

        foreach ($opportunityData as $data) {
            $status = $this->getStatus($manager, $data['status']);
            $customer = $data['customer'];
            $opportunity = new Opportunity();
            $opportunity->setName(sprintf('Opportunity name_%d', random_int(1, 10)));
            $opportunity->setStatus($status);
            $opportunity->setOrganization($organization);
            $opportunity->setCustomerAssociation($customer);
            $manager->persist($opportunity);
            $manager->flush();

            if (\array_key_exists('create_quote', $data) && $data['create_quote']) {
                $this->createQuote($manager, $opportunity, $website);
            }

            $this->setReference($data['reference_name'], $opportunity);
        }
    }

    private function createCommerceCustomer(ObjectManager $manager): Customer
    {
        $customer = new CommerceCustomer();
        $customer->setName('Default customer');
        $manager->persist($customer);
        $manager->flush();

        $this->setReference('customer', $customer);

        return $this->container->get('oro_sales.manager.account_customer')->getAccountCustomerByTarget($customer);
    }

    private function createB2bCustomer(ObjectManager $manager, Organization $organization): Customer
    {
        $customer = new B2bCustomer();
        $customer->setAccount($this->getReference(CreateDefaultAccountFixture::DEFAULT_ACCOUNT_REF));
        $customer->setName('B2BCustomer');
        $customer->setOrganization($organization);
        $manager->persist($customer);
        $manager->flush();

        return $this->container->get('oro_sales.manager.account_customer')->getAccountCustomerByTarget($customer);
    }

    private function getStatus(ObjectManager $manager, string $statusId): AbstractEnumValue
    {
        return $manager->getRepository(ExtendHelper::buildEnumValueClassName(Opportunity::INTERNAL_STATUS_CODE))
            ->find(ExtendHelper::buildEnumValueId($statusId));
    }

    private function createQuote(ObjectManager $manager, Opportunity $opportunity, Website $website): void
    {
        $quote = new Quote();
        $quote->setOrganization($opportunity->getOrganization());
        $quote->setCustomerUser(null);
        $quote->setCustomer($opportunity->getCustomerAssociation()->getTarget());
        $quote->setPoNumber('CA1000USD');
        $quote->setWebsite($website);
        $quote->setOpportunity($opportunity);
        $manager->persist($quote);
        $manager->flush();
    }
}
