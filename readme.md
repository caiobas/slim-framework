# Slim Framework

This is a Slim application created to learn Slim framework and apply tools/concepts as Docker, RabbitMQ, Swift Mailer, authentication using JWT. Tests written using PHPUnit. PostgreSQL is the database used to store data.

The project has an architecture using Controller, Services and Repositories. Also has Response classes and Entity classes in order to manipulate data. 

## Installation

In order to run this project, please install:

- Docker (tested on version 24.0.6)
- Docker Compose (tested on version 1.29.2)

create a copy of `.env.sample` file and name as `.env`.

### Run Docker

Run `make up` command to create the containers.

If it is first time, run `make init` command to install composer packages and run migrations.

The application should be running on `http://localhost:8080`

In order to run tests, run `make test` command when the application is up.

Note: you can see other commands on Makefile file on the application root path.

## How to Use

Now that the containers are running. This is how to use the application.

The application has 4 endpoints:
- POST /sign-up
- POST /sign-in
- GET /stock
- GET /history

### POST /sign-up

The goal for this endpoint is to sign up a new user on the application. Here is the curl for the endpoint:

```
curl --location --request POST 'http://localhost:8080/sign-up' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "email@email.com",
    "password": "password",
    "first_name": "first",
    "last_name": "last"
}'
```

It validates if the email is already registered. So can only register one user per email.
The success return is empty with 201 http code;

### POST /sign-in

The goal for this endpoint is to sign in a registered user on the application. Here is the curl for the endpoint:

```
curl --location --request POST 'http://localhost:8080/sign-in' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "email@email.com",
    "password": "password"
}'
```

It validates both email and password to verify the authenticity of the user. Note that the password is encrypted on database.

The success return is a JWT token with 200 http code:
```json
{
  "token":"jwt_token"
}
```

**NOTE: In order to make the test easier, JWT token is set to expire in 5 minutes from sign-in. Have that in mind when testing `/stock` and `/history` endpoints.**

### GET /stock

The goal for this endpoint is to get a stock quote of a stock code passed as query string on request.
This endpoint is authenticated. So the request needs to use JWT token returned on `/sign-in` endpoint.
Here is the curl for the endpoint:

```
curl --location --request GET 'http://localhost:8080/stock?q={stockCode}' \
--header 'Authorization: Bearer jwt_token'
```

This endpoint hits Stooq API and gets stock quote for the stock code received on request, save the info on database and send a message to RabbitMQ queue with the stock info to be sent by email.
The success return is stock quote info with 200 http code:
```json
{
  "name":"WIG",
  "symbol":"WIG",
  "open":66499.72,
  "high":66722.56,
  "low":66187,
  "close":66519.31
}
```

**NOTE: In order to send the email correctly, go to https://mailtrap.io/, create an account and get Mailer Settings for the follow env variables:**
- MAILER_HOST
- MAILER_PORT
- MAILER_USERNAME
- MAILER_PASSWORD

**After that you should open a terminal and run `make queue-email`. This command will receive all messages sent to the queue and will send to email previously configured in the message.**
### GET /history

The goal for this endpoint is to get user stock quote requests history.
This endpoint is authenticated. So the request needs to use JWT token returned on `/sign-in` endpoint.
Here is the curl for the endpoint:

```
curl --location --request GET 'http://localhost:8080/history' \
--header 'Authorization: Bearer jwt_token'
```

This endpoint search in database all stock quotes requested by the user identified through JWT token. The stock quotes are ordered for newest to the oldest request.
The success return is an array with stock quote info with 200 http code:
```json
[
  {
    "date": "2023-09-25T19:05:57",
    "name": "APPLE",
    "symbol": "AAPL.US",
    "open": 174.19,
    "high": 176.97,
    "low": 174.15,
    "close": 175.5723
  },
  {
    "date": "2023-09-25T18:25:25",
    "name": "WIG",
    "symbol": "WIG",
    "open": 66425.3,
    "high": 66741.01,
    "low": 65606.13,
    "close": 65788.09
  },
  {
    "date": "2023-09-25T00:43:53",
    "name": "WIG",
    "symbol": "WIG",
    "open": 66499.72,
    "high": 66722.56,
    "low": 66187,
    "close": 66519.31
  },
  {
    "date": "2023-09-24T23:33:18",
    "name": "APPLE",
    "symbol": "AAPL.US",
    "open": 174.67,
    "high": 177.079,
    "low": 174.05,
    "close": 174.79
  }
]
```
