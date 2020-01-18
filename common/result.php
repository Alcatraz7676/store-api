<?php
    class Result {

        private $code;
        private $message;

        public function __construct($code, $message) {
            $this->code = $code;
            $this->message = $message;
        }

        function getCode() {
            return $this->code;
        }

        function getMessage() {
            return $this->message;
        }
    }
?>
