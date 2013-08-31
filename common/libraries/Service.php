<?php


namespace P\Common\Libraries;
use P\Common\Models\Modules;

class Service extends \Phalcon\Mvc\Controller
{

    public function create_tb_tables($module_name)
    {
        if(is_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/schema.sql'))
        {
            $from_modules_sql = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/schema.sql');

            $this->db->query($from_modules_sql);
        }

    }

    public function registration_module($module_name)
    {
        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/modules.xml');
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml');
        $modules = $xml->addChild( $from_modules_xml->module['name'] );
        $modules->addChild('className', $from_modules_xml->module['className']);
        $modules->addChild('path', $from_modules_xml->module['path']);
        $this->formatting_and_save_xml($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml', $xml);
        // add to db
        $from_info_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/info.xml');

        $modules = new Modules();
        $modules->name        = (string) $from_info_xml->info['name'];
        $modules->path        = (string) $module_name;
        $modules->description = (string) $from_info_xml->info['description'];
        $modules->author      = (string) $from_info_xml->info['author'];
        $modules->version     = (string) $from_info_xml->info['version'];
        $modules->save();

    }

    public function registration_routing($module_name)
    {
        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/routes.xml');
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml');

        foreach ($from_modules_xml->route as $r)
        {
            $route = $xml->addChild('route');
            $route->addAttribute('name', $r['name']);
            $route->addAttribute('url', $r['url']);
            $route->addAttribute('module', $r['module']);
            $route->addAttribute('controller', $r['controller']);
            $route->addAttribute('action', $r['action']);
            $this->formatting_and_save_xml($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml', $xml);
        }

    }

    public function registration_resources($module_name)
    {
        $from_resources_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/resources.xml');
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml');

        foreach ($from_resources_xml->resource as $resource)
        {
            $res = $xml->addChild('resource');
            $res->addAttribute('module', $resource['module']);
            $res->addAttribute('controller', $resource['controller']);
            $this->formatting_and_save_xml($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml', $xml);
        }

    }

    public function registration_allows($module_name)
    {
        $from_allow_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/allow.xml');
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml');

        foreach ($from_allow_xml->allow as $a)
        {
            $allow = $xml->addChild('allow');
            $allow->addAttribute('role', $a['role']);
            $allow->addAttribute('module', $a['module']);
            $allow->addAttribute('controller', $a['controller']);
            $this->formatting_and_save_xml($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml', $xml);
        }

    }

    public function registration_menus($module_name)
    {
        $from_menus_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/admin_templates_left_menu.xml');
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/admin_templates_left_menu.xml');

        foreach ($from_menus_xml->menu as $m)
        {
            $menus = $xml->addChild('menu');
            $menus->addAttribute('href', $m['href']);
            $menus->addAttribute('link_name', $m['link_name']);
            $menus->addAttribute('mpath', $module_name);
            $this->formatting_and_save_xml($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/admin_templates_left_menu.xml', $xml);
        }

    }

    public function remove_acl_cache()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/apps/config/data/acl.data'))
        {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/apps/config/data/acl.data');
        }
    }


    public function formatting_and_save_xml($path, $xml)
    {
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml->asXML());
        if( $domxml->save($path) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function add_extensions($module_name)
    {
        if(is_dir( $_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'ext/pnews/' ))
        {
            $this->recurse_copy($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'ext/',
                $_SERVER['DOCUMENT_ROOT'] . '/public/ext/');
        }
    }

    public function delete_extensions($path)
    {
        if(is_dir( $_SERVER['DOCUMENT_ROOT'] . '/public/ext/' . $path ))
        {
            $this->removeDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/ext/' . $path);
        }
    }
    // recurse_copy ///////////////////////////////////////////////
    private function recurse_copy($src,$dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    if(is_dir($dst . '/' . $file))
                    {
                        chmod($dst . '/' . $file, 0777);
                    }
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file );
                }
            }
        }
        closedir($dir);
    }
    // recurse_copy ///////////////////////////////////////////////

    public function removeDirectory($dir)
    {
        if ($objs = glob($dir."/*"))
        {
            foreach($objs as $obj)
            {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function check_resource($module_name)
    {

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/modules.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/modules.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml') )
        {
            $this->logger->log('/apps/config/xml/modules.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/info.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/info.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/routes.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/routes.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml') )
        {
            $this->logger->log('/apps/config/xml/routes.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/resources.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/resources.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml') )
        {
            $this->logger->log('/apps/config/xml/axml/resources.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/allow.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/allow.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml') )
        {
            $this->logger->log('/apps/config/xml/axml/allow.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $module_name .'config/xml/admin_templates_left_menu.xml') )
        {
            $this->logger->log('/apps/modules/'. $module_name .'config/xml/admin_templates_left_menu.xml not available');
            return false;
        }

        if( !is_writable($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/admin_templates_left_menu.xml') )
        {
            $this->logger->log('/apps/config/xml/admin_templates_left_menu.xml not available');
            return false;
        }

        return true;

    }

    //----------------------------------------------------------------------------------------------------------------//

    public function drop_tb_tables($path)
    {
        if(is_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/schema_drop.sql'))
        {
            $from_modules_sql = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/schema_drop.sql');

            $this->db->query($from_modules_sql);
        }

    }


    public function unregistration_module($path)
    {

        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/xml/modules.xml');
        // получаем название удаляемого модуля
        $module_name = $from_modules_xml->module['name'];
        // загружаем файл модулей системы
        $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml');
        // ищем элемент по названию
        $result = $xml->xpath($module_name);

        foreach($result as $res)
        {

            $s1 = (string) $res->className;
            $s2 = (string) $from_modules_xml->module['className'];
            // если названия классов элемента по поиску и $from_modules_xml совпадают
            if($s1 == $s2)
            {
                // удаляем
                $dom = dom_import_simplexml($res);
                $dom->parentNode->removeChild($dom);
            }

        }
        $xml->asXML ( $_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/modules.xml' );


        $module = Modules::find("path='$path'");
        $module->delete();

    }

    public function unregistration_routing($path)
    {
        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/xml/routes.xml');
        // получаем название удаляемого роутинга
        $route_name = (string) $from_modules_xml->route['name'];

        // загружаем файл роутинга системы
        $dom = new \DOMDocument('1.0');
        $dom->load($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml');


        foreach (iterator_to_array( $dom->getElementsByTagName('route') ) as $route)
        {
            if($route->getAttribute('name') == $route_name)
            {
                $route->parentNode->removeChild($route);

            }
        }

        $dom->save($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/routes.xml' );

    }

    public function unregistration_resources($path)
    {

        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/xml/resources.xml');
        // получаем название удаляемых ресерсов
        $resources_name = (string) $from_modules_xml->resource['module'];

        // загружаем файл ресерсов системы
        $dom = new \DOMDocument('1.0');
        $dom->load($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml');

        foreach (iterator_to_array( $dom->getElementsByTagName('resource') ) as $route)
        {
            if($route->getAttribute('module') == $resources_name)
            {
                $route->parentNode->removeChild($route);
            }
        }

        $dom->save($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/resources.xml' );

    }

    public function unregistration_allows($path)
    {
        $from_modules_xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/'. $path .'config/xml/allow.xml');
        // получаем название удаляемых allows
        $allow_name = (string) $from_modules_xml->allow['module'];

        // загружаем файл allows системы
        $dom = new \DOMDocument('1.0');
        $dom->load($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml');

        foreach (iterator_to_array( $dom->getElementsByTagName('allow') ) as $route)
        {
            if($route->getAttribute('module') == $allow_name)
            {
                $route->parentNode->removeChild($route);
            }
        }

        $dom->save($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/allow.xml' );


    }

    public function unregistration_menus($path)
    {

        // загружаем файл меню системы
        $dom = new \DOMDocument('1.0');
        $dom->load($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/admin_templates_left_menu.xml');

        foreach (iterator_to_array( $dom->getElementsByTagName('menu') ) as $route)
        {
            if($route->getAttribute('mpath') == $path)
            {
                $route->parentNode->removeChild($route);
            }
        }

        $dom->save($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/admin_templates_left_menu.xml' );


    }




}