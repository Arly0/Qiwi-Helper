<?php
  class qiwiJson {

    private $_phone;
    private $_token;
    private $_url;
    private $_resp;
    private $_errors;

    public function __construct ($phone, $token, bool $autoResp = false) {
      $this->_phone = $phone;
      // HEADER
      $this->_token = "Bearer:" . $token;
      // def value
      $this->_url   = 'https://edge.qiwi.com/';
      $this->_resp  = $autoResp;

      $this->_errors = [];
    }


    private function sendData () {
      
    }

    public function getLimitPurse ($persID, array $customHEADERS = []) {
      if (strpos($persID, "+")) {
        $persID = substr($persID, 1);
      }
      $headersStr = "";
      if (!empty($customHEADERS)) {
        foreach ($customHEADERS as $key => $item) {
          $headersStr .= "&" . $key . "=" . $item;
        }
        $headersStr = substr($headersStr, 1);
      }

      $secPath = "/qw-limits/v1/persons/" . $persID . "/actual-limits?" . $headersStr;

      // TODO: CURL можно спокойно вынести как отджельный объект 
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->_url . $secPath);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        "Authorization: " . $this->_token,
      ));

      $responce = curl_exec($ch);
      curl_close($ch);
      // check status 200 or smth
      $data = $responce['data'];
      if ($data['status'] === 200) {
        // ok
      } else {
        // errors
      }
    }
   
    



    // task: continue programm or return error
    private function dataHandler (array $data) {
      
    }
  }
