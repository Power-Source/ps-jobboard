<?php

trait JE_Bootstrap_Module {
	function dispatch() {
		new JE_Custom_Content();
		add_action( 'wp_loaded', array( &$this, 'init_pages' ) );

		include_once( $this->plugin_path . 'app/components/ig-uploader.php' );
		ig_uploader()->init_uploader( $this->can_upload(), $this->domain );

		include_once( $this->plugin_path . 'app/components/ig-social-wall.php' );
		include_once( $this->plugin_path . 'app/components/ig-skill.php' );

		if ( is_admin() ) {
			$this->global['admin'] = new JE_Unified_Admin_Controller();
			new JE_Settings_Controller();
		} else {
			$router = new JE_Router();
		}

		$buttons     = new JE_Buttons_Shortcode_Controller();
		$job_archive = new JE_Job_Archive_Shortcode_Controller;
		$job_single  = new JE_Job_Single_Shortcode_Controller();
		$job_form    = new JE_Job_Form_Shortcode_Controller();
		$my_job      = new JE_My_Job_Shortcode_Controller();

		$expert_archive = new JE_Expert_Archive_Shortcode_Controller();
		$expert_single  = new JE_Expert_Single_Shortcode_Controller();
		$my_expert      = new JE_My_Expert_Shortcode_Controller();
		$expert_form    = new JE_Expert_Form_Shortcode_Controller();
		$expert_search  = new JE_Expert_Search_Shortcode_Controller();

		$contact = new JE_Contact_Shortcode_Controller();
		$landing = new JE_Landing_Shortcode_Controller();
		$shared  = new JE_Shared_Controller();
		$profile_panel = new JE_Profile_Panel_Shortcode_Controller();

		new JE_GDPR_Controller();

		$addons = $this->settings()->plugins;
		if ( ! is_array( $addons ) ) {
			$addons = array();
		}

		foreach ( $addons as $addon ) {
			if ( file_exists( $addon ) && $addon != $this->plugin_path . 'app/addons/je-message.php' ) {
				include_once $addon;
			}
		}
	}

	function init_pages() {
		$this->pages = new JE_Page_Factory();
		$this->pages->init();
	}

	function init_widget() {
		register_widget( 'JE_Job_Add_Widget_Controller' );
		register_widget( 'JE_Job_Recent_Widget_Controller' );
		register_widget( 'JE_Job_Search_Widget_Controller' );
		register_widget( 'JE_Expert_Add_Widget_Controller' );
	}
}
