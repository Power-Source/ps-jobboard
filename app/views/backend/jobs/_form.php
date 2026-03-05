<?php if ($this->has_flash('job_saved')): ?>
    <div class="alert alert-success">
        <?php echo $this->get_flash('job_saved') ?>
    </div>
<?php endif; ?>
<div id="job-form-container">
    <?php $form = new IG_Active_Form($model);
    $form->open(array("attributes" => array("class" => "form-horizontal")));
    $form->hidden('id') ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e('Allgemeine Information', 'psjb') ?></strong>
                </div>
                <div class="panel-body">
                    <div class="form-group <?php echo $model->has_error("job_title") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Job Titel', 'psjb') ?></label>

                        <div class="col-sm-4">
                            <?php $form->text('job_title', array('attributes' => array('class' => 'regular-text form-control'))) ?>
                            <span class="help-block m-b-none error-job_title"><?php $form->error("job_title") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("description") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Beschreibung', 'psjb') ?></label>

                        <div class="col-sm-8">
                            <?php wp_editor($model->description, 'description', array(
                                'name' => $form->build_name('description'),
                                'textarea_rows' => '8'
                            )) ?>
                            <span
                                class="help-block m-b-none error-job_title"><?php $form->error("description") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("user_login") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Job Anbieter', 'psjb') ?></label>

                        <div class="col-sm-8">
                            <?php
                            $users = get_users();
                            $form->select('owner', array(
                                'data' => array_combine(wp_list_pluck($users, 'ID'), wp_list_pluck($users, 'user_login'))
                            )) ?>
                            <span class="help-block m-b-none error-job_title"><?php $form->error("owner") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("status") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Job Status', 'psjb') ?></label>

                        <div class="col-sm-8">
                            <?php $form->select('status', array(
                                'data' => array(
                                    'publish' => 'Publish',
                                    'draft' => 'Draft'
                                )
                            )) ?>
                            <span class="help-block m-b-none error-job_title"><?php $form->error("status") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e('Job Meta', 'psjb') ?></strong>
                </div>
                <div class="panel-body">
                    <div
                        class="form-group <?php echo ($model->has_error("budget") || $model->has_error("min_budget") || $model->has_error("max_budget")) ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Budget (€)', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php if (!je()->settings()->job_budget_range): ?>
                                <?php $form->text('budget', array(
                                    'attributes' => array('class' => 'form-control')
                                )) ?>
                                <span class="help-block m-b-none error-job_title"><?php $form->error("budget") ?></span>
                            <?php else: ?>
                                <?php $form->text('min_budget', array(
                                    'attributes' => array('class' => 'form-control')
                                )) ?>
                                <span
                                    class="help-block m-b-none error-job_title"><?php $form->error("min_budget") ?></span>
                                <?php $form->text('max_budget', array(
                                    'attributes' => array('class' => 'form-control')
                                )) ?>
                                <span
                                    class="help-block m-b-none error-job_title"><?php $form->error("max_budget") ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("contact_email") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Kontakt Email', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php $form->text('contact_email', array(
                                'attributes' => array(
                                    'class' => 'regular-text form-control'
                                )
                            )) ?>
                            <span
                                class="help-block m-b-none error-job_title"><?php $form->error("contact_email") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("dead_line") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Fertigstellungstermin', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php
                            $form->text('dead_line', array(
                                'attributes' => array(
                                    'class' => 'datepicker regular-text form-control'
                                )
                            )) ?>
                            <span class="help-block m-b-none error-job_title"><?php $form->error("dead_line") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("open_for") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Job Offen für', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php $days = je()->settings()->open_for_days;
                            $days = array_filter(explode(',', $days));
                            $data = array();
                            foreach ($days as $day) {
                                $data[$day] = $day . ' ' . __('Tage', 'psjb');
                            }

                            $form->select('open_for', array(
                                'data' => apply_filters('je_open_days_limit', $data),
                                'nameless' => '--WÄHLEt--',
                                'attributes' => array(
                                    'class' => 'form-control'
                                )
                            ));
                            ?>
                            <span class="help-block m-b-none error-job_title"><?php $form->error("open_for") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <?php do_action('je_job_after_form', $model, $form) ?>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e( 'Ausgewähltes Bild', 'psjb' ) ?></strong>
                </div>
                <div class="panel-body">
                    <div class="form-group <?php echo $model->has_error("id") ? "has-error" : null ?>">
                        <label class="col-sm-5 control-label">
                            <?php _e( 'Lade ein Bild hoch oder wähle es aus', 'psjb' ) ?>
                        </label>
                        <div class="col-sm-7">
                            <?php
                                $class = 'hidden';
                                if( isset( $model->job_img ) && $model->job_img != '' && is_numeric( $model->job_img ) ) {
                                    $class = '';
                                }
                            ?>
                            <p class="hide-if-no-js">
                                <?php $form->hidden( 'job_img', array( 'attributes' => array( 'id' => 'job_img' ) ) ) ?>
                                <a title="Setze ausgewähltes Bild" href="javascript:;" id="je_ftr_img" class="button button-secondary"><?php _e( 'Bild setzen', 'psjb' ) ?></a>
                                <a title="Entferne ausgewähltes Bild" href="javascript:;" id="je_ftr_img_rmv" class="button button-secondary <?php echo $class; ?>"><?php _e( 'Bild entfernen', 'psjb' ) ?></a>
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
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e('Kategorie & Fähigkeiten', 'psjb') ?></strong>
                </div>
                <div class="panel-body">
                    <div class="form-group <?php echo $model->has_error("id") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Kategorie', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php
                            foreach ((array)$model->categories as $k => $cat) {
                                $c = term_exists($cat, 'jbp_category');
                                if (is_array($c)) {
                                    $model->categories[$k] = $c['term_id'];
                                }
                            }
                            ?>
                            <?php $form->select('categories', array(
                                'data' => array_combine(wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'term_id'), wp_list_pluck(get_terms('jbp_category', 'hide_empty=0'), 'name')),
                                'attributes' => array(
                                    'class' => 'regular-text'
                                )
                            )); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("id") ? "has-error" : null ?>">
                        <label
                            class="col-sm-2 control-label"><?php _e('Fähigkeiten', 'psjb') ?>
                        </label>

                        <div class="col-sm-8">
                            <?php $model->skills = !empty($model->skills) ? implode(',', $model->skills) : ''; ?>
                            <?php $form->hidden('skills', array('attributes' => array('id' => 'jbp_skill_tag', 'style' => 'width:100%'))) ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php $form->hidden('portfolios') ?>
        <div class="col-md-12">
            <?php ig_uploader()->show_upload_control($model, 'portfolios', true, array(
                'title' => __("Fügen technische Beispiele oder zusätzliche Informationen hinzu", 'psjb')
            )); ?>
        </div>
        <?php wp_nonce_field('ig_job_add') ?>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary"><?php _e("Änderungen speichern", 'psjb') ?></button>
        </div>
    </div>
    <?php $form->close(); ?>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d',
                minDate: '<?php echo date('Y-m-d') ?>',
                locale: typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.de ? flatpickr.l10ns.de : 'default',
                allowInput: true
            });
        }
        $('#jbp_skill_tag').select2({
            tags: <?php echo json_encode(get_terms('jbp_skills_tag', array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
            placeholder: "<?php esc_attr_e('Füge ein Tag hinzu und trenne es durch Kommas', 'psjb'); ?>",
            tokenSeparators: [","]
        });
    })
</script>