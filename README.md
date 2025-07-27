<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## System reqruitments
- Apache Server 2.4.63
- PHP >= 8.4
- Mysql >= 8.0
- PhpMyAdmin >= 6.0 <sup>\*optional</sup>

## How to Install This Project

1. Clone this project to your local computer
   
   ```bash
   git clone https://github.com/Galihuyyy/sistem-peminjaman-barang-api.git
   ```
   ```bash
   cd project-name
   ```

2. Install Composer Dependencies
   ```bash
   composer install
   ```
   or
   ```vim
   composer i
   ```

3. Create .env file
   ```bash
    cp .env.example .env
   ```

4. Generate App Key
   ```bash
    php artisan key:generate
   ```

5. Set up database in your .env file
   ```bash
    DB_CONNECTION  = mysql (default)
    DB_HOST        = 127.0.0.1 (default)
    DB_PORT        = 3306 (default)
    DB_DATABASE    = database_name
    DB_USERNAME    = database_username
    DB_PASSWORD    = database_password
   ```

6. Run migration
   ```bash
    php artisan migrate
   ```

7. Last step, run project in local
   ```bash
    php artisan serve
    ```
   or
   ```bash
    php artisan ser
    ```

8. Congrats, your project now is running enjoy your time!
   

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
