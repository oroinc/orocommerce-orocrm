UPGRADE FROM 1.1 to 1.2
=======================

CustomerAccount
---------------
* The `CustomerAccountImportExportSubscriber::__construct(DoctrineHelper $doctrineHelper, $customerClassName)`<sup>[[?]](https://github.com/orocommerce/orocommerce-orocrm/tree/1.1.0/src/Oro/Bridge/CustomerAccount/EventSubscriber/CustomerAccountImportExportSubscriber.php#L39 "Oro\Bridge\CustomerAccount\EventSubscriber\CustomerAccountImportExportSubscriber")</sup> method was changed to `CustomerAccountImportExportSubscriber::__construct(TranslatorInterface $translator, CustomerAccountImportExportHelper $customerAccountImportExportHelper, $customerClassName)`<sup>[[?]](https://github.com/laboro/dev/tree/maintenance/2.2/package/commerce-crm/src/Oro/Bridge/CustomerAccount/EventSubscriber/CustomerAccountImportExportSubscriber.php#L45 "Oro\Bridge\CustomerAccount\EventSubscriber\CustomerAccountImportExportSubscriber")</sup>
