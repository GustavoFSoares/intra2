<?php
namespace Helper;

use Cocur\Slugify\Slugify;

class SlugHelper {

    public static $slug;
    public static $letter =  [
        '.' => '', 'º' => '',
        'Ç' => 'c', 'ç' => 'c'
    ];

    public static function initializer(){
        self::$slug = new Slugify();
        self::$slug->activateRuleset('portuguese-brazil');
        self::$slug->addRules(self::$letter);
    }

    public static function get($var){
        self::initializer();
        return self::$slug->slugify($var);
    }
    
    public static function removeSpaces($var){
        return preg_replace('/\s.\s./', '', $var);
    }

}