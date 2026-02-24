<?php

/**
 * Unified Admin Controller
 * Zentralisiertes Menü für Jobs und Experten
 * 
 * @author: DerN3rd
 */
class JE_Unified_Admin_Controller extends IG_Request {
	protected $flash_key = 'je_flash';
	protected $job_admin;
	protected $expert_admin;
	protected $current_tab = 'dashboard';

	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'register_menu' ), 5 );
		add_action( 'admin_menu', array( &$this, 'register_legacy_redirects' ), 99 );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( &$this, 'menu_order' ) );
		
		// Catch legacy edit.php URLs with jobs-plus-menu page - redirect BEFORE WordPress loads it
		add_action( 'load-edit.php', array( &$this, 'redirect_legacy_edit_page' ), 1 );
		
		// Load sub-controllers
		$admin_job_class_name    = apply_filters( 'je_admin_job_class_name', 'JE_Job_Admin_Controller' );
		$this->job_admin         = new $admin_job_class_name();
		
		$admin_expert_class_name = apply_filters( 'je_admin_expert_class_name', 'JE_Expert_Admin_Controller' );
		$this->expert_admin      = new $admin_expert_class_name();
		
		add_action( 'wp_ajax_je_plugin_action', array( &$this, 'plugins_action' ) );
		add_action( 'admin_init', array( &$this, 'redirect_pro_setting' ) );
		add_filter( 'admin_url', array( &$this, 'fix_add_new_url' ), 10, 3 );
	}

	/**
	 * Register main Jobboard menu
	 */
	public function register_menu() {
		// Main menu page with WordPress Dashicon
		add_menu_page(
			__( 'Jobboard', 'psjb' ),
			__( 'Jobboard', 'psjb' ),
			'manage_options',
			'je-jobboard',
			array( &$this, 'render_main_page' ),
			'dashicons-building',
			25
		);

		// Dashboard submenu - MUST BE FIRST
		add_submenu_page(
			'je-jobboard',
			__( 'Dashboard', 'psjb' ),
			__( 'Dashboard', 'psjb' ),
			'manage_options',
			'je-jobboard',
			array( &$this, 'render_main_page' )
		);

		// Jobs and Experts are automatically added via Post Types with show_in_menu => 'je-jobboard'

		// Help & Overview
		add_submenu_page(
			'je-jobboard',
			__( 'Übersichtsseiten', 'psjb' ),
			__( 'Übersichtsseiten', 'psjb' ),
			'manage_options',
			'je-jobboard-help',
			array( &$this, 'getting_start' )
		);

		// Settings submenu
		add_submenu_page(
			'je-jobboard',
			__( 'Einstellungen', 'psjb' ),
			__( 'Einstellungen', 'psjb' ),
			'manage_options',
			'je-jobboard-settings',
			array( &$this, 'backend_setting' )
		);

		// Add settings under old CPT menus for backward compatibility
		add_submenu_page( 'edit.php?post_type=jbp_job',
			__( 'Einstellungen', 'psjb' ),
			__( 'Einstellungen', 'psjb' ),
			'manage_options',
			'jobs-plus-menu',
			array( &$this, 'backend_setting' )
		);

		add_submenu_page( 'edit.php?post_type=jbp_pro',
			__( 'Einstellungen', 'psjb' ),
			__( 'Einstellungen', 'psjb' ),
			'manage_options',
			'jobs-plus-menu',
			array( &$this, 'backend_setting' )
		);
	}

	/**
	 * Register hidden submenu pages for legacy URL redirects
	 */
	public function register_legacy_redirects() {
		// Register hidden page for old job settings URL
		add_submenu_page(
			'je-jobboard',
			'',
			'',
			'manage_options',
			'jobs-plus-menu',
			array( &$this, 'legacy_redirect_callback' )
		);
	}

	/**
	 * Redirect legacy edit.php URLs before WordPress checks permissions
	 */
	public function redirect_legacy_edit_page() {
		// Check if this is a jobs-plus-menu page request
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';
		
		if ( 'jobs-plus-menu' === $page ) {
			$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
			wp_redirect( admin_url( "admin.php?page=je-jobboard-settings&tab={$tab}" ) );
			exit;
		}
	}

	/**
	 * Callback for legacy redirect pages (hidden under Jobboard)
	 */
	public function legacy_redirect_callback() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
		wp_redirect( admin_url( "admin.php?page=je-jobboard-settings&tab={$tab}" ) );
		exit;
	}

	/**
	 * Fix add new URL routing
	 */
	public function fix_add_new_url( $url, $path, $blog_id ) {
		return $url;
	}

	/**
	 * Redirect old expert settings to main settings
	 */
	public function redirect_pro_setting() {
		if ( je()->get( 'post_type', null ) == 'jbp_pro' && je()->get( 'page', null ) == 'jobs-plus-menu' ) {
			$this->redirect( admin_url( "admin.php?page=je-jobboard-settings&tab=expert" ) );
		}
	}

	/**
	 * Reorder menu items
	 */
	public function menu_order( $menu_order ) {
		global $submenu;

		// Reorder the Jobboard submenu
		if ( isset( $submenu['je-jobboard'] ) ) {
			return $menu_order;
		}

		return $menu_order;
	}

	/**
	 * Render main Jobboard page with dashboard/tabs
	 */
	public function render_main_page() {
		wp_enqueue_style( 'jbp_admin' );
		wp_enqueue_script( 'jquery-tabs' );
		
		$this->current_tab = je()->get( 'tab', 'dashboard' );
		
		$this->render( 'backend/jobboard-dashboard', array(
			'current_tab' => $this->current_tab,
			'job_labels' => get_post_type_object( 'jbp_job' )->labels,
			'pro_labels' => get_post_type_object( 'jbp_pro' )->labels
		) );
	}

	/**
	 * Getting start / Help page
	 */
	public function getting_start() {
		wp_enqueue_style( 'jbp_admin' );
		$this->render( 'backend/getting_start', array(
			'job_labels' => get_post_type_object( 'jbp_job' )->labels,
			'pro_labels' => get_post_type_object( 'jbp_pro' )->labels
		) );
	}

	/**
	 * Backend settings page
	 */
	public function backend_setting() {
		wp_enqueue_style( 'jbp_admin' );
		$this->render( 'backend/settings' );
	}

	/**
	 * Plugin activation/deactivation handler
	 */
	public function plugins_action() {
		$setting = je()->settings();
		$addons  = $setting->plugins;
		if ( ! is_array( $addons ) ) {
			$addons = array();
		}
		
		$id   = je()->post( 'id' );
		$meta = get_file_data( $id, array(
			'Name'        => 'Name',
			'Author'      => 'Author',
			'Description' => 'Description',
			'AuthorURI'   => 'Author URI',
			'Network'     => 'Network'
		), 'component' );

		if ( ! in_array( $id, $addons ) ) {
			// Activate addon
			$addons[]         = $id;
			$setting->plugins = $addons;
			$setting->save();
			do_action( 'je_addon_activated', $id, $meta );
			wp_send_json( array(
				'noty' => __( "Die Erweiterung <strong>{$meta['Name']}</strong> wurde aktiviert.", 'psjb' ),
				'text' => __( "Deaktivieren", 'psjb' )
			) );
			exit;
		} else {
			// Deactivate addon
			unset( $addons[ array_search( $id, $addons ) ] );
			$setting->plugins = $addons;
			$setting->save();
			do_action( 'je_addon_deactivated', $id, $meta );
			wp_send_json( array(
				'noty' => __( "Die Erweiterung <strong>{$meta['Name']}</strong> wurde deaktiviert.", 'psjb' ),
				'text' => __( "Aktivieren", 'psjb' )
			) );
			exit;
		}
	}
}
?>
