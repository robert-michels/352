<?php
namespace Util\HTML;

/**
 * Class Tags
 * @package Util\HTML
 *
 * Heavily inspired by this StackOverflow answer: https://stackoverflow.com/a/3488714
 */
class Tags {

    public static function tag($tag, $text, $attributes = array()){
        return "<$tag" . (new self)->attributesToArray($attributes) . ">$text</$tag>";
    }

    private function attributesToArray($attributes = array()){
        $a = '';
        foreach ($attributes as $attribute => $value) {
            $a .= " $attribute='$value'";
        }
        return $a;
    }
}