<?php

namespace Oro\Bridge\ContactUs\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CMSBundle\Entity\ContentWidget;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

/**
 * Loads Contact Us Form content widget.
 */
class LoadContactUsFormContentWidgetData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $organization = $manager->getRepository(Organization::class)->getFirst();

        $qb = $manager->getRepository(ContentWidget::class)->createQueryBuilder('cw');
        $qb
            ->select($qb->expr()->count('cw.id'))
            ->where($qb->expr()->eq('cw.organization', ':organization'))
            ->setParameter('organization', $organization);

        if (!$qb->getQuery()->getSingleScalarResult()) {
            $contentWidget = new ContentWidget();
            $contentWidget
                ->setName('contact_us_form')
                ->setWidgetType('contact_us_form')
                ->setOrganization($organization);

            $manager->persist($contentWidget);
            $manager->flush();
        }
    }
}
