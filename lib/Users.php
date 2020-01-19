<?php

class Users
{
    /**
     * Initiation of user session
     */
    public function initSession()
    {
        session_start();
    }

    /**
     * Checks if user authorized:
     * Function returns TRUE if user is authorized, otherwise - FALSE
     *
     * @return bool
     */
    public function isAuth()
    {
        return isset($_SESSION['auth']);
    }

    /**
     * Checks if authorized user is administrator
     * function returns TRUE if user is administrator, otherwise - FALSE
     *
     * @return bool
     */
    public function isAuthAdmin()
    {
        return isset($_SESSION['auth']) && ($_SESSION['auth']['type'] == 'administrator') ? true : false;
    }

    /**
     * Performs user authorization action
     *
     * @param $login
     * @param $password
     * @return bool
     */
    public function logIn($login, $password)
    {
        $data = DB::getInstance()->getRow(
            'SELECT * FROM `user` WHERE `email` = :login',
            [
                'login' => $login,
            ]
        );

        if (empty($data) || !password_verify($password, $data['password'])) {
            return false;
        }

        $_SESSION['auth'] = [
            'id' => $data['id'],
            'email' => $data['email'],
            'nickname' => $data['nickname'],
            'type' => $data['type']
        ];

        return true;
    }

    /**
     * Returns list\array of users
     *
     * @param $filter
     * @return array
     */
    public function getList($filter = '')
    {
        $query = 'SELECT * FROM `user` WHERE 1';

        if (is_string($filter) && (trim($filter) != '')) {
            $query .= ' AND ( (`email` LIKE :filter) OR (`nickname` LIKE :filter) OR (`type` LIKE :filter) )';
            $params['filter'] = '%' . $filter . '%';
        }

        return DB::getInstance()->getAllRows($query, $params);
    }

    /**
     * Adds a new user to DB
     *
     * @param $login
     * @param $password
     * @param $nickname
     *
     * @return bool
     */
    public function add($login, $password, $nickname)
    {
        $login = trim($login);
        $nickname = trim($nickname);

        $query = 'INSERT INTO `user` (`email`,`password`,`nickname`) VALUES (:login, :password, :nickname)';
        $params = [
            'login' => $login,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nickname' => $nickname,
        ];

        if(empty($this->errorList($login, $password, $nickname))) {
            $result = DB::getInstance()->execQuery($query, $params);
            return $result;
        }
    }

    /**
     * Returns array with error messages
     *
     * @param string $login
     * @param string $password
     * @param string $nickname
     * @return array $feedbackUserData
     */
    public function errorList($login, $password, $nickname)
    {
        $login = trim($login);
        $nickname = trim($nickname);

        $findSimilarLoginQuery = 'SELECT `email` from `user` WHERE `email` = :login';
        $findSimilarNicknameQuery = 'SELECT `nickname` from `user` WHERE `nickname` = :nickname';
        $loginParam = [
            'login' => $login,
        ];
        $nicknameParam = [
            'nickname' => $nickname,
        ];

        if (DB::getInstance()->getRow($findSimilarLoginQuery, $loginParam)) {
            $feedbackUserError['regLogin'] = 'Login already exists';
        }

        if (DB::getInstance()->getRow($findSimilarNicknameQuery, $nicknameParam)) {
            $feedbackUserError['regNickname'] = 'Nickname already exists';
        }

        if (!(strlen($login)>5 && strlen($login)<25)) {
            $feedbackUserError['regLogin'] = 'Login min. length is 3 symbols, max. 25 symbols';
        }

        if (!(strlen($nickname)>4 && strlen($nickname)<16)) {
            $feedbackUserError['regNickname'] = 'Nickname min. length is 4 symbols, max. 16 symbols';
        }

        if (!(strlen($password)>5 && strlen($password)<25)) {
            $feedbackUserError['regPassword'] = 'Password min. length is 5 symbols, max. 25 symbols';
        }

        return $feedbackUserError;
    }

    /**
     * Used as alternative error list with no login existence checking and no password existence checking
     *
     * @param string $login
     * @param string $nickname
     * @return array $ErrorData
     */
    public function altErrorList($login, $nickname)
    {
        $login = trim($login);
        $nickname = trim($nickname);

        if(!(strlen($login)>5 && strlen($login)<25)) {
            $feedbackUserError['regLogin'] = 'Login min. length is 3 symbols, max. 25 symbols';
        }

        if(!(strlen($nickname)>4 && strlen($nickname)<16)) {
            $feedbackUserError['regNickname'] = 'Nickname min. length is 4 symbols, max. 16 symbols';
        }

        return $feedbackUserError;
    }

    /**
     * Returns string with error message if $login already exist in data base
     *
     * @param $login
     * @return mixed
     */
    public function editLoginExistenceChecker($login)
    {
        $login = trim($login);

        $findSimilarLoginQuery = 'SELECT `email` from `user` WHERE `email` = :login';
        $loginParam = [
            'login' => $login,
        ];

        if(DB::getInstance()->getRow($findSimilarLoginQuery, $loginParam)) {
            $feedbackUserError['regLogin'] = 'Login already exists';
        }

        if(!(strlen($login)>5 && strlen($login)<25)) {
            $feedbackUserError['regLogin'] = 'Login min. length is 3 symbols, max. 25 symbols';
        }

        return $feedbackUserError;
    }

    /**
     * Returns string with error message if $nickname already exist in data base
     *
     * @param $nickname
     * @return mixed
     */
    public function editNicknameExistenceChecker($nickname)
    {
        $nickname = trim($nickname);

        $findSimilarNicknameQuery = 'SELECT `nickname` from `user` WHERE `nickname` = :nickname';
        $nicknameParam = [
            'nickname' => $nickname,
        ];

        if(DB::getInstance()->getRow($findSimilarNicknameQuery, $nicknameParam)) {
            $feedbackUserError['regNickname'] = 'Nickname already exists';
        }

        if(!(strlen($nickname)>4 && strlen($nickname)<16)) {
            $feedbackUserError['regNickname'] = 'Nickname min. length is 4 symbols, max. 16 symbols';
        }

        return $feedbackUserError;
    }

    /**
     * Edits user with given id
     *
     * @param int $id
     * @param string $email
     * @param string $nickname
     * @param string $type
     * @param string $validationType (full = use full account validation, none = use alternative account validation
     * login = check if login exists, nickname = check if nickname exists)
     *
     * @return bool
     */
    public function edit($id, $email, $nickname, $type, $validationType)
    {
        $email = trim($email);
        $nickname = trim($nickname);

        $query = 'UPDATE `user` SET `email`= :email,`nickname`= :nickname,`type`= :type WHERE `id`= :id;';
        $params = [
            'id' => $id,
            'email' => $email,
            'nickname' => $nickname,
            'type' => $type,
        ];

        if($validationType == 'full') {
            if(empty($this->errorList($email, '******', $nickname))) {
                $result = DB::getInstance()->execQuery($query, $params);
                return $result;
            }
        } elseif ($validationType == 'none') {
            if(empty($this->altErrorList($email, $nickname))) {
                $result = DB::getInstance()->execQuery($query, $params);
                return $result;
            }
        } elseif ($validationType == 'login') {
            if(empty($this->editLoginExistenceChecker($email))) {
                $result = DB::getInstance()->execQuery($query, $params);
                return $result;
            }
        } elseif ($validationType == 'nickname') {
            if(empty($this->editNicknameExistenceChecker($nickname))) {
                $result = DB::getInstance()->execQuery($query, $params);
                return $result;
            }
        }
    }

    /**
     * Removes user with given id
     *
     * @param $id
     * @return bool
     */
    public function remove($id)
    {
        $query = 'DELETE FROM `user` WHERE `id` = :id;';
        $params = [
            'id' => $id,
        ];

        $result = DB::getInstance()->execQuery($query, $params);

        return  $result;
    }

    /**
     * Changes user password where user is identified with $id param
     *
     * @param $id
     * @param $oldPassword
     * @param $newPassword
     * @param $repeatNewPassword
     * @return array|string
     */
    public function passwordChange($id, $oldPassword, $newPassword, $repeatNewPassword)
    {
        $getPasswordQuery = 'SELECT `password` FROM `user` WHERE `id` = :id;';
        $getPasswordParam = ['id' => $id];
        $oldPasswordFromDb = DB::getInstance()->getRow($getPasswordQuery, $getPasswordParam);

        if (password_verify($oldPassword, $oldPasswordFromDb['password'])) {
            if ($newPassword == $repeatNewPassword) {
                if (!(strlen($newPassword)>5 && strlen($newPassword)<25)) {
                    $feedbackPasswordError['newPassword'] = 'Password min. length is 5 symbols, max. 25 symbols';
                } else {
                    $feedbackPasswordError = [];
                }
            } else {
                $feedbackPasswordError['repeatNewPassword'] = 'Entered passwords dont much!';
            }
        } else {
            $feedbackPasswordError['oldPassword'] = 'Password is incorrect!';
        }

        if(empty($feedbackPasswordError)) {
            $replacePasswordQuery = 'UPDATE `user` SET `password` = :password WHERE id = :id;';
            $param = [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $id,
            ];

            $replacePasswordQueryResult = DB::getInstance()->execQuery($replacePasswordQuery, $param);
            $feedbackPasswordError = $replacePasswordQueryResult? [] : 'Some problems with DB';
        }

        return $feedbackPasswordError;
    }
}




















