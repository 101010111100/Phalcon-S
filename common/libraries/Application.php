<?php
namespace P\Common\Libraries;
use P\Common\Libraries\Authorization;

class Application extends \Phalcon\Mvc\Controller
{
    public $user_data;
    public $user_role;

    // метод выполняемый до запуска контроллера
    public function beforeExecuteRoute()
    {

        // автологин
        if($this->cookies->has('RMU'))
        {
            if(!$this->session->has("user_data"))
            {
                $auth = new Authorization();
                $auth->authorizeWithRememberMe();
            }
        }

        //определение роли для пользователя
        if(!$this->session->has("user_data"))
        {
            $this->user_role = 'Guest';
        }
        else
        {
            $this->user_data = $this->session->get("user_data");
            $this->user_role = $this->user_data['role'];
        }


        $ModuleName = strtolower( $this->dispatcher->getModuleName() );
        $ControllerName = strtolower( $this->dispatcher->getControllerName() );
        //$this->logger->log($ModuleName.' '.$ControllerName);
        //$this->logger->log($this->user_role);

        if($this->acl->isAllowed($this->user_role, $ModuleName , $ControllerName ) == 0 )
        {

            $this->response->redirect("front");
            $this->response->send();

            return false;
        }




        $this->view->setVar("t", $this->_getTranslation());

    }


    protected function _getTranslation()
    {

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common/languages/'.$this->config->site->language.'.php'))
        {
            require $_SERVER['DOCUMENT_ROOT'] . '/common/languages/'.$this->config->site->language.'.php';

        }
        else
        {
            require $_SERVER['DOCUMENT_ROOT'] . '/common/languages/en.php';
        }

        return new \Phalcon\Translate\Adapter\NativeArray(array(
            "content" => $messages
        ));

    }


}