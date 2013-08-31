<?php
(new Phalcon\Debug)->listen();
use Phalcon\Mvc\Router,
    Phalcon\Mvc\Application,
    Phalcon\DI\FactoryDefault,
    Phalcon\Exception;

// загрузка основных настроек системы
$config = new Phalcon\Config\Adapter\Ini( $_SERVER['DOCUMENT_ROOT'] . '/apps/config/config.ini' );

// регистрация Namespaces используемых всеми модулями
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(array(
    'P\Common\Libraries' => $_SERVER['DOCUMENT_ROOT'] . '/common/libraries/',
    'P\Common\Models' =>  $_SERVER['DOCUMENT_ROOT'] . '/common/models/'
));
$loader->register();



//  Dependency Injection
$di = new FactoryDefault();


// загрузка сервисов приложения
require $_SERVER['DOCUMENT_ROOT'] . '/apps/config/services.php';


//try {

// Создание приложения
$application = new Application($di);


// Регистрация установленных модулей
if( $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml') )
{
    // переводим в массив
    $json = json_encode($xml);
    $array = json_decode($json,TRUE);
    // регистрируем модули
    $application->registerModules(
        $array
    );
}
else
{
    exit('Не удалось подключить файл списка модулей <br> Unable to connect a file list of all modules');
}



// Обработка запроса
echo $application->handle()->getContent();

//}
//catch(Exception $e)
//{
//    echo $e->getMessage();
//}