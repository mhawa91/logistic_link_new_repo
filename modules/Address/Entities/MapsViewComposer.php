<?php
 
namespace Modules\Address\Entities;
 
use Illuminate\View\View;
 
class MapsViewComposer
{ 
    /**
     * Create a new FCM composer.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
    }
 
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->__env->startPush('head');
        echo view('address::partials.styles')->render();
        $view->__env->stopPush();

        $view->__env->startPush('script');
        echo view('address::partials.scripts')->render();
        $view->__env->stopPush();
    }
}