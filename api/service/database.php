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
            // TODO: password hash on either client or server-side
            $sql = "INSERT INTO users(id, email, username, password, description) 
            VALUES(NULL, '$user->email', '$user->username', '$user->password', '$user->description')";

            mysqli_query($this->connection, $sql);

            return mysqli_insert_id($this->connection);
        }

        public function userExistsOrPasswordTaken(string $username, string $password) { // user exists if username or password are taken
            $sql = "SELECT username FROM users WHERE username = '$username' OR password = '$password'";

            $exists_result = mysqli_query($this->connection, $sql);

            return mysqli_num_rows($exists_result) > 0; // if more than one row found => user exists
        }

        public function validateUser(string $email, string $password) {
            $sql = "SELECT username, password FROM users WHERE email = '$email'"; // get associated user by email

            $result = mysqli_query($this->connection, $sql);

            if(mysqli_num_rows($result) > 0 && ($rows = mysqli_fetch_all($result, MYSQLI_ASSOC))) {
                    
                $filteredRows = array_filter($rows, function (array $row) use ($password) {
                    return password_verify($password, $row['password']);
                });

                if(count($filteredRows) && $row = $filteredRows[0]) {
                    return $row['username'];
                } // if more than one row found AND the passwords match => auth'd user => return username for token
            }

            return null; // else return nothing and mark user as unauth'd
        }

        public function getUser(string $username) { // user already auth'd at this point due to token => get user by username
            $sql = "SELECT id, description, username, email FROM users WHERE username = '$username'";

            $result = mysqli_query($this->connection, $sql);
        
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result); // fetch the resulting rows in the form of a map (associative array)
                
                return new User($row['id'], $row['email'], null, $row['username'], $row['description']);
            }

            return null;
        }
        
        // gets all users with any id BUT this one
        public function getUsers(int $id) {
            $sql = "SELECT id, description, username, email FROM users WHERE id != $id";

            $result = mysqli_query($this->connection, $sql);

            if(mysqli_num_rows($result) > 0) {
                $rows = mysqli_fetch_all($result);

                return array_map(function(array $row) {
                    return new User($row[0], $row[3], null, $row[2], $row[1]);
                }, $rows); // might bug out with the mapping here FIXME
            }

            return null;
        }
    }
?>