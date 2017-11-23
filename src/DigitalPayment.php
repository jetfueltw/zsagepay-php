<?php

namespace Jetfuel\Zsagepay;

use Jetfuel\Zsagepay\Traits\ResultParser;

class DigitalPayment extends Payment
{
    use ResultParser;

    const MODEL          = 'QR_CODE';
    const CREDIT_SUPPORT = 1;

    /**
     * DigitalPayment constructor.
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
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param int $channel
     * @param float $amount
     * @param string $clientIp
     * @param string $notifyUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $clientIp, $notifyUrl)
    {
        $payload = $this->signPayload([
            'outOrderId'      => $tradeNo,
            'amount'          => $this->convertYuanToFen($amount),
            'noticeUrl'       => $notifyUrl,
            'isSupportCredit' => self::CREDIT_SUPPORT,
            'orderCreateTime' => $this->getCurrentTime(),
        ]);

        $payload['payChannel'] = $channel;
        $payload['ip'] = $clientIp;
        $payload['lastPayTime'] = $this->getExpireTime();
        $payload['model'] = self::MODEL;

        return $this->parseResponse($this->httpClient->post('scan/entrance.do', $payload));
    }
}
