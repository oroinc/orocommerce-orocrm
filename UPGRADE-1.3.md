UPGRADE FROM 1.2 to 1.3
=======================

ContactUs bridge
----------------
- Class `Oro\Bridge\ContactUs\Form\Type\ContactRequestType`
    - changed the constructor signature. Parameter `SecurityFacade $securityFacade` was replaced with `TokenAccessorInterface $tokenAccessor`

CustomerAccount bridge
----------------------
- Class `Oro\Bridge\CustomerAccount\Controller\CustomerController`
    - removed method `getSecurityFacade`
