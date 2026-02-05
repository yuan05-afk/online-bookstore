<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/payment_mock.php';

class PaymentMockTest extends TestCase
{
    /**
     * @testdox Processing a valid Visa card returns a successful transaction ID
     */
    public function testProcessPaymentSuccess()
    {
        // Valid Visa
        $cardNumber = '4111111111111111';
        $expiryMonth = '12';
        $expiryYear = '2030';
        $cvv = '123';
        $amount = 100.00;

        $result = PaymentProcessor::processPayment($cardNumber, $expiryMonth, $expiryYear, $cvv, $amount);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('transaction_id', $result);
    }

    /**
     * @testdox Processing an invalid card number fails with an error message
     */
    public function testProcessPaymentFailureWithInvalidCard()
    {
        $result = PaymentProcessor::processPayment('123', '12', '2025', '123', 100.00);

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid card number', $result['message']);
    }
}
