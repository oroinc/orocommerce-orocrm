@ticket-CRM-7573
@automatically-ticket-tagged
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroCheckoutBundle:Checkout.yml
Feature: Default workflow for Commerce Sales Rep
  In order to work with all Commerce features from the Opportunity view conveniently
  As a Sales Rep
  I want to have a default workflow for Opportunities

  Scenario: Feature Background
    Given there is following Customer:
      | Name          |
      | Commerce John |
    And two users charlie and samantha exists in the system
    And there is following Website:
      | Name     | guest_role | default_role |
      | SaleSite | @buyer     | @buyer       |

  Scenario: Administrator activates CRM Opportunity Management Flow
    Given I login as administrator
    When I go to System/Workflows
    Then I should see "Opportunity Management Flow" in grid with following data:
      | Active                  | No                     |
      | Exclusive Active Groups | opportunity_management |
    When I click Activate Opportunity Management Flow in grid
    And I click "Activate" in modal window
    Then I should see "Workflow activated" flash message
    And I should see "Opportunity Management Flow" in grid with following data:
      | Active                  | Yes                    |
      | Exclusive Active Groups | opportunity_management |

  Scenario: Administrator activates "Quote flow"
    Given I check "Opportunity" in Related Entity filter
    And I should see "Quote flow" in grid with following data:
      | Active                  | No               |
      | Exclusive Active Groups | quote_management |
    When I click Activate Quote flow in grid
    And I click "Activate" in modal window
    Then I should see "Workflow activated" flash message
    And I should see "Quote flow" in grid with following data:
      | Active                  | Yes              |
      | Exclusive Active Groups | quote_management |

  Scenario: Check workflow permissions for user
    Given I go to System/User Management/Roles
    And I filter Label as is equal to "Sales Rep"
    And I click Edit Sales Rep in grid
    And I check "Override quote prices" entity permission
    And I save and close form
    Then I should see "Role saved" flash message

  Scenario: Sales Rep creates Opportunity
    Given I login as "charlie" user
    And I go to Sales/Opportunities
    When I click "Create Opportunity"
    And I fill form with:
      | Opportunity Name | Europe Oppo 2x1  |
      | Probability (%)  | 2             |
    And I type "Commerce John" in "Account"
    And I should see "Commerce John (Customer)"
    And I click on "Customer Related Account"
    And I save and close form
    Then I should see "Open" green status
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |

  Scenario: Sales Rep develops Opportunity
    Given I click "Develop"
    When I fill form with:
      | Status          | Needs Analysis  |
      | Probability (%) | 52              |
    And I click "Submit"
    Then I should see "Needs Analysis" green status
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |

  Scenario: Sales Rep creates Quote for Opportunity
    Given I click "Create Quote"
    When I fill form with:
      | Website | SaleSite |
    And fill "Quote Line Items" with:
      | Product    | SKU123 |
      | Unit Price | 100    |
      | Quantity   | 6      |
    And I save and close form
    And agree that shipping cost may have changed
    Then I should see "Quote Created"
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |

  Scenario: Sales Rep closes Opportunity as Lost
    Given I click "Close as Lost"
    When I click "Submit"
    Then I should see "Closed Lost" gray status
    And I should see following buttons:
      | Reopen      |

  Scenario: Sales Rep reopens Opportunity
    Given I click "Reopen"
    When I fill form with:
      | Status          | Open |
      | Probability (%) | 3 |
    And I click "Submit"
    Then I should see "Open" green status
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |

  Scenario: Sales Rep creates Quote for Opportunity and closes as Won
    Given I click "Create Quote"
    When I fill form with:
      | Website  | SaleSite |
    And fill "Quote Line Items" with:
      | Product    | SKU123 |
      | Unit Price | 90     |
      | Quantity   | 6      |
    And I save and close form
    And agree that shipping cost may have changed
    Then I should see "Quote Created"
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |
    But I click "Close as Won"
    And I fill "Opportunity Transition Form" with:
      | Close Revenue | 12000 |
    And click "Submit"
    Then I should see "Closed Won" green status
    And I should see following buttons:
      | Reopen      |

  Scenario: Sales Rep reopens and closes Opportunity and as Lost
    Given I click "Reopen"
    When I fill form with:
      | Status          | Open   |
      | Probability (%) | 4               |
    And click "Submit"
    Then I should see "Open" green status
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |
    But I click "Close as Lost"
    And click "Submit"
    Then I should see "Closed Lost" gray status
    And I should see following buttons:
      | Reopen      |

  Scenario: Sales Rep reopens and closes Opportunity and as Won
    Given I click "Reopen"
    When I fill form with:
      | Status          | Open |
      | Probability (%) | 5    |
    And click "Submit"
    Then I should see "Open" green status
    And I should see following buttons:
      | Develop       |
      | Close as Won  |
      | Close as Lost |
      | Create Quote  |
    But I click "Close as Won"
    And I fill "Opportunity Transition Form" with:
      | Close Revenue | 13000 |
    And click "Submit"
    Then I should see "Closed Won" green status
    And I should see following buttons:
      | Reopen      |
