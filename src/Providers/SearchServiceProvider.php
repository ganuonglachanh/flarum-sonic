<?php
namespace GaNuongLaChanh\Sonic\Providers;

use Flarum\Foundation\AbstractServiceProvider;


class SearchServiceProvider extends AbstractServiceProvider
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


        ////$value = new \GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver();
        // $value = $this->app->make('GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver');
        // $this->app->when('GaNuongLaChanh\Sonic\Gambit\TitleGambit')
        //     ->needs('GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver')
        //     ->give($value);
        $this->app->singleton('GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver', function () {
            return new \GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver();
        });

        static::$iamcalled = true;
    }

    public function boot() {

    }
}
