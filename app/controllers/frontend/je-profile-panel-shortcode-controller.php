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
		if ( ! is_array( $models ) ) {
			$models = array();
		}

		return $this->render( 'my-job/main', array( 'models' => $models ), false );
	}

	function render_my_expert() {
		je()->load_script( 'experts' );

		$models = JE_Expert_Model::model()->find_by_attributes( array(
			'user_id' => get_current_user_id(),
			'status'  => array( 'publish', 'draft', 'pending' )
		), false, 'modified DESC' );

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
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			if ( ! wp_style_is( 'ig-packed', 'registered' ) ) {
				wp_register_style( 'ig-packed', false, array(), null );
			}
			if ( ! wp_script_is( 'ig-packed', 'registered' ) ) {
				wp_register_script( 'ig-packed', '', array( 'jquery' ), null, true );
			}
		}

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
			$jobs = JE_Job_Model::model()->find_by_attributes( array(
				'owner'  => get_current_user_id(),
				'status' => array( 'publish', 'draft', 'pending', 'je-draft' )
			), false, 'modified DESC' );
			$saved_jobs = array_values( array_filter( $jobs, function( $job ) {
				return $job->status !== 'je-draft';
			} ) );
			$model = JE_Job_Model::model()->find_one_by_attributes( array(
				'status' => 'je-draft',
				'owner'  => get_current_user_id()
			), 'modified DESC' );

			$max_jobs = (int) je()->settings()->job_max_records;
			if ( count( $saved_jobs ) >= $max_jobs ) {
				if ( $max_jobs === 1 ) {
					$model = $saved_jobs[0];
				} else {
					return $this->render( 'job-form/limit', array(), false );
				}
			}

			if ( ! is_object( $model ) ) {
				$model              = new JE_Job_Model();
				$model->status      = 'je-draft';
				$model->description = '';
				$model->owner       = get_current_user_id();
			}
		}

		if ( ! is_object( $model ) ) {
			$jobs = isset( $jobs ) ? $jobs : JE_Job_Model::model()->find_by_attributes( array(
				'owner'  => get_current_user_id(),
				'status' => array( 'publish', 'draft', 'pending', 'je-draft' )
			), false, 'modified DESC' );

			if ( count( $jobs ) >= (int) je()->settings()->job_max_records ) {
				return $this->render( 'job-form/limit', array(), false );
			}

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

		$profiles = JE_Expert_Model::model()->find_by_attributes( array(
			'user_id' => get_current_user_id(),
			'status'  => array( 'publish', 'draft', 'pending', 'je-draft' )
		), false, 'modified DESC' );
		$saved_profiles = array_values( array_filter( $profiles, function( $profile ) {
			return $profile->status !== 'je-draft';
		} ) );
		$model = JE_Expert_Model::model()->find_one_by_attributes( array(
			'user_id' => get_current_user_id(),
			'status'  => 'je-draft'
		), 'modified DESC' );

		$max_profiles = (int) je()->settings()->expert_max_records;
		if ( count( $saved_profiles ) >= $max_profiles ) {
			if ( $max_profiles === 1 ) {
				$model = $saved_profiles[0];
			} else {
				return $this->render( 'expert-form/limit', array(), false );
			}
		}

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

		$final_style_queue = isset( $styles->queue ) && is_array( $styles->queue ) ? $styles->queue : array();
		$final_script_queue = isset( $scripts->queue ) && is_array( $scripts->queue ) ? $scripts->queue : array();

		// Simple diff: what was added to queue during section render
		$new_style_handles = array_values( array_diff( $final_style_queue, $initial_style_queue ) );
		$new_script_handles = array_values( array_diff( $final_script_queue, $initial_script_queue ) );

		// Fallback: if no handles enqueued, force enqueue core jobboard assets
		if ( empty( $new_style_handles ) && empty( $new_script_handles ) ) {
			$fallback_styles = array( 'jobs-main', 'jobs-buttons-shortcode', 'jobs-list-shortcode', 'expert-list-shortcode', 'jobs-landing-shortcode' );
			$fallback_scripts = array( 'jobs-main' );

			foreach ( $fallback_styles as $handle ) {
				wp_enqueue_style( $handle );
			}
			foreach ( $fallback_scripts as $handle ) {
				wp_enqueue_script( $handle );
			}

			$new_style_handles = $fallback_styles;
			$new_script_handles = $fallback_scripts;
		}

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

		ob_start();
		?>
<script>
(function() {
	'use strict';

	var jeProfilePanel = {
		nonce: '<?php echo esc_js( $nonce ); ?>',
		loading: false,
		initialized: false,

		init: function() {
			// Prevent multiple initialization
			if (this.initialized) {
				return;
			}
			this.initialized = true;

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
						jQuery('.je-profile-panel-content').css('opacity', '1');
					}
					self.loading = false;
				},
				error: function() {
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
				// Reset initialized flag to allow re-init on tab reload
				jeProfilePanel.initialized = false;
				jeProfilePanel.init();
			}
		});
	}
})();
</script>
		<?php

		return ob_get_clean();
	}
}
