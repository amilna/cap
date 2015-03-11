CAP
===
Company Accounting Plugin, very simple business process

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Since this package do not have stable release on packagist, you should use these settings in your composer.json file :

```
"minimum-stability": "dev",
"prefer-stable": true,
```
After, either run

```
php composer.phar require --prefer-dist amilna/yii2-cap "dev-master"
```

or add

```
"amilna/yii2-cap": "dev-master"
```

to the require section of your `composer.json` file.

run migration for database

```
./yii migrate --migrationPath=@amilna/cap/migrations
```

add in modules section of main config

```
	'gridview' =>  [
		'class' => 'kartik\grid\Module',
	],
	'cap' => [
		'class' => 'amilna\cap\Module',
		'currency' => ["symbol"=>"Rp","decimal_separator"=>",","thousand_separator"=>"."],
	],
```

Usage
-----

Once the extension is installed, check the url:
[your application base url]/index.php/cap
