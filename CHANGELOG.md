The upgrade instructions are available at [Oro documentation website](https://doc.oroinc.com/backend/setup/upgrade-to-new-version/).

The current file describes significant changes in the code that may affect the upgrade of your customizations.

## 4.2.0 (2020-01-29)
[Show detailed list of changes](incompatibilities-4-2.md)

## 4.1.0 (2020-01-31)
[Show detailed list of changes](incompatibilities-4-1.md)

### Removed
* `*.class` parameters for all entities were removed from the dependency injection container.
The entity class names should be used directly, e.g. `'Oro\Bundle\EmailBundle\Entity\Email'`
instead of `'%oro_email.email.entity.class%'` (in service definitions, datagrid config files, placeholders, etc.), and
`\Oro\Bundle\EmailBundle\Entity\Email::class` instead of `$container->getParameter('oro_email.email.entity.class')`
(in PHP code).

## 4.1.0-rc (2019-12-10)
[Show detailed list of changes](incompatibilities-4-1-rc.md)

## 4.0.0 (2019-07-31)
[Show detailed list of changes](incompatibilities-4-0.md)

## 4.0.0-beta (2019-03-28)
[Show detailed list of changes](incompatibilities-4-0-beta.md)

## 3.0.0-rc (2018-05-31)
[Show detailed list of changes](incompatibilities-3-0-rc.md)

## 1.6.0 (2018-01-31)
[Show detailed list of changes](incompatibilities-1-6.md)

## 1.4.0 (2017-09-29)
[Show detailed list of changes](incompatibilities-1-4.md)

## 1.3.0 (2017-07-28)
[Show detailed list of changes](incompatibilities-1-3.md)

## 1.2.0 (2017-05-31)
[Show detailed list of changes](incompatibilities-1-2.md)
