<?php

namespace Tests\Feature;

use GetCandy;
use Laravel\Passport\Client;
use Tests\Stubs\User;
use Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class FeatureCase extends TestCase
{
    protected $userToken;

    protected $clientToken;

    protected $headers = [];

    public function setUp() : void
    {
        parent::setUp();
        GetCandy::routes();
        $this->artisan('key:generate');
        $this->artisan('passport:install');
    }

    public function admin()
    {
        $user = User::first();
        $user->assignRole('admin');
        return $user;
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = $user->createToken('TestToken', [])->accessToken;

        // dd($token);
        $this->headers['Accept'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer ' . $token;

        return $this;
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('auth.guards.api', [
            'driver' => 'passport',
            'provider' => 'users',
        ]);
    }

    public function json($method, $uri, array $data = [], array $headers = [])
    {
        return parent::json($method, $uri, $data, array_merge($this->headers, $headers));
    }
}
