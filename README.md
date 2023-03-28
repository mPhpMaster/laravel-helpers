# Laravel Helpers

- `composer a:routes` Use `artisan route:list` and write the output to `routes.txt`.
- `composer helpers` Use `ide-helper` to regenerate ide helpers files.
- `composer c:c` Use `artisan` to clear **cache**, **route**, **config** and **view** files.
- `composer m:c` Create `sessions`, `views` and `sessions` directories in `storage/framework` then chomd `storage/framework` 775 for the user as owner.
- Autoload functions.
- Models to functions.
    > `config(app.models_to_functions)` and `config(app.debug)` are both required to be `true` in order to active *Models to functions*. 

---

### Autoload functions loading files order:
1. `app/Helpers/` _(This is the loader default path, it's changeable)_
2. `vendor/mphpmaster/laravel-helpers/Helpers/src/`
3. `vendor/mphpmaster/laravel-helpers/Helpers/macro/`
4. `vendor/mphpmaster/laravel-helpers/Helpers/src-interfaces/`
5. `vendor/mphpmaster/laravel-helpers/Helpers/src-traits/`
6. `vendor/mphpmaster/laravel-helpers/Helpers/src-class/`

##### NOTE:<small>
> Only files ends with __.functions.php__ and __.class.php__ will be auto-loaded. _(Loading is not **recursive**, depth: **1**)_</small>

---

#### How to Change the loader default path (`app/Helpers/`) ?
Use `define` method to define `LOAD_PATH` which will change the default path.

<small>**Example:**</small>
```php
define('LOAD_PATH', __DIR__.'/../app/Helpers/Autoload/');
```
<small>**CLI Only:**</small>
1. To change it only on `artisan`, you need to modify `./artisan`.
<br>
<small>add your code before or after <code>define('LARAVEL_START', microtime(true));</code> line.</small>

<small>**WEB Only:**</small>

2. To change it only when browsing/api, you need to modify `./public/index.php`.
<br>
<small>add your code before or after <code>define('LARAVEL_START', microtime(true));</code> line.</small>

---

### Configuration:
- You need to define alias for your abstract class model as `\Model`.

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel Helpers is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
