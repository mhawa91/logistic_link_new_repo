<?php

namespace Modules\SystemCore\System\Classes;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;

class Helpers
{
    /**
     * Generate protected string from a string
     * EX: example.com => ex*******om
     *
     * @param string $string
     * @return string|null
     */
    public function stringToSecret(? string $string) :? string
    {
        if (! $string) {
            return null;
        }
        $length       = strlen($string);
        $visibleCount = (int) round($length / 4);
        $hiddenCount  = $length - ($visibleCount * 2);
        return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
    }

    /**
     * Transform number to (+) based numbers
     * EX: (905050111222 || 00905050111222)  => +905050111222
     *
     * @param string $Phone
     * @return string|null
     */
    public function parsePhoneNumber(? string $Phone) :? string
    {
        if (empty($Phone)) {
            return null;
        }
        if (Str::startsWith($Phone, '+')) {
            $Phone = substr($Phone, 1);
        }
        if (Str::startsWith($Phone, '00')) {
            $Phone = substr($Phone, 1);
            $Phone = substr($Phone, 1);
        }
        $Number = '+' . str_replace(['+', '-', '.', '_'], '', $Phone);
        $Number = PhoneNumber::parse($Number);
        return $Number->format(PhoneNumberFormat::E164);
    }

    /**
     * Generate protected string from a string
     * EX: example.com => ex*******om
     *
     * @param string $Phone
     * @return PhoneNumber|null
     */
    public function phoneNumber($Phone)
    {
        $Number = $this->parsePhoneNumber($Phone);
        /* Available methods on the results
        /----------------------------------------------
        | $number->getRegionCode();     => TR
        | $number->getCountryCode();    => 90
        | $number->getNationalNumber(); => 5050111222
        | $number->getNumberType();     => PhoneNumberType::FIXED_LINE | PhoneNumberType::MOBILE
        */
        return empty($Number) ? null : PhoneNumber::parse($Number);
    }

    public function randomNumber($length = 5) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * Generate initials from a name
     *
     * @param string $name
     * @return string
     */
    public function shortName(string $name) : string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        }
        return $this->makeInitialsFromSingleWord($name);
    }

    /**
     * Make initials from a word with no spaces
     *
     * @param string $name
     * @return string
     */
    protected function makeInitialsFromSingleWord(string $name) : string
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return substr(implode('', $capitals[1]), 0, 2);
        }
        return strtoupper(substr($name, 0, 2));
    }

    /**
     * generate unique timestamp using micro seconds
     *
     * @param string $name
     * @return string
     */
    public function uniqueTimeStamp($date = null, $MicroSecondsInterval = 0.1) 
    {
        usleep($MicroSecondsInterval);
        $TimeStamp = Carbon::now()->format('Y-m-d H:i:s.u');
        if (! empty($date) ) {
            $YMD       = Carbon::parse($date)->format('Y-m-d');
            $HI        = Carbon::parse($date)->format('H:i');
            $HI        = $HI == '00:00' ? Carbon::now()->format('H:i') : $HI;
            $SU        = Carbon::now()->format('s.u');
            $TimeStamp = Carbon::parse( 
                $YMD . ' ' . Carbon::parse( $HI . ':' . $SU )->format('H:i:s.u') 
            )->format('Y-m-d H:i:s.u');
        }
        return $TimeStamp;
    }

    /**
     * Format number to money
     * EX: 2430 => 2,430.00
     *
     * @param string $number
     * @param int $decimals
     * @return object
     */
    public function moneyFormat($number, $decimals = 2)
    {
        $number = (float) $number;
        return (object) [
            'abs'  => number_format(abs($number), $decimals, ".", ","),
            'real' => number_format($number, $decimals, ".", ","),
        ];
    }

    public function treeCollapse($subKey, array $data = [], $result = null)
    {
        $result = $result ? $result : collect([]);
        foreach ($data as $key => $value) {
            if (! empty($value[$subKey])) {
                $this->treeCollapse($subKey, $value[$subKey], $result);
            }
            unset($value[$subKey]);
            $result->push($value);
        }
        return $result;
    }
}