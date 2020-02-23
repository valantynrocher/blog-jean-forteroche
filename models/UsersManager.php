<?php

class UsersManager extends Manager
{
    private $userObject = 'User';


    /* =================================================================================================================================
        REQUESTS SETTERS
    ================================================================================================================================= */

    protected function selectAllUsers($userTable, $obj)
    {
        $this->getBdd();
        $var = [];
        $req = self::$bdd->query(
            "SELECT user_first_name, user_last_name, user_login, user_email, user_role,
            DATE_FORMAT(user_creation_date, '%d/%m/%Y') AS user_creation_date_fr
            FROM $userTable 
            ORDER BY user_creation_date_fr 
            ASC"
        );

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = new $obj($data);
        }
        return $var;
        $req->closeCursor();
    }

    protected function selectAdminUsers($userTable, $obj)
    {
        $this->getBdd();
        $req = self::$bdd->query(
            "SELECT user_id, user_first_name, user_last_name, user_login, user_password, user_email, user_role,
            DATE_FORMAT(user_creation_date, '%d/%m/%Y') AS user_creation_date_fr
            FROM $userTable
            WHERE user_role = 'admin'
            ORDER BY user_creation_date_fr 
            ASC"
        );

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = new $obj($data);
        }
        return $var;
        $req->closeCursor();
    }

    protected function selectReaderUsers($userTable, $obj)
    {
        $this->getBdd();
        $req = self::$bdd->query(
            "SELECT user_id, user_first_name, user_last_name, user_login, user_email, user_role,
            DATE_FORMAT(user_creation_date, '%d/%m/%Y') AS user_creation_date_fr
            FROM $userTable
            WHERE user_role = 'reader'
            ORDER BY user_creation_date_fr 
            ASC"
        );

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = new $obj($data);
        }
        return $var;
        $req->closeCursor();
    }

    protected function insertNewUser($userTable, $userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole)
    {
        $this->getBdd();
        $req = self::$bdd->prepare(
            "INSERT INTO $userTable(user_first_name, user_last_name, user_login, user_password, user_email, user_role, user_creation_date)
            VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $affectedUser = $req->execute(array(
            $userFirstName,
            $userLastName,
            $userLogin,
            $userPassword,
            $userEmail,
            $userRole
        ));
        return $affectedUser;
        $req->closeCursor();
    }

    protected function selectOneUser($userTable, $obj, $userId)
    {
        $this->getBdd();
        $var = [];
        $req = self::$bdd->prepare(
            "SELECT user_id, user_first_name, user_last_name, user_login, user_password, user_email, user_role,
            DATE_FORMAT(user_creation_date, '%d/%m/%Y') AS user_creation_date_fr
            FROM $userTable
            WHERE user_id = ?"
        );
        $req->execute(array(
            $userId
        ));

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $var[] = new $obj($data);
        }

        return $var;
        $req->closeCursor();
    }

    protected function updateChangedUser($userTable, $userId, $userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole)
    {
        $this->getBdd();
        $req = self::$bdd->prepare(
            "UPDATE $userTable
            SET user_first_name = :new_user_first_name, user_last_name = :new_user_last_name,
            user_login = :new_user_login, user_password = :new_user_password,
            user_email = :new_user_email, user_role = :new_user_role
            WHERE user_id = $userId"
        );
        $affectedUser = $req->execute(array(
            'new_user_first_name' => $userFirstName,
            'new_user_last_name' => $userLastName,
            'new_user_login' => $userLogin,
            'new_user_password' => $userPassword,
            'new_user_email' => $userEmail,
            'new_user_role' => $userRole
        ));

        return $affectedUser;
    }

    protected function deleteUserDeleted($userTable, $userId)
    {
        $this->getBdd();
        $req = self::$bdd->prepare(
            "DELETE FROM $userTable
            WHERE user_id = ?"
        );
        $deletedUser = $req->execute(array(
            $userId
        ));

        return $deletedUser;
    }

    /* =================================================================================================================================
        REQUESTS GETTERS
    ================================================================================================================================= */
    
    public function getAuthUser($userLogin, $userHashPassword)
    {
        return $this->selectAuthUser($this->userTable, $this->userObject, $userLogin, $userHashPassword);
    }

    public function getAllUsers()
    {
        return $this->selectAllUsers($this->userTable, $this->userObject);
    }
    
    public function getAdminUsers()
    {
        return $this->selectAdminUsers($this->userTable, $this->userObject);
    }

    public function getReaderUsers()
    {
        return $this->selectReaderUsers($this->userTable, $this->userObject);
    }

    public function setNewUser($userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole)
    {
        return $this->insertNewUser($this->userTable, $userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole);
    }

    public function getOneUser($userId)
    {
        return $this->selectOneUser($this->userTable, $this->userObject, $userId);
    }

    public function setChangedUser($userId, $userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole)
    {
        return $this->updateChangedUser($this->userTable, $userId, $userFirstName, $userLastName, $userLogin, $userPassword, $userEmail, $userRole);
    }

    public function setUserDeleted($userId)
    {
        return $this->deleteUserDeleted($this->userTable, $userId);
    }
}