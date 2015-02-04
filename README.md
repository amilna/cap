CAP
===
Company Accounting Plugin

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist amilna/yii2-cap "*"
```

or add

```
"amilna/yii2-cap": "*"
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
	],
```

Usage
-----

Once the extension is installed, check the url:
[your application base url]/index.php/cap
