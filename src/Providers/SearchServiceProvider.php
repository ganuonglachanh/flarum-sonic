<?php
namespace GaNuongLaChanh\Search\Providers;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;


class SearchServiceProvider extends ServiceProvider
{
    static protected $iamcalled = false;

    protected $defer = true;
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        if(static::$iamcalled) { return; }


        //$value = new \GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver();
        $value = $this->app->make('GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver');
        $this->app->when('GaNuongLaChanh\Search\Gambit\TitleGambit')
            ->needs('GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver')
            ->give($value);
        /*$this->app->singleton('GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver', function ($app) {
            return new \GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver();
        });*/

        static::$iamcalled = true;
    }

    public function boot() {

    }
}
