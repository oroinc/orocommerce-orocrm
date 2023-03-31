- [ContactUs](#contactus)
- [CustomerAccount](#customeraccount)

ContactUs
---------
* The `SystemConfigListener`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/ContactUs/EventListener/SystemConfigListener.php#L15 "Oro\Bridge\ContactUs\EventListener\SystemConfigListener")</sup> class was removed.
* The `ContactRequestHelper::__construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager, LocalizationHelper $localizationHelper, TranslatorInterface $translator)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/ContactUs/Helper/ContactRequestHelper.php#L41 "Oro\Bridge\ContactUs\Helper\ContactRequestHelper")</sup> method was changed to `ContactRequestHelper::__construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager, LocalizationHelper $localizationHelper, TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.1.0/src/Oro/Bridge/ContactUs/Helper/ContactRequestHelper.php#L29 "Oro\Bridge\ContactUs\Helper\ContactRequestHelper")</sup>
* The `ContactRequestType::__construct(TokenAccessorInterface $tokenAccessor, LocalizationHelper $localizationHelper)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/ContactUs/Form/Type/ContactRequestType.php#L34 "Oro\Bridge\ContactUs\Form\Type\ContactRequestType")</sup> method was changed to `ContactRequestType::__construct(ManagerRegistry $doctrine, TokenAccessorInterface $tokenAccessor, LocalizationHelper $localizationHelper)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.1.0/src/Oro/Bridge/ContactUs/Form/Type/ContactRequestType.php#L35 "Oro\Bridge\ContactUs\Form\Type\ContactRequestType")</sup>
* The `OroContactUsBridgeExtension::getAlias`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/ContactUs/DependencyInjection/OroContactUsBridgeExtension.php#L35 "Oro\Bridge\ContactUs\DependencyInjection\OroContactUsBridgeExtension::getAlias")</sup> method was removed.

CustomerAccount
---------------
* The following classes were removed:
   - `ReassingCustomerProcessor`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/CustomerAccount/Async/ReassingCustomerProcessor.php#L14 "Oro\Bridge\CustomerAccount\Async\ReassingCustomerProcessor")</sup>
   - `Topics`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/CustomerAccount/Async/Topics.php#L5 "Oro\Bridge\CustomerAccount\Async\Topics")</sup>
* The `CustomerCreateListener::postPersist(Customer $customer, LifecycleEventArgs $args)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerCreateListener.php#L12 "Oro\Bridge\CustomerAccount\EventListener\CustomerCreateListener")</sup> method was changed to `CustomerCreateListener::postPersist(Customer $customer, LifecycleEventArgs $args)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.1.0/src/Oro/Bridge/CustomerAccount/EventListener/CustomerCreateListener.php#L15 "Oro\Bridge\CustomerAccount\EventListener\CustomerCreateListener")</sup>
* The `OroCustomerAccountBridgeExtension::getAlias`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/5.0.0/src/Oro/Bridge/CustomerAccount/DependencyInjection/OroCustomerAccountBridgeExtension.php#L36 "Oro\Bridge\CustomerAccount\DependencyInjection\OroCustomerAccountBridgeExtension::getAlias")</sup> method was removed.

