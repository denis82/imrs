<?php

/**
 * Класс обработки авторизации пользователей 
 * 
 * @author Alexandr Kirshin <kirshin.as@gmail.com>
 * @author Martianov Ivan <i@ivansky.ru>
 *
 * @property User $_user
 */
class CAdminUserIdentity extends CUserIdentity {

    private $_id;
    private $_role;
	private $_user;

    /**
     * Авторизация зарегистрированого пользовтеля
     * @return boolean
     */
    public function authenticate() {
        $this->_user = User::model()->find('LOWER(username)=?', array(strtolower($this->username)));

        if ($this->_user === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if (!$this->_user->validatePassword($this->password))
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->_id = $this->_user->id;
            $this->_role = $this->_user->role;
            $this->username = $this->_user->username;
            $this->errorCode = self::ERROR_NONE;
		}

        return $this->errorCode == self::ERROR_NONE;
    }

    /**
     * Получить Ид текущего пользователя
     * @return integer
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Получить роль текущего пользователя
     * @return string
     */
    public function getRole() {
        return $this->_role;
		//return 0;
    }

}