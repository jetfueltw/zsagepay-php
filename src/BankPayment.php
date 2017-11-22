<?php

namespace Jetfuel\Zsagepay;

class BankPayment extends Payment
{
    const BASE_API_URL   = 'http://payment.zsagepay.com/';
    const BANK_CARD_TYPE = '01';

    /**
     * BankPayment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        $baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param int $amount
     * @param string $returnUrl
     * @param string $notifyUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $returnUrl, $notifyUrl)
    {
        $payload = $this->signPayload([
            'outOrderId'      => $tradeNo,
            'totalAmount'     => $this->convertYuanToFen($amount),
            'orderCreateTime' => $this->getCurrentTime(),
            'lastPayTime'     => $this->getExpireTime(),
        ]);

        $payload['bankCode'] = $bank;
        $payload['merUrl'] = $returnUrl;
        $payload['noticeUrl'] = $notifyUrl;
        $payload['bankCardType'] = self::BANK_CARD_TYPE;

        return $this->httpClient->post('ebank/pay.do', $payload);
    }
}
