<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=finflow_db',
    'username' => 'finflow_user',
    'password' => '12345678', 
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public'
        ]
    ],
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];