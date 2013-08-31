<?php

namespace P\Admin\Controllers;
use P\Common\Libraries\Application,
    P\Common\Libraries\Service;

class ModuleManagerController extends Application
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

        $builder = $this->modelsManager->createBuilder()
             ->from('P\Common\Models\Modules')
             ->orderBy('id');

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit"=> 15,
            "page" => $currentPage
        ));


        $page = $paginator->getPaginate();

        $this->view->setVar("page", $page);

    }

    public function addAction()
    {
        if($this->request->isPost() == true)
        {
            // если файл загружен
            if ($this->request->hasFiles() == true)
            {
                // получаем имя файла
                $file_name = $_FILES["file"]["name"];
                // получаем расширение
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                // если расширение верно работаем дальше
                if($file_extension === 'zip')
                {

                    $zip = new \ZipArchive;

                    //открываем zip архив
                    if ($zip->open($_FILES["file"]["tmp_name"]) === TRUE)
                    {
                        /////////////////////////// 1 step //////////////////////////////////////////

                        //получаем название первой папки в архиве для использования в пути, пример: 'module/'
                        $stat = $zip->statIndex(0);
                        $module_name = $stat['name'];

                        if(!is_dir($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/' . $module_name))
                        {
                            //извлекаем модуль
                            $zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/');
                            //закрываем архив
                            $zip->close();

                            /////////////////////////// 2 step //////////////////////////////////////////

                            $s = new Service();

                            if($s->check_resource($module_name))
                            {
                                //создаем таблицы в бд
                                $s->create_tb_tables($module_name);
                                // регистрируем модуль
                                $s->registration_module($module_name);

                                // регистрируем роутинг
                                $s->registration_routing($module_name);

                                // регистрируем ресурсы
                                $s->registration_resources($module_name);

                                // добавляем права доступа
                                $s->registration_allows($module_name);

                                // добавляем пункт меню
                                $s->registration_menus($module_name);

                                $s->remove_acl_cache();
                                // добавляем раширения
                                $s->add_extensions($module_name);
                            }
                            else
                            {
                                // проблема с ресурсами
                                $this->flashSession->error($this->_getTranslation()->_("res_av_error"));
                                $s->removeDirectory($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/' . $module_name);
                            }


                        }
                        else
                        {
                            // дериктория с таким названием уже создана
                            $this->flashSession->error($this->_getTranslation()->_("Проблема не совместимости модулей"));

                        }

                    }
                    else
                    {
                        // не удалось открыть архив
                        $this->flashSession->error($this->_getTranslation()->_("Unable to open file"));

                    }

                }
                else
                {
                    // неверное расширение
                    $this->flashSession->error($this->_getTranslation()->_("Invalid file extension"));
                }

            }
            else
            {
                // файл не загружен
                $this->flashSession->error($this->_getTranslation()->_("File not loaded"));
            }
        }

    }


    public function deleteAction()
    {

        $path = (string) $this->dispatcher->getParam("path").'/';

        $s = new Service();
        $s->drop_tb_tables($path);
        $s->unregistration_module($path);
        $s->unregistration_routing($path);
        $s->unregistration_resources($path);
        $s->unregistration_allows($path);
        $s->unregistration_menus($path);
        $s->removeDirectory($_SERVER['DOCUMENT_ROOT'] . '/apps/modules/' . $path);
        $s->delete_extensions($path);
        $s->remove_acl_cache();

        $this->flashSession->success($this->_getTranslation()->_("Module Deleted"));
        $this->response->redirect("admin/modulemanager");

    }

}