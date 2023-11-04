# About SureStore

SureStore is multi tenant eCommerce + Store Management System

## Setup
- clone repo
- copy `.env.example` to `.env`
- `php artisan migrate:fresh --seed`
- set one of the store slug to `mystore`
- add the following to your hosts:
```
    127.0.0.1 surestore.online
    127.0.0.1 mystore.surestore.online
    127.0.0.1 not-found.surestore.online
```
- `php artisan serve --host=surestore.online`
- visit http://mystore.surestore.online

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
