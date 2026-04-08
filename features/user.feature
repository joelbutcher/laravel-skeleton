Feature: User management
    As the system
    I want to manage users
    So that the application has authenticated users

    Scenario: Creating a new user
        Given a user exists with name "Joel Butcher" and email "joel@example.com"
        Then the user "joel@example.com" should exist in the database
        And the user should have a valid ULID as their identifier

    Scenario: Creating an unverified user
        Given an unverified user exists with email "unverified@example.com"
        Then the user "unverified@example.com" should not have a verified email
