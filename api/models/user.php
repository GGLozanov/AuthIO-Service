<?php
    class User {
        public $id; // might be null; maybe var
        public $email;
        public $password;
        public $username;
        public $description;
        public $hasImage;

        public function __construct($id, string $email, $password, string $username, string $description, bool $hasImage) {     
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->username = $username;
            $this->description = $description;
            $this->hasImage = $hasImage;
        }
    }
?>