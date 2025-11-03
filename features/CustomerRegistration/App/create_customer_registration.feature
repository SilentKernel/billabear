Feature: Customer Registration Creation
  In order to allow customer self-registration
  As an APP user
  I need to create customer registration links

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |

  Scenario: Create a Customer Registration
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer registration with:
      | Name      | Test Registration |
      | Permanent | true              |
    Then there should be a customer registration called "Test Registration"
    And the customer registration should have a slug
    And the customer registration should be valid

  Scenario: List Customer Registrations
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following customer registrations exist:
      | Name               | Permanent |
      | Registration One   | true      |
      | Registration Two   | false     |
    When I list customer registrations
    Then I should see "Registration One" in the list
    And I should see "Registration Two" in the list

  Scenario: View Customer Registration
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following customer registrations exist:
      | Name             | Permanent |
      | Test Registration | true      |
    When I view the customer registration "Test Registration"
    Then I should see the customer registration details for "Test Registration"
    And the registration should be permanent
