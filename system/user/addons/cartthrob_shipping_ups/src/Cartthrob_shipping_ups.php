<?php

if (!defined('CARTTHROB_PATH')) {
    Cartthrob_core::core_error('No direct script access allowed');
}

use CartThrob\Plugins\Shipping\ShippingPlugin;
use Money\Money;

class Cartthrob_shipping_ups extends ShippingPlugin
{
    public $title = 'ups_live_rates';
    public $overview = 'ups_overview';

    public $html = '';

    public $settings = [
        [
            'name' => 'Set shipping to zero, if cart weight is zero',
            'note' => 'By default this plugin will assume a cart weight of at least one pound. If there is a possibility that your customers may purchase a cart full of items with zero weight (digital downloads) you may want to set this to "YES" so that they will not be charged if the entire cart weight is zero',
            'short_name' => 'no_shipping_on_zero_weight_carts',
            'type' => 'select',
            'options' => ['yes' => 'Yes', 'no' => 'No'],
            'default' => 'yes',
        ],
        [
            'name' => 'API Access Key',
            'short_name' => 'access_key',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'Username',
            'short_name' => 'username',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'Password',
            'short_name' => 'password',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'Account/Shipper Number (needed for negotiated rates)',
            'short_name' => 'shipper_number',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'Use Negotiated Rates?',
            'short_name' => 'use_negotiated_rates',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                    'y' => 'Yes',
                    'n' => 'No',
                ],
        ],
        [
            'name' => 'Test Mode?',
            'short_name' => 'test_mode',
            'default' => 'y',
            'type' => 'radio',
            'options' => [
                    'y' => 'Yes',
                    'n' => 'No',
            ],
        ],
        [
            'name' => 'Units of Measurement',
            'short_name' => 'length_code',
            'type' => 'radio',
            'default' => 'IN',
            'options' => [
                    'IN' => 'Inches / Pounds',
                    'CM' => 'Centimeters / Kilograms',
            ],
        ],
        /// DEFAULTS FOR SHIPPING OPTIONS

        [
            'name' => 'Pickup Type Default',
            'short_name' => 'rate_chart',
            'type' => 'select',
            'default' => '03',
            'options' => [
                '03' => 'ups_customer_counter',
                '19' => 'ups_letter_center',
                '06' => 'ups_one_time_pickup',
                '07' => 'ups_on_call_air',
                '01' => 'ups_regular_daily_pickup',
                '11' => 'ups_suggested_retail_rates',
                '20' => 'ups_air_service_center',
            ],
        ],
        [
            'name' => 'Packaging Type Default',
            'short_name' => 'container',
            'type' => 'select',
            'default' => '02',
            'options' => [
                '00' => 'Unknown',
                '01' => 'UPS Letter',
                '02' => 'Package',
                '03' => 'UPS Tube',
                '04' => 'UPS Pak',
                '21' => 'Express Box',
                '24' => '25KG Box',
                '25' => '10KG Box',
                '30' => 'Pallet',
                '2a' => 'Small Express Box',
                '2b' => 'Medium Express Box',
                '2c' => 'Large Express Box',
            ],
        ],
        // BUSINESS RATES ARE CHEAPER
        [
            'name' => 'Origination Type Default',
            'short_name' => 'origination_res_com',
            'type' => 'radio',
            'default' => 'RES',
            'options' => [
                'RES' => 'Residential Origination',
                'COM' => 'Commercial Origination',
            ],
        ],
        [
            'name' => 'Origination State',
            'short_name' => 'origination_state',
            'type' => 'select',
            'attributes' => [
                'class' => 'states_blank',
            ],
        ],

        [
            'name' => 'Origination Zip',
            'short_name' => 'origination_zip',
            'type' => 'text',
        ],
        [
            'name' => 'Origination Country',
            'short_name' => 'orig_country_code',
            'type' => 'select',
            'default' => 'USA',
            'attributes' => [
                'class' => 'countries_blank',
            ],
        ],
        [
            'name' => 'Delivery Type Default',
            'short_name' => 'destination_res_com',
            'type' => 'radio',
            'default' => 'RES',
            'options' => [
                'RES' => 'Residential Delivery',
                'COM' => 'Commercial Delivery',
            ],
        ],
        [
            'name' => 'Default Package Length',
            'short_name' => 'def_length',
            'type' => 'text',
            'default' => '15',
        ],
        [
            'name' => 'Default Package Width',
            'short_name' => 'def_width',
            'type' => 'text',
            'default' => '15',
        ],
        [
            'name' => 'Default Package Height',
            'short_name' => 'def_height',
            'type' => 'text',
            'default' => '15',
        ],
        // CUSTOMER CHOICES

        [
            'name' => 'Customer Selectable Rate Options',
            'short_name' => 'selectable_rates',
            'type' => 'header',
        ],
        /// DEFAULTS FOR SHIPPING OPTIONS
        [
            'name' => 'Service Default',
            'short_name' => 'product_id',
            'type' => 'select',
            'default' => '03',
            'options' => [
                '' => '--- Valid Domestic Values ---',
                '14' => 'Next Day Air Early AM',
                '01' => 'Next Day Air',
                '13' => 'Next Day Air Saver',
                '59' => '2nd Day Air AM',
                '02' => '2nd Day Air',
                '12' => '3 Day Select',
                '03' => 'Ground',
                '' => '--- Valid International Values ---',
                '11' => 'International Standard',
                '07' => 'Worldwide Express',
                '54' => 'Worldwide Express Plus',
                '08' => 'Worldwide Expidited',
                '65' => 'International Saver',
            ],
        ],
        [
            'name' => 'Next Day Air Early AM',
            'short_name' => 'c_14',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Next Day Air',
            'short_name' => 'c_01',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Next Day Air Saver',
            'short_name' => 'c_13',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => '2nd Day Air AM',
            'short_name' => 'c_59',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => '2nd Day Air',
            'short_name' => 'c_02',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => '3 Day Select',
            'short_name' => 'c_12',
            'type' => 'checkbox',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Ground',
            'short_name' => 'c_03',
            'type' => 'checkbox',
            'type' => 'radio',
            'default' => 'y',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'International Standard',
            'short_name' => 'c_11',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Worldwide Express',
            'short_name' => 'c_07',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Worldwide Express Plus',
            'short_name' => 'c_54',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Worldwide Expidited',
            'short_name' => 'c_08',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
        [
            'name' => 'Worldwide Saver',
            'short_name' => 'c_65',
            'type' => 'radio',
            'default' => 'n',
            'options' => [
                'n' => 'no',
                'y' => 'yes',
                ],
        ],
    ];

    public $required_fields = [];
    public $prefix = 'c_';
    public $shipping_methods = [
            '' => '--- Valid Domestic Values ---',
            '14' => 'Next Day Air Early AM',
            '01' => 'Next Day Air',
            '13' => 'Next Day Air Saver',
            '59' => '2nd Day Air AM',
            '02' => '2nd Day Air',
            '12' => '3 Day Select',
            '03' => 'Ground',
            '' => '--- Valid International Values ---',
            '11' => 'International Standard',
            '07' => 'WorldWide Express',
            '54' => 'WorldWide Express Plus',
            '08' => 'WorldWide Expedited',
            '65' => 'International Saver',
        ];

    /**
     * @param string $option_value
     * @return array
     */
    public function get_live_rates($option_value = 'ALL')
    {
        ee()->load->library('cartthrob_shipping_plugins');
        $this->core->cart->set_custom_data('shipping_error', '');

        $orig_state = ($this->plugin_settings('origination_state')) ? $this->plugin_settings('origination_state') : ee()->cartthrob_shipping_plugins->customer_location_defaults('state');
        $orig_zip = ($this->plugin_settings('origination_zip')) ? $this->plugin_settings('origination_zip') : ee()->cartthrob_shipping_plugins->customer_location_defaults('zip');
        $orig_country_code = ($this->plugin_settings('orig_country_code')) ? alpha2_country_code($this->plugin_settings('orig_country_code')) : alpha2_country_code(ee()->cartthrob_shipping_plugins->customer_location_defaults('country_code'));
        $orig_res_com = ($this->plugin_settings('origination_res_com') == 'RES') ? 1 : 0;
        $destination_res_com = ($this->plugin_settings('destination_res_com') == 'RES') ? 1 : 0;

        // the following variables are set, so that we can maintain this code, and CT1's code easier. setting these variables allows us to keep some of the following code in parity
        $rate_chart = $this->plugin_settings('rate_chart');
        $shipping_address = ee()->cartthrob_shipping_plugins->customer_location_defaults('address');
        $shipping_address2 = ee()->cartthrob_shipping_plugins->customer_location_defaults('address2');
        $shipping_city = ee()->cartthrob_shipping_plugins->customer_location_defaults('city');
        $shipping_state = ee()->cartthrob_shipping_plugins->customer_location_defaults('state');
        $shipping_zip = ee()->cartthrob_shipping_plugins->customer_location_defaults('zip');
        $dest_country_code = alpha2_country_code(ee()->cartthrob_shipping_plugins->customer_location_defaults('country_code'));
        $container = ee()->cartthrob_shipping_plugins->customer_location_defaults('container', $this->plugin_settings('container'));
        $dim_width = ee()->cartthrob_shipping_plugins->customer_location_defaults('width', $this->plugin_settings('def_width'));
        $dim_length = ee()->cartthrob_shipping_plugins->customer_location_defaults('length', $this->plugin_settings('def_length'));
        $dim_height = ee()->cartthrob_shipping_plugins->customer_location_defaults('height', $this->plugin_settings('def_height'));
        // set default weight
        $weight_total = ($this->core->cart->weight() ? $this->core->cart->weight() : 1);

        if ($option_value == 'ALL') {
            $product_id = $this->plugin_settings('product_id');
        } else {
            $product_id = $option_value;
        }

        $shipping = [
                'error_message' => null,
                'price' => [],
                'option_value' => [],
                'option_name' => [],
            ];

        if (!$this->plugin_settings('access_key') || !$this->plugin_settings('username') || !$this->plugin_settings('password')) {
            $shipping['error_message'] = ee()->lang->line('shipping_settings_not_configured');

            return $shipping;
        }

        $access = new SimpleXMLElement('<AccessRequest xml:lang="en-US"></AccessRequest>');

        $access->addChild('AccessLicenseNumber', $this->plugin_settings('access_key'));
        $access->addChild('UserId', $this->plugin_settings('username'));
        $access->addChild('Password', $this->plugin_settings('password'));

        $rating = new SimpleXMLElement('<RatingServiceSelectionRequest xml:lang="en-US"></RatingServiceSelectionRequest>');
        $Request = $rating->addChild('Request');
        $Request->addChild('RequestAction', 'Rate');
        $Request->addChild('RequestOption', 'Shop');
        $TransactionReference = $Request->addChild('TransactionReference');
        $TransactionReference->addChild('CustomerContext', 'Rating and Service');
        $TransactionReference->addChild('XpciVersion', '1.0');

        $PickupType = $rating->addChild('PickupType');
        $PickupType->addChild('Code', $rate_chart);

        $Shipment = $rating->addChild('Shipment');
        $Shipper = $Shipment->addChild('Shipper');
        $Shipper->addChild('ShipperNumber', $this->plugin_settings('shipper_number'));
        $Address = $Shipper->addChild('Address');
        $Address->addChild('PostalCode', $orig_zip);
        $Address->addChild('CountryCode', $orig_country_code);
        $Address->addChild('StateProvinceCode', $orig_state);
        if ($orig_res_com) {
            $Address->addChild('ResidentialAddressIndicator');
        }

        $ShipTo = $Shipment->addChild('ShipTo');
        $ToAddress = $ShipTo->addChild('Address');
        $ToAddress->addChild('AddressLine1', $shipping_address);
        $ToAddress->addChild('AddressLine2', $shipping_address2);
        $ToAddress->addChild('City', $shipping_city);
        $ToAddress->addChild('StateProvinceCode', $shipping_state);
        $ToAddress->addChild('PostalCode', $shipping_zip);
        $ToAddress->addChild('CountryCode', $dest_country_code);
        if ($destination_res_com) {
            $ToAddress->addChild('ResidentialAddressIndicator');
        }

        $ShipFrom = $Shipment->addChild('ShipFrom');
        $FromAddress = $ShipFrom->addChild('Address');
        $FromAddress->addChild('PostalCode', $orig_zip);
        $FromAddress->addChild('CountryCode', $orig_country_code);
        $FromAddress->addChild('StateProvinceCode', $orig_state);
        if ($destination_res_com) {
            $FromAddress->addChild('ResidentialAddressIndicator');
        }

        $Service = $Shipment->addChild('Service');
        $Service->addChild('Code', $product_id);

        $Package = $Shipment->addChild('Package');
        $PackagingType = $Package->addChild('PackagingType');
        $PackagingType->addChild('Code', $container);

        $Dimensions = $Package->addChild('Dimensions');
        $UnitOfMeasurement = $Dimensions->addChild('UnitOfMeasurement');
        $UnitOfMeasurement->addChild('Code', $this->plugin_settings('length_code'));
        $Dimensions->addChild('Length', $dim_length);
        $Dimensions->addChild('Width', $dim_width);
        $Dimensions->addChild('Height', $dim_height);

        $PackageWeight = $Package->addChild('PackageWeight');
        $WeightMeasurement = $PackageWeight->addChild('UnitOfMeasurement');
        $weight_code = ($this->plugin_settings('length_code') == 'IN' ? 'LBS' : 'KGS');
        $WeightMeasurement->addChild('Code', $weight_code);
        $PackageWeight->addChild('Weight', $weight_total);

        if ($this->plugin_settings('use_negotiated_rates') == 'y') {
            $RateInformation = $Shipment->addChild('RateInformation');
            $RateInformation->addChild('NegotiatedRatesIndicator');
        }

        $url = 'https://www.ups.com/ups.app/xml/Rate';

        $data = (string)$access->asXML() . (string)$rating->asXML();
        $result = new SimpleXMLElement(ee()->cartthrob_shipping_plugins->curl_transaction($url, $data));

        if (!empty($result->Response->Error->ErrorDescription)) {
            $shipping['error_message'] = (string)$result->Response->Error->ErrorDescription;
            if ($result->Response->Error->ErrorCode == '111209') {
                // @TODO convert to lang
                $shipping['error_message'] = 'Service Type Requested: ' . $product_id;
            }
            // update cart hash and shipping hash
            $this->cart_hash($shipping);
            $this->core->cart->set_custom_data('shipping_error', $shipping['error_message']);

            return $shipping;
        }

        if (isset($result->RatedShipment)) {
            $use_negotiated_rates = $this->plugin_settings('use_negotiated_rates') == 'y' ? true : false;
            foreach ($result->RatedShipment as $rating) {
                if (!empty($rating->Service->Code)) {
                    // setting all of the prices. add handling values here.
                    if ($use_negotiated_rates && !empty($rating->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue)) {
                        $shipping['price'][] = number_format((string)$rating->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue, 2, '.', ',');
                    } else {
                        $shipping['price'][] = number_format((string)$rating->TotalCharges->MonetaryValue, 2, '.', ',');
                    }
                    $shipping['option_value'][] = (string)$rating->Service->Code;
                    $shipping['option_name'][] = $this->shipping_methods((string)$rating->Service->Code);
                    $shipping['error_message'] = null;
                    $this->core->cart->set_custom_data('shipping_error', '');
                }
            }
        }

        // CHECKING THE PRESELECTED OPTIONS THAT ARE AVAILABLE
        $available_shipping = [];
        foreach ($shipping['option_value'] as $key => $value) {
            if ($this->plugin_settings('c_' . $value) != 'n') {
                $available_shipping['price'][$key] = $shipping['price'][$key];
                $available_shipping['option_value'][$key] = $shipping['option_value'][$key];
                $available_shipping['option_name'][$key] = $shipping['option_name'][$key];
            }
        }

        if ($shipping['error_message']) {
            $available_shipping['error_message'] = $shipping['error_message'];
            $this->core->cart->set_custom_data('shipping_error', $shipping['error_message']);
        }
        // update cart shipping hash
        $this->cart_hash($available_shipping);

        // @TODO update with lang
        // if there's no errors, but we removed all of the shipping options, it's because none of the values were configured on the backend. We need to warn.
        if (empty($available_shipping['error_message']) && empty($available_shipping['price']) && !empty($available_shipping)) {
            $available_shipping['error_message'] = 'Shipping options compatible with your location: (' . $shipping_address . ' ' . $shipping_address2 . ' ' . $shipping_city . ' ' . ($shipping_state ? ',' . $shipping_state : '') . ' ' . $shipping_zip . ' ' . $dest_country_code . ') have not been configured in the cart settings. Please contact the webmaster';
            if ($dest_country_code != $orig_country_code) {
                $available_shipping['error_message'] .= ' International shipping options may need to be added. ';
            }
            $this->core->cart->set_custom_data('shipping_error', $available_shipping['error_message']);
        }

        return $available_shipping;
    }

    // END

    public function shipping_methods($number = null, $prefix = null)
    {
        if (isset($this->prefix)) {
            $prefix = $this->prefix;
        }
        if ($number) {
            if (array_key_exists($number, $this->shipping_methods)) {
                return $this->shipping_methods[$number];
            } else {
                return '--';
            }
        }
        foreach ($this->shipping_methods as $key => $method) {
            if ($this->plugin_settings($prefix . $key) == 'y') {
                $available_options[$key] = $method;
            }
        }

        return $available_options;
    }

    /**
     * @return array
     */
    public function plugin_shipping_options(): array
    {
        $options = [];
        // GETTING THE RATES FROM SESSION
        $shipping_data = $this->core->cart->custom_data(ucfirst(get_class($this)));

        /*
        if (!$shipping_data)
        {
            // IF NONE ARE IN SESSION, WE WILL *TRY* TO GET RATES BASED ON CURRENT CART CONTENTS
            $shipping_data = $this->get_live_rates();
        }
        */

        $shipping_data = $this->get_live_rates();

        if (!empty($shipping_data['option_value'])) {
            foreach ($shipping_data['option_value'] as $key => $value) {
                $options[] = [
                    'rate_short_name' => $value,
                    'price' => $shipping_data['price'][$key],
                    'rate_price' => $shipping_data['price'][$key],
                    'rate_title' => $shipping_data['option_name'][$key],
                ];
            }
        }

        return $options;
    }

    /**
     * @param Cartthrob_cart $cart
     * @return Money
     */
    public function rate(Cartthrob_cart $cart): Money
    {
        $cart_hash = $this->core->cart->custom_data('cart_hash');

        $this->cart_hash();

        if ($this->core->cart->weight() <= 0) {
            // perhaps you have all digital items
            if ($this->plugin_settings('no_shipping_on_zero_weight_carts')) {
                return ee('cartthrob:MoneyService')->fresh();
            }
        }

        if ($this->core->cart->count() <= 0 || $this->core->cart->shippable_subtotal() <= 0) {
            return ee('cartthrob:MoneyService')->fresh();
        }

        if ($cart_hash != md5(serialize($this->core->cart->items_array()))) {
            return ee('cartthrob:MoneyService')->fresh();
        }

        $shipping_data = $this->core->cart->custom_data(ucfirst(get_class($this)));

        if (!$this->core->cart->shipping_info('shipping_option')) {
            $temp_key = false;

            // if no option has been set, we'll get the cheapest option, and set that as the customer's shipping option.
            if (!empty($shipping_data['price'])) {
                // this looks weird, but we're trying to get the key. we have to find the min value, then pull the key from that.
                $temp_key = array_search(min($shipping_data['price']), $shipping_data['price']);
            }
            if ($temp_key !== false && !empty($shipping_data['option_value'][$temp_key])) {
                $this->shipping_option = $shipping_data['option_value'][$temp_key];
                $this->core->cart->set_shipping_info('shipping_option', $shipping_data['option_value'][$temp_key]);
            } else {
                $this->shipping_option = $this->plugin_settings('product_id');
                $this->core->cart->set_shipping_info('shipping_option', $this->plugin_settings('product_id'));
            }
        } else {
            $this->shipping_option = $this->core->cart->shipping_info('shipping_option');
        }

        if (!empty($shipping_data['option_value']) && !empty($shipping_data['price'])) {
            if ($this->shipping_option && in_array($this->shipping_option, $shipping_data['option_value'])) {
                $key = array_pop(array_keys($shipping_data['option_value'], $this->shipping_option));
                if (!empty($shipping_data['price'][$key])) {
                    return ee('cartthrob:MoneyService')->toMoney($shipping_data['price'][$key]);
                }
            } elseif (!$this->shipping_option) {
                return ee('cartthrob:MoneyService')->fresh();
            } else {
                return ee('cartthrob:MoneyService')->toMoney(min($shipping_data['price']));
            }
        }

        return ee('cartthrob:MoneyService')->fresh();
    }

    /**
     * @param null $shipping
     * @return string
     */
    public function cart_hash($shipping = null)
    {
        // hashing the cart data, so we can check later if the cart has been updated
        $cart_hash = md5(serialize($this->core->cart->items_array()));
        $this->core->cart->set_custom_data('cart_hash', $cart_hash);

        if ($shipping) {
            $this->core->cart->set_custom_data(ucfirst(get_class($this)), $shipping);
        }

        $this->core->cart->save();

        return $cart_hash;
    }
}
