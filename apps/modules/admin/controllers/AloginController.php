<?php

namespace P\Admin\Controllers;
use P\Common\Libraries\Application,
    P\Common\Libraries\Authorization,
    P\Common\Models\RememberTokens;

class AloginController extends Application
{


    public function loginAction()
    {
        if($this->request->isPost() == true)
        {
            $username = $this->request->getPost("username", "string");
            $password = $this->request->getPost("password");
            $remember_me = $this->request->getPost("remember-me", 'trim');

            $auth = new Authorization();

            $remember_me != '' ? $flag = true : $flag = false;

            if( $auth->authorize($username, null, $password, $flag) )
            {
                $this->response->redirect("admin");
            }
            else
            {
                $this->flashSession->error($this->_getTranslation()->_("Unable to log in"));
            }
        }
    }

    public function exitAction()
    {
        $this->cookies->set('RMU', '');
        $this->cookies->set('RMT', '');
        $this->session->destroy();
        $uid = $this->user_data['id'];
        $RememberTokens = RememberTokens::find("usersId='$uid'");
        $RememberTokens->delete();
        $this->response->redirect("admin/login");

    }

}