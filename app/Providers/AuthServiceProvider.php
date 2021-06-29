<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Forum Moderators
	 */
	protected $moderators = [];

	/**
	 * Forum Admins
	 */
	protected $admins = [
		'Raymonf',
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		// Forum shit

		Gate::define('edit-post', function ($user, $post) {
			if(!Auth::guest() && (Auth::user()->id == $post->user->id))
			{
				return true;
			}

			return in_array($user->name, $this->admins) || in_array($user->name, $this->moderators);
		});

		Gate::define('delete-post', function ($user, $post) {
			if(!Auth::guest() && (Auth::user()->id == $post->user->id))
			{
				return true;
			}

			return in_array($user->name, $this->admins) || in_array($user->name, $this->moderators);
		});

		// lol, bad method

		Gate::define('is-mod', function ($user, $post) {
			return in_array($user->name, $this->moderators);
		});

		Gate::define('is-admin', function ($user, $post) {
			return in_array($user->name, $this->admins);
		});
	}
}
