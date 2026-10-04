<?php

namespace App\Helpers;

class NumberToFrenchWords
{
    private static array $units = [
        '', 'UN', 'DEUX', 'TROIS', 'QUATRE', 'CINQ', 'SIX', 'SEPT', 'HUIT', 'NEUF',
        'DIX', 'ONZE', 'DOUZE', 'TREIZE', 'QUATORZE', 'QUINZE', 'SEIZE',
        'DIX-SEPT', 'DIX-HUIT', 'DIX-NEUF',
    ];

    private static array $tens = [
        '', 'DIX', 'VINGT', 'TRENTE', 'QUARANTE', 'CINQUANTE', 'SOIXANTE',
        'SOIXANTE', 'QUATRE-VINGT', 'QUATRE-VINGT',
    ];

    /**
     * Convert a number to French words.
     *
     * @param float $amount The amount in DA (e.g. 193700.00)
     * @return string French words representation
     */
    public static function convert(float $amount): string
    {
        $isNegative = $amount < 0;

        $integerPart = (int) floor(abs($amount));
        $decimalPart = (int) round((abs($amount) - $integerPart) * 100);

        if ($integerPart === 0 && $decimalPart === 0) {
            return 'ZÉRO';
        }

        $result = '';

        if ($integerPart > 0) {
            $result = self::convertInteger($integerPart);
        }

        if ($decimalPart > 0) {
            $result .= ' DINARS ALGERIEN ET ' . self::convertInteger($decimalPart) . ' CENTIMES';
        } else {
            $result .= ' DINARS ALGERIEN ET ZÉRO CENTIMES';
        }

        if ($isNegative) {
            $result = 'MOINS ' . trim($result);
        }

        return trim($result);
    }

    /**
     * Convert an integer to French words.
     */
    private static function convertInteger(int $number): string
    {
        if ($number === 0) {
            return 'ZÉRO';
        }

        if ($number < 0) {
            return 'MOINS ' . self::convertInteger(abs($number));
        }

        $parts = [];

        // Billions
        if ($number >= 1_000_000_000_000) {
            $billions = (int) floor($number / 1_000_000_000_000);
            if ($billions === 1) {
                $parts[] = 'UN BILLION';
            } else {
                $parts[] = self::convertInteger($billions) . ' BILLIONS';
            }
            $number %= 1_000_000_000_000;
        }

        // Milliards
        if ($number >= 1_000_000_000) {
            $milliards = (int) floor($number / 1_000_000_000);
            if ($milliards === 1) {
                $parts[] = 'UN MILLIARD';
            } else {
                $parts[] = self::convertInteger($milliards) . ' MILLIARDS';
            }
            $number %= 1_000_000_000;
        }

        // Millions
        if ($number >= 1_000_000) {
            $millions = (int) floor($number / 1_000_000);
            if ($millions === 1) {
                $parts[] = 'UN MILLION';
            } else {
                $parts[] = self::convertInteger($millions) . ' MILLIONS';
            }
            $number %= 1_000_000;
        }

        // Thousands
        if ($number >= 1_000) {
            $thousands = (int) floor($number / 1_000);
            if ($thousands === 1) {
                $parts[] = 'MILLE';
            } else {
                $parts[] = self::convertInteger($thousands) . ' MILLE';
            }
            $number %= 1_000;
        }

        // Hundreds
        if ($number >= 100) {
            $hundreds = (int) floor($number / 100);
            if ($hundreds === 1) {
                $parts[] = 'CENT';
            } else {
                $remainder = $number % 100;
                if ($remainder === 0) {
                    $parts[] = self::$units[$hundreds] . ' CENTS';
                } else {
                    $parts[] = self::$units[$hundreds] . ' CENT';
                }
            }
            $number %= 100;
        }

        // Tens and units
        if ($number > 0) {
            $parts[] = self::convertTensAndUnits($number);
        }

        return implode(' ', $parts);
    }

    /**
     * Convert a number from 1-99 to French words.
     */
    private static function convertTensAndUnits(int $number): string
    {
        if ($number < 20) {
            return self::$units[$number];
        }

        $tensIndex = (int) floor($number / 10);
        $unit = $number % 10;

        // Handle 70-79: SOIXANTE-DIX, SOIXANTE ET ONZE, etc.
        if ($tensIndex === 7) {
            $subNumber = 10 + $unit;
            if ($unit === 1) {
                return 'SOIXANTE ET ONZE';
            }
            return 'SOIXANTE-' . self::$units[$subNumber];
        }

        // Handle 80-89: QUATRE-VINGTS, QUATRE-VINGT-UN, etc.
        if ($tensIndex === 8) {
            if ($unit === 0) {
                return 'QUATRE-VINGTS';
            }
            return 'QUATRE-VINGT-' . self::$units[$unit];
        }

        // Handle 90-99: QUATRE-VINGT-DIX, QUATRE-VINGT-ONZE, etc.
        if ($tensIndex === 9) {
            $subNumber = 10 + $unit;
            return 'QUATRE-VINGT-' . self::$units[$subNumber];
        }

        // Handle standard tens (20-69)
        if ($unit === 0) {
            return self::$tens[$tensIndex];
        }

        if ($unit === 1) {
            return self::$tens[$tensIndex] . ' ET UN';
        }

        return self::$tens[$tensIndex] . '-' . self::$units[$unit];
    }
}
