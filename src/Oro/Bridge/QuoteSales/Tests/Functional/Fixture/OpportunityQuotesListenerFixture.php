<?php
namespace Oro\Bridge\QuoteSales\Tests\Functional\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SaleBundle\Entity\Quote;
use Oro\Bundle\SalesBundle\Entity\Opportunity;

class OpportunityQuotesListenerFixture extends AbstractFixture
{
    /**
     * @var Organization
     */
    protected $organization;
    /**
     * @var ObjectManager
     */
    protected $em;

    protected $customer;

    protected $website;

    protected $user;

    public function createOpportunity()
    {
        $className = ExtendHelper::buildEnumValueClassName(Opportunity::INTERNAL_STATUS_CODE);
        $openStatus = $this->em->getRepository($className)->find(ExtendHelper::buildEnumValueId('in_progress'));
        $opportunity = new Opportunity();
        $opportunity->setName('Opportunity name');
        $opportunity->setStatus($openStatus);
        $opportunity->setOrganization($this->organization);
        $this->em->persist($opportunity);
        $this->em->flush();
        $this->createQuote($opportunity);
        $this->setReference('opportunity', $opportunity);
    }

    protected function createQuote($opportunity)
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
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
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
