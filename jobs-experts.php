<?php
/**
 * Plugin Name: PS-Jobboard
 * Plugin URI: https://cp-psource.github.io/ps-jobboard/
 * Description: Bringe Menschen mit Projekten und Branchenfachleute zusammen - es ist mehr als eine durchschnittliche Jobbörse.
 * Version: 1.0.2
 * Author: PSOURCE
 * Author URI: https://nerdservice.eimen.net
 * ClassicPress: 2.6.0
 * Text Domain: psjb
 * Domain Path: languages
 * Network: false
 * License: GPLv2 or later
 */

// Core bootstrap
require_once( dirname( __FILE__ ) . '/framework/loader.php' );
require_once( dirname( __FILE__ ) . '/Helper.php' );

// Main class modules
require_once( dirname( __FILE__ ) . '/app/components/je-utils.php' );
require_once( dirname( __FILE__ ) . '/app/components/je-assets-module.php' );
require_once( dirname( __FILE__ ) . '/app/components/je-bootstrap-module.php' );
require_once( dirname( __FILE__ ) . '/app/components/je-permissions-module.php' );
require_once( dirname( __FILE__ ) . '/app/components/je-loader-module.php' );

//add action to load language
/*add_action( 'plugins_loaded', 'jbp_load_languages' );
function jbp_load_languages() {
	load_plugin_textdomain( 'psjb', false, plugin_basename( je()->plugin_path . 'languages/' ) );
}*/

// Third-party
if ( ! class_exists( 'SmartDOMDocument' ) ) {
	include_once( dirname( __FILE__ ) . '/vendors/SmartDOMDocument.class.php' );
}

class Jobs_Experts {
	use JE_Assets_Module;
	use JE_Bootstrap_Module;
	use JE_Permissions_Module;
	use JE_Loader_Module;

	public $plugin_url;
	public $plugin_path;
	public $domain;
	public $prefix;

	public $version = "1.0.2";
	public $db_version = "1.0";

	public $global = array();

	private static $_instance;

	private $dev = false;

	/**
	 * @vars
	 * Short hand for pages factory
	 */
	public $pages;

	private function __construct() {
		//variables init
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->domain      = 'psjb';
		$this->prefix      = 'je_';
		//load the framework
		//autoload
		spl_autoload_register( array( &$this, 'autoload' ) );

		//enqueue scripts, use it here so both frontend and backend can use
		add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );

		add_action( 'init', array( &$this, 'dispatch' ) );
		add_action( 'widgets_init', array( &$this, 'init_widget' ) );

		add_action( 'wp_trash_post', array( &$this, 'delete_je_cache' ) );
		add_action( 'save_post', array( &$this, 'delete_je_cache' ) );
		add_action( 'delete_post', array( &$this, 'delete_je_cache' ) );

		$this->upgrade();
		//
		$this->load_addons();
	}

	function upgrade() {
		$vs = get_option( $this->prefix . 'db_version' );
		if ( $vs == false || $vs != $this->version ) {
			global $wpdb;
			$sql = "UPDATE " . $wpdb->posts . " SET post_type='iup_media' WHERE post_type='jbp_media';";
			$wpdb->query( $sql );
			update_option( $this->prefix . 'db_version', $this->db_version );
		}
	}

	function delete_je_cache( $post_id ) {

		if ( get_post_type( $post_id ) != 'jbp_job' && get_post_type( $post_id ) != 'jbp_pro' ) {
			return;
		}

		global $wpdb;
		$query = $wpdb->prepare( "DELETE from `{$wpdb->options}` WHERE option_name LIKE %s;", '%' . $wpdb->esc_like( JE_Job_Model::model()->cache_prefix() ) . '%' );
		$wpdb->query( $query );
		// Also clear dashboard cache
		delete_transient( 'jbp_dashboard_stats' );
	}

	function date_format_php_to_js( $sFormat ) {
		switch ( $sFormat ) {
			//Predefined WP date formats
			case 'F j, Y':
				return ( 'MM dd, yy' );
				break;
			case 'Y/m/d':
				return ( 'yy/mm/dd' );
				break;
			case 'm/d/Y':
				return ( 'mm/dd/yy' );
				break;
			case 'd/m/Y':
				return ( 'dd/mm/yy' );
				break;
		}
	}

	function strip_array_indices( $ArrayToStrip ) {
		$NewArray = array();
		foreach ( $ArrayToStrip as $objArrayItem ) {
			$NewArray[] = $objArrayItem;
		}

		return ( $NewArray );
	}

	function compress_assets( $write_path, $css = array(), $js = array() ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$css_write_path = $write_path . '/' . implode( '-', $css ) . '.css';
		$css_cache      = get_option( $this->prefix . 'style_last_cache' );
		if ( $css_cache && file_exists( $css_write_path ) && strtotime( '+1 hour', $css_cache ) < time() ) {
			//remove cache
			unlink( $css_write_path );
		}
		$js_write_path = $write_path . '/' . implode( '-', $js ) . '.js';
		if ( ! file_exists( $css_write_path ) ) {
			global $wp_styles;
			$css_paths = array();
			//loop twice, position is important
			foreach ( $css as $c ) {
				foreach ( $wp_styles->registered as $style ) {
					if ( $style->handle == $c ) {
						$css_paths[] = $style->src;
					}
				}
			}
			//started
			$css_strings = '';
			foreach ( $css_paths as $path ) {
				//path is an url, we need to changeed it to local
				$path        = str_replace( $this->plugin_url, $this->plugin_path, $path );
				$css_strings = $css_strings . PHP_EOL . file_get_contents( $path );
			}

			file_put_contents( $css_write_path, trim( $css_strings ) );
			update_option( $this->prefix . 'style_last_cache', time() );
		}
		$css_write_path = str_replace( $this->plugin_path, $this->plugin_url, $css_write_path );
		wp_enqueue_style( implode( '-', $css ), $css_write_path );

		$js_cache = get_option( $this->prefix . 'script_last_cache' );
		if ( $js_cache && file_exists( $js_write_path ) && strtotime( '+1 hour', $js_cache ) < time() ) {
			//remove cache
			unlink( $js_write_path );
		}
		if ( ! file_exists( $js_write_path ) ) {
			global $wp_scripts;
			$js_paths = array();
			//js
			foreach ( $js as $j ) {
				foreach ( $wp_scripts->registered as $script ) {
					if ( $script->handle == $j ) {
						$js_paths[] = $script->src;
					}
				}
			}
			$js_strings = '';
			foreach ( $js_paths as $path ) {
				//path is an url, we need to changeed it to local
				$path = str_replace( $this->plugin_url, $this->plugin_path, $path );
				if ( file_exists( $path ) ) {
					$js_strings = $js_strings . PHP_EOL . file_get_contents( $path );
				}
			}

			file_put_contents( $js_write_path, trim( $js_strings ) );
			update_option( $this->prefix . 'script_last_cache', time() );
		}
		$js_write_path = str_replace( $this->plugin_path, $this->plugin_url, $js_write_path );
		wp_enqueue_script( implode( '-', $js ), $js_write_path );
	}

	function can_compress() {
		$runtime_path = $this->plugin_path . 'framework/runtime';
		if ( ! is_dir( $runtime_path ) ) {
			//try to create
			mkdir( $runtime_path );
		}
		if ( ! is_dir( $runtime_path ) ) {
			return false;
		}
		$use_compress = false;
		if ( ! is_writeable( $runtime_path ) ) {
			chmod( $runtime_path, 775 );
		}
		if ( is_writeable( $runtime_path ) ) {
			$use_compress = $runtime_path;;
		}

		return $use_compress;
	}

	function load_addons() {
		$addons = $this->settings()->plugins;
		if ( ! is_array( $addons ) ) {
			$addons = array();
		}
		if ( array_search( $this->plugin_path . 'app/addons/je-message.php', $addons ) !== false ) {
			include $this->plugin_path . 'app/addons/je-message.php';
		}
	}

	public static function get_instance() {
		if ( ! self::$_instance instanceof Jobs_Experts ) {
			self::$_instance = new Jobs_Experts();
		}

		return self::$_instance;
	}

	function get_avatar_url( $get_avatar ) {
		if ( preg_match( "/src='(.*?)'/i", $get_avatar, $matches ) ) {
			preg_match( "/src='(.*?)'/i", $get_avatar, $matches );

			return $matches[1];
		} else {
			preg_match( "/src=\"(.*?)\"/i", $get_avatar, $matches );

			return $matches[1];
		}
	}

	function mb_word_wrap( $string, $max_length = 100, $end_substitute = null, $html_linebreaks = false ) {

		if ( $html_linebreaks ) {
			$string = preg_replace( '/\<br(\s*)?\/?\>/i', "\n", $string );
		}
		$string = strip_tags( $string ); //gets rid of the HTML

		if ( empty( $string ) || mb_strlen( $string ) <= $max_length ) {
			if ( $html_linebreaks ) {
				$string = nl2br( $string );
			}

			return $string;
		}

		if ( $end_substitute ) {
			$max_length -= mb_strlen( $end_substitute, 'UTF-8' );
		}

		$stack_count = 0;
		while ( $max_length > 0 ) {
			$char = mb_substr( $string, -- $max_length, 1, 'UTF-8' );
			if ( preg_match( '#[^\p{L}\p{N}]#iu', $char ) ) {
				$stack_count ++;
			} //only alnum characters
			elseif ( $stack_count > 0 ) {
				$max_length ++;
				break;
			}
		}
		$string = mb_substr( $string, 0, $max_length, 'UTF-8' ) . $end_substitute;
		if ( $html_linebreaks ) {
			$string = nl2br( $string );
		}

		return $string;
	}

	function encrypt( $text ) {
		if ( function_exists( 'mcrypt_encrypt' ) ) {
			$key       = SECURE_AUTH_KEY;
			$encrypted = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $text, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );

			return $encrypted;
		} else {
			return $text;
		}
	}

	function decrypt( $text ) {
		if ( function_exists( 'mcrypt_decrypt' ) ) {
			$key       = SECURE_AUTH_KEY;
			$decrypted = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $text ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0" );

			return $decrypted;
		} else {
			return $text;
		}
	}

	function trim_text( $input, $length, $ellipses = true, $strip_html = true ) {
		//strip tags, if desired
		if ( $strip_html ) {
			$input = strip_tags( $input );
		}

		//no need to trim, already shorter than trim length
		if ( strlen( $input ) <= $length ) {
			return $input;
		}

		//find last space within length
		$last_space   = strrpos( substr( $input, 0, $length ), ' ' );
		$trimmed_text = substr( $input, 0, $last_space );

		//add ellipses (...)
		if ( $ellipses ) {
			$trimmed_text .= '...';
		}

		return $trimmed_text;
	}

	function get_available_addon() {
		//load all shortcode
		$coms = glob( $this->plugin_path . 'app/addons/*.php' );
		$data = array();
		foreach ( $coms as $com ) {
			if ( file_exists( $com ) ) {
				$meta = get_file_data( $com, array(
					'Name'        => 'Name',
					'Author'      => 'Author',
					'Description' => 'Description',
					'AuthorURI'   => 'Author URI',
					'Network'     => 'Network',
					'Required'    => 'Required'
				), 'component' );

				if ( strlen( trim( $meta['Name'] ) ) > 0 ) {
					$data[ $com ] = $meta;
				}
			}
		}

		return $data;
	}

	function settings() {
		return new JE_Settings_Model();
	}

	function get_logger( $type = 'file', $location = '' ) {
		if ( empty( $location ) ) {
			$location = $this->domain;
		}
		$logger = new IG_Logger( $type, $location );

		return $logger;
	}

	function get( $key, $default = null ) {
		$value = isset( $_GET[ $key ] ) ? $_GET[ $key ] : $default;

		return apply_filters( 'je_query_get_' . $key, $value );
	}

	function post( $key, $default = null ) {
		$array_dereference = null;
		if ( strpos( $key, '[' ) ) {
			$bracket_pos       = strpos( $key, '[' );
			$array_dereference = substr( $key, $bracket_pos );
			$key               = substr( $key, 0, $bracket_pos );
		}
		$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : $default;
		if ( $array_dereference ) {
			preg_match_all( '#(?<=\[)[^\[\]]+(?=\])#', $array_dereference, $array_keys, PREG_SET_ORDER );
			$array_keys = array_map( 'current', $array_keys );
			foreach ( $array_keys as $array_key ) {
				if ( ! is_array( $value ) || ! isset( $value[ $array_key ] ) ) {
					$value = $default;
					break;
				}
				$value = $value[ $array_key ];
			}
		}

		return apply_filters( 'je_query_post_' . $key, $value );
	}

	function login_form( $args = array() ) {
		$defaults = array(
			'echo'           => true,
			'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			// Default redirect is back to the current page
			'form_id'        => 'loginform',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Sign In' ),
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'remember'       => true,
			'value_username' => '',
			'value_remember' => false,
			// Set this to true to default the "Remember me" checkbox to checked
		);

		/**
		 * Filter the default login form output arguments.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_login_form()
		 *
		 * @param array $defaults An array of default login form arguments.
		 */
		$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

		/**
		 * Filter content to display at the top of the login form.
		 *
		 * The filter evaluates just following the opening form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_top = apply_filters( 'login_form_top', '', $args );

		/**
		 * Filter content to display in the middle of the login form.
		 *
		 * The filter evaluates just following the location where the 'login-password'
		 * field is displayed.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_middle = apply_filters( 'login_form_middle', '', $args );

		/**
		 * Filter content to display at the bottom of the login form.
		 *
		 * The filter evaluates just preceding the closing form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

		$form = '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
			' . $login_form_top . '
			 <div class="form-group">
				<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
				<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="form-control" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
			</div>
			<div class="form-group">
				<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
				<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="form-control" value="" size="20" />
			</div>
			' . $login_form_middle . '
			' . ( $args['remember'] ? '<p class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label>
			<a class="pull-right" href="' . wp_lostpassword_url() . '">' . __( "Forgot password?", 'psjb' ) . '</a></p>' : '' ) . '
			<p class="login-submit">
				<button type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="btn btn-primary">' . esc_attr( $args['label_log_in'] ) . '</button>
				<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
			</p>
			' . $login_form_bottom . '
		</form>';

		if ( $args['echo'] ) {
			echo $form;
		} else {
			return $form;
		}
	}
}

function je() {
	return Jobs_Experts::get_instance();
}

je();


register_deactivation_hook( __FILE__, 'je_remove_rewrite' );
function je_remove_rewrite() {
	delete_option( 'je_rewrite' );
}