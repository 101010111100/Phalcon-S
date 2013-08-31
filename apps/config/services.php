<?php

/****************************************************************************************************************
 *  УРЛ
 ***************************************************************************************************************/
$di->set('url', function () use ($config) {
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri("/");
    return $url;
}, true);

/***************************************************************************************************************
 *  Роутинг
 ***************************************************************************************************************/
$di->set('router', function() {

    $router = new Phalcon\Mvc\Router(false);

    $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml');

    foreach ($xml->route as $route)
    {

        $router->add(''. $route['url'] .'',
            array(
                'module'     => ''. $route['module'] .'',
                'controller' => ''. $route['controller'] .'',
                'action'     => ''. $route['action'] .''
            )
        );

    }

    $router->notFound(array(
        'module' => 'front',
        "controller" => "index",
        "action" => "error404"
    ));

    return $router;

});

/***************************************************************************************************************
 * Сессии
 ***************************************************************************************************************/
$di->set('session', function() {
    $session = new Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

/***************************************************************************************************************
 * База данных с логгированием
 ***************************************************************************************************************/
$di->set('db', function() use($config) {

    $eventsManager = new \Phalcon\Events\Manager();

    $logger = new \Phalcon\Logger\Adapter\File( $_SERVER['DOCUMENT_ROOT'] . '/mysqllogger.log');

    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
            $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO .' ' . __METHOD__);
        }
    });
    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name,
        "options" => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    ));

    //Assign the eventsManager to the db adapter instance
    $connection->setEventsManager($eventsManager);

    return $connection;

});

/***************************************************************************************************************
 * Memcache
 ***************************************************************************************************************/
$di->set('modelsCache', function() {

    //Cache data for one day by default
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 86400
    ));

    //Memcached connection settings
    $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
        "host" => "127.0.0.1",
        "port" => "11211"
    ));

    return $cache;
});


/***************************************************************************************************************
 * Менеджер модулей
 ***************************************************************************************************************/
$di->set('modelsManager', function(){
    return new Phalcon\Mvc\Model\Manager();
});

/***************************************************************************************************************
 * Response Request
 ***************************************************************************************************************/
$di->set('response', function(){
    return new \Phalcon\Http\Response();
});
$di->set('request', function(){
    return new \Phalcon\Http\Request;
});

/***************************************************************************************************************
 * Фильтры
 ***************************************************************************************************************/
$di->set('filter', function(){
    $filter = new \Phalcon\Filter();
    $filter->add('password', function($value) {
        return preg_replace('/[^0-9a-f]/', '', $value);
    });
    return $filter;
});

/***************************************************************************************************************
 * Конфигурация как сервис
 ***************************************************************************************************************/
$di->set('config', function() {
    return new Phalcon\Config\Adapter\Ini( $_SERVER['DOCUMENT_ROOT'] . '/apps/config/config.ini' );
});

/***************************************************************************************************************
 * Security
 ***************************************************************************************************************/
$di->set('security', function(){
    return new \Phalcon\Security();
});

/***************************************************************************************************************
 * Cookies
 ***************************************************************************************************************/
$di->set('cookies', function() {
    $cookies = new Phalcon\Http\Response\Cookies();
    return $cookies;
});

/***************************************************************************************************************
 * Crypt
 ***************************************************************************************************************/
$di->set('crypt', function() {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey('#1dj8$=dp?.ak//j1V$');
    return $crypt;
});

/***************************************************************************************************************
 *  Логгирование
 ***************************************************************************************************************/
$di->set('logger', function() {
    return new \Phalcon\Logger\Adapter\File( $_SERVER['DOCUMENT_ROOT'] . '/logger.log');
});

/***************************************************************************************************************
* ACL контроль доступа
 * ***************************************************************************************************************/
$di->set("acl", function() {

    // Проверяем существует ли сериализованный файл
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/apps/config/data/acl.data'))
    {

        $acl = new \Phalcon\Acl\Adapter\Memory();

        require $_SERVER['DOCUMENT_ROOT'] .'/apps/config/acl.php';

        // Сохраняем сериализованный объект в файл
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/apps/config/data/acl.data', serialize($acl));

        return $acl;
    }
    else
    {
        // Восстанавливаем ACL объект из текстового файла
        $acl = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/apps/config/data/acl.data'));
        return $acl;
    }

});

/***************************************************************************************************************
 * Помощники представлений
 * ***************************************************************************************************************/

$di->set('tag', function() {
    return new \Phalcon\Tag();
});

/***************************************************************************************************************
 * Информационные сообщения:flash
 * ***************************************************************************************************************/
$di->set('flash', function(){
    $flash = new \Phalcon\Flash\Direct(array(
        'warning' => 'alert',
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
    return $flash;
});
/***************************************************************************************************************
 * Информационные сообщения:Session
 * ***************************************************************************************************************/
$di->set('flashSession', function(){
    $flash = new \Phalcon\Flash\Session(array(
        'warning' => 'alert',
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
    return $flash;
});
