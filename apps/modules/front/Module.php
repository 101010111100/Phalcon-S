<?php

namespace P\Front;

use Phalcon\Loader,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\View,
    Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders()
    {

        $loader = new Loader();

        $loader->registerNamespaces(
            array(
                'P\Front\Controllers' => '../apps/modules/front/controllers/',
                'P\Front\Models'      => '../apps/modules/front/models/',
            )
        );

        $loader->register();
    }



    public function registerServices($di)
    {
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("P\Front\Controllers\\");
            return $dispatcher;
        });


        // Установка сервиса voltService как шаблонизатора
        $di->set('view', function(){
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir('../apps/modules/front/views/');
            return $view;
        });




    }




}