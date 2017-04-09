<?php

namespace App\Traits;

trait BilletParsing
{
    protected function parseTextFromMarkdown($text, array $var=null)
    {
        $return = markdown($text);

        return $this->parseText($return, $var);
    }

    protected function parseText($text, array $var=null)
    {
        $return = $text;
        if(!is_null($var))
        {
            foreach ($var as $name=>$value)
            {
                $return = str_replace('%'.strtoupper($name).'%', $value, $return);
            }
        }
        return $return;
    }
}