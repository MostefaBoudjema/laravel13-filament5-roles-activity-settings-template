<?php

namespace App\Helpers;

class NumberToArabicWords
{
    private static array $units = [
        '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر',
        'سبعة عشر', 'ثمانية عشر', 'تسعة عشر',
    ];

    private static array $tens = [
        '', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون',
    ];

    private static array $hundreds = [
        '', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة',
    ];

    /**
     * Convert a number to Arabic words.
     *
     * @param float $amount The amount in DA (e.g. 193700.00)
     * @return string Arabic words representation
     */
    public static function convert(float $amount): string
    {
        $integerPart = (int) floor(abs($amount));
        $decimalPart = (int) round((abs($amount) - $integerPart) * 100);

        if ($integerPart === 0 && $decimalPart === 0) {
            return 'صفر';
        }

        $result = '';

        if ($integerPart > 0) {
            $result = self::convertInteger($integerPart);
        }

        if ($decimalPart > 0) {
            $result .= ' دينار جزائري و' . self::convertInteger($decimalPart) . ' سنتيم';
        } else {
            $result .= ' دينار جزائري وصفر سنتيم';
        }

        return trim($result);
    }

    /**
     * Convert an integer to Arabic words.
     */
    private static function convertInteger(int $number): string
    {
        if ($number === 0) {
            return 'صفر';
        }

        if ($number < 0) {
            return 'سالب ' . self::convertInteger(abs($number));
        }

        $parts = [];

        // Billions
        if ($number >= 1_000_000_000) {
            $billions = (int) floor($number / 1_000_000_000);
            if ($billions === 1) {
                $parts[] = 'مليار';
            } elseif ($billions === 2) {
                $parts[] = 'ملياران';
            } elseif ($billions <= 10) {
                $parts[] = self::convertInteger($billions) . ' مليارات';
            } else {
                $parts[] = self::convertInteger($billions) . ' مليار';
            }
            $number %= 1_000_000_000;
        }

        // Millions
        if ($number >= 1_000_000) {
            $millions = (int) floor($number / 1_000_000);
            if ($millions === 1) {
                $parts[] = 'مليون';
            } elseif ($millions === 2) {
                $parts[] = 'مليونان';
            } elseif ($millions <= 10) {
                $parts[] = self::convertInteger($millions) . ' ملايين';
            } else {
                $parts[] = self::convertInteger($millions) . ' مليون';
            }
            $number %= 1_000_000;
        }

        // Thousands
        if ($number >= 1_000) {
            $thousands = (int) floor($number / 1_000);
            if ($thousands === 1) {
                $parts[] = 'ألف';
            } elseif ($thousands === 2) {
                $parts[] = 'ألفان';
            } elseif ($thousands <= 10) {
                $parts[] = self::convertInteger($thousands) . ' آلاف';
            } else {
                $parts[] = self::convertInteger($thousands) . ' ألف';
            }
            $number %= 1_000;
        }

        // Hundreds
        if ($number >= 100) {
            $hundredsIndex = (int) floor($number / 100);
            $parts[] = self::$hundreds[$hundredsIndex];
            $number %= 100;
        }

        // Tens and units
        if ($number > 0) {
            $parts[] = self::convertTensAndUnits($number);
        }

        return implode(' و', $parts);
    }

    /**
     * Convert a number from 1–99 to Arabic words.
     */
    private static function convertTensAndUnits(int $number): string
    {
        if ($number < 20) {
            return self::$units[$number];
        }

        $tensIndex = (int) floor($number / 10);
        $unit = $number % 10;

        if ($unit === 0) {
            return self::$tens[$tensIndex];
        }

        // In Arabic, units come before tens: "واحد وعشرون" (one and twenty)
        return self::$units[$unit] . ' و' . self::$tens[$tensIndex];
    }
}