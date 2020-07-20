<?php
    class Database {
            
        public $host = "localhost"; // to be changed if hosted on server
        public $user_name = "root";
        public $user_password = "";
        public $db_name = "authdb";
        public $connection;

        public function getConnection() {
            $this->connection = mysqli_connect(
                $this->host,
                 $this->user_name, 
                 $this->user_password,
                  $this->db_name, "3308"
                );
            return $this->connection;
        }

        public function closeConnection() {
            mysqli_close($this->connection);
        }

        public function createUser(User $user) {
            // TODO: password hash on either client or server-side
            $sql = "INSERT INTO users(id, email, username, password, description) 
            VALUES(NULL, '$user->email', '$user->username', '$user->password', '$user->description')";

            return mysqli_query($this->connection, $sql);
        }

        public function userExistsOrPasswordTaken(string $username, string $password) { // user exists if username or password are taken
            $sql = "SELECT username FROM users WHERE username = '$username' OR password = '$password'";

            $exists_result = mysqli_query($this->connection, $sql);

            return mysqli_num_rows($exists_result) > 0; // if more than one row found => user exists
        }

        public function validateUser(string $email, string $password) {
            $sql = "SELECT username FROM users WHERE email = '$email' OR password = '$password'";

            $result = mysqli_query($this->connection, $sql);

            if(mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result)['username'];
            } // if more than one row found => auth'd user => return username for token

            return null;
        }

        public function getUser(string $username) { // user already auth'd at this point due to token => get user by username
            $sql = "SELECT id, description, username FROM users WHERE username = '$username'";

            $result = mysqli_query($this->connection, $sql);
        
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result); // fetch the resulting rows in the form of a map (associative array)
                
                return new User($row['id'], $row['email'], $row['password'], $row['username'], $row['description']);
            }

            return null;
        }
    }
?>