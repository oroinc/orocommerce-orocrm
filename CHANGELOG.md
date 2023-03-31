The upgrade instructions are available at [Oro documentation website](https://doc.oroinc.com/master/backend/setup/upgrade-to-new-version/).

The current file describes significant changes in the code that may affect the upgrade of your customizations.

## Changes in the Commerce-CRM package versions

- [5.1.0](#510-2023-03-31)
- [5.0.0](#500-2022-01-26)
- [4.2.0](#420-2020-01-29)
- [4.1.0](#410-2020-01-31)
- [4.0.0](#400-2019-07-31)
- [3.1.0](#310-2019-01-30)
- [3.0.0](#300-2018-07-27)
- [1.6.0](#160-2018-01-31)
- [1.4.0](#140-2017-09-29)
- [1.3.0](#130-2017-07-28)
- [1.2.0](#120-2017-06-01)

## 5.1.0 (2023-03-31)

[Show detailed list of changes](incompatibilities-5-1.md)

## 5.0.0 (2022-01-26)
[Show detailed list of changes](incompatibilities-5-0.md)

## 4.2.0 (2020-01-29)
[Show detailed list of changes](incompatibilities-4-2.md)

## 4.1.0 (2020-01-31)
[Show detailed list of changes](incompatibilities-4-1.md)

### Removed
* The `*.class` parameters for all entities were removed from the dependency injection container.
The entity class names should be used directly, e.g.,`'Oro\Bundle\EmailBundle\Entity\Email'`
instead of `'%oro_email.email.entity.class%'` (in service definitions, datagrid config files, placeholders, etc.), and
`\Oro\Bundle\EmailBundle\Entity\Email::class` instead of `$container->getParameter('oro_email.email.entity.class')`
(in PHP code).

## 4.0.0 (2019-07-31)
[Show detailed list of changes](incompatibilities-4-0.md)

## 3.1.0 (2019-01-30)
[Show detailed list of changes](incompatibilities-3-1.md)

## 3.0.0 (2018-07-27)
[Show detailed list of changes](incompatibilities-3-0.md)

## 1.6.0 (2018-01-31)
[Show detailed list of changes](incompatibilities-1-6.md)

## 1.4.0 (2017-09-29)
[Show detailed list of changes](incompatibilities-1-4.md)

## 1.3.0 (2017-07-28)
[Show detailed list of changes](incompatibilities-1-3.md)

## 1.2.0 (2017-06-01)
[Show detailed list of changes](incompatibilities-1-2.md)
