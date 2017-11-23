<?php

namespace Jetfuel\Zsagepay;

use Jetfuel\Zsagepay\Traits\ResultParser;

class TradeQuery extends Payment
{
    use ResultParser;

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
     * Find Order by trade number.
     *
     * @param $tradeNo
     * @return array|null
     */
    public function find($tradeNo)
    {
        $payload = $this->signPayload([
            'outOrderId' => $tradeNo,
        ]);

        $order = $this->parseResponse($this->httpClient->post('ebank/queryOrder.do', $payload));

        if (empty($order['data']) || !isset($order['data']['amount'])) {
            return null;
        }

        $order['data']['amount'] = $this->convertFenToYuan($order['data']['amount']);

        return $order;
    }

    /**
     * Is order already paid.
     *
     * @param $tradeNo
     * @return bool
     */
    public function isPaid($tradeNo)
    {
        $order = $this->find($tradeNo);

        if ($order === null || !isset($order['data']['replyCode']) || $order['data']['replyCode'] !== '00') {
            return false;
        }

        return true;
    }
}
