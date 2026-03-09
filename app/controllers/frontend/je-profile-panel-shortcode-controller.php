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
		$nav_html  = '<div style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;">';
		$nav_html .= do_shortcode( '[jbp-landing-btn]' );
		$nav_html .= do_shortcode( '[jbp-my-job-btn]' );
		$nav_html .= do_shortcode( '[jbp-expert-profile-btn]' );
		$nav_html .= do_shortcode( '[jbp-job-browse-btn]' );
		$nav_html .= do_shortcode( '[jbp-expert-browse-btn]' );
		$nav_html .= do_shortcode( '[jbp-job-post-btn]' );
		$nav_html .= do_shortcode( '[jbp-expert-post-btn]' );
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

		$model = JE_Expert_Model::model()->find_by_attributes( array(
			'owner' => get_current_user_id()
		), true );

		return $this->render( 'my-expert/main', array( 'model' => $model ), false );
	}

	function render_job_list() {
		je()->load_script( 'jobs' );

		$models = JE_Job_Model::model()->find_by_attributes( array(
			'status' => 'publish'
		) );

		return $this->render( 'job-archive/main', array( 'models' => $models ), false );
	}

	function render_expert_list() {
		je()->load_script( 'experts' );

		$models = JE_Expert_Model::model()->find_by_attributes( array(
			'status' => 'publish'
		) );

		return $this->render( 'expert-archive/main', array( 'models' => $models ), false );
	}

	function render_job_add() {
		je()->load_script( 'job-form' );

		$slug = isset( $_GET['job'] ) ? sanitize_key( $_GET['job'] ) : null;

		if ( ! empty( $slug ) ) {
			$model = JE_Job_Model::model()->find_by_attributes( array(
				'id'    => $slug,
				'owner' => get_current_user_id()
			), true );
		} else {
			$model = null;
		}

		return $this->render( 'job-form/main', array(
			'model'       => $model,
			'form_action' => '#'
		), false );
	}

	function render_expert_add() {
		je()->load_script( 'expert-form' );

		$model = JE_Expert_Model::model()->find_by_attributes( array(
			'owner' => get_current_user_id()
		), true );

		return $this->render( 'expert-form/main', array(
			'model'       => $model,
			'form_action' => '#'
		), false );
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
