<?php

use Jetfuel\Zsagepay\Constants\Channel;
use Jetfuel\Zsagepay\Constants\Bank;
use Jetfuel\Zsagepay\Traits\NotifyWebhook;
use Jetfuel\Zsagepay\DigitalPayment;
use Jetfuel\Zsagepay\BankPayment;
use Jetfuel\Zsagepay\TradeQuery;

class UnitTest extends PHPUnit\Framework\TestCase
{
    private $merchantId = '1000000001';
    private $secretKey  = '123456ADSEF';

    public function testDigitalPaymentOrder()
    {
        $faker = Faker\Factory::create();
        $tradeNo = $faker->uuid;
        $channel = Channel::WECHAT;
        $amount = 1;
        $clientIp = $faker->ipv4;
        $notifyUrl = $faker->url;

        $payment = new DigitalPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $channel, $amount, $clientIp, $notifyUrl);

        $this->assertEquals('00', $result['code']);

        return $tradeNo;
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertEquals('00', $result['code']);
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testBankPaymentOrder()
    {
        $faker = Faker\Factory::create();
        $tradeNo = $faker->uuid;
        $bank = Bank::CCB;
        $amount = 1;
        $returnUrl = $faker->url;
        $notifyUrl = $faker->url;

        $payment = new BankPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $bank, $amount, $returnUrl, $notifyUrl);

        $this->assertContains('<form', $result, '', true);

        return $tradeNo;
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertEquals('00', $result['code']);
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testTradeQueryFindOrderNotExist()
    {
        $faker = Faker\Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertNull($result);
    }

    public function testTradeQueryIsPaidOrderNotExist()
    {
        $faker = Faker\Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'merchantCode' => '1000000267',
            'instructCode' => '150211000000000018',
            'transType'    => '00200',
            'outOrderId'   => '80904482661769148113436093416980',
            'transTime'    => '20150211155604',
            'totalAmount'  => '1',
            'ext'          => 'ext',
            'sign'         => '69C0A709C58C7E7BFA5CF5B7F8D690C0',
        ];

        $this->assertTrue($mock->verifyNotifyPayload($payload, '123456ABDDFF'));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'merchantCode' => '1000000267',
            'instructCode' => '150211000000000018',
            'transType'    => '00200',
            'outOrderId'   => '80904482661769148113436093416980',
            'transTime'    => '20150211155604',
            'totalAmount'  => '1',
            'ext'          => 'ext',
            'sign'         => '69C0A709C58C7E7BFA5CF5B7F8D690C0',
        ];

        $this->assertEquals([
            'merchantCode' => '1000000267',
            'instructCode' => '150211000000000018',
            'transType'    => '00200',
            'outOrderId'   => '80904482661769148113436093416980',
            'transTime'    => '20150211155604',
            'totalAmount'  => 0.01,
            'ext'          => 'ext',
            'sign'         => '69C0A709C58C7E7BFA5CF5B7F8D690C0',
        ], $mock->parseNotifyPayload($payload, '123456ABDDFF'));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('{"code":"00"}', $mock->successNotifyResponse());
    }
}
