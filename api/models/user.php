<?php
    class User {
        public $id; // might be null; maybe var
        public $email;
        public $password;
        public $username;
        public $description;

        public function __construct($id, string $email, string $password, string $username, string $description) {     
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->username = $username;
            $this->description = $description;
        }
    }
?>