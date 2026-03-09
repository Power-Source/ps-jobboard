<?php

/**
 * Jobboard Profile Panel Shortcode Controller
 *
 * Renders Jobboard content inline within PS Community profile tabs
 * with smooth AJAX-based navigation between sections.
 */
class JE_Profile_Panel_Shortcode_Controller extends IG_Request {
	public function __construct() {
		add_shortcode( 'jbp-profile-panel', array( &$this, 'main' ) );
		add_action( 'wp_ajax_je_load_profile_section', array( &$this, 'ajax_load_profile_section' ) );
		add_action( 'wp_ajax_nopriv_je_load_profile_section', array( &$this, 'ajax_load_profile_section' ) );
	}

	/**
	 * Main shortcode render
	 */
	function main() {
		if ( ! is_user_logged_in() ) {
			return $this->render( 'login', array(), false );
		}

		$this->ensure_profile_panel_assets_registered();

		// Set global flag to indicate we're in profile panel context
		global $je_in_profile_panel_context;
		$je_in_profile_panel_context = true;

		$section = isset( $_GET['je_section'] ) ? sanitize_key( $_GET['je_section'] ) : 'landing';

		$html  = '';
		$html .= $this->render_profile_nav();
		$html .= $this->render_section_content( $section );
		$html .= $this->get_profile_panel_js();

		return $html;
	}

	/**
	 * Render profile navigation menu with original Jobboard buttons
	 */
	function render_profile_nav() {
		$shortcodes = apply_filters(
			'je_buttons_on_single_page',
			'[jbp-my-job-btn][jbp-expert-profile-btn][jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn]'
		);

		$nav_html  = '<div style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">';
		$nav_html .= do_shortcode( '[jbp-landing-btn]' );
		$nav_html .= do_shortcode( $shortcodes );
		$nav_html .= '</div>';

		return $nav_html;
	}

	/**
	 * Render section content based on requested section
	 */
	function render_section_content( $section ) {
		$html = '<div class="je-profile-panel-content" data-section="' . esc_attr( $section ) . '">';

		switch ( $section ) {
			case 'landing':
				$html .= $this->render_landing();
				break;

			case 'my-jobs':
				$html .= $this->render_my_jobs();
				break;

			case 'my-expert':
				$html .= $this->render_my_expert();
				break;

			case 'job-list':
				$html .= $this->render_job_list();
				break;

			case 'expert-list':
				$html .= $this->render_expert_list();
				break;

			case 'job-add':
				$html .= $this->render_job_add();
				break;

			case 'expert-add':
				$html .= $this->render_expert_add();
				break;

			case 'my-wallet':
				$html .= $this->render_my_wallet();
				break;

			default:
				$html .= '<p class="je-error">' . __( 'Unbekannte Sektion.', 'psjb' ) . '</p>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render landing content (jobs + experts)
	 */
	function render_landing() {
		je()->load_script( 'landing' );
		return do_shortcode( '[jbp-landing-page]' );
	}

	function render_my_jobs() {
		je()->load_script( 'jobs' );

		$models = JE_Job_Model::model()->find_by_attributes( array(
			'owner'  => get_current_user_id(),
			'status' => array( 'publish', 'draft', 'pending' )
		) );

		return $this->render( 'my-job/main', array( 'models' => $models ), false );
	}

	function render_my_expert() {
		je()->load_script( 'experts' );

		$models = JE_Expert_Model::model()->find_by_attributes( array(
			'user_id' => get_current_user_id()
		) );

		return $this->render( 'my-expert/main', array( 'models' => $models ), false );
	}

	function render_job_list() {
		je()->load_script( 'jobs' );

		return do_shortcode( '[jbp-job-archive-page]' );
	}

	function render_expert_list() {
		je()->load_script( 'experts' );

		return do_shortcode( '[jbp-expert-archive-page]' );
	}

	function ensure_profile_panel_assets_registered() {
		if ( method_exists( je(), 'scripts' ) ) {
			je()->scripts();
		}

		if ( function_exists( 'ig_social_wall' ) && method_exists( ig_social_wall(), 'scripts' ) ) {
			ig_social_wall()->scripts();
		}

		if ( function_exists( 'ig_skill' ) && method_exists( ig_skill(), 'scripts' ) ) {
			ig_skill()->scripts();
		}

		if ( function_exists( 'ig_uploader' ) && method_exists( ig_uploader(), 'scripts' ) ) {
			ig_uploader()->scripts();
		}
	}

	function render_job_add() {
		je()->load_script( 'job-form' );

		$slug = isset( $_GET['job'] ) ? sanitize_key( $_GET['job'] ) : null;

		if ( ! empty( $slug ) ) {
			if ( filter_var( $slug, FILTER_VALIDATE_INT ) ) {
				$model = JE_Job_Model::model()->find( $slug );
			} else {
				$model = JE_Job_Model::model()->find_by_slug( $slug );
			}

			if ( ! is_object( $model ) || ! $model->is_current_owner() ) {
				$model = null;
			}
		} else {
			$model              = new JE_Job_Model();
			$model->status      = 'je-draft';
			$model->description = '';
			$model->owner       = get_current_user_id();
		}

		if ( ! is_object( $model ) ) {
			$model              = new JE_Job_Model();
			$model->status      = 'je-draft';
			$model->description = '';
			$model->owner       = get_current_user_id();
		}

		return $this->render( 'job-form/main', array(
			'model'       => $model,
			'form_action' => '#'
		), false );
	}

	function render_expert_add() {
		je()->load_script( 'expert-form' );

		$model = JE_Expert_Model::model()->find_one_by_attributes( array(
			'user_id' => get_current_user_id()
		) );

		if ( ! is_object( $model ) ) {
			$model            = new JE_Expert_Model();
			$model->status    = 'je-draft';
			$model->biography = '';
			$model->user_id   = get_current_user_id();
		}

		return $this->render( 'expert-form/main', array(
			'model'       => $model,
			'form_action' => '#'
		), false );
	}

	function render_my_wallet() {
		return do_shortcode( '[jbp-my-wallet]' );
	}

	/**
	 * AJAX handler for loading different profile sections
	 */
	function ajax_load_profile_section() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'je_profile_panel_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Nonce verification failed', 'psjb' ) ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Not logged in', 'psjb' ) ) );
		}

		// Set global flag to indicate we're in profile panel context
		global $je_in_profile_panel_context;
		$je_in_profile_panel_context = true;

		$this->ensure_profile_panel_assets_registered();

		$section = isset( $_POST['section'] ) ? sanitize_key( $_POST['section'] ) : 'landing';

		$styles = wp_styles();
		$scripts = wp_scripts();

		$initial_style_queue = isset( $styles->queue ) && is_array( $styles->queue ) ? $styles->queue : array();
		$initial_script_queue = isset( $scripts->queue ) && is_array( $scripts->queue ) ? $scripts->queue : array();

		$content = $this->render_section_content( $section );

		$new_style_handles = array_values( array_diff( $styles->queue, $initial_style_queue ) );
		$new_script_handles = array_values( array_diff( $scripts->queue, $initial_script_queue ) );

		$styles_html = '';
		$scripts_html = '';

		if ( ! empty( $new_style_handles ) ) {
			ob_start();
			$styles->do_items( $new_style_handles );
			$styles_html = ob_get_clean();
		}

		if ( ! empty( $new_script_handles ) ) {
			ob_start();
			$scripts->do_items( $new_script_handles );
			$scripts_html = ob_get_clean();
		}

		wp_send_json_success( array(
			'content' => $content,
			'section' => $section,
			'styles'  => $styles_html,
			'scripts' => $scripts_html,
		) );
	}

	function get_profile_panel_js() {
		$nonce = wp_create_nonce( 'je_profile_panel_nonce' );

		$js = <<<'JSCODE'
<script>
(function() {
	'use strict';

	var jeProfilePanel = {
		nonce: '%s',
		loading: false,

		init: function() {
			var self = this;
			jQuery('body').on('click', '.jbp-shortcode-button', function(e) {
				var href = jQuery(this).attr('href');
				if (href && href.indexOf('je_section=') > -1) {
					e.preventDefault();
					var match = href.match(/je_section=([^&]+)/);
					if (match && match[1]) {
						self.loadSection(match[1]);
					}
				}
			});
		},

		loadSection: function(section) {
			var self = this;

			if (this.loading) return;
			this.loading = true;

			jQuery('.je-profile-panel-content').css('opacity', '0.6');

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'je_load_profile_section',
					section: section,
					nonce: self.nonce
				},
				success: function(response) {
					if (response.success) {
						if (response.data.styles) {
							jQuery('head').append(response.data.styles);
						}

						jQuery('.je-profile-panel-content').html(response.data.content);
						jQuery('.je-profile-panel-content').attr('data-section', section);
						jQuery('.je-profile-panel-content').css('opacity', '1');

						if (response.data.scripts) {
							jQuery('body').append(response.data.scripts);
						}

						jQuery(document).trigger('je_profile_section_loaded', [section]);
					} else {
						console.error('Error loading section:', response.data.message);
						jQuery('.je-profile-panel-content').css('opacity', '1');
					}
					self.loading = false;
				},
				error: function() {
					console.error('AJAX error loading section');
					jQuery('.je-profile-panel-content').css('opacity', '1');
					self.loading = false;
				}
			});
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			jeProfilePanel.init();
		});
	} else {
		jeProfilePanel.init();
	}

	if (typeof jQuery !== 'undefined') {
		jQuery(document).on('cpc_profile_tab_loaded', function(e, tab) {
			if (tab === 'jobboard') {
				jeProfilePanel.init();
			}
		});
	}
})();
</script>
JSCODE;

		return sprintf( $js, $nonce );
	}
}
