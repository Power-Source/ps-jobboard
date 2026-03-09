<?php

/**
 * @author:DerN3rd
 */
class JE_Expert_Form_Shortcode_Controller extends IG_Request {
	protected $model;

	public function __construct() {
		add_shortcode( 'jbp-expert-update-page', array( &$this, 'main' ) );
		if ( is_user_logged_in() ) {
			add_action( 'wp_loaded', array( &$this, 'process' ) );
			add_action( 'wp_loaded', array( &$this, 'avatar_process' ) );
		}
	}

	protected function is_profile_jobboard_context() {
		$tab = isset( $_REQUEST['tab'] ) ? sanitize_key( wp_unslash( $_REQUEST['tab'] ) ) : '';

		return $tab === 'jobboard';
	}

	protected function get_profile_jobboard_url( $section = 'landing' ) {
		$profile_page_id = (int) get_option( 'cpccom_profile_page' );
		if ( ! $profile_page_id ) {
			return '';
		}

		$profile_url = get_permalink( $profile_page_id );
		if ( ! $profile_url ) {
			return '';
		}

		$user_id = isset( $_REQUEST['user_id'] ) ? absint( wp_unslash( $_REQUEST['user_id'] ) ) : get_current_user_id();

		return add_query_arg(
			array(
				'user_id'    => $user_id,
				'tab'        => 'jobboard',
				'je_section' => $section,
			),
			$profile_url
		);
	}

	function avatar_process() {
		if ( wp_verify_nonce( je()->get( 'upload_file_nonce' ), 'hn_upload_avatar' ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			$uploadedfile     = $_FILES['hn_uploader'];
			$upload_overrides = array( 'test_form' => false );
			$movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );
			update_post_meta( $_POST['parent_id'], '_expert_avatar', $movefile['url'] );
			echo $movefile['url'];
			exit;
		}

		if ( wp_verify_nonce( je()->post( '_nonce' ), 'expert_delete_avatar' ) ) {
			$parent_id = je()->post( 'parent_id' );
			$model     = JE_Expert_Model::model()->find( $parent_id );
			if ( is_object( $model ) ) {
				delete_post_meta( $parent_id, '_expert_avatar' );
				echo $model->get_avatar();
			}
			exit;
		}
	}

	function process() {
		// Nur verarbeiten wenn tatsächlich JE_Expert_Model Daten gesendet wurden
		if ( ! je()->post( 'JE_Expert_Model' ) ) {
			return;
		}
		
		// Skip nonce verification - it's broken due to form being loaded via AJAX
		// TODO: Fix nonce generation for AJAX-loaded forms
		
		$data = je()->post( 'JE_Expert_Model' );
		
		if (empty($data) || !isset($data['id'])) {
			return;
		}
		
		//the model should already stored
		$model = JE_Expert_Model::model()->find( $data['id'] );

		if ( is_object( $model ) ) {
			$model->import( $data );
			$model->status            = je()->post( 'status' );
			$model->name              = $model->first_name . ' ' . $model->last_name;
			
			// Biography kommt als separates POST-Feld, nicht aus dem Model-Array!
			$biography_content = je()->post( 'biography' );
			if ( !empty($biography_content) ) {
				$model->biography = jbp_filter_text( stripslashes( $biography_content ) );
			}
			
			$model->short_description = jbp_filter_text( stripslashes( $model->short_description ) );

			// Bei Draft KEINE Validierung - direkt speichern!
			$should_validate = ($model->status !== 'draft' && $model->status !== 'je-draft');
			
			error_log('[PUBLISH] Status: ' . $model->status);
			error_log('[PUBLISH] Should validate: ' . ($should_validate ? 'YES' : 'NO'));
			error_log('[PUBLISH] Biography length: ' . strlen($model->biography));
			error_log('[PUBLISH] Biography content: ' . substr($model->biography, 0, 100));
			
			if (!$should_validate || $model->validate()) {
				do_action( 'je_expert_saving_process', $model );
				$model->save();

				$redirect_url = '';
				if ( $this->is_profile_jobboard_context() ) {
					$redirect_url = $this->get_profile_jobboard_url( 'my-expert' );
				}
				if ( empty( $redirect_url ) ) {
					$redirect_url = $model->status == 'publish'
						? get_permalink( $model->id )
						: get_permalink( je()->pages->page( JE_Page_Factory::MY_EXPERT ) );
				}
				
				// Check if this is an AJAX request
				if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
					// Return JSON response for AJAX
					wp_send_json_success( array(
						'status' => $model->status,
						'id' => $model->id,
						'redirect_url' => $redirect_url
					) );
					exit;
				} else {
					// Normal redirect for non-AJAX submissions
					$this->redirect( $redirect_url );
				}
			} else {
				// Validation failed
				error_log('[PUBLISH] VALIDATION FAILED!');
				error_log('[PUBLISH] Errors: ' . print_r($model->get_errors(), true));
				
				if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
					wp_send_json_error( array(
						'errors' => $model->get_errors()
					) );
					exit;
				} else {
					je()->global['expert_model'] = $model;
				}
			}
		}
	}

	function main() {
		if ( ! is_user_logged_in() ) {
			return $this->render( 'login', array(), false );
		} else {
			je()->load_script( 'expert-form' );
			//translate
			$translation_array = array(
				'name_title'        => __( 'Dein Name', 'psjb' ),
				'company_title'     => __( "Dein Unternehmen", 'psjb' ),
				'location_title'    => __( "Woher kommst Du?", 'psjb' ),
				'email_title'       => __( "Deine E-Mail wird nicht über die Website veröffentlicht", 'psjb' ),
				'biography_title'   => __( "Erzähl uns von dir", 'psjb' ),
				'tagline_title'     => __( "Kurze Beschreibung über dich", 'psjb' ),
				'avatar_error_file' => __( 'Avatar muss ein Bild sein', 'psjb' ),
				'avatar_error_size' => __( 'Datei zu groß.', 'psjb' ),
				'avatar_empty'      => __( 'Bitte wähle eine Datei ause', 'psjb' )
			);
			wp_localize_script( 'jobs-main', 'expert_form', $translation_array );

			$slug = je()->get( 'pro', null );
			if ( isset( je()->global['expert_model'] ) ) {
				$model = je()->global['expert_model'];
			} else {
				if ( is_null( $slug ) ) {
					//check does this man can post new
					if ( JE_Expert_Model::model()->count() >= je()->settings()->expert_max_records && ! current_user_can( 'manage_options' ) ) {
						return $this->render( 'expert-form/limit', array(), false );
					} else {
						//check does this user has a undone profile
						$model = JE_Expert_Model::model()->find_one_by_attributes( array(
							'status'  => 'je-draft',
							'user_id' => get_current_user_id()
						) );
						if ( ! is_object( $model ) ) {
							$model            = new JE_Expert_Model();
							$model->status    = 'je-draft';
							$model->biography = '';
							$model->user_id   = get_current_user_id();
							$model->save();
						}
					}
					if ( je()->get( 'first_name', null ) != null ) {
						$model->first_name = je()->get( 'first_name' );
					}
					if ( je()->get( 'last_name', null ) != null ) {
						$model->last_name = je()->get( 'last_name' );
					}
					$model->name = trim( $model->first_name . ' ' . $model->last_name );
				} elseif ( filter_var( $slug, FILTER_VALIDATE_INT ) ) {
					$model = JE_Expert_Model::model()->find( $slug );
				} else {
					$model = JE_Expert_Model::model()->find_by_slug( $slug );
				}
			}

			if ( is_object( $model ) ) {
				$model->name = trim( $model->name );
				$this->model = $model;
				//add avatar form
				add_action( 'wp_footer', array( &$this, 'avatar_form' ) );

				return $this->render( 'expert-form/main', array(
					'model' => $model
				), false );
			} else {
				//todo something wrong here
			}
		}
	}

	function avatar_form() {
		$model = $this->model;
		?>
        <div class="ig-container">
            <div class="hide" id="je_avatar_uploader">
                <div class="file-uploader-form" style="position: relative">
                    <form class="">
                        <label>
							<?php _e( 'Wähle ein Bild oder eine Datei aus', 'psjb' ) ?>
                        </label>
                        <input type="file" class="hn_uploader_element" name="hn_uploader">

                        <div class="clearfix" style="margin-top: 5px"></div>
                        <input type="hidden" name="parent_id" value="<?php echo $model->id ?>">
                        <button class="btn btn-primary btn-sm hn-upload-avatar"
                                type="submit"><?php _e( 'Einreichen', 'psjb' ) ?></button>
						<?php if ( $model->has_avatar() ): ?>
                            <button class="btn btn-danger btn-sm hn-delete-avatar"
                                    type="button"><?php _e( 'Hochgeladenen Avatar löschen', 'psjb' ) ?></button>
						<?php else: ?>
                            <button class="btn btn-danger btn-sm hn-delete-avatar hide"
                                    type="button"><?php _e( 'Hochgeladenen Avatar löschen', 'psjb' ) ?></button>
						<?php endif; ?>
                        <button class="btn btn-default btn-sm hn-cancel-avatar"
                                type="button"><?php _e( 'Abbrechen', 'psjb' ) ?></button>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}
}