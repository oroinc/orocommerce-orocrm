@not-automated
@draft
Feature: Default workflow for Commerce Sales Rep
  In order to work with all Commerce features from the Opportunity view conveniently
  As a Sales Rep
  I want to have a default workflow for Opportunities

  Scenario: Feature Background
    Given there is following Commerce Customer:
      | Name          |
      | Commerce John |
    And there are following User:
      | Username | Roles         |
      | OroAdmin | Administrator |
      | SalesMan | Sales Rep     |
    And there is following Website:
      | Name     |
      | SaleSite |
    And there is following Product:
      | SKU   | Name   | Status  | Precision |
      | Prod1 | Tables | Enabled | 10        |
    And "OroCommerce Opportunity Management Flow" Workflow is activated
    
  Scenario: Admnistrator activates CRM Opportunity Management Flow
    Given I login as "OroAdmin" user
    When I go to System/Workflows
    And I open Opportunity Management Flow page
    And I click "Activate"
    And I submit form
    And I go to System/Workflows
    Then I should see OroCommerce Opportunity Management Flow in grid with following data:
    | ACTIVE |
    | No     |
    And I should see Opportunity Management Flow in grid with following data:
    | ACTIVE |
    | Yes    |
    But I go to System/Workflows
    And I open Commerce Opportunity Management Flow page
    And I click "Activate"
    And I submit form
    And I go to System/Workflows
    Then I should see Opportunity Management Flow in grid with following data:
      | ACTIVE |
      | No     |
    And I should see OroCommerce Opportunity Management Flow in grid with following data:
      | ACTIVE |
      | Yes    |

  Scenario: Sales Rep creates Opportunity
    Given I login as "SalesMan" user
    And I go to Sales/Opportunities
    When I click "Create Opportunity"
    And I fill in the following:
      | Opportunity Name | Account       | Status | Probability (%) |
      | Europe Oppo 2x1  | Commerce John | Open   | 2               |
    And I save setting
    Then I should see "Open" green status
    And there should be following buttons:
    | Button Name   |
    | Develop       |
    | Close As Won  |
    | Close As Lost |
    | Create Quote  |

  Scenario: Sales Rep develops Opportunity
    Given I click "Develop"
    When I fill in the following:
      | Status         | Probability (%) |
      | Needs Analysis | 52              |
    And click "Submit"
    Then I should see "Open" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |

  Scenario: Sales Rep creates Quote for Opportunity
    Given I click "Create Quote"
    When I fill in the following:
      | Website  | Product       | Unit Price |
      | SaleSite | Prod1 - Table | 100        |
    And I save setting
    Then I should see "Quote Created" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |

  Scenario: Sales Rep closes Opportunity as Lost
    Given I click "Close As Lost"
    When I submit form
    Then I should see "Lost" green status
    And there should be following buttons:
      | Button Name |
      | Reopen      |

  Scenario: Sales Rep reopens Opportunity
    Given I click "Reopen"
    When I fill in the following:
      | Status | Probability (%) |
      | Open   | 3               |
    And I submit form
    Then I should see "Open" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |

  Scenario: Sales Rep creates Quote for Opportunity and closes as Won
    Given I click "Create Quote"
    When I fill in the following:
      | Website  | Product       | Unit Price |
      | SaleSite | Prod1 - Table | 90         |
    And I save setting
    Then I should see "Quote Created" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |
    But I click "Close As Won"
    And I fill in "Close Revenue" with "12000"
    And I submit form
    Then I should see "Won" green status
    And there should be following buttons:
      | Button Name |
      | Reopen      |

  Scenario: Sales Rep reopens and closes Opportunity and as Lost
    Given I click "Reopen"
    When I fill in the following:
      | Status | Probability (%) |
      | Open   | 4               |
    And I submit form
    Then I should see "Open" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |
    But I click "Close As Lost"
    And I submit form
    Then I should see "Lost" green status
    And there should be following buttons:
      | Button Name |
      | Reopen      |

  Scenario: Sales Rep reopens and closes Opportunity and as Won
    Given I click "Reopen"
    When I fill in the following:
      | Status | Probability (%) |
      | Open   | 5               |
    And I submit form
    Then I should see "Open" green status
    And there should be following buttons:
      | Button Name   |
      | Develop       |
      | Close As Won  |
      | Close As Lost |
      | Create Quote  |
    But I click "Close As Won"
    And I fill in "Close Revenue" with "13000"
    And I submit form
    Then I should see "Won" green status
    And there should be following buttons:
      | Button Name |
      | Reopen      |
