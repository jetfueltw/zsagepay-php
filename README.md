## 介紹

澤聖聚合支付 PHP 版本封裝。

## 安裝

使用 Composer 安裝。

```
composer require jetfueltw/zsagepay-php
```

## 使用方法

### 掃碼支付下單

使用微信支付、QQ錢包、支付寶掃碼支付，下單後返回支付網址，請自行轉為 QR Code。

```
$merchantId = '1XXXXXXX1'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // md5 密鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
$channel = Channel::WECHAT; // 支付通道，支援微信支付、QQ錢包、支付寶
$amount = 1.00; // 消費金額 (元)
$clientIp = 'XXX.XXX.XXX.XXX'; // 消費者端 IP 位址
$notifyUrl = 'https://XXX.XXX.XXX'; // 交易完成後異步通知接口
```
```
$payment = new DigitalPayment($merchantId, $secretKey);
$result = $payment->order($tradeNo, $channel, $amount, $clientIp, $notifyUrl);
```
```
Result:
[
    'code' => '00',
    'data' => [
        'merchantCode' => '1XXXXXXX1', // 商家號
        'orderId' => '2XXXXXXXXXXXXXXXXX7', // 聚合支付平台訂單號
        'outOrderId' => '20170101235959XXX', // 商家產生的唯一訂單號
        'sign' => '946XXXXXXXXXXXXXXXXXXXXXXXXXX008', // 簽名
        'url' => 'weixin://wxpay/bizpayurl?pr=XXXXXXX', // 支付網址
    ],
    'msg' => '成功',
]
```

### 掃碼支付交易成功通知

消費者支付成功後，平台會發出 HTTP POST 請求到你下單時填的 $notifyUrl，商家在收到通知並處理完後必須回應 `{"code":"00"}`，否則平台會認為通知失敗，並重發多次通知。

* 商家必需正確處理重複通知的情況。
* 能使用 `NotifyWebhook@successNotifyResponse` 返回成功回應。  
* 務必使用 `NotifyWebhook@verifyNotifyPayload` 驗證簽證是否正確。
* 通知的消費金額單位為 `分`，使用 `NotifyWebhook@parseNotifyPayload` 能驗證簽證並把消費金額單位轉為 `元`。 

```
Post Data:
[
    'merchantCode' => '1XXXXXXX1', // 商家號
    'instructCode' => '1XXXXXXXXXXXXXXXX8', // 聚合支付平台訂單號
    'transType' => '00200', // 交易類型
    'outOrderId' => '20170101235959XXX', // 商家產生的唯一訂單號
    'transTime' => '20150211155604', // 交易時間
    'totalAmount' => 100, // 消費金額 (分)
    'sign' => '69C0A709C58C7E7BFA5CF5B7F8D690C0', // 簽名
]
```

### 掃碼支付訂單查詢

使用商家訂單號查詢單筆訂單狀態。

```
$merchantId = '1XXXXXXX1'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // md5 密鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
```
```
$tradeQuery = new TradeQuery($merchantId, $secretKey);
$result = $tradeQuery->find($tradeNo);
```
```
Result:
[
    'code' => '00',
    'data' => [
        'amount' => 1.00, // 消費金額 (元)
        'instructCode' => '1XXXXXXXXXXXXXXXX8', // 聚合支付平台訂單號
        'merchantCode' => '1XXXXXXX1', // 商家號
        'outOrderId' => '20170101235959XXX', // 商家產生的唯一訂單號
        'replyCode' => '00', // 狀態碼
        'sign' => '946XXXXXXXXXXXXXXXXXXXXXXXXXX008', // 簽名
        'transTime' => '20150211155604', // 交易時間
        'transType' => '00200', // 交易類型
    ],
    'msg' => '成功',
]
```

### 掃碼支付訂單支付成功查詢

使用商家訂單號查詢單筆訂單是否支付成功。

```
$merchantId = '1XXXXXXX1'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // md5 密鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
```
```
$tradeQuery = new TradeQuery($merchantId, $secretKey);
$result = $tradeQuery->isPaid($tradeNo);
```
```
Result:
bool(true|false)
```

### 網銀支付下單

使用網路銀行支付，下單後返回跳轉頁面，請 render 到客戶端。

```
$merchantId = '1XXXXXXX1'; // 商家號
$secretKey = 'XXXXXXXXXXXXXXX'; // md5 密鑰
$tradeNo = '20170101235959XXX'; // 商家產生的唯一訂單號
$bank = Bank::CCB; // 銀行編號
$amount = 1.00; // 消費金額 (元)
$returnUrl = 'https://XXX.XXX.XXX'; // 交易完成後會跳轉到這個頁面
$notifyUrl = 'https://XXX.XXX.XXX'; // 交易完成後異步通知接口
```
```
$payment = new BankPayment($merchantId, $secretKey);
$result = $payment->order($tradeNo, $bank, $amount, $returnUrl, $notifyUrl);
```
```
Result:
跳轉用的 HTML，請 render 到客戶端
```

### 網銀支付交易成功通知

同掃碼支付交易成功通知

### 網銀支付訂單查詢

同掃碼支付訂單查詢

### 網銀支付訂單支付成功查詢

同掃碼支付訂單支付成功查詢
