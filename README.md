# About

This project is created for code evaluation that will be used by kumu.

## API Challenge

### Introduction
Create PHP API project that has an API endpoint that takes a list of github usernames (up to a maximum of 10 names) and returns to the user a list of basic information for those users including:
- Name
- Login
- Company
- Number of followers
- Number of public repositories
- The average number of followers per public repository (ie. number of followers divided by the number of public repositories)
In order to access the API endpoint described above, another endpoint should be created for user registration and login.
Rules to Follow:
- Schema for User registration should be created in MySQL
- Only registered users can request a list of GitHub user information.
- The returned users should be sorted alphabetically by name
- If some usernames cannot be found, this should not fail the other usernames requested
- Implement a caching layer using Redis that will store a user that has been retrieved from GitHub for 2 minutes
- Each user should be cached individually. For example, if I request users A and B, then do another request inside 2 minutes for users B, C and D, user B should come from the cache and users C and D should come from GitHub
- If a user is returned from the cache, it should not call GitHub API
- The API endpoint needed to get github user information is
https://api.github.com/users/{username}
- Include proper error handling
- Include proper logging
- Provide a Readme.md with instructions on how to execute your API endpoint


### Prerequesites
List of necessary applications and modules for the project to be able to run.
- Composer
- Laravel Sail
- PHP Artisan
- Postman
- MySQL Client GUI for better data viewing (e.g. SQLYog which is used in the examples.)


### Installation/Configuration
1. Clone/Download the Repository

    Open the terminal or command prompt and go to the directory you want to clone the application and do the following command:
    ```
    git clone https://github.com/yoru214/kumu.git
    ```
    This will download the project source code.

2. Install the application using Composer

    Go to the `kumu` directory by using this command:
    ```
    cd kumu
    ```
    Run the following command to install
    ```
    composer install
    ```
    This will download and install the necessary dependencies.

3. Build the application via Laravel Sail
    Build the application on laravel sail containers using the following command:
    ```
    ./vendor/bin/sail up --build
    ```

4. Configure Enviroment values

    Copy all values from `.env.example` to `.env`
    
5. Configure the application using PHP Artisan
    Once the build is finished, open another terminal and go to the `kumu` directory and run the following command: 
    ```
    php artisan migrate
    ```
    This will setup the database tables needed for the application to run.
5. Run the application
    After the migration, run the laravel app using the following command:
    ```
    php artisan serve
    ```
    This will allow us to access the API via `localhost` at port `8000`
    ```
    http://localhost:8000
    ```

### User Manual

The application uses `GraphQL` thus we will assume each query and mutation as a single endpoint. 

1. Register User

    By default, no user is registered on the application. 

    And the users had to be authenticated to be able to use the main endpoint.

    To register a user, we have to use the `Register` mutation.

    <b>`Query:`</b>
    ```
    mutation Register ($email: String!, $password: String!, $name: String){
        Register (email:$email, password: $password, name: $name) {
            ID
            Name
            Email
            Token
        }

    }
    ```


    <b>`Variables:`</b>
    ```
    {
        "name": "User",
        "email": "user@mail.com",
        "password":"12345678"
    }
    ```

    The mutation accepts three (3) arguments which upon request can be represented as variables on the request headers:

    1. `email`
        
        Refers to the email of the user and will serve as username during authentication, though it is still presented as an email field on the `Authenticate` query.

        This field is `required` and should also be of email format and should be unique. Meaning, no user should have the same email.

    2. `password`

        Refers to the password of the user that will be used on authentication.
        This field is required and should have a minimum of 8 characters.

    3. `name`

        Refers to the name of the user. It could serve as a label to identify the user.

        This field is not required and could be left blank.

    The mutation could return four (4) fields. It is not required to return all of the fields but you have to return at least one or `Graphql` will throw an error.

    1. `ID`

        Refers to the ID of the user in the database.

    2. `Name`

        Refers to the name set to the user upon registration.

    3. `Email`

        Refers to the email/username of the User

    4. `Token`

        Refers to the string token that will be used for authentication when accessing the main endpoint using the authenticated user.



2. Authenticate User

    To authenticate user, the `Authenticate` query is used.

    <b>`Query`</b>

    ```
    query Authenticate ($email: String!, $password: String!){
        Authenticate (email:$email, password: $password) {
            ID
            Name
            Email
            Token
        }
    }
    ```

    <b>`Variables:`</b>

    ```
    {
        "email": "user@mail.com",
        "password":"12345678"
    }
    ```

    The query accepts two (2) arguments which upon request can be represented as variables on the request headers:

    1. `email`
        
        Email which serves as the username of the User

    2. `password`

        Password set to the user upon registration.

    Both fields are used to validate if the user exists in the database, thus making them required.

    The query could return four (4) fields. It is not required to return all of the fields but you have to return at least one or `Graphql` will throw an error.


    1. `ID`

        Refers to the ID of the user in the database.

    2. `Name`

        Refers to the name set to the user upon registration.

    3. `Email`

        Refers to the email/username of the User

    4. `Token`

        Refers to the string token that will be used for authentication when accessing the main endpoint using the authenticated user.


3. Get GitHub Users

    This endpoint is where we try to access details of a list of GitHub users.

    <b>`Query`</b>

    ```
    query GitHubUsers ($usernames: [String]!) {
        GitHubUsers (usernames: $usernames) {
            Name
            Login
            Company
            NumFollowers
            NumPublicRepo
            AvgFollowersPerRepo
        }
    }
    ```

    <b>`Variables:`</b>

    ```
    {
        "usernames": [
            "pjhyett",
            "wycats",
            "ezmobius",
            "ivey",
            "evanphx",
            "vanpelt",
            "wayneeseguin",
            "brynary",
            "mojombo",
            "defunkt"
        ]
    }
    ```

    The query accepts a single argument which is a `list of Github usernames`.

    This list is formatted as an array of string. Each element is equivalent to a single username.

    Upon successful query, it will then return a list of Github users with some additional details:

    1. `Name`

        Refers to the name of the GitHub user.

    2. `Login`

        Refers to the username of the GitHub user.

    3. `Company`

        Refers to the company where the Github user is attached to.

    4. `NumFollowers`

        Total number of followers.

    5. `NumPublicRepo`

        Total number of public repositories.

    6. `AvgFollowersPerRepo`

        The average number of followers per public repository (ie. number of followers divided by the number of public repositories)


### Test Application using `Postman`

## Bonus Challenge (Optional)