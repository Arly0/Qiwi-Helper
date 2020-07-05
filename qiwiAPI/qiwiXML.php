<?php

class qiwiXML {

  private $_user_id;
  private $_password;
  private $_url;
  private $_autoResponce;
  public  $_transactionID;

  public function __construct ($user_id, $pass, bool $resp = false) {
    $this->_user_id = $user_id;
    $this->_password = $pass;
    $this->_autoResponce = $resp;
    $this->_url = 'https://api.qiwi.com/xml/topup.jsp';
  }

  private function sendData (array $content, bool $post = false) {
    $ch = curl_init($this->_url);
      curl_setopt($ch, CURLOPT_POST, $post);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
      curl_setopt($ch, CURLOPT_POSTFIELDS, "$content");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $responce = curl_exec($ch);
      curl_close($ch);
      return $responce;
  }

  public function sendMoneyQiwi ($phoneNum, $summ) {
    $timeID = time() + 10 * 5;
    $data = '<?xml version="1.0" encoding="utf-8"?>
    <request>
      <request-type>pay</request-type>
      <terminal-id>' . $this->_user_id . '</terminal-id>
      <extra name="password">' . $this->_password . '</extra>
      <auth>
        <payment>
          <transaction-number>' . $timeID . '</transaction-number>
          <from>
            <ccy>RUB</ccy>
          </from>
          <to>
            <amount>' . $summ . '</amount>
            <ccy>RUB</ccy>
            <service-id>99</service-id>
            <account-number>' . $phoneNum . '</account-number>
          </to>
        </payment>
      </auth>
    </request>';
    $this->_transactionID = $timeID;

    $responce = $this->sendData($data, true);
    if ($this->_autoResponce) {
      $status = $this->defaultResp($responce);
      return $status;
    } else {
      return $responce;
    }

    return 0;
  }

  public function sendMoneyCard ($cardNum, $summ) {
      $timeID = time() + 10 * 5;
      $data = '<?xml version="1.0" encoding="utf-8"?>
      <request>
        <request-type>pay</request-type>
        <terminal-id>' . $this->_user_id . '</terminal-id>
        <extra name="password">' . $this->_password . '</extra>
        <auth>
          <payment>
            <transaction-number>' . $timeID . '</transaction-number>
            <from>
              <ccy>RUB</ccy>
            </from>
            <to>
              <amount>' . $summ . '</amount>
              <ccy>RUB</ccy>
              <service-id>34020</service-id>
              <account-number>' . $cardNum . '</account-number>
            </to>
          </payment>
        </auth>
      </request>';
      $this->_transactionID = $timeID;

      $responce = $this->sendData($data, true);
      if ($this->_autoResponce) {
        $status = $this->defaultResp($responce);
        return $status;
      } else {
        return $responce;
      }

    return 0;
  }

  public function checkProvider ($phoneNum) {
      $data = '<?xml version="1.0" encoding="utf-8"?>
      <request>
      <request-type>get-provider-by-phone-number</request-type>
      <terminal-id>' . $this->_user_id . '</terminal-id>
      <phonenumber>' . $phoneNum . '</phonenumber>
      <extra name="password">' . $this->_password . '</extra>
      </request>';
      $this->_transactionID = $timeID;

      return $this->sendData($data, true);
  }

  public function sendMoneyPhone ($phoneNum, $summ) {
    $timeID = time() + 10 * 5;
    $shortPhone = null;
    if ($phoneNum[0] == "+") {
      $shortPhone = substr($phoneNum, 2);
    } else {
      $shortPhone = substr($phoneNum, 1);
    }
    $providerID = $this->checkProvider($phoneNum);
    $data = '<?xml version="1.0" encoding="utf-8"?>
    <request>
      <request-type>pay</request-type>
      <terminal-id>' . $this->_user_id . '</terminal-id>
      <extra name="password">' . $this->_password . '</extra>
      <auth>
        <payment>
          <transaction-number>' . $timeID . '</transaction-number>
          <from>
            <ccy>RUB</ccy>
          </from>
          <to>
            <amount>' . $summ . '</amount>
            <ccy>RUB</ccy>
            <service-id>29130</service-id>
            <account-number>' . $phoneNum . '</account-number>
            <extra name="account0">id:' . $providerID . ';acc:' . $shortPhone . ';sum:' . $summ . '</extra>
          </to>
        </payment>
      </auth>
    </request>';
    $this->_transactionID = $timeID;

    $responce = $this->sendData($data, true);
    if ($this->_autoResponce) {
      $status = $this->defaultResp($responce);
      return $status;
    } else {
      return $responce;
    }

    return 0;
  }

  public function checkPaymentStatus ($phoneNum, $transactionID) {
    $data = '<?xml version="1.0" encoding="utf-8"?>
    <request>
      <request-type>pay</request-type>
      <extra name="password">' . $this->_password . '</extra>
      <terminal-id>' . $this->_user_id . '</terminal-id>
      <status>
        <payment>
          <transaction-number>' . $transactionID . '</transaction-number>
          <to>
               <account-number>'. $phoneNum . '</account-number>
          </to>
        </payment>
      </status>
    </request>';
    $this->_transactionID = $timeID;

    return $this->sendData($data, true);
  }

  public function checkBalance () {
    $data = '<request>
    <request-type>ping</request-type>
    <terminal-id>' . $this->_user_id . '</terminal-id>
    <extra name="password">' . $this->_password . '</extra>
    </request>';

    return $this->sendData($data, true);
  }


  // responce handling functions
  private function defaultResp ($content) {
    $result = simplexml_load_string($content);

    // common status
    $status = $result->payment['status'];
    if ($status == -1) {
      $status = "Ошибка. Попробуйте повторить запрос позже.";
    } elseif ($status >= 0 && $status <= 49) {
      $status = "Платеж принят, но ждет подтверждения со стороны системы QIWI Wallet.";
    } elseif ($status >= 50 && $status <= 59) {
      $status = "Платеж находится в проведении. Ожидайте";
    } elseif ($status == 60) {
      $status = "Платеж проведен";
    } elseif ($status >= 100 && $status <= 150) {
      $status = "Ошибка. Платеж не принят";
    } elseif ($status == 151) {
      $status = "Ошибка авторизации платежа";
    } elseif ($status == 160) {
      $status = "Платеж не проведен или отменен	";
    }

    // error status
    $errorCode = $result->payment['result-code'];
    if ($errorCode == 220) {
      $status = "Недостаточно средств на счете для проведения платежа";
    } elseif ($errorCode == 242) {
      $status = "Сумма платежа больше допустимой";
    } else if ($errorCode == 316) {
      $status = "Попытка авторизации заблокированного Контрагента";
    }

    return $status;
  }

  private function providerResp ($content) {
    $result = simplexml_load_string($content);

    $status = $result->{'result-code'};
    if ($status == 0) {
      $status = $result->provider['id'];
    } elseif ($status == 13) {
      $status = "Повторите запрос через минуту.";
    } elseif ($status == 150) {
      $status = "Ошибка авторизации. Уточните логин и пароль, а затем повторите запрос.";
    } elseif ($status == 300) {
      $status = "Неизвестная ошибка. Повторите запрос.";
    } elseif ($status == 339) {
      $status = "Ограничение по IP адресу. Обратитесь в поддержку.";
    }
  }

  private function balanceResp ($content) {
    $result = simplexml_load_string($content);

    $status = $result->{'result-code'};
    if ($status == 0) {
      $status = $result->balance;
    } elseif ($status == 13) {
      $status = "Повторите запрос через минуту.";
    } elseif ($status == 150) {
      $status = "Ошибка авторизации. Уточните логин и пароль, а затем повторите запрос.";
    } elseif ($status == 300) {
      $status = "Неизвестная ошибка. Повторите запрос.";
    } elseif ($status == 339) {
      $status = "Ограничение по IP адресу. Обратитесь в поддержку.";
    }
  }
}

?>