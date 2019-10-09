<?php

namespace App\Plugins;

class UserPlugin
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';
    const STATUS_PENDING_VERIFICATION = 'PendingVerification';

    public static $tableGroup = 'snv_users_group';
    public static $tableGroupSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("Title", "STRING"),
        array("Description", "TEXT"),
        array("CreatedAt", "DATETIME"),
        array("UpdatedAt", "DATETIME"),
    );
    public static $tableRole = 'snv_users_role';
    public static $tableRoleSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("Title", "STRING"),
        array("Description", "TEXT"),
        array("CreatedAt", "DATETIME"),
        array("UpdatedAt", "DATETIME"),
    );
    public static $tableUser = 'snv_users_user';
    public static $tableUserSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("LibreId", "STRING"),
        array("FirstName", "STRING"),
        array("MiddleName", "STRING"),
        array("LastName", "STRING"),
        array("Email", "STRING"),
        array("Birthday", "STRING"),
        array("Country", "STRING"),
        array("Gender", "STRING"),
        array("Role", "STRING"),
        array("Password", "STRING"),
        array("IsEmailConfirmed", "STRING"),
        array("EmailConfirmedAt", "DATETIME"),
        array("LastLoginAt", "DATETIME"),
        array("CreatedAt", "DATETIME"),
        array("UpdatedAt", "DATETIME"),
    );
    public static $tableUserRole = 'snv_users_user_role';
    public static $tableUserRoleSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("UserId", "STRING"),
        array("RoleId", "STRING"),
        array("CreatedAt", "DATETIME"),
        array("UpdatedAt", "DATETIME"),
    );
    public static $table_userfiles = 'users_userfiles';
    public static $table_usergroups = 'users_usergroups';
    public static $tableUsergroupSchema = array(
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("Title", "STRING"),
        array("Description", "STRING"),
    );
    public static $table_usergroups_users = 'users_usergroups_users';
    public static $table_userroles = 'users_userroles';
    public static $table_userstatuses = 'users_userstatuses';

    public static function createTables()
    {
        if (self::getTableUser()->exists() == false) {
            self::getTableUser()->create(self::$tableUserSchema);
        }
    }

    public static function getTableRole()
    {
        return self::getDatabase()->table(self::$tableUser);
    }

    public static function getTableUser()
    {
        return self::getDatabase()->table(self::$tableUser);
    }

    public static function getTableUserRole()
    {
        return self::getDatabase()->table(self::$tableUserRole);
    }

    public static function getRoleById($id)
    {
        $result = self::getDatabase()->table(self::$table_userroles)->select();
        return $result;
    }

    public static function getUserRoles($userId)
    {
        //self::getDatabase()->debug = true;
        $result = self::getTableUserRole()->join(self::$tableRole, 'RoleId', 'Id')->where('UserId', '=', $userId)->select();
        //var_dump($result);
        return $result;
    }

    public static function getUserStatuses()
    {
        $result = self::getDatabase()->table(self::$table_userstatuses)->select();
        return $result;
    }

    public static function createUserfile($data)
    {
        $result = self::getDatabase()->table(self::$table_userfiles)->insert($data);
        if ($result !== false) {
            return self::getDatabase()->lastInsertId();
        }
        return false;
    }

    public static function deleteUserById($id)
    {
        $result = self::getDatabase()->table(self::$tableUser)->where('id', '==', $id)->delete();
        return $result;
    }

    public static function deleteUserfileById($id)
    {
        $result = self::getDatabase()->table(self::$table_userfiles)->where('id', '==', $id)->delete();
        return $result;
    }

    public static function deleteUsergroupById($id)
    {
        $result = self::getDatabase()->table(self::$table_usergroups)->where('id', '==', $id)->delete();
        return $result;
    }

    /**
     * @return \Sinevia\SqlDb
     */
    public static function getDatabase()
    {
        return db();
    }

    public static function getUsergroupIdsByUserId($user_id)
    {
        $result = self::getDatabase()->table(self::$table_usergroups_users)->where('user_id', '==', $user_id)->select();
        return $result;
    }

    public static function getUsersByUsergroupId($usergroup_id, $options = array())
    {
        $limit_from = isset($options['limit_from']) ? trim($options['limit_from']) : '0';
        $limit_to = isset($options['limit_to']) ? trim($options['limit_to']) : '1000';
        $orderby = isset($options['orderby']) ? trim($options['orderby']) : 'id';
        $sort = isset($options['sort']) ? trim($options['sort']) : 'desc';
        //$group_id = isset($options['group_id']) ? trim($options['group_id']) : '';
        $append_count = isset($options['append_count']) ? $options['append_count'] : false;
        $db = self::getDatabase();

        $sql = "SELECT SQL_CALC_FOUND_ROWS ";

        //$sql .= "usergroups_users_map.user_id, usergroups_users_map.group_id, users.* FROM usergroups_users_map JOIN users";
        //$sql .= " ON users.id=usergroups_users_map.user_id";
        $sql .= " * FROM " . self::$tableUser;
        $sql .= " WHERE 1";
        $sql .= " AND id IN (SELECT user_id FROM " . self::$table_usergroups_users . " WHERE usergroup_id='" . $usergroup_id . "')";
        //        if ($group_id != '') {
        //            $sql .= " AND group_id=" . $group_id;
        //        }
        $sql .= " ORDER BY " . $orderby . " " . $sort;
        $sql .= " LIMIT " . $limit_from . "," . $limit_to . ";";

        $results = $db->executeQuery($sql);
        //echo $sql;
        // Get total rows count
        if ($append_count == true) {
            $sql = 'SELECT FOUND_ROWS();';
            $result = $db->executeQuery($sql);
            $total = $result[0]['FOUND_ROWS()'];
            $results[] = $total;
        }

        return $results;
    }

    /**
     * Finds a user by his email
     * @param string $libreid
     * @return \Member|null
     */
    public static function getUserByEmail($email)
    {
        $result = self::getDatabase()->table(self::$tableUser)->where('email', '==', $email)->limit(1)->select();

        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }

    public static function generatePasswordHash($password, $password_salt)
    {
        $hash = sha1($password_salt . $password . $password_salt);
        return $hash;
    }

    /**
     * Finds a user by his email and password
     * @param string $libreid
     * @return array|null
     */
    public static function getUserByEmailAndPassword($email, $password)
    {
        $user = self::getTableUser()->where('email', '==', $email)->limit(1)->selectOne();

        if ($user != null) {
            $isEqual = Sinevia\AuthUtils::equals($password, $user['Password']);
            if ($isEqual == true) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Creates a new user group
     * @param Member $user_group
     * @return boolean
     * @throws RuntimeException
     */
    public static function createUsergroup($data)
    {
        if (is_array($data) == false) {
            throw new RuntimeException('Calling createMember with non-Array parameter');
        }
        $result = self::getDatabase()->table(self::$table_usergroups)->insert($data);
        if ($result !== false) {
            //return true;
            return self::getDatabase()->lastInsertId();
        }
        return false;
    }

    public static function addUserToUsergroup($usergroup_id, $user_id)
    {
        $record = self::getDatabase()->table(self::$table_usergroups_users)
            ->where('usergroup_id', '==', $usergroup_id)
            ->where('user_id', '==', $user_id)
            ->selectOne();
        if ($record !== null) {
            return true;
        }

        $result = self::getDatabase()->table(self::$table_usergroups_users)->insert(array(
            'user_id' => $user_id,
            'usergroup_id' => $usergroup_id,
        ));

        if ($result !== false) {
            return self::getDatabase()->lastInsertId();
        }

        return false;
    }

    public static function removeUserFromUsergroup($usergroup_id, $user_id)
    {
        $result = self::getDatabase()->table(self::$table_usergroups_users)
            ->where('usergroup_id', '==', $usergroup_id)
            ->where('user_id', '==', $user_id)
            ->delete();

        if ($result !== false) {
            return true;
        }

        return false;
    }

    /**
     * Creates a new user
     * @param array $data
     * @return mixed the created user or null on failure
     * @throws RuntimeException
     */
    public static function createUser($data)
    {
        if (is_array($data) == false) {
            throw new RuntimeException('Calling createMember with non-Array parameter');
        }
        if (isset($data['Id']) == false) {
            $data['Id'] = \Sinevia\Uid::microUid();
        }
        if (isset($data['CreatedAt']) == false) {
            $data['CreatedAt'] = date('Y-m-d H:i:s');
        }
        if (isset($data['UpdatedAt']) == false) {
            $data['UpdatedAt'] = date('Y-m-d H:i:s');
        }
        $result = self::getDatabase()->table(self::$tableUser)->insert($data);
        if ($result == false) {
            return null;
        }
        return self::getUserById($data['Id']);
    }

    /**
     * Creates a new user using his LibreId credentials and data
     * @param Member $user
     * @return boolean
     * @throws RuntimeException
     */
    public static function createUserUsingLibreIdAndData($libre_id, $libre_data)
    {
        $password = \Sinevia\Utils::stringRandom(8, 'BCDGHJKLMN123456789bcdghj');
        $pass_salt = \Sinevia\Utils::stringRandom(32);
        $pass_hash = UsersModel::generatePasswordHash($password, $pass_salt);

        $user = array();
        $user['libreid'] = $libre_id;
        $user['first_name'] = $libre_data['first_name'];
        $user['last_name'] = $libre_data['last_name'];
        $user['email'] = $libre_data['emails'][0]['email'];
        $user['country'] = $libre_data['country'];
        $user['time_registered'] = date('Y-m-d H:i:s', time());
        $user['time_last_login'] = date('Y-m-d H:i:s', time());
        $user['status'] = 'Active';
        $user['level'] = 'Basic';
        $user['pass_hash'] = $pass_hash;
        $user['pass_salt'] = $pass_salt;

        return self::createUser($user);
    }

    /**
     * Returns a user by looking through the emails in the data returned by LibreId
     * It will also update the user's LibreId value in the database
     * @param array $libre_data
     * @return null|array
     */
    public static function getUserUsingLibreData($libre_data)
    {
        $emails = $libre_data['emails'];
        foreach ($emails as $email) {
            $user = self::getUserByEmail($email['email']);
            if ($user != null) {
                //                $user_update = array();
                //                $user_update['libreid'] = $libreid;
                //                self::updateMemberById($user['id'], $user_update);
                return $user;
            }
        }
        return null;
    }

    /**
     * Finds a user by his Member ID
     * @param string $libreid
     * @return array|null
     */
    public static function getUserById($id)
    {
        if (is_string($id) == false || is_numeric($id) == false) {
            throw new \RuntimeException('Non-string or non-numeric parameter');
        }
        $result = self::getDatabase()->table(self::$tableUser)->where('Id', '==', $id)->select();

        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * Finds a user by his Member LibreId
     * @param string $libreid
     * @return array|null
     */
    public static function getUserByLibreId($libreid)
    {
        $result = self::getDatabase()->table(self::$tableUser)->where('libreid', '==', $libreid)->select();

        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }

        return null;
    }

    public static function getUserfileById($id)
    {
        $result = self::getDatabase()->table(self::$table_userfiles)->where('id', '==', $id)->selectOne();
        return $result;
    }

    public static function getUserfilesByUserId($user_id)
    {
        $result = self::getDatabase()
            ->table(self::$table_userfiles)
            ->where('user_id', '==', $user_id)
            ->orderBy('did', 'asc')
            ->select();
        return $result;
    }

    public static function getUsers($options = array())
    {
        $limit_from = isset($options['limit_from']) ? trim($options['limit_from']) : '0';
        $limit_to = isset($options['limit_to']) ? trim($options['limit_to']) : '1000';
        $orderby = isset($options['orderby']) ? trim($options['orderby']) : 'Id';
        $sort = isset($options['sort']) ? trim($options['sort']) : 'desc';
        $status = isset($options['status']) ? trim($options['status']) : '';
        $not_in_usergroup_id = isset($options['not_in_usergroup_id']) ? trim($options['not_in_usergroup_id']) : '';
        $name = isset($options['Name']) ? trim($options['Name']) : '';
        $uniqueId = isset($options['Id']) ? trim($options['Id']) : '';
        $append_count = isset($options['append_count']) ? $options['append_count'] : false;

        $db = self::getDatabase();

        $sql = "SELECT SQL_CALC_FOUND_ROWS *";
        $sql .= " FROM " . self::$tableUser;
        $sql .= " WHERE 1";

        if ($uniqueId != '') {
            $sql .= " AND Id=" . $db->quote($id) . "";
        }

        if ($not_in_usergroup_id != '') {
            $sql .= " AND Id NOT IN (SELECT user_id FROM " . self::$table_usergroups_users . " WHERE usergroup_id=" . $db->quote($not_in_usergroup_id) . ")";
        }

        if ($name != '') {
            $names = stripos($name, ',') == false ? $name : explode(',', $name);
            if (is_array($names) == false) {
                $names = stripos($name, ' ') == false ? $name : explode(' ', $name);
            }
            if (is_array($names) == false) {
                $names = array($names);
            }
            $sql_names = array();
            foreach ($names as $name) {
                $sql_names[] = "(FirstName LIKE " . $db->quote('%' . $name . '%') . " OR LastName LIKE " . $db->quote('%' . $name . '%') . ")";
            }
            $sql .= " AND (" . implode(" OR ", $sql_names) . ")";
        }

        if ($status != '') {
            if ($status == 'not_deleted') {
                $sql .= " AND Status<>'Deleted'";
            } else {
                $sql .= " AND Status=" . $db->quote($status) . "";
            }
        }

        $sql .= " ORDER BY " . $orderby . " " . $sort;
        $sql .= " LIMIT " . $limit_from . "," . $limit_to . ";";

        self::log($sql);
        //echo $sql;
        $results = $db->executeQuery($sql);

        // Get total rows count
        if ($append_count == true) {
            $sql = 'SELECT FOUND_ROWS();';
            $result = $db->executeQuery($sql);
            $total = $result[0]['FOUND_ROWS()'];
            $results[] = $total;
        }

        return $results;
    }

    public static function getUsergroups($options = array())
    {
        $limit_from = isset($options['limit_from']) ? trim($options['limit_from']) : '0';
        $limit_to = isset($options['limit_to']) ? trim($options['limit_to']) : '10';
        $orderby = isset($options['orderby']) ? trim($options['orderby']) : 'id';
        $sort = isset($options['sort']) ? trim($options['sort']) : 'desc';
        $status = isset($options['status']) ? trim($options['status']) : '';
        $append_count = isset($options['append_count']) ? $options['append_count'] : false;

        $db = self::getDatabase();

        $sql = "SELECT SQL_CALC_FOUND_ROWS *";
        $sql .= " FROM " . self::$table_usergroups;
        $sql .= " WHERE 1";

        if ($status != '') {
            if ($status == 'not_deleted') {
                $sql .= " AND status<>'Deleted'";
            } else {
                $sql .= " AND status=" . $db->quote($status) . "";
            }
        }

        $sql .= " ORDER BY " . $orderby . " " . $sort;
        $sql .= " LIMIT " . $limit_from . "," . $limit_to . ";";

        self::log($sql);

        $results = $db->executeQuery($sql);

        // Get total rows count
        if ($append_count == true) {
            $sql = 'SELECT FOUND_ROWS();';
            $result = $db->executeQuery($sql);
            $total = $result[0]['FOUND_ROWS()'];
            $results[] = $total;
        }

        return $results;
    }

    public static function getUsergroupById($id)
    {
        $result = self::getDatabase()->table(self::$table_usergroups)->where('id', '==', $id)->selectOne();
        return $result;
    }

    /**
     * Updates a user
     * @param Member $user
     * @return boolean
     * @throws RuntimeException
     */
    public static function updateUserById($id, $data)
    {
        $result = self::getDatabase()->table(self::$tableUser)->where('id', '==', $id)->update($data);
        return $result;
    }

    public static function updateUserfileById($id, $data)
    {
        $result = self::getDatabase()->table(self::$table_userfiles)->where('id', '==', $id)->update($data);
        return $result;
    }

    public static function updateUsergroupsById($id, $data)
    {
        $result = self::getDatabase()->table(self::$table_usergroups)->where('id', '==', $id)->update($data);
        return $result;
    }
}