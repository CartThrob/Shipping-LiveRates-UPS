<?php

require_once __DIR__ . '/vendor/autoload.php';

define('CARTTHROB_SHIPPING_UPS_NAME', 'CartThrob UPS Shipping');
define('CARTTHROB_SHIPPING_UPS_VERSION', '1.0.0');
define('CARTTHROB_SHIPPING_UPS_DESC', 'CartThrob UPS Shipping Integration');
define('CARTTHROB_SHIPPING_UPS_SETTINGS_EXIST', false);

return [
    'author' => 'Foster Made',
    'author_url' => 'https://cartthrob.com',
    'docs_url' => '',
    'name' => CARTTHROB_SHIPPING_UPS_NAME,
    'description' => CARTTHROB_SHIPPING_UPS_DESC,
    'version' => CARTTHROB_SHIPPING_UPS_VERSION,
    'namespace' => 'CartThrob\ShippingUps',
    'settings_exist' => CARTTHROB_SHIPPING_UPS_SETTINGS_EXIST,
];
