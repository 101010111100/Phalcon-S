<?php


namespace P\Admin\Controllers;
use P\Common\Libraries\Application;
use P\Admin\Models\ManipulationUsers;
use P\Common\Models\Users;


class UsersControlController extends Application
{
    public function indexAction()
    {
        // получаем текущуую страницу
        $currentPage = (int) $this->dispatcher->getParam("page");

        // если страница не установлена передаем значение первой страницы
        if(empty($currentPage))
        {
            $currentPage = 1;
        }

        // если введены данные для поиска
        if($this->request->isPost() == true)
        {
            $username = $this->request->getPost("s_username", "string");
            $id       = $this->request->getPost("s_id", "int");

            $builder = $this->modelsManager->createBuilder()
                ->from('P\Common\Models\Users')
                ->where('id = "'.$id.'"')
                ->orWhere('username = "'.$username.'"')
                ->orderBy('id');
        }
        else
        {
            $builder = $this->modelsManager->createBuilder()
                ->from('P\Common\Models\Users')
                ->orderBy('id');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit"=> 15,
            "page" => $currentPage
        ));


        $page = $paginator->getPaginate();

        $this->view->setVar("page",$page);


    }

    public function deleteAction()
    {
        $user_id     = (int) $this->dispatcher->getParam("user_id");
        $page_number = (int) $this->dispatcher->getParam("page_number");


        $mu = new ManipulationUsers();



        if( $mu->deleteUser($user_id) )
        {
            $this->flashSession->success($this->_getTranslation()->_("User Deleted"));
            $this->response->redirect("admin/uc/".$page_number);
        }
        else
        {
            $this->flashSession->error($this->_getTranslation()->_("Unable to delete user"));
            $this->response->redirect("admin/uc/".$page_number);
        }

    }

    public function editAction()
    {

        $id_from_url = (int) $this->dispatcher->getParam("user_id");
        $page_number =  $this->dispatcher->getParam("page_number");

        // получаем список ролей
        $roles = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/apps/config/xml/axml/roles.xml');

        $this->view->setVar("roles", $roles);
        $this->view->setVar("page_id", $page_number);
        $this->view->setVar("user", Users::findFirst("id='$id_from_url'"));

        if($this->request->isPost() == true)
        {

            $user_id   = (int) $this->request->getPost("id");

            $user = Users::findFirst("id='$user_id'");

            $firstname = strlen( $this->request->getPost("firstname",'trim') ) == 0 ? $user->firstname : $this->request->getPost("firstname",'trim');
            $lastname  = strlen( $this->request->getPost("lastname",'trim') )  == 0 ? $user->lastname  : $this->request->getPost("lastname",'trim');
            $email     = strlen( $this->request->getPost("email",'trim') )     == 0 ? $user->email     : $this->request->getPost("email",'trim');
            $birthday  = strlen( $this->request->getPost("birthday",'trim') )  == 0 ? $user->birthday  : $this->request->getPost("birthday",'trim');
            $sex       = strlen( $this->request->getPost("sex",'trim') )       == 0 ? $user->sex       : $this->request->getPost("sex",'trim');
            $username  = strlen( $this->request->getPost("username",'trim') )  == 0 ? $user->username  : $this->request->getPost("username",'trim');
            $role      = strlen( $this->request->getPost("role",'trim') )      == 0 ? $user->username  : $this->request->getPost("role",'trim');

            $mu = new ManipulationUsers();
            if( $mu->updateUser($user_id, $firstname, $lastname, $email, $birthday, $sex, $username, $role) )
            {
                $this->response->redirect("admin/uc/edit/".$user_id."/".$page_number);
            }
        }

    }


}