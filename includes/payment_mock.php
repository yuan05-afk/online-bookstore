<?php
/**
 * Mock Payment Processor
 * Simulates payment gateway with realistic validation
 */

class PaymentProcessor
{
    /**
     * Validate credit card number using Luhn algorithm
     */
    public static function validateCardNumber($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);

        if (!preg_match('/^[0-9]{13,19}$/', $cardNumber)) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        $numDigits = strlen($cardNumber);
        $parity = $numDigits % 2;

        for ($i = 0; $i < $numDigits; $i++) {
            $digit = (int) $cardNumber[$i];

            if ($i % 2 == $parity) {
                $digit *= 2;
            }

            if ($digit > 9) {
                $digit -= 9;
            }

            $sum += $digit;
        }

        return ($sum % 10) == 0;
    }

    /**
     * Validate expiry date
     */
    public static function validateExpiryDate($month, $year)
    {
        if (!is_numeric($month) || !is_numeric($year)) {
            return false;
        }

        $month = (int) $month;
        $year = (int) $year;

        if ($month < 1 || $month > 12) {
            return false;
        }

        // Convert 2-digit year to 4-digit
        if ($year < 100) {
            $year += 2000;
        }

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            return false;
        }

        return true;
    }

    /**
     * Validate CVV
     */
    public static function validateCVV($cvv)
    {
        return preg_match('/^[0-9]{3,4}$/', $cvv);
    }

    /**
     * Process payment (mock)
     * Returns array with success status and transaction ID
     */
    public static function processPayment($cardNumber, $expiryMonth, $expiryYear, $cvv, $amount)
    {
        // Validate all fields
        if (!self::validateCardNumber($cardNumber)) {
            return [
                'success' => false,
                'message' => 'Invalid card number',
                'transaction_id' => null
            ];
        }

        if (!self::validateExpiryDate($expiryMonth, $expiryYear)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired card',
                'transaction_id' => null
            ];
        }

        if (!self::validateCVV($cvv)) {
            return [
                'success' => false,
                'message' => 'Invalid CVV',
                'transaction_id' => null
            ];
        }

        // Simulate payment processing (90% success rate)
        $success = (rand(1, 100) <= 90);

        if ($success) {
            $transactionId = 'TXN-' . strtoupper(uniqid()) . '-' . rand(10000, 99999);

            // Log successful transaction
            self::logTransaction($transactionId, $amount, 'SUCCESS');

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_id' => $transactionId
            ];
        } else {
            $transactionId = 'TXN-' . strtoupper(uniqid()) . '-FAILED';

            // Log failed transaction
            self::logTransaction($transactionId, $amount, 'FAILED');

            return [
                'success' => false,
                'message' => 'Payment declined. Please try again or use a different card.',
                'transaction_id' => $transactionId
            ];
        }
    }

    /**
     * Log transaction
     */
    private static function logTransaction($transactionId, $amount, $status)
    {
        $logFile = __DIR__ . '/../logs/transactions.log';
        $logDir = dirname($logFile);

        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = sprintf(
            "[%s] Transaction ID: %s | Amount: $%.2f | Status: %s\n",
            date('Y-m-d H:i:s'),
            $transactionId,
            $amount,
            $status
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Get card type from number
     */
    public static function getCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'Mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } elseif (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'Discover';
        }

        return 'Unknown';
    }
}
