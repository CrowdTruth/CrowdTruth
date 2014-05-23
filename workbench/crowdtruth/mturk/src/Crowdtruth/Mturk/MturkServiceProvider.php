<?php namespace CrowdTruth\Mturk;

use Illuminate\Support\ServiceProvider;

class MturkServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('crowdtruth/mturk');
		$this->app['amt.retrievejobs'] = $this->app->share(function(){
			return new RetrieveJobs;
		});
		$this->commands('amt.retrievejobs');

		$this->app->bind('amt', function()
	        {
	           return new Mturk;
	        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
