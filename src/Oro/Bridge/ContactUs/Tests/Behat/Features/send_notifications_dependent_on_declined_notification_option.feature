@regression
@feature-BB-24651
@fixture-OroContactUsBridgeBundle:CustomerUserProfileFixture.yml
Feature: Send notifications dependent on declined notification option
  In order to be able to manage declined consents notifications in OroCommerce
  As an Administrator
  I should see Contact Requests with declined consents only when
  declined consent notification is enabled and not see when it disabled

  Scenario: Create two sessions
    Given sessions active:
      | Admin | first_session  |
      | User  | second_session |

  Scenario: Enable consent functionality via feature toggle
    Given I proceed as the Admin
    And I login as administrator
    And I go to System/ Configuration
    And follow "Commerce/Customer/Interactions" on configuration sidebar
    And I should not see a "Sortable Consent List" element
    And I uncheck "Use default" for "Enable user consents feature" field
    And I check "Enable user consents feature"
    And click "Save settings"

  Scenario: Create Consent
    Given I go to System/ Consent Management
    And click "Create Consent"
    And fill "Consent Form" with:
      | Name                          | Consent with enabled declined notification |
      | Type                          | Mandatory                                  |
      | Declined Consent Notification | true                                       |
    When I click "Save and Close"
    Then should see "Consent has been created" flash message
    When go to System/ Consent Management
    And click "Create Consent"
    And fill "Consent Form" with:
      | Name                          | Consent with disabled declined notification |
      | Type                          | Mandatory                                   |
      | Declined Consent Notification | false                                       |
    When I click "Save and Close"
    Then should see "Consent has been created" flash message
    When go to System/ Consent Management
    Then I should see following grid:
      | Name                                        | Type      | Content Node  | Content Source | Declined Consent Notification |
      | Consent with disabled declined notification | Mandatory |               |                | No                            |
      | Consent with enabled declined notification  | Mandatory |               |                | Yes                           |

  Scenario: Enable Consent on the system level
    Given go to System/ Configuration
    And follow "Commerce/Customer/Interactions" on configuration sidebar
    And I uncheck "Use default" for "Enabled user consents" field
    When click "Save settings"
    Then I should see "Configuration saved" flash message
    And click "Add Consent"
    And I choose Consent "Consent with enabled declined notification" in 1 row
    And click "Add Consent"
    And I choose Consent "Consent with disabled declined notification" in 2 row
    When click "Save settings"
    Then I should see "Configuration saved" flash message

  Scenario: Decline accepted consent from My profile page
    Given I proceed as the User
    And I signed in as AmandaRCole@example.org on the store frontend
    And I click "Account Dropdown"
    And I click "My Profile"
    And I click "Edit"
    And I check "Consent with enabled declined notification"
    And I check "Consent with disabled declined notification"
    And I save form
    And I click "Edit Profile Button"
    And the "I Agree with Consent with enabled declined notification" checkbox should be checked
    And the "I Agree with Consent with disabled declined notification" checkbox should be checked
    And fill form with:
      | Consent with enabled declined notification  | false |
      | Consent with disabled declined notification | false |
    When I save form
    Then I should see "UiWindow" with elements:
      | Title        | Data Protection                                                 |
      | Content      | Are you sure you want to decline the consents accepted earlier? |
      | okButton     | Yes, Decline                                                    |
      | cancelButton | No, Cancel                                                      |
    When click "Yes, Decline"
    Then should see "Customer User profile updated" flash message

  Scenario: Check notifications on declined consents
    Given I proceed as the Admin
    When I go to Activities/ Contact Requests
    Then there are 1 records in grid
    And I should see following grid:
      | First Name | Last Name | Email                   | Contact Reason                             | Website |
      | Amanda     | Cole      | AmandaRCole@example.org | General Data Protection Regulation details | Default |
    When click view "General Data Protection Regulation details" in grid
    Then I should see Contact Request with:
      | First Name     | Amanda                                                                  |
      | Last Name      | Cole                                                                    |
      | Email          | AmandaRCole@example.org                                                 |
      | Contact Reason | General Data Protection Regulation details                              |
      | Comment        | Consent Consent with enabled declined notification declined by customer |
      | Customer User  | Amanda Cole                                                             |