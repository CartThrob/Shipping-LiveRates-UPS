<?php

class Cartthrob_shipping_ups_ext
{
    public $name = CARTTHROB_SHIPPING_UPS_NAME;
    public $version = CARTTHROB_SHIPPING_UPS_VERSION;
    public $description = CARTTHROB_SHIPPING_UPS_DESC;
    public $settings_exist = CARTTHROB_SHIPPING_UPS_SETTINGS_EXIST;
    public $docs_url = '';

    public $settings = [];

    /**
     * Cartthrob_shipping_ups_ext constructor.
     * @param string $settings
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }

    /**
     * Register the shipping plugin with the plugin service
     */
    public function cartthrob_boot()
    {
        ee()->lang->load('cartthrob_shipping_ups', $idiom = '', $return = false, $add_suffix = true, $alt_path = __DIR__ . '/');

        ee('cartthrob:PluginService')->register(Cartthrob_shipping_ups::class);
    }

    public function activate_extension()
    {
        ee()->db->insert('extensions', [
            'class' => __CLASS__,
            'method' => 'cartthrob_boot',
            'hook' => 'cartthrob_boot',
            'settings' => serialize($this->settings),
            'priority' => 10,
            'version' => $this->version,
            'enabled' => 'y',
        ]);
    }

    /**
     * @param string $current
     * @return bool
     */
    public function update_extension($current = '')
    {
        if ($current == '' or $current == $this->version) {
            return false;
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update('extensions', ['version' => $this->version]);
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }
}
