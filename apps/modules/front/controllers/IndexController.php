<?php

namespace P\Front\Controllers;
use P\Common\Libraries\Application;
use P\Common\Libraries\Authorization;
use P\Common\Libraries\Service;

class IndexController extends Application
{

	public function indexAction()
	{



    }

    public function error404Action()
    {
        $this->logger->log($_SERVER['REQUEST_URI']);
        echo 'error404';
    }


}