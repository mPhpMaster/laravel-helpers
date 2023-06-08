<?php
/*
 * Copyright © 2023. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class HelperProvider
 *
 * @package MPhpMaster\LaravelHelpers\Providers
 */
class HelperProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerMacros();
	}
	
	/**
	 * Bootstrap services.
	 *
	 * @param Router $router
	 *
	 * @return void
	 */
	public function boot(Router $router)
	{
		// Builder::defaultStringLength(191);
		// Schema::defaultStringLength(191);
	}
	
	/**
	 *
	 */
	public function registerMacros()
	{
	
	}
	
	/**
	 * @return array
	 */
	public function provides()
	{
		return [];
	}
}
