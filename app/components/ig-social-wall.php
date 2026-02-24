<?php

/**
 * @author:DerN3rd
 */
if (!class_exists('IG_Social_Wall')) {
    class IG_Social_Wall
    {
        private static $_instance;

        public $plugin_url;
        public $plugin_path;
        public $domain;
        public $prefix;

        private $controller;

        private function __construct()
        {
            //variables init
            $this->plugin_url = plugin_dir_url(__FILE__) . 'ig-social-wall/';
            $this->plugin_path = plugin_dir_path(__FILE__) . 'ig-social-wall/';
            $this->domain = 'jbp';
            $this->prefix = 'icw';

            spl_autoload_register(array(&$this, 'loader'));

            add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
            add_action('admin_enqueue_scripts', array(&$this, 'scripts'));

            $this->controller = new Social_Wall_Controller();
        }

        function scripts()
        {
            wp_register_style('jbp-social', $this->plugin_url . 'assets/style.css');
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

        public function display($model, $attribute, $element)
        {
            $this->controller->display($model, $attribute, $element);
        }

        public function show_front($model, $attribute)
        {
            $this->controller->show_front($model, $attribute);
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
                self::$_instance = new IG_Social_Wall();
            }
            return self::$_instance;
        }

        function get($key, $default = NULL)
        {
            $value = isset($_GET[$key]) ? $_GET[$key] : $default;
            return apply_filters($this->prefix . 'query_get_' . $key, $value);
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
            return apply_filters($this->prefix . 'query_get_' . $key, $value);
        }

        function can_compress()
        {
            //getting basepath, because this component always inside the components folder,just need to back to root
            $root_path = dirname(dirname(dirname($this->plugin_path)));
            $runtime_path = $root_path . '/framework/runtime';

            if (!is_dir($runtime_path)) {
                //try to create
                mkdir($runtime_path);
            }
            if (!is_dir($runtime_path))
                return false;
            $use_compress = false;
            if (!is_writeable($runtime_path)) {
                chmod($runtime_path, 775);
            }
            if (is_writeable($runtime_path)) {
                $use_compress = $runtime_path;;
            }
            return $use_compress;
        }

        function compress_assets( $write_path, $css = array(), $js = array() )
        {
            if (defined('DOING_AJAX') && DOING_AJAX)
                return;

            $css_write_path = $write_path . '/' . implode('-', $css) . '.css';
            $css_cache = get_option('iguploader_style_last_cache');
            if ($css_cache && file_exists($css_write_path) && strtotime('+1 hour', $css_cache) < time()) {
                //remove cache
                unlink($css_write_path);
            }
            $js_write_path = $write_path . '/' . implode('-', $js) . '.js';
            if (!file_exists($css_write_path)) {
                global $wp_styles;
                $css_paths = array();
                //loop twice, position is important
                foreach ($css as $c) {
                    foreach ($wp_styles->registered as $style) {
                        if ($style->handle == $c) {
                            $css_paths[] = $style->src;
                        }
                    }
                }
                //started
                $css_strings = '';
                foreach ($css_paths as $path) {
                    //path is an url, we need to changeed it to local
                    $path = str_replace($this->plugin_url, $this->plugin_path, $path);
                    $css_strings = $css_strings . PHP_EOL . file_get_contents($path);
                }

                file_put_contents($css_write_path, trim($css_strings));
                update_option('iguploader_style_last_cache', time());
            }
            $css_write_path = str_replace($this->plugin_path, $this->plugin_url, $css_write_path);
            wp_enqueue_style(implode('-', $css), $css_write_path);

            $js_cache = get_option('mm_script_last_cache');
            if ($js_cache && file_exists($js_write_path) && strtotime('+1 hour', $js_cache) < time()) {
                //remove cache
                unlink($js_write_path);
            }
            if (!file_exists($js_write_path)) {
                global $wp_scripts;
                $js_paths = array();
                //js
                foreach ($js as $j) {
                    foreach ($wp_scripts->registered as $script) {
                        if ($script->handle == $j) {
                            $js_paths[] = $script->src;
                        }
                    }
                }
                $js_strings = '';
                foreach ($js_paths as $path) {
                    //path is an url, we need to changeed it to local
                    $path = str_replace($this->plugin_url, $this->plugin_path, $path);
                    if (file_exists($path)) {
                        $js_strings = $js_strings . PHP_EOL . file_get_contents($path);
                    }
                }

                file_put_contents($js_write_path, trim($js_strings));
                update_option('mm_script_last_cache', time());
            }
            $js_write_path = str_replace($this->plugin_path, $this->plugin_url, $js_write_path);
            wp_enqueue_script(implode('-', $js), $js_write_path);

        }

        public function get_social_list()
        {
            $social_list = array(
                'blogger' =>
                    array(
                        'key' => 'blogger',
                        'name' => 'Blogger',
                        'domain' => 'blogger.com',
                        'type' => 'url',
                        'url' => $this->plugin_url . 'assets/social_icon/blogger.png',
                    ),
                'deviantart' =>
                    array(
                        'key' => 'deviantart',
                        'name' => 'Deviantart',
                        'domain' => 'deviantart.com',
                        'type' => 'url',
                        'url' => $this->plugin_url . 'assets/social_icon/deviantart.png',
                    ),
                'dribble' =>
                    array(
                        'key' => 'dribble',
                        'name' => 'Dribbble',
                        'type' => 'url',
                        'domain' => 'dribbble.com',
                        'url' => $this->plugin_url . 'assets/social_icon/dribble.png',
                    ),
                'discord' =>
                    array(
                        'key' => 'discord',
                        'name' => 'Discord',
                        'type' => 'text',
                        'url' => $this->plugin_url . 'assets/social_icon/discord.png',
                    ),
                'email' =>
                    array(
                        'key' => 'email',
                        'name' => 'Email',
                        'type' => 'email',
                        'url' => $this->plugin_url . 'assets/social_icon/email.png',
                    ),
                'fb' =>
                    array(
                        'key' => 'fb',
                        'name' => 'Facebook',
                        'type' => 'url',
                        'domain' => 'facebook.com',
                        'url' => $this->plugin_url . 'assets/social_icon/fb.png',
                    ),
                'flickr' =>
                    array(
                        'key' => 'flickr',
                        'name' => 'Flickr',
                        'type' => 'url',
                        'domain' => 'flickr.com',
                        'url' => $this->plugin_url . 'assets/social_icon/flickr.png',
                    ),
                'github' =>
                    array(
                        'key' => 'github',
                        'name' => 'GitHub',
                        'type' => 'url',
                        'domain' => 'github.com',
                        'url' => $this->plugin_url . 'assets/social_icon/github.png',
                    ),
                'instagram' =>
                    array(
                        'key' => 'instagram',
                        'name' => 'Instagram',
                        'type' => 'url',
                        'domain' => 'instagram.com',
                        'url' => $this->plugin_url . 'assets/social_icon/instagram.png',
                    ),
                'linkedin' =>
                    array(
                        'key' => 'linkedin',
                        'name' => 'LinkedIn',
                        'domain' => 'linkedin.com',
                        'type' => 'url',
                        'url' => $this->plugin_url . 'assets/social_icon/linkedin.png',
                    ),
                'pinterest' =>
                    array(
                        'key' => 'pinterest',
                        'name' => 'Pinterest',
                        'type' => 'url',
                        'domain' => 'pinterest.com',
                        'url' => $this->plugin_url . 'assets/social_icon/pinterest.png',
                    ),
                'reddit' =>
                    array(
                        'key' => 'reddit',
                        'name' => 'Reddit',
                        'type' => 'url',
                        'domain' => 'reddit.com',
                        'url' => $this->plugin_url . 'assets/social_icon/reddit.png',
                    ),
                'rss' =>
                    array(
                        'key' => 'rss',
                        'name' => 'RSS',
                        'type' => 'url',
                        'url' => $this->plugin_url . 'assets/social_icon/rss.png',
                    ),
                'skype' =>
                    array(
                        'key' => 'skype',
                        'name' => 'Skype',
                        'type' => 'text',
                        'url' => $this->plugin_url . 'assets/social_icon/skype.png',
                    ),
                'tumblr' =>
                    array(
                        'key' => 'tumblr',
                        'name' => 'Tumblr',
                        'type' => 'url',
                        'domain' => 'tumblr.com',
                        'url' => $this->plugin_url . 'assets/social_icon/tumblr.png',
                    ),
                'tiktok' =>
                    array(
                        'key' => 'tiktok',
                        'name' => 'TikTok',
                        'type' => 'url',
                        'domain' => 'tiktok.com',
                        'url' => $this->plugin_url . 'assets/social_icon/tiktok.png',
                    ),
                'twitch' =>
                    array(
                        'key' => 'twitch',
                        'name' => 'Twitch',
                        'type' => 'url',
                        'domain' => 'twitch.tv',
                        'url' => $this->plugin_url . 'assets/social_icon/twitch.png',
                    ),
                'twitter' =>
                    array(
                        'key' => 'twitter',
                        'name' => 'Twitter/X',
                        'type' => 'url',
                        'domain' => 'twitter.com',
                        'url' => $this->plugin_url . 'assets/social_icon/twitter.png',
                    ),
                'wordpress' =>
                    array(
                        'key' => 'wordpress',
                        'name' => 'WordPress',
                        'type' => 'url',
                        'url' => $this->plugin_url . 'assets/social_icon/wordpress.png',
                    ),
                'youtube' =>
                    array(
                        'key' => 'youtube',
                        'name' => 'YouTube',
                        'type' => 'url',
                        'domain' => 'youtube.com',
                        'url' => $this->plugin_url . 'assets/social_icon/youtube.png',
                    ),
            );
            return apply_filters('get_social_list', $social_list);
        }

        public function social($key)
        {
            $list = $this->get_social_list();
            return $list[$key];
        }

    }

    function ig_social_wall()
    {
        return IG_Social_Wall::get_instance();
    }

    ig_social_wall();
}