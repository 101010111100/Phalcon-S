<?php

$acl = new \Phalcon\Acl\Adapter\Memory();
$acl->setDefaultAction(\Phalcon\Acl::DENY);

// Получаем роли
$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/roles.xml');


foreach ($xml->role as $role)
{
    $acl->addRole(''. $role['name'] .'');
}


// Получаем ресурсы
$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml');

foreach ($xml->resource as $resource)
{
    $acl->addResource(new \Phalcon\Acl\Resource(''. $resource['module'] .''), ''. $resource['controller'] .'');
}

// allow  // module - controller
$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml');

foreach ($xml->allow as $allow)
{
    $acl->allow(''. $allow['role'] .'',''. $allow['module'] .'',''. $allow['controller'] .'');
}
