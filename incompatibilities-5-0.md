- [CustomerAccount](#customeraccount)

CustomerAccount
---------------
* The `CustomerLifetimeListener::initializeFromEventArgs`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L216 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener::initializeFromEventArgs")</sup> method was removed.
* The `CustomerLifetimeListener::__construct(ServiceLink $rateConverterLink, LifetimeProcessor $lifetimeProcessor, PaymentStatusManager $paymentStatusManager, DoctrineHelper $doctrineHelper)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L54 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener")</sup> method was changed to `CustomerLifetimeListener::__construct(DoctrineHelper $doctrineHelper, ContainerInterface $container)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L32 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener")</sup>
* The following properties in class `CustomerLifetimeListener`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L25 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener")</sup> were removed:
   - `$uow`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L25 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener::$uow")</sup>
   - `$em`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L28 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener::$em")</sup>
   - `$rateConverter`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.2.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerLifetimeListener.php#L40 "Oro\Bridge\CustomerAccount\EventListener\CustomerLifetimeListener::$rateConverter")</sup>

