<?php

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router = new RouteList();
        Route::$styles['#raw'] = array(
            Route::FILTER_OUT => null,
            Route::PATTERN => ".*",
        );
        $router[] = new Route('v/<filename #raw>', 'Document:view');
        $router[] = new Route('edit/<filename #raw>/process', 'Document:process');
        $router[] = new Route('edit/<filename #raw>', 'Document:edit');
        $router[] = new Route('sign/in/[<step (1|2)>]', 'Sign:in');
        $router[] = new Route('<presenter>/[<action>]', 'Homepage:default');
        return $router;
    }
}
