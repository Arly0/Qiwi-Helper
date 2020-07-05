<?php
  class qiwiJson {

    private $_phone;
    private $_token;
    private $_url;

    public function __construct ($phone, $token) {
      $this->_phone = $phone;
      $this->_token = $token;
      // def value
      $this->_url = 'https://edge.qiwi.com/';
    }


    private function sendData () {

    }

    
  }
?>