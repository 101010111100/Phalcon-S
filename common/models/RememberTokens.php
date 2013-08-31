<?php


namespace P\Common\Models;


class RememberTokens extends \Phalcon\Mvc\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $usersId;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @var integer
     */
    public $createdAt;

    public function beforeValidationOnCreate()
    {
        $this->createdAt = time();
    }

    public function getSource()
    {
        return 'remember_tokens';
    }


}