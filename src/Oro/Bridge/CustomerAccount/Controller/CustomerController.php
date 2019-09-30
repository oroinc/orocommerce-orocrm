<?php

namespace Oro\Bridge\CustomerAccount\Controller;

use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\CustomerBundle\Entity\Customer as Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provides customer account info actions
 * @Route("/account-customer")
 */
class CustomerController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            AccountCustomerManager::class,
        ]);
    }

    /**
     * @param Account $account
     * @param Channel $channel
     * @return array
     *
     * @Route(
     *         "/widget/customers-info/{accountId}/{channelId}",
     *          name="oro_account_widget_customers_info",
     *          requirements={"accountId"="\d+"}
     * )
     * @ParamConverter("account", class="OroAccountBundle:Account", options={"id" = "accountId"})
     * @ParamConverter("channel", class="OroChannelBundle:Channel", options={"id" = "channelId"})
     * @AclAncestor("oro_customer_account_view"))
     * @Template
     */
    public function accountCustomersInfoAction(Account $account, Channel $channel)
    {
        $field = AccountCustomerManager::getCustomerTargetField(Customer::class);

        /** @var QueryBuilder $qb */
        $qb = $this->getDoctrine()
            ->getRepository('OroCustomerBundle:Customer')
            ->createQueryBuilder('c');
        $customers = $qb->join(CustomerAssociation::class, 'ca', 'WITH', sprintf('ca.%s = c', $field))
            ->where('ca.account = :account')
            ->andWhere('c.dataChannel = :dataChannel')
            ->setParameter('account', $account)
            ->setParameter('dataChannel', $channel)
            ->getQuery()
            ->getResult();

        $customers = array_filter(
            $customers,
            function ($item) {
                return $this->isGranted('VIEW', $item);
            }
        );

        return [
            'account' => $account,
            'customers' => $customers,
            'channel' => $channel
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/customer-info/{id}",
     *        name="oro_account_customer_widget_customer_info",
     *        requirements={"id"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_customer_account_view"))
     * @Template
     */
    public function customerInfoAction(Customer $customer)
    {
        $accountCustomerManager = $this->get(AccountCustomerManager::class);

        return [
            'customer' => $customer,
            'account' => $accountCustomerManager->getAccountCustomerByTarget($customer)->getAccount(),
            'channel' => $customer->getDataChannel(),
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/customer-users-info/{id}",
     *        name="oro_account_customer_widget_customer_user_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_account_account_user_view")
     * @Template
     */
    public function customerUsersAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/shopping-lists-info/{id}",
     *        name="oro_account_customer_widget_shopping_lists_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_shopping_list_view")
     * @Template
     */
    public function shoppingListsAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/rfq-info/{id}",
     *        name="oro_account_customer_widget_rfq_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_rfp_request_view")
     * @Template
     */
    public function rfqAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/orders-info/{id}",
     *        name="oro_account_customer_widget_orders_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_order_view")
     * @Template
     */
    public function ordersAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/quotes-info/{id}",
     *        name="oro_account_customer_widget_quotes_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_sale_quote_view")
     * @Template
     */
    public function quotesAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/opportunities-info/{id}",
     *        name="oro_account_customer_widget_opportunities_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Customer", options={"id" = "id"})
     * @AclAncestor("oro_sales_opportunity_view")
     * @Template
     */
    public function opportunitiesAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }
}
