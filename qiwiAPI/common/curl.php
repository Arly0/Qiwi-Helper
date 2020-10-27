<?php


class Curl
{
  private $_url;
  private $_headers;

  public function __construct(string $url, string $token, array $headers = [])
  {
    $this->_url = $url;
    $this->_headers = array(
      "Accept: application/json",
      "Authorization: " . $this->_token,
      // TODO проверить синтаксис
      ...$headers
    );
  }

  public function init()
  {
    // TODO: CURL можно спокойно вынести как отджельный объект 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->_url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      $this->_headers
    ));

    // return responce
    $responce = curl_exec($ch);
    curl_close($ch);
  }
}
