<div style="text-align: center">
    <h1>Outlay</h1>
    An app for managing your money flow.
</div>

---

## Getting started

### Prerequisites

In order to be able to run the application, you will need the following programs installed and configured correctly:

- PHP 8.1 or greater with `gd` extension enabled
- MySQL 8/MariaDB 10 database
- A user and a database on which the user has privileges

### Installation

Clone the GitHub repository

```shell
$ git clone https://github.com/hubatruck/outlay.git
```

Go into the project's directory

```shell
$ cd outlay
```

Install dependencies

```shell
$ composer install
$ npm install
```

Copy `.env.example` file to `.env` file

```shell
$ cp .env.example .env
```

Generate application key

```shell
$ php artisan key:generate
```

Customize the `.env` file to match your configuration.

**Note:** Don't forget to set the correct username, password and database name! The user should have all privileges on the table so that the application can work correctly.

Migrate tables
```shell
$ php artisan migrate
```

Compile the UI files, by running Laravel Mix

```shell
$ npm run dev
```

### Running

To run the application, use the artisan serve command

```shell
$ php artisan serve
```

## Getting into the application

By default, registration is disabled for the application, and no default users exist. You can change this, by editing
the `routes/web.php` file, and enabling the registration feature.

```php
Auth::routes([
    'register' => true,
    /// ...
]);

Route::get('/', static function () {
/// ...
```

If the app is open in your browser, refresh it, and you should see the `Register` button in the upper right-hand side
menu.

After registering, you can log in with the created user.

## License

The project is open-sourced, licensed under the [MIT License](LICENSE).
