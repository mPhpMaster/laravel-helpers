# Laravel Helpers
<small>v3.0.0</small>

## Dependencies:
* php >=8.1 **REQUIRED IN YOUR PROJECT**
* laravel >=9 **REQUIRED IN YOUR PROJECT**
* illuminate/support >=9 _composer will install it automatically_
* laravel/helpers ^1.5 _composer will install it automatically_
* mphpmaster/laravel-dev-helpers ^1 _composer will install it automatically_
* mphpmaster/laravel-guesser-helpers ^1 _composer will install it automatically_
* mphpmaster/laravel-helpers2 ^1 _composer will install it automatically_
* mphpmaster/laravel-app-helpers ^1 _composer will install it automatically_
* mphpmaster/laravel-nova-helpers ^1 _composer will install it automatically_

## Installation:
### Composer:
  ```shell
  composer require mphpmaster/laravel-helpers
  ```

### NPM:
  ```shell
  npm install @mphpmaster/laravel-helpers
  ```

## Content
- Providers:
  - `MPhpMaster\LaravelHelpers\Providers\HelperProvider`

- Functions:
  - `columnLocalize`
  - `unzip`

---

## <span style="color: red;">To add:</span>
- Add `developer` key to `config/app.php`
- Add `dev_mode` key to `config/app.php`

```php
// example:
return [
//  ...
    'dev_mode' => env('DEV_MODE', false),
    'developer' => env('DEVELOPER', 'safadi'),
//  ...
];
```

> *Inspired by laravel/helpers.*

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel Helpers: App is open-sourced software licensed under the [MIT license](https://github.com/mPhpMaster/laravel-helpers/blob/master/LICENSE).

