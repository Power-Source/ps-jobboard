<?php

/**
 * Name: PM-System
 * Description: Integriert das eigenständige Private-Messaging Plugin in PS Jobboard
 * Author: PSOURCE
 * Requires: messaging/messaging.php
 */
class JE_Message
{
	private $is_messaging_active = false;

	public function __construct()
	{
		// Check if the private-messaging plugin exists and is activated
		$this->check_dependencies();

		if ( $this->is_messaging_active ) {
			$this->setup_integrations();
		} else {
			$this->setup_admin_notices();
		}
	}

	/**
	 * Check if private-messaging plugin is active
	 */
	private function check_dependencies()
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		if ( is_plugin_active( 'private-messaging/messaging.php' ) ) {
			$this->is_messaging_active = true;
		}
	}

	/**
	 * Setup integrations with the messaging plugin
	 */
	private function setup_integrations()
	{
		// Contact buttons - replace default contact form with PM
		add_filter( 'jbp_job_contact_btn', array( &$this, 'contact_job_poster_btn' ), 10, 2 );
		add_filter( 'jbp_expert_contact_btn', array( &$this, 'contact_expert_poster_btn' ), 10, 2 );

		// Inbox integration
		add_filter( 'the_content', array( &$this, 'append_inbox_button' ) );
		add_shortcode( 'jbp-message-inbox-btn', array( &$this, 'inbox_btn' ) );
		add_filter( 'je_buttons_on_single_page', array( &$this, 'append_inbox_button' ) );

		// Messaging layout integration
		add_action( 'mm_before_layout', array( &$this, 'je_buttons_for_mm' ) );
	}

	/**
	 * Show admin notices if messaging plugin is not active
	 */
	private function setup_admin_notices()
	{
		add_action( 'admin_notices', array( &$this, 'missing_plugin_notice' ) );
		add_action( 'network_admin_notices', array( &$this, 'missing_plugin_notice' ) );
	}

	/**
	 * Admin notice for missing plugin
	 */
	public function missing_plugin_notice()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$message = sprintf(
			__( 
				'Das PN-System Add-On benötigt das <strong>Private Messaging Plugin</strong>. ' .
				'<a href="%s">Jetzt herunterladen und installieren</a>.',
				'psjb'
			),
			'https://github.com/Power-Source/private-messaging/releases'
		);

		echo '<div class="notice notice-warning"><p>' . wp_kses_post( $message ) . '</p></div>';
	}

/**
	 * Display Jobboard buttons in messaging layout
	 */
	public function je_buttons_for_mm()
	{
		$shortcodes = apply_filters( 
			'je_buttons_on_single_page', 
			'[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]'
		);
		echo '<p style="text-align: center">' . do_shortcode( $shortcodes ) . '</p>';
	}

	/**
	 * Inbox button shortcode - uses external MM_Setting_Model
	 */
	public function inbox_btn( $atts )
	{
		if ( ! $this->is_messaging_active ) {
			return '';
		}

		wp_enqueue_style( 'jbp_message' );
		
		$setting = new MM_Setting_Model();
		$setting->load();
		$link = ! empty( $setting->inbox_page ) ? get_permalink( $setting->inbox_page ) : null;

		$args = array(
			'text'     => __( 'Postfach', 'psjb' ),
			'view'     => 'both',
			'class'    => je()->settings()->theme,
			'template' => '',
			'url'      => $link
		);

		$atts = shortcode_atts( $args, $atts );

		if ( ! $this->can_view( $atts['view'] ) ) {
			return '';
		}

		return sprintf(
			'<a class="ig-container jbp-shortcode-button jbp-message-inbox %s" href="%s"><i style="display: block" class="fa fa-inbox fa-2x"></i>%s</a>',
			esc_attr( $atts['class'] ),
			esc_url( $atts['url'] ),
			esc_html( $atts['text'] )
		);
	}

	/**
	 * Check if user can view content based on login status
	 */
	public function can_view( $view = 'both' )
	{
		$view = strtolower( $view );
		
		if ( is_user_logged_in() ) {
			return 'loggedout' !== $view;
		}

		return 'loggedin' !== $view;
	}

	/**
	 * Append inbox button to expert profile
	 */
	public function append_inbox_button( $content )
	{
		if ( ! $this->is_messaging_active ) {
			return $content;
		}

		$pattern = get_shortcode_regex();
		
		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches )
			&& array_key_exists( 2, $matches )
			&& in_array( 'jbp-expert-profile-btn', $matches[2] ) ) {
			
			$key         = array_search( 'jbp-expert-profile-btn', $matches[2] );
			$sc          = $matches[0][ $key ];
			$new_content = str_replace( $sc, $sc . '[jbp-message-inbox-btn]', $content );
			
			return $new_content;
		}

		return $content;
	}

	/**
	 * Replace job contact button with PM shortcode
	 */
	public function contact_job_poster_btn( $content, JE_Job_Model $model )
	{
		if ( ! $this->is_messaging_active ) {
			return $content;
		}

		$user_id = $model->owner;
		return do_shortcode( '[pm_user user_id="' . $user_id . '" text="' . __( 'Kontakt', 'psjb' ) . '"]' );
	}

	/**
	 * Replace expert contact button with PM shortcode
	 */
	public function contact_expert_poster_btn( $content, JE_Expert_Model $model )
	{
		if ( ! $this->is_messaging_active ) {
			return $content;
		}

		$user_id = $model->user_id;
		return do_shortcode( '[pm_user user_id="' . $user_id . '" text="' . __( 'Nimm Kontakt auf', 'psjb' ) . '"]' );
    }
}

new JE_Message();