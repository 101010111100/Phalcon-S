<?php


namespace P\Admin\models;
use P\Common\Models\Users;

class ManipulationUsers extends \Phalcon\Mvc\Model
{

    public function deleteUser($id)
    {

        if($Users = Users::find("id='$id'"))
        {
            if(count($Users) > 0)
            {
                return $Users->delete();
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }



    }

    public function updateUser($id, $firstname = null, $lastname = null, $email = null, $birthday = null, $sex = null, $username = null, $role = null)
    {
        if($user = Users::findFirst("id='$id'"))
        {
            if(count($user) > 0)
            {
                $user->firstname = $firstname;
                $user->lastname  = $lastname;
                $user->email     = $email;
                $user->birthday  = $birthday;
                $user->sex       = $sex;
                $user->username  = $username;
                $user->role      = $role;
                if($user->save())
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }



}