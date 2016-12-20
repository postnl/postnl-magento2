<?php
return array (
  'backend' =>
  array (
    'frontName' => 'admin',
  ),
  'install' =>
  array (
    'date' => 'Mon, 01 Feb 2016 15:21:04 +0000',
  ),
  'crypt' =>
  array (
    'key' => 'ef0e78a6c44764690780138ce89b7791',
  ),
  'session' =>
  array (
    'save' => 'db',
  ),
  'db' =>
  array (
    'table_prefix' => '',
    'connection' =>
    array (
      'default' =>
      array (
        'host' => 'MAGENTO_DB_HOST',
        'dbname' => 'MAGENTO_DB_NAME',
        'username' => 'MAGENTO_DB_USER',
        'password' => 'MAGENTO_DB_PASS',
        'active' => '1',
      ),
    ),
  ),
  'resource' =>
  array (
    'default_setup' =>
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'default',
);
