<div class="ig-container">
	<?php $form = new IG_Active_Form( $model );
	$form->open( array( "attributes" => array( "class" => "form-horizontal" ) ) );
	$form->hidden( 'id' );
	?>
	<?php do_action( 'je_job_before_form', $model, $form ) ?>
	<?php do_action( 'je_before_cat_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "categories" ) ? "has-error" : null ?>">
		<?php $form->label( "categories", array(
			"text"       => __( "Wähle eine Kategorie", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php
			$cats = get_terms( "jbp_category", array( "hide_empty" => false ) );
			?>
                        <?php
                            foreach ((array)$model->categories as $k => $cat) {
                                $c = term_exists($cat, 'jbp_category');
                                if (is_array($c)) {
                                    $model->categories[$k] = $c['term_id'];
                                }
                            }
                        ?>
			<?php $form->select( "categories", array(
				"attributes" => array( "class" => "form-control" ),
				"data"       => array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name'))
			) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Wähle die Kategorie, in der Interessierte deinen Job am ehesten suchen.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-categories"><?php $form->error( "categories" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_cat_field', $model, $form ) ?>
	<?php do_action( 'je_before_job_title_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "job_title" ) ? "has-error" : null ?>">
		<?php $form->label( "job_title", array(
			"text"       => __( "Gib dem Job einen Titel", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $form->text( "job_title", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Formuliere kurz und konkret, worum es geht.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-job_title"><?php $form->error( "job_title" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_job_title_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "engagement_type" ) ? "has-error" : null ?>">
		<?php $form->label( "engagement_type", array(
			"text"       => __( "Beschäftigungsart", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $form->select( "engagement_type", array(
				"data" => array(
					"freelance"  => __( "Freelance/Projektarbeit", 'psjb' ),
					"employment" => __( "Festanstellung", 'psjb' )
				),
				"attributes" => array( "class" => "form-control", "id" => "je-engagement-type" )
			) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Wähle Projektarbeit für einen Auftrag oder Festanstellung für eine feste Stelle.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-engagement_type"><?php $form->error( "engagement_type" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_before_description_field', $model, $form ) ?>
	<?php $form->hidden( 'owner', array( 'value' => get_current_user_id() ) ) ?>
	<div class="form-group <?php echo $model->has_error( "description" ) ? "has-error" : null ?>">
		<?php $form->label( "description", array(
			"text"       => __( "Beschreibe die Stelle oder Aufgabe", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php if ( class_exists( 'JE_WYSIWYG' ) ): ?>
				<?php wp_editor( $model->description, 'job_description_editor', array(
					'textarea_name'     => $form->build_name( 'description' ),
					'teeny'             => false,
					'media_buttons'     => false,
					'quicktags'         => false,
					'wpautop'           => true,
					'drag_drop_upload'  => false,
					'tinymce'           => array(
						'toolbar1'   => 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_adv',
						'toolbar2'   => 'forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
						'height'     => 300,
						'resize'     => false,
						'statusbar'  => false,
						'elementpath' => false,
						'branding'   => false,
						'menubar'    => false,
						'plugins'    => 'paste,lists,textcolor,colorpicker,hr,charmap,link'
					)
				) ); ?>
			<?php else: ?>
				<?php $form->text_area( "description", array(
					"attributes" => array(
						"class" => "form-control",
						"style" => "height:150px"
					)
				) ) ?>
			<?php endif; ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Beschreibe Aufgaben, Ziel, Umfang und was dir bei der Zusammenarbeit wichtig ist.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-description"><?php $form->error( "description" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_description_field', $model, $form ) ?>
	<?php do_action( 'je_before_skill_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "skills" ) ? "has-error" : null ?>">
		<?php $form->label( "skills", array(
			"text"       => __( "Welche Fähigkeiten werden benötigt?", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $model->skills = ! empty( $model->skills ) ? implode( ',', $model->skills ) : ''; ?>
			<?php $form->hidden( 'skills', array(
				'attributes' => array(
					'id'    => 'jbp_skill_tag',
					'style' => 'width:100%'
				)
			) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Füge passende Fähigkeiten als Schlagwörter hinzu und trenne sie mit Kommas.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-skills"><?php $form->error( "skills" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_skill_field', $model, $form ) ?>
	<?php do_action( 'je_before_price_field', $model, $form ) ?>
	<div id="je-compensation-fields">
	<?php if ( je()->settings()->job_budget_range == 1 ): ?>
		<div class="form-group <?php echo $model->has_error( "min_budget" ) ? "has-error" : null ?>">
			<?php $form->label( "min_budget", array(
				"text"       => __( "Min. Budget", 'psjb' ),
				"attributes" => array( "class" => "col-lg-3 control-label je-compensation-min-label" )
			) ) ?>
			<div class="col-lg-9">
				<div class="input-group">
                    <span
	                    class="input-group-addon"><?php echo JobsExperts_Helper::format_currency( je()->settings()->currency ) ?></span>
					<?php $form->text( "min_budget", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
				</div>
				<span class="help-block job-field-hint"><?php esc_html_e( 'Gib den kleinsten Betrag an, den du einplanst.', 'psjb' ); ?></span>
				<span class="help-block m-b-none error-min_budget"><?php $form->error( "min_budget" ) ?></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group <?php echo $model->has_error( "max_budget" ) ? "has-error" : null ?>">
			<?php $form->label( "max_budget", array(
				"text"       => __( "Max. Budget", 'psjb' ),
				"attributes" => array( "class" => "col-lg-3 control-label je-compensation-max-label" )
			) ) ?>
			<div class="col-lg-9">
				<div class="input-group">
                    <span
	                    class="input-group-addon"><?php echo JobsExperts_Helper::format_currency( je()->settings()->currency ) ?></span>
					<?php $form->text( "max_budget", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
				</div>
				<span class="help-block job-field-hint"><?php esc_html_e( 'Gib den höchsten Betrag an, den du einplanst.', 'psjb' ); ?></span>
				<span class="help-block m-b-none error-max_budget"><?php $form->error( "max_budget" ) ?></span>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php else: ?>
		<div class="form-group <?php echo $model->has_error( "budget" ) ? "has-error" : null ?>">
			<?php $form->label( "budget", array(
				"text"       => __( "Budget", 'psjb' ),
				"attributes" => array( "class" => "col-lg-3 control-label je-compensation-label" )
			) ) ?>
			<div class="col-lg-9">
				<div class="input-group">
                    <span
	                    class="input-group-addon"><?php echo JobsExperts_Helper::format_currency( je()->settings()->currency ) ?></span>
					<?php $form->text( "budget", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
				</div>
				<span class="help-block job-field-hint"><?php esc_html_e( 'Gib ein realistisches Budget für den Auftrag an.', 'psjb' ); ?></span>
				<span class="help-block m-b-none error-budget"><?php $form->error( "budget" ) ?></span>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php endif; ?>
		<div class="form-group" id="je-compensation-period-row">
			<?php $form->label( "compensation_period", array(
				"text"       => __( "Gehaltszeitraum", 'psjb' ),
				"attributes" => array( "class" => "col-lg-3 control-label" )
			) ) ?>
			<div class="col-lg-9">
				<?php $form->select( "compensation_period", array(
					"data" => array(
						"year"  => __( "Pro Jahr", 'psjb' ),
						"month" => __( "Pro Monat", 'psjb' ),
						"hour"  => __( "Pro Stunde", 'psjb' )
					),
					"attributes" => array( "class" => "form-control" )
				) ) ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<?php do_action( 'je_after_price_field', $model, $form ) ?>
	<?php do_action( 'je_before_email_field', $model, $form ) ?>

	<div class="form-group <?php echo $model->has_error( "contact_email" ) ? "has-error" : null ?>">
		<?php $form->label( "contact_email", array(
			"text"       => __( "Kontakt Email", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<div class="input-group">
				<span class="input-group-addon">@</span>
				<?php $form->text( "contact_email", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
			</div>
			<span class="help-block job-field-hint"><?php esc_html_e( 'An diese Adresse können Interessierte ihre Anfrage senden.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-contact_email"><?php $form->error( "contact_email" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_email_field', $model, $form ) ?>
	<div class="form-group">
		<?php $form->label( "external_url_type", array(
			"text"       => __( "Externer Link", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $form->select( "external_url_type", array(
				"data" => array(
					"company"     => __( "Firmenwebseite", 'psjb' ),
					"application" => __( "Externes Bewerbungsformular", 'psjb' )
				),
				"attributes" => array( "class" => "form-control" )
			) ) ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group <?php echo $model->has_error( "external_url" ) ? "has-error" : null ?>">
		<?php $form->label( "external_url", array(
			"text"       => __( "URL (optional)", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-link"></i></span>
				<?php $form->text( "external_url", array( "attributes" => array( "class" => "form-control", "placeholder" => "https://" ) ) ) ?>
			</div>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Lass das Feld leer, wenn die Kontaktaufnahme direkt über das Jobboard erfolgen soll.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-external_url"><?php $form->error( "external_url" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_before_complete_date_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "schedule_mode" ) ? "has-error" : null ?>">
		<?php $form->label( "schedule_mode", array(
			"text"       => __( "Terminangabe", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $form->select( "schedule_mode", array(
				"data" => array(
					"date"        => __( "Konkretes Datum", 'psjb' ),
					"immediately" => __( "Ab sofort", 'psjb' ),
					"arrangement" => __( "Nach Absprache", 'psjb' ),
					"custom"      => __( "Eigene Angabe", 'psjb' )
				),
				"attributes" => array( "class" => "form-control", "id" => "je-schedule-mode" )
			) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Wähle die Terminart, die am besten zu deinem Job passt.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-schedule_mode"><?php $form->error( "schedule_mode" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="je-schedule-date-row" class="form-group <?php echo $model->has_error( "dead_line" ) ? "has-error" : null ?>">
		<?php $form->label( "dead_line", array(
			"text"       => __( "Fertigstellung bis", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label", "id" => "je-schedule-date-label" )
		) ) ?>
		<div class="col-lg-9">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				<?php $attributes = apply_filters( 'je_completion_date_attributes', array( "attributes" => array( "class" => "form-control datepicker" ) ) ) ?>
				<?php $form->text( "dead_line", $attributes ) ?>
			</div>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Wähle den gewünschten Abschluss- oder Starttermin.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-dead_line"><?php $form->error( "dead_line" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="je-schedule-text-row" class="form-group <?php echo $model->has_error( "schedule_text" ) ? "has-error" : null ?>">
		<?php $form->label( "schedule_text", array(
			"text"       => __( "Alternative Terminangabe", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $form->text( "schedule_text", array( "attributes" => array( "class" => "form-control", "maxlength" => "25" ) ) ) ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Zum Beispiel: „Im Laufe des Monats“ oder „Nach Rücksprache“.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-schedule_text"><?php $form->error( "schedule_text" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_complete_date_field', $model, $form ) ?>
	<?php do_action( 'je_before_open_for_field', $model, $form ) ?>
	<div class="form-group <?php echo $model->has_error( "open_for" ) ? "has-error" : null ?>">
		<?php $form->label( "open_for", array(
			"text"       => __( "Anzeige veröffentlichen für", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
		) ) ?>
		<div class="col-lg-9">
			<?php $days = je()->settings()->open_for_days;
			$days       = array_filter( explode( ',', $days ) );
			$data       = array();
			foreach ( $days as $day ) {
				$data[ $day ] = $day . ' ' . __( 'Tage', 'psjb' );
			}
			$data = apply_filters( 'je_open_days_limit', $data );

			$form->select( 'open_for', array(
				'data'       => $data,
				'nameless'   => __( '--Wähle--', 'psjb' ),
				'attributes' => array(
					'class' => 'form-control'
				)
			) ); ?>
			<span class="help-block job-field-hint"><?php esc_html_e( 'Danach endet die Anzeige automatisch und kann bei Bedarf neu veröffentlicht werden.', 'psjb' ); ?></span>
			<span class="help-block m-b-none error-open_for"><?php $form->error( "open_for" ) ?></span>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php do_action( 'je_after_open_for_field', $model, $form ) ?>
	<?php do_action( 'je_job_after_form', $model, $form ) ?>
	<?php $form->hidden( 'portfolios' );
	wp_nonce_field( 'je_job_form' ) ?>
	<div class="form-group">
            
            <?php $form->label( "job_img", array(
			"text"       => __( "Job-Bild", 'psjb' ),
			"attributes" => array( "class" => "col-lg-3 control-label" )
	    ) ) ?>
            
            <div class="col-md-9">
                
                <?php
                    $class = 'hidden';
                    if( isset( $model->job_img ) && $model->job_img != '' && is_numeric( $model->job_img ) ) {
                        $class = '';
                    }
                ?>
                <p class="hide-if-no-js">
                    <?php $form->hidden( 'job_img', array( 'attributes' => array( 'id' => 'job_img' ) ) ) ?>
                    <a title="Setze ein Bild für diesen Job" href="javascript:;" id="je_ftr_img" class="btn btn-primary"><?php _e( 'Bild setzen', 'psjb' ) ?></a>
                    <a title="Entferne das Job-Bild" href="javascript:;" id="je_ftr_img_rmv" class="btn btn-primary <?php echo $class; ?>"><?php _e( 'Bild entfernen', 'psjb' ) ?></a>
                </p>
                <?php
                    $image = wp_get_attachment_url( $model->job_img );
                ?>
                <div id="je_ftr_img_container" class="<?php echo $class ?>">
                    <img src="<?php echo $image; ?>" alt="" title="" width="100" />
                </div>
            
            
            
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
		<div class="col-md-12">
			<?php ig_uploader()->show_upload_control( $model, 'portfolios', false, array(
				'title' => __( "Füge technische Beispiele oder zusätzliche Informationen hinzu", 'psjb' )
			) ) ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-12">
			<?php if ( je()->settings()->job_new_job_status == 'publish' ): ?>
				<button class="btn btn-primary job-submit" name="status" value="publish"
				        type="submit"><?php _e( 'Veröffentlichen', 'psjb' ) ?></button>
			<?php else: ?>
				<button type="submit" class="btn btn-primary job-submit" name="status"
				        value="pending"><?php _e( 'Zur Überprüfung einreichen', 'psjb' ) ?></button>
			<?php endif; ?>
			<?php if ( je()->settings()->job_allow_draft == 1 ): ?>
				<button class="btn btn-info job-submit" name="status" value="draft"
				        type="submit"><?php _e( 'Entwurf speichern', 'psjb' ) ?></button>
			<?php endif; ?>
		</div>
	</div>
	<?php $form->close(); ?>
</div>
<?php $lang = defined( 'WPLANG' ) ? WPLANG : 'en_US';
$lang       = str_replace( '_', '-', $lang );
?>
<script type="text/javascript">
	jQuery(function ($) {
		function updateJobTypeFields() {
			var isEmployment = $('#je-engagement-type').val() === 'employment';
			$('.je-compensation-label').text(isEmployment ? '<?php echo esc_js( __( 'Gehalt (optional)', 'psjb' ) ); ?>' : '<?php echo esc_js( __( 'Budget', 'psjb' ) ); ?>');
			$('.je-compensation-min-label').text(isEmployment ? '<?php echo esc_js( __( 'Min. Gehalt (optional)', 'psjb' ) ); ?>' : '<?php echo esc_js( __( 'Min. Budget', 'psjb' ) ); ?>');
			$('.je-compensation-max-label').text(isEmployment ? '<?php echo esc_js( __( 'Max. Gehalt (optional)', 'psjb' ) ); ?>' : '<?php echo esc_js( __( 'Max. Budget', 'psjb' ) ); ?>');
			$('#je-compensation-period-row').toggle(isEmployment);
			$('#je-schedule-date-label').text(isEmployment ? '<?php echo esc_js( __( 'Einstellung ab', 'psjb' ) ); ?>' : '<?php echo esc_js( __( 'Fertigstellung bis', 'psjb' ) ); ?>');
		}

		function updateScheduleFields() {
			var mode = $('#je-schedule-mode').val();
			$('#je-schedule-date-row').toggle(mode === 'date');
			$('#je-schedule-text-row').toggle(mode === 'custom');
		}

		$('#je-engagement-type').on('change', updateJobTypeFields);
		$('#je-schedule-mode').on('change', updateScheduleFields);
		updateJobTypeFields();
		updateScheduleFields();

		// Flatpickr - moderner Vanilla-JS Datepicker (ersetzt jQuery UI)
		if (typeof flatpickr !== 'undefined') {
			flatpickr('.datepicker', {
				dateFormat: 'Y-m-d',
				minDate: '<?php echo date('Y-m-d') ?>',
				locale: typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.de ? flatpickr.l10ns.de : 'default',
				allowInput: true,
				disableMobile: false
			});
		} else {
			console.warn('Flatpickr is not loaded.');
		}
		
		$('#jbp_skill_tag').select2({
			tags: <?php echo json_encode(get_terms('jbp_skills_tag', array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
			placeholder: "<?php esc_attr_e('Füge ein Tag hinzu und trenne es durch Kommas','psjb'); ?>",
			tokenSeparators: [","],
			formatNoMatches: function () {
				return '<?php esc_attr_e('Keine Treffer gefunden','psjb') ?>'
			}
		});
		$('textarea#job_description_editor').closest('form').on('submit', function () {
			if (typeof tinyMCE !== 'undefined') {
				tinyMCE.triggerSave();
			}
		});
		$('.job-submit').on('click', function () {
			$(this).addClass('disabled').text('<?php echo esc_js(__("Einreichen...",'psjb')) ?>');
		})
	})
</script>