- [ContactUs](#contactus)
- [CustomerAccount](#customeraccount)
- [QuoteSales](#quotesales)

ContactUs
---------
* The `ContactRequestHelper::__construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager, LocalizationHelper $localizationHelper, TranslatorInterface $translator)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0-rc/src/Oro/Bridge/ContactUs/Helper/ContactRequestHelper.php#L47 "Oro\Bridge\ContactUs\Helper\ContactRequestHelper")</sup> method was changed to `ContactRequestHelper::__construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager, LocalizationHelper $localizationHelper, TranslatorInterface $translator)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0/src/Oro/Bridge/ContactUs/Helper/ContactRequestHelper.php#L47 "Oro\Bridge\ContactUs\Helper\ContactRequestHelper")</sup>

CustomerAccount
---------------
* The `CustomerAccountImportExportSubscriber::__construct(TranslatorInterface $translator, CustomerAccountImportExportHelper $customerAccountImportExportHelper, $customerClassName)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0-rc/src/Oro/Bridge/CustomerAccount/EventSubscriber/CustomerAccountImportExportSubscriber.php#L44 "Oro\Bridge\CustomerAccount\EventSubscriber\CustomerAccountImportExportSubscriber")</sup> method was changed to `CustomerAccountImportExportSubscriber::__construct(TranslatorInterface $translator, CustomerAccountImportExportHelper $customerAccountImportExportHelper, $customerClassName)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0/src/Oro/Bridge/CustomerAccount/EventSubscriber/CustomerAccountImportExportSubscriber.php#L44 "Oro\Bridge\CustomerAccount\EventSubscriber\CustomerAccountImportExportSubscriber")</sup>
* The `AccountViewListener::__construct($entityClass, DoctrineHelper $doctrineHelper, RequestStack $requestStack, TranslatorInterface $translator, ConfigManager $configManager)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0-rc/src/Oro/Bridge/CustomerAccount/EventListener/AccountViewListener.php#L37 "Oro\Bridge\CustomerAccount\EventListener\AccountViewListener")</sup> method was changed to `AccountViewListener::__construct($entityClass, DoctrineHelper $doctrineHelper, RequestStack $requestStack, TranslatorInterface $translator, ConfigManager $configManager)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0/src/Oro/Bridge/CustomerAccount/EventListener/AccountViewListener.php#L37 "Oro\Bridge\CustomerAccount\EventListener\AccountViewListener")</sup>

QuoteSales
----------
* The `OpportunityQuotesListener::__construct(OpportunityQuotesProvider $opportunityQuotesProvider, TranslatorInterface $translator)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0-rc/src/Oro/Bridge/QuoteSales/EventListener/OpportunityQuotesListener.php#L26 "Oro\Bridge\QuoteSales\EventListener\OpportunityQuotesListener")</sup> method was changed to `OpportunityQuotesListener::__construct(OpportunityQuotesProvider $opportunityQuotesProvider, TranslatorInterface $translator)`<sup>[[?]](https://github.com/oroinc/orocommerce-orocrm/tree/4.0.0/src/Oro/Bridge/QuoteSales/EventListener/OpportunityQuotesListener.php#L26 "Oro\Bridge\QuoteSales\EventListener\OpportunityQuotesListener")</sup>

