<?php
    class Database { 
        public $host = "localhost"; // to be changed if hosted on server
        public $user_name = "root";
        public $user_password = "";
        public $db_name = "authdb";
        public $connection;

        function __construct() {
            $this->connection = mysqli_connect(
                $this->host,
                 $this->user_name, 
                 $this->user_password,
                  $this->db_name, "3308"
            );
        }

        public function closeConnection() {
            mysqli_close($this->connection);
        }

        public function createUser(User $user) {
            $sql = "INSERT INTO users(id, email, username, password, description, has_image) 
            VALUES(NULL, '$user->email', '$user->username', '$user->password', '$user->description', $user->hasImage)"; // might cause DB errors here

            mysqli_query($this->connection, $sql);

            return mysqli_insert_id($this->connection);
        }

        public function setUserHasImage(int $id) {
            $sql = "UPDATE users SET has_image = 1 WHERE id = $id";
            
            $result = mysqli_query($this->connection, $sql);

            return mysqli_affected_rows($this->connection) >= 0;
        }

        public function userExistsOrPasswordTaken(string $username, string $password) { // user exists if username or password are taken
            $sql = "SELECT username FROM users WHERE username = '$username' OR password = '$password'";

            $exists_result = mysqli_query($this->connection, $sql);

            return mysqli_num_rows($exists_result) > 0; // if more than one row found => user exists
        }

        public function validateUser(string $email, string $password) {
            $sql = "SELECT id, password FROM users WHERE email = '$email'"; // get associated user by email

            $result = mysqli_query($this->connection, $sql);

            if(mysqli_num_rows($result) > 0 && ($rows = mysqli_fetch_all($result, MYSQLI_ASSOC))) {
                    
                $filteredRows = array_filter($rows, function (array $row) use ($password) {
                    return password_verify($password, $row['password']);
                });

                if(count($filteredRows) && $row = $filteredRows[0]) {
                    return $row['id'];
                } // if more than one row found AND the passwords match => auth'd user => return username for token
            }

            return null; // else return nothing and mark user as unauth'd
        }

        public function getUser(int $id) { // user already auth'd at this point due to token => get user by username
            $sql = "SELECT description, username, email, has_image FROM users WHERE id = $id";

            $result = mysqli_query($this->connection, $sql);
        
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result); // fetch the resulting rows in the form of a map (associative array)
                
                return new User($id, $row['email'], null, $row['username'], $row['description'], $row['has_image']);
            }

            return null;
        }
        
        // gets all users with any id BUT this one
        public function getUsers(int $id) {
            $sql = "SELECT id, description, username, email, has_image FROM users WHERE id != $id";

            $result = mysqli_query($this->connection, $sql);

            if(mysqli_num_rows($result) > 0) {
                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                return array_map(function(array $row) {
                    return new User($row['id'], $row['email'], null, $row['username'], $row['description'], $row['has_image']);
                }, $rows); // might bug out with the mapping here FIXME
            }

            return null;
        }
    }
?>