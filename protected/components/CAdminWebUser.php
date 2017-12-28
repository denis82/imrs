<?php

/**
 * Class CAdminWebUser
 *
 * @property User $_model
 */
class CAdminWebUser extends CWebUser {
	
	protected $user_id = 0;
	protected $username;

    private $_model = null;
	public $allowAutoLogin = true;


	public function setUserId($user_id){
		$this->user_id = (int)$user_id;
	}

	public function getUserId(){
		return $this->user_id;
	}

    function getRole() {
        if ($user = $this->getModel()) {
            return $user->role;
        }
    }

    public function getModel() {
        if (!$this->isGuest && $this->_model === null) {
            $this->_model = User::model()->findByPk($this->id);
        }
        return $this->_model;
    }

	public function setUsername($username){
		$this->username = mb_strtolower(trim($username), 'UTF-8');
		$this->_model = User::model()->find(['username' => $this->username]);
	}

    public function getUsername() {
        if ($user = $this->getModel()) {
            return $user->name;
        }
		return $this->_model? $this->_model->name : false;
    }

	protected function beforeLogin($id,$states,$fromCookie)
	{
		//var_dump($id,$states,$fromCookie);
		//die();
		return true;
	}

	/**
	 * @param boolean $fromCookie whether the login is based on cookie.
	 */
	protected function afterLogin($fromCookie)
	{
	}

	/**
	 * @return boolean whether to log out the user
	 */
	protected function beforeLogout()
	{
		return true;
	}


	protected function afterLogout()
	{
	}

}

