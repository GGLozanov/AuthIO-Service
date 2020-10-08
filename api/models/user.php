<?php
    class User {
        public $id; // might be null; maybe var
        public $email;
        public $password;
        public $username;
        public $description;
        public $hasImage;

        public function __construct($id, $email, $password, $username, $description, $hasImage) {     
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->username = $username;
            $this->description = $description;
            $this->hasImage = $hasImage;
        }

        // TODO: Banal and dumb function; there has to be a better way to do this
        // TODO: Add new method for email and password update seperately eventually
        // this method is called for an update user to get the query for its existing/non-null fields
        public function getUpdateQuery() {
            $sql = "UPDATE users SET";

            $condStatus = 0b0000; // bitmask for all the possibilities of null vals

            $condStatus |= ($this->email != null) << 3; // 1000
            $condStatus |= ($this->password != null) << 2; // 0100
            $condStatus |= ($this->username != null) << 1; // 0010
            $condStatus |= $this->description != null; // 0001

            $firstField = (int) log($condStatus, 2) + 1; // base 2 log (with 1 added); finds position of MSB

            $commaCount = substr_count(decbin($condStatus), 1);

            $this->addUpdateFieldToQuery($condStatus & 0b1000, $sql, $commaCount, "email", $this->email, 
                $firstField == 4);
            $this->addUpdateFieldToQuery($condStatus & 0b0100, $sql, $commaCount, "password", $this->password,
                $firstField == 3);
            $this->addUpdateFieldToQuery($condStatus & 0b0010, $sql, $commaCount, "username", $this->username,
                $firstField == 2);
            $this->addUpdateFieldToQuery($condStatus & 0b0001, $sql, $commaCount, "description", $this->description,
                $firstField == 1);


            $sql .= " WHERE id = $this->id";

            return $sql;
        }

        private function addUpdateFieldToQuery(bool $fieldNull, string &$sql, int &$commaCount, string $field, $value, bool $isFirstField) {
            $isValueString = is_string($value);
            if($fieldNull) {
                if($commaCount > 0 && !$isFirstField) {
                    $sql .= $isValueString ?  ", $field = '$value'" : ", $field = $value";
                    $commaCount--;
                } else
                    $sql .= $isValueString ? " $field = '$value'" : " $field = $value";
            }
        }
    }
?>