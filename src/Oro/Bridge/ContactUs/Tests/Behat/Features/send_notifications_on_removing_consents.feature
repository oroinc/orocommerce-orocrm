@regression
@feature-BB-13768
@fixture-OroContactUsBridgeBundle:CustomerUserFixture.yml
@fixture-OroConsentBundle:ConsentLandingPagesFixture.yml
Feature: Send notifications on removing consents
  In order to be able to manage consents in OroCommerce
  As an Administrator
  I should see Contact Requests with declined Consents

  Scenario: Create two sessions
    Given sessions active:
      | Admin | first_session  |
      | User  | second_session |

  Scenario: Create Content Node in Web Catalog
    Given I proceed as the Admin
    And I login as administrator
    And go to Marketing/ Web Catalogs
    And click "Create Web Catalog"
    And fill form with:
      | Name | Store and Process |
    When I click "Save and Close"
    Then I should see "Web Catalog has been saved" flash message
    And I click "Edit Content Tree"
    And I fill "Content Node Form" with:
      | Titles | Home page |
    And I click "Add System Page"
    And I save form
    And I click "Create Content Node"
    And I click on "Show Variants Dropdown"
    And I click "Add Landing Page"
    And I fill "Content Node Form" with:
      | Titles       | Store and Process Node |
      | Url Slug     | store-and-process-node |
      | Landing Page | Test CMS Page          |
    When I save form
    Then I should see "Content Node has been saved" flash message
    And I set "Store and Process" as default web catalog

  Scenario: Enable consent functionality via feature toggle
    Given go to System/ Configuration
    And follow "Commerce/Customer/Consents" on configuration sidebar
    And I should not see a "Sortable Consent List" element
    And fill form with:
      | Use Default                  | false |
      | Enable User Consents Feature | true  |
    And click "Save settings"

  Scenario: Create Consent
    Given I go to System/ Consent Management
    And click "Create Consent"
    And fill "Consent Form" with:
      | Name        | Collecting and storing personal data |
      | Type        | Mandatory                            |
      | Web Catalog | Store and Process                    |
    And click "Store and Process Node"
    When save form
    Then should see "Consent has been created" flash message
    When go to System/ Consent Management
    Then I should see following grid:
      | Name                                 | Type      | Content Node           | Content Source |
      | Collecting and storing personal data | Mandatory | Store and Process Node | Test CMS Page  |

  Scenario: Enable Consent on the system level
    Given go to System/ Configuration
    And follow "Commerce/Customer/Consents" on configuration sidebar
    And fill "Consent Settings Form" with:
      | Enabled User Consents Use Default | false |
    When click "Save settings"
    Then I should see "Configuration saved" flash message
    And click "Add Consent"
    And I choose Consent "Collecting and storing personal data" in 1 row
    When click "Save settings"
    Then I should see "Configuration saved" flash message

  Scenario: Decline accepted consent from My profile page
    Given I proceed as the User
    And I signed in as AmandaRCole@example.org on the store frontend
    And follow "Account"
    And I click "Edit Profile Button"
    And I click "Collecting and storing personal data"
    And click "Agree"
    And I save form
    And I click "Edit Profile Button"
    And the "Collecting and storing personal data" checkbox should be checked
    And fill form with:
      | Collecting and storing personal data | false |
    When I save form
    Then I should see "UiWindow" with elements:
      | Title        | Data Protection                                                 |
      | Content      | Are you sure you want to decline the consents accepted earlier? |
      | okButton     | Yes, Decline                                                    |
      | cancelButton | No, Cancel                                                      |
    When click "Yes, Decline"
    Then should see "Customer User profile updated" flash message

  Scenario: Check notifications on removing consents
    Given I proceed as the Admin
    When I go to Activities/ Contact Requests
    Then I should see following grid:
      | First Name | Last Name | Email                   | Contact Reason                             | Website |
      | Amanda     | Cole      | AmandaRCole@example.org | General Data Protection Regulation details | Default |
    When click view "General Data Protection Regulation details" in grid
    Then I should see Contact Request with:
      | First Name     | Amanda                                                            |
      | Last Name      | Cole                                                              |
      | Email          | AmandaRCole@example.org                                           |
      | Contact Reason | General Data Protection Regulation details                        |
      | Comment        | Consent Collecting and storing personal data declined by customer |
      | Customer User  | Amanda Cole                                                       |

  Scenario: Change Contact Reason
    Given go to System/ Configuration
    And follow "Commerce/Customer/Consents" on configuration sidebar
    And I fill form with:
      | Сontact Reason | Other |
    When click "Save settings"
    Then I should see "Configuration saved" flash message

  Scenario: Decline accepted consent from My profile page
    Given I proceed as the User
    And I click "Edit Profile Button"
    And I click "Collecting and storing personal data"
    And click "Agree"
    And I save form
    And I click "Edit Profile Button"
    And the "Collecting and storing personal data" checkbox should be checked
    And fill form with:
      | Collecting and storing personal data | false |
    And I save form
    When click "Yes, Decline"
    Then should see "Customer User profile updated" flash message

  Scenario: Check notifications on removing consents
    Given I proceed as the Admin
    When I go to Activities/ Contact Requests
    Then I should see following grid:
      | First Name | Last Name | Email                   | Contact Reason                             | Website |
      | Amanda     | Cole      | AmandaRCole@example.org | Other                                      | Default |
      | Amanda     | Cole      | AmandaRCole@example.org | General Data Protection Regulation details | Default |

  Scenario: Remove Contact Reason
    Given go to System/ Contact Reasons
    And I click delete "Other" in grid
    And I should see "Delete Confirmation"
    When I click "Yes"
    Then should see "Item deleted" flash message

  Scenario: Decline accepted consent from My profile page
    Given I proceed as the User
    And I click "Edit Profile Button"
    And I click "Collecting and storing personal data"
    And click "Agree"
    And I save form
    And I click "Edit Profile Button"
    And the "Collecting and storing personal data" checkbox should be checked
    And fill form with:
      | Collecting and storing personal data | false |
    And I save form
    When click "Yes, Decline"
    Then should see "Customer User profile updated" flash message

  Scenario: Check notifications on removing consents
    Given I proceed as the Admin
    When I go to Activities/ Contact Requests
    Then I should see following grid:
      | First Name | Last Name | Email                   | Contact Reason                             | Website |
      | Amanda     | Cole      | AmandaRCole@example.org |                                            | Default |
      | Amanda     | Cole      | AmandaRCole@example.org | Other                                      | Default |
      | Amanda     | Cole      | AmandaRCole@example.org | General Data Protection Regulation details | Default |
