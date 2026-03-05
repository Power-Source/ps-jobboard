<?php
/**
 * @author:DerN3rd
 */

if (!class_exists('IG_Wallet')) {
    class IG_Wallet
    {
        private static $_instance;

        public $plugin_url;
        public $plugin_path;
        public $domain;
        public $prefix;

        public $global;

        public $controller;

        private function __construct()
        {
            //variables init
            $this->plugin_url = plugin_dir_url(__FILE__) . 'ig-wallet/';
            $this->plugin_path = plugin_dir_path(__FILE__) . 'ig-wallet/';
            $this->domain = 'psjb';
            $this->prefix = 'iwl_';

            spl_autoload_register(array(&$this, 'loader'));

            add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_menu', array(&$this, 'admin_menu'));
            add_action('admin_init', array(&$this, 'redirect_legacy_urls'));
            //add_filter('menu_order', array(&$this, 'menu_order'));
            //$this->custom_post_type();
            $this->controller = new Credit_Plan_Controller();
        }

        function admin_menu()
        {
            $parent_slug = 'je-jobboard';

        // Integration in Jobboard-Einstellungen wenn Jobboard-Menü existiert
        if (isset($GLOBALS['admin_page_hooks'][$parent_slug])) {
            // Statt separaten Menü-Seiten: Integration über Hooks in das Settings-System
            add_action('jbp_setting_menu', array(&$this, 'wallet_menu_tabs'));
            add_action('je_settings_content_wallet_plans', array($this->controller, 'main'));
            add_action('je_settings_content_wallet_rules', array($this->controller, 'rules'));
            add_action('je_settings_content_wallet_settings', array($this->controller, 'settings'));
            return;
        }

        // Fallback: Separates Menü nur wenn Jobboard-Menü nicht existiert
        add_menu_page(__('Guthabenpakete', $this->domain), __('Guthabenpakete', $this->domain), 'manage_options', 'ig-credit-plans', array($this->controller, 'main'), 'dashicons-products', 35);
        add_submenu_page('ig-credit-plans', __("Regeln", $this->domain), __("Regeln", $this->domain), 'manage_options', 'ig-credit-rules', array($this->controller, 'rules'));
        add_submenu_page('ig-credit-plans', __("Einstellungen", $this->domain), __("Einstellungen", $this->domain), 'manage_options', 'ig-credits-setting', array($this->controller, 'settings'));
        //add_submenu_page('ig-credit-plans', __("Getting Start", $this->domain), __("Getting Start", $this->domain), 'manage_options', 'ig-credit-getting-start', array($this->controller, 'getting_start'));
    }

    /**
     * Fügt Wallet-Tabs zum Jobboard-Einstellungen-Menü hinzu
     */
    function wallet_menu_tabs()
    {
        ?>
        <li <?php echo je()->get('tab') == 'wallet_plans' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('admin.php?page=je-jobboard-settings&tab=wallet_plans') ?>">
                <i class="dashicons dashicons-products"></i> <?php _e('Guthabenpakete', $this->domain) ?>
            </a>
        </li>
        <li <?php echo je()->get('tab') == 'wallet_rules' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('admin.php?page=je-jobboard-settings&tab=wallet_rules') ?>">
                <i class="dashicons dashicons-admin-settings"></i> <?php _e('Guthaben-Regeln', $this->domain) ?>
            </a>
        </li>
        <li <?php echo je()->get('tab') == 'wallet_settings' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('admin.php?page=je-jobboard-settings&tab=wallet_settings') ?>">
                <i class="dashicons dashicons-admin-generic"></i> <?php _e('Wallet-Einstellungen', $this->domain) ?>
            </a>
        </li>
        <?php
    }

    /**
     * Leitet alte Wallet-URLs zu den neuen integrierten Tabs um (Backwards Compatibility)
     */
    function redirect_legacy_urls()
    {
        if (!is_admin()) {
            return;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : '';
        
        // Redirect alte URLs zu neuen Tab-URLs
        if ($page == 'ig-credit-plans') {
            wp_redirect(admin_url('admin.php?page=je-jobboard-settings&tab=wallet_plans'));
            exit;
        }
        if ($page == 'ig-credit-rules') {
            wp_redirect(admin_url('admin.php?page=je-jobboard-settings&tab=wallet_rules'));
            exit;
        }
        if ($page == 'ig-credits-setting') {
            wp_redirect(admin_url('admin.php?page=je-jobboard-settings&tab=wallet_settings'));
            exit;
        }
    }

    function menu_order($menu_order)
    {
        global $submenu;

        if (isset($submenu['ig-credit-plans'])) {
            $nav = $submenu['ig-credit-plans'];
            //get the last
            $start = array_pop($nav);
            $nav = array_merge(array($start), $nav);
            $submenu['ig-credit-plans'] = $nav;
        }

        return $menu_order;
    }

        function custom_post_type()
        {
            $labels = array(
                'name' => __('Guthabenpaket', $this->domain),
                'singular_name' => __('Guthabenpaket', $this->domain),
                'menu_name' => __('Guthabenpakete', $this->domain),
                'parent_item_colon' => __('Eltern Artikel:', $this->domain),
                'all_items' => __('Alle Artikel', $this->domain),
                'view_item' => __('Artikel anzeigen', $this->domain),
                'add_new_item' => __('Neuen Artikel hinzufügen', $this->domain),
                'add_new' => __('Neue hinzufügen', $this->domain),
                'edit_item' => __('Artikel bearbeiten', $this->domain),
                'update_item' => __('Artikel aktualisieren', $this->domain),
                'search_items' => __('Artikel suchen', $this->domain),
                'not_found' => __('Nicht gefunden', $this->domain),
                'not_found_in_trash' => __('Nicht im Papierkorb gefunden', $this->domain),
            );
            $args = array(
                'label' => __('ig_credit_plan', $this->domain),
                'labels' => $labels,
                'supports' => array(
                    'title', //'custom-fields'
                ),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_admin_bar' => true,
                //'menu_position' => 5,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'page',
                'menu_icon' => 'dashicons-products',
                'register_meta_box_cb' => array(new Credit_Plan_Controller(), 'metabox')
            );
            register_post_type('ig_credit_plan', $args);
        }

        function scripts()
        {

        }

        function loader($class)
        {
            $filename = str_replace('_', '-', strtolower($class)) . '.php';
            if (strstr($filename, '-controller.php')) {
                //looking in the controllers folder and sub folders to get this class
                $files = $this->listFolderFiles($this->plugin_path . 'app/controllers');
                foreach ($files as $file) {
                    if (strcmp($filename, pathinfo($file, PATHINFO_BASENAME)) === 0) {
                        include_once $file;
                        break;
                    }
                }
            } elseif (strstr($filename, '-model.php')) {
                $files = $this->listFolderFiles($this->plugin_path . 'app/models');

                foreach ($files as $file) {
                    if (strcmp($filename, pathinfo($file, PATHINFO_BASENAME)) === 0) {
                        include_once $file;
                        break;
                    }
                }
            } else {
                //include normal file inside app folder
                if (file_exists($this->plugin_path . 'app/' . $filename)) {
                    include_once $this->plugin_path . 'app/' . $filename;
                }
            }
        }

        function listFolderFiles($dir)
        {
            $ffs = scandir($dir);
            $i = 0;
            $list = array();
            foreach ($ffs as $ff) {
                if ($ff != '.' && $ff != '..') {
                    if (strlen($ff) >= 5) {
                        if (substr($ff, -4) == '.php') {
                            $list[] = $dir . '/' . $ff;
                        }
                    }
                    if (is_dir($dir . '/' . $ff))
                        $list = array_merge($list, $this->listFolderFiles($dir . '/' . $ff));
                }
            }
            return $list;
        }

        static function get_instance()
        {
            if (!is_object(self::$_instance)) {
                self::$_instance = new IG_Wallet();
            }
            return self::$_instance;
        }

        function get($key, $default = NULL)
        {
            $value = isset($_GET[$key]) ? $_GET[$key] : $default;
            return apply_filters('igu_wallet_get_' . $key, $value);
        }

        function post($key, $default = NULL)
        {
            $array_dereference = NULL;
            if (strpos($key, '[')) {
                $bracket_pos = strpos($key, '[');
                $array_dereference = substr($key, $bracket_pos);
                $key = substr($key, 0, $bracket_pos);
            }
            $value = isset($_POST[$key]) ? $_POST[$key] : $default;
            if ($array_dereference) {
                preg_match_all('#(?<=\[)[^\[\]]+(?=\])#', $array_dereference, $array_keys, PREG_SET_ORDER);
                $array_keys = array_map('current', $array_keys);
                foreach ($array_keys as $array_key) {
                    if (!is_array($value) || !isset($value[$array_key])) {
                        $value = $default;
                        break;
                    }
                    $value = $value[$array_key];
                }
            }
            return apply_filters('igu_wallet_get_' . $key, $value);
        }

        function settings()
        {
            return new Credit_Plan_Settings_Model();
        }
    }

    function ig_wallet()
    {
        return IG_Wallet::get_instance();
    }

    ig_wallet();
}