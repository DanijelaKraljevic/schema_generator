<?php

return
    // [
    //     'class' => 'yii\db\Connection',
    //     'dsn' => 'mysql:host=localhost;dbname=test',
    //     'username' => 'root',
    //     'password' => '',
    //     'charset' => 'utf8',
    // ];

    // For postgresql use next configuration:


    [
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:port=5432;dbname=test',
        'username' => 'postgres',
        'password' => '',
        'charset' => 'utf8',
    ];