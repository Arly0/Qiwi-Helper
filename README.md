# Библиотека для работы с Qiwi API под PHP

## Работа с XML 

Подключение:
```
require_once('./qiwiAPI/qiwiXML.php');
```

```
$qiwi = new qiwiXML($user_id, $password, $responcive);
```
* $user_id - можно посмотреть в Qiwi кассы -> "Услуги" -> "Баланс" -> "Идентификатор"
* $password - скажет Вам Qiwi по вашему $user_id
* $responcive - (необязательный - по стандарту FALSE) авто обработчик ответа, если флаг установлен в TRUE, тогад ответ будет отдаваться в уже обработанном виде, как пишет QIWI API https://developer.qiwi.com/ru/topup-mobile/#statuses 

 Work with XML API (in progress)
