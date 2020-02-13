<p align="center">
    <h1 align="center">Json schema generator</h1>
    <br>
</p>

Aplication contains several console commands which generate [json schema](https://json-schema.org/) and open API (po [OAS 3](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md) ) docs.


CONFIGURATION
-------------

[PHP](http://php.net/downloads.php) (> 5.4.0) and [Composer](https://getcomposer.org/) are required to have.
From base directory, using CLI call command
```shell
composer update
```

### Base

Edit file `config/db.php` with real data for db connection:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=test',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTE:**
- App will not create database, it should exist beforehand.



### Modul

Edit file `config/console.php` if the default configuration is not appliable:

```php
    $config['modules']['generator'] = [
        'class' => 'app\modules\ConsoleModule',

        // Properties can be changed here
        //'allowedIPs' => ['127.0.0.1', '::1'],
        //'newFileMode' => '0666';
        //'newDirMode' => '0777';
        //'newFileMode' => '0666';    ];
    ];
```

**NOTE:**
-Change only commented properties, not the class.


### HOW TO USE
-------------

Call console commands with  `php yii` from base directory of project.


```shell
~/www/schema-generator$ php yii generator/jsonschema --tableName=test --modelClass=Test --httpMethod=Post
```

**generator/jsonschema:**

* --tableName : correct name of database table
* --modelClass : name which will be used to generate name of the file
* --httpMethod: http method to generate the json schema for

**generator/swagger:**

* --tableName : correct name of database table
* --tableNamePlural : correct name of database table



Files will be generated in appropriate directories in /commands/generated directory.
