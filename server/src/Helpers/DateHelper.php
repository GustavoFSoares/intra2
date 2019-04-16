<?php
namespace Helper;

class DateHelper
{
    public static $date;
    
    public static function initialize($month = null, $year = null) {
        if(!self::$date) {
            if($month) {
                self::$date = \DateTime::createFromFormat('m', $month);
            } else {
                self::$date = new \DateTime();
            }
        }
        return self::$date;
    }
    
    public static function getFirstDate($month = null) {
        self::initialize($month);
        $date = [
            'year' => self::getYear(),
            'month' => self::getMonth(),
            'day' => '01',
        ];
        return implode('/', $date);
    }

    public static function getLastDate($month = null) {
        self::initialize($month);
        $date = [
            'year' => self::getYear(),
            'month' => self::getMonth(),
            'day' => self::$date->format('t'),
        ];
        return implode('/', $date);
    }

    public static function getDay() {
        self::initialize();
        return self::$date->format('d');
    }

    public static function getMonth() {
        self::initialize();
        return self::$date->format('m');
    }

    public static function getYear() {
        self::initialize();
        return self::$date->format('Y');
    }


}
