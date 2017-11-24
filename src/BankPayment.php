<?php

namespace Jetfuel\Zsagepay;

class BankPayment extends Payment
{
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
        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            'outOrderId'      => $tradeNo,
            'totalAmount'     => $this->convertYuanToFen($amount),
            'orderCreateTime' => $this->getCurrentTime(),
            'lastPayTime'     => $this->getExpireTime(),
        ]);

        $payload['bankCode'] = $bank;
        $payload['noticeUrl'] = $notifyUrl;
        $payload['merUrl'] = $returnUrl;
        $payload['bankCardType'] = self::BANK_CARD_TYPE;

        return $this->httpClient->post('ebank/pay.do', $payload);
    }
}
