# Laravel Helpers `1.0.0`

- `php artisan mphpmaster:app:setup` to setup the app (Use `--seed` or `-s` to run seeders).
- `composer a:routes` Use `artisan route:list` and write the output to `routes.txt`.
- `composer helpers` Use `ide-helper` to regenerate ide helpers files.
- `composer c:c` Use `artisan` to clear **cache**, **route**, **config** and **view** files.
- `composer m:c` Create `sessions`, `views` and `sessions` directories in `storage/framework` then chomd `storage/framework` 775 for the user as owner.
- Autoload functions cycle:
    1. `app/Helpers/Helper.php`.
    2. `Helpers/src/Helpers/Functions.php`.
    3. `Helpers/src/Helpers/Global.functions.php`.
    4. The remaining helpers.
    5. `app/Helpers/*.functions.php` | `app/Helpers/*.class.php`.


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel Helpers is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
