<?php

namespace P\PNews;

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
                'P\PNews\Controllers' => '../apps/modules/pnews/controllers/',
                'P\PNews\Models'      => '../apps/modules/pnews/models/',
            )
        );

        $loader->register();
    }



    public function registerServices($di)
    {
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("P\PNews\Controllers\\");
            return $dispatcher;
        });


        // Установка сервиса voltService как шаблонизатора
        $di->set('view', function(){
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir('../apps/modules/pnews/views/');
            return $view;
        });




    }




}