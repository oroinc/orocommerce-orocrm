<?php

namespace Oro\Bridge\CustomerAccount\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SecurityBundle\SecurityFacade;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;
use Oro\Bundle\SalesBundle\Entity\Customer as CustomerAssociation;

use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;

/**
 * @Route("/account-customer")
 */
class CustomerController extends Controller
{
    /**
     * @param Account $account
     * @return array
     *
     * @Route(
     *         "/widget/customers-info/{accountId}",
     *          name="oro_account_widget_customers_info",
     *          requirements={"accountId"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("account", class="OroAccountBundle:Account", options={"id" = "accountId"})
     * @AclAncestor("oro_customer_account_view"))
     * @Template
     */
    public function accountCustomersInfoAction(Account $account)
    {
        $field = AccountCustomerManager::getCustomerTargetField(Customer::class);

        $customers = $this->getDoctrine()
            ->getRepository('OroCustomerBundle:Account')
            ->createQueryBuilder('c')
            ->join(CustomerAssociation::class, 'ca', 'WITH', sprintf('ca.%s = c', $field))
            ->where('ca.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getResult();

        $customers = array_filter(
            $customers,
            function ($item) {
                return $this->getSecurityFacade()->isGranted('VIEW', $item);
            }
        );

        return ['customers' => $customers];
    }

    /**
     * @param Customer $customer
     * @return array
     *
     * @Route(
     *        "/widget/customer-info/{id}",
     *        name="oro_account_customer_widget_customer_info",
     *        requirements={"id"="\d+"}
     * )
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
     * @AclAncestor("oro_customer_account_view"))
     * @Template
     */
    public function customerInfoAction(Customer $customer)
    {
        return [
            'customer' => $customer,
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
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
     * @ParamConverter("customer", class="OroCustomerBundle:Account", options={"id" = "id"})
     * @AclAncestor("oro_sales_opportunity_view")
     * @Template
     */
    public function opportunitiesAction(Customer $customer)
    {
        return [
            'customer' => $customer
        ];
    }

    /**
     * @return SecurityFacade
     */
    protected function getSecurityFacade()
    {
        return $this->get('oro_security.security_facade');
    }
}
