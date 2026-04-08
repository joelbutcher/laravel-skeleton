Feature: Welcome page
    As a visitor
    I want to see the welcome page
    So that I know the application is running

    Scenario: Visiting the welcome page
        When I visit the welcome page
        Then the response status code should be 200
