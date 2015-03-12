<?php

namespace amilna\cap;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'amilna\cap\controllers';
    public $currency = ["symbol"=>"Rp","decimal_separator"=>",","thousand_separator"=>"."];
    public $company = ["name"=>"Your Company Name","address"=>"Your company address","phone"=>"+62-21-123456","email"=>"iyo@amilna.com"];

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
