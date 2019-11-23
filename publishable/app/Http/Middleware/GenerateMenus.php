<?php

namespace App\Http\Middleware;
use App\Helpers\AccessHelper;
use Lavary\Menu\Facade as Menu;

use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, $guard) {

        $this->$guard();

        return $next($request);
    }

/*
     * Pour ajouter les contrôles voir https://github.com/lavary/laravel-menu#filtering-the-items
     * Exemple :
     *

        ->data('middleware', 'CheckRoles:' . Role::SUPER_ADMIN)

     *
     * Exemple de sous-menus :
     * 

        $menu->add('Test', [
                'url' => null
            ])
            ->data('icon', 'question')
            ->data('order', '3');

        $menu->test->add('Link 1', [
                'route' => 'back.index'
            ])
            ->data('order', '1');

        $menu->test->add('Link 2', [
                'url' => null
            ])
            ->data('order', '2')
            ->add('Link 1', [
                'route' => 'back.users.index'
            ])
            ->data('order', '1');

     *
*/


    /**
     * Front office menu
     *
     * @return void
     */
    public function front(){

        Menu::make('menu', function($menu) {

            /*$menu->add(_i('Tableau de bord'), [
                    'route' => 'front.index'
                ])
                ->data('icon', 'tachometer-alt')
                ->data('order', '1');

            $menu->sortBy('order', 'asc');*/

        })->filter(function($item) {
            return AccessHelper::validate($item->data('middleware'));
        });
    }



    /**
     * Back office menu
     *
     * @param Request $request
     * @param \Closure $next
     * @return void
     */
    public function backoffice(){
        
        Menu::make('menu', function ($menu) {

             $menu->add('Home')
				->data('icon', 'home'); 
            
        })->filter(function($item) {
            return AccessHelper::validate($item->data('middleware'));
        });

    }
}
