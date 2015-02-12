<?php

namespace amilna\cap;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'amilna\cap\controllers';
    public $currency = ["symbol"=>"Rp","decimal_separator"=>",","thousand_separator"=>"."];

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
