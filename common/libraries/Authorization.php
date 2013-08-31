<?php

namespace P\Common\Libraries;

use P\Common\Models\Users,
    P\Common\Models\RememberTokens;


class Authorization extends \Phalcon\Mvc\Controller
{

    public function authorize($username = null, $email = null, $password, $remember_me = false)
    {

        $user = Users::findFirst(array(
            'conditions' => 'username = ?1 OR email = ?2',
            'bind'       => array(1 => $username, 2 => $email)
        ));

        if($user)
        {
            if ($this->security->checkHash($password, $user->password))
            {
                // записываем данные пользователя в сессию
                $this->session->set('user_data', $user->toArray());

                // если нужно запомнить записываем данные
                if($remember_me)
                {
                    $this->createRememberEnviroment($user);
                }

                return true;
            }
            else
            {
                //пароли не совпали
                return false;
            }
        }
        else
        {
            // если пользователь не найден
            return false;
        }


    }

    public function createRememberEnviroment($user)
    {

        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);
        // удаляем старый Token
        $RememberTokens = RememberTokens::find("usersId='$user->id'");
        $RememberTokens->delete();

        $remember = new RememberTokens();

        $remember->usersId   = $user->id;
        $remember->token     = $token;
        $remember->userAgent = $userAgent;

        if ($remember->save() != false)
        {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }

    }


    public function authorizeWithRememberMe()
    {
        // получайм id пользователя
        $userId = $this->cookies->get('RMU')->getValue();
        // получаем токен из кукисов
        $cookieToken = $this->cookies->get('RMT')->getValue();

        // находим пользователя по id из кукисов
        $user = Users::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);

            if ($cookieToken == $token) {

                $remember = RememberTokens::findFirst(array(
                    'usersId = ?0 AND token = ?1',
                    'bind' => array($user->id, $token)
                ));
                if ($remember) {

                    //Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        // запускаем сессию
                        $this->session->set('user_data', $user->toArray());

                    }
                }

            }

        }
        else
        {
            // если не удалось найти пользователя
        }

    }




}