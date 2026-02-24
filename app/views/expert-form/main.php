<div class="ig-container">
    <div class="hn-container">
        <?php do_action('je_begin_expert_form') ?>
        <?php $form = new IG_Active_Form($model);
        $form->open(array("attributes" => array("class" => "form-horizontal")));
        ?>
        <div class="jobs-expert-form">
            <?php if (is_array($model->get_errors()) && count($model->get_errors())): ?>
                <div class="alert alert-danger">
                    <?php echo implode('<br/>', $model->get_errors()) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- LEFT COLUMN: Avatar & Tagline -->
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->render_partial('expert-form/_avatar_upload', array(
                                'model' => $model
                            )) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tag-line">
                                <div class="panel panel-default" style="z-index: 0">
                                    <div class="panel panel-heading">
                                        <?php _e('Meine Einstellung', 'psjb') ?>
                                    </div>
                                    <div class="panel-body">
                                        <div class="inline-edit-field">
                                            <?php $form->text_area('short_description', array(
                                                'attributes' => array(
                                                    'class' => 'form-control',
                                                    'placeholder' => __("Kurze Beschreibung oder Motto (max. 100 Zeichen)", 'psjb'),
                                                    'rows' => 6,
                                                    'maxlength' => 100,
                                                    'style' => 'resize: both; min-height: 140px;'
                                                )
                                            )); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Main Form Data -->
                <div class="col-md-8">
                    <!-- Name Section -->
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h2 style="margin-top: 0;">
                                    <?php $form->text('first_name', array(
                                        'attributes' => array(
                                            'class' => 'form-control inline-edit-name',
                                            'placeholder' => __("ॠVorname", 'psjb'),
                                            'required' => true,
                                            'style' => 'display: inline-block; width: 48%; margin-right: 2%;'
                                        )
                                    )); ?>
                                    <?php $form->text('last_name', array(
                                        'attributes' => array(
                                            'class' => 'form-control inline-edit-name',
                                            'placeholder' => __("ॠNachname", 'psjb'),
                                            'required' => true,
                                            'style' => 'display: inline-block; width: 50%;'
                                        )
                                    )); ?>
                                </h2>
                            </div>
                        </div>
                        <h4><?php echo sprintf(__('Mitglied seit %s', 'psjb'), date("M Y", strtotime(get_the_author_meta('user_registered', $model->user_id)))) ?></h4>
                    </div>

                    <!-- Company -->
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="glyphicon glyphicon-briefcase"></i>
                                <?php _e('Unternehmen:', 'psjb') ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                            <?php $form->text('company', array(
                                'attributes' => array(
                                    'class' => 'form-control',
                                    'placeholder' => __("Dein Unternehmen", 'psjb'),
                                    'style' => 'border: none; padding: 0; box-shadow: none;'
                                )
                            )); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Location -->
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="glyphicon glyphicon-map-marker"></i> <?php _e('Standort:', 'psjb') ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                            <?php echo apply_filters('je_expert_form_location_field', 
                                $form->text('location', array(
                                    'attributes' => array(
                                        'class' => 'form-control',
                                        'placeholder' => __("Dein Standort", 'psjb'),
                                        'style' => 'border: none; padding: 0; box-shadow: none;'
                                    )
                                )), 
                                $model
                            ); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Email -->
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="fa fa-envelope"></i> <?php _e('Kontakt Email:', 'psjb') ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                            <?php $form->text('contact_email', array(
                                'attributes' => array(
                                    'class' => 'form-control',
                                    'placeholder' => __("Deine Kontakt-E-Mail", 'psjb'),
                                    'required' => true,
                                    'style' => 'border: none; padding: 0; box-shadow: none;',
                                    'type' => 'email'
                                )
                            )); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Biography & Skills Tabs -->
                    <div class="row">
                        <div class="col-md-12">
                            <br/>
                            <div id="expert-content-tabs">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li><a href="#biography"><?php _e('Biografie', 'psjb') ?></a></li>
                                    <li><a href="#profile"><?php _e('Soziales & Fähigkeiten', 'psjb') ?></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="biography">
                                        <?php 
                                        if (class_exists('JE_WYSIWYG')) {
                                            // WYSIWYG-Addon aktiviert: WordPress-Editor verwenden (nur visueller Modus)
                                            echo '<div style="margin-bottom: 10px; color: #666; font-size: 14px;">';
                                            _e('Biografie (erforderlich, mindestens 200 Zeichen)', 'psjb');
                                            echo '</div>';
                                            
                                            // CSS zum Verstecken von überflüssigen Elementen
                                            ?>
                                            <style type="text/css">
                                                /* Textarea verstecken */
                                                #biography textarea#biography,
                                                #biography #biography,
                                                textarea#biography { 
                                                    display: none !important; 
                                                    visibility: hidden !important;
                                                    opacity: 0 !important;
                                                    position: absolute !important;
                                                    left: -9999px !important;
                                                }
                                                /* Editor anzeigen */
                                                #biography .mce-tinymce,
                                                #biography #wp-biography-wrap { 
                                                    display: block !important; 
                                                }
                                                /* Überflüssige Tools/Icons/Statusbar verstecken */
                                                #wp-biography-editor-tools,
                                                #biography .mce-fullscreen,
                                                #biography .wp-editor-tools,
                                                #biography .mce-statusbar,
                                                #biography .mce-path,
                                                #biography .mce-resizehandle,
                                                #wp-biography-wrap .mce-statusbar,
                                                .mce-container .mce-statusbar,
                                                #wp-biography-editor-container .mce-statusbar,
                                                #wp-biography-editor-container .mce-path,
                                                #wp-biography-editor-container .mce-resizehandle,
                                                #wp-biography-editor-container > .mce-container:first-child > .mce-btn:last-child { 
                                                    display: none !important; 
                                                    visibility: hidden !important;
                                                }
                                            </style>
                                            <?php
                                            
                                            wp_editor($model->biography, 'biography', array(
                                                'textarea_name' => 'biography',
                                                'teeny' => false,
                                                'media_buttons' => false,
                                                'quicktags' => false,
                                                'wpautop' => true,
                                                'drag_drop_upload' => false,
                                                'editor_class' => '',
                                                'tinymce' => array(
                                                    'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_adv',
                                                    'toolbar2' => 'forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
                                                    'height' => 300,
                                                    'resize' => false,
                                                    'statusbar' => false,
                                                    'elementpath' => false,
                                                    'branding' => false,
                                                    'menubar' => false,
                                                    'wpautop' => true,
                                                    'indent' => false,
                                                    'plugins' => 'paste,lists,textcolor,colorpicker,hr,charmap,link',
                                                    'remove_linebreaks' => false,
                                                    'apply_source_formatting' => true
                                                )
                                            ));
                                            ?>
                                            <script type="text/javascript">
                                            jQuery(document).ready(function($) {
                                                // NUR das Textarea verstecken
                                                $('textarea#biography').hide().css({'display': 'none', 'visibility': 'hidden', 'opacity': 0});
                                                
                                                // TinyMCE: Content aus Textarea holen und explizit setzen
                                                if (typeof tinymce !== 'undefined') {
                                                    var attempts = 0;
                                                    var checkEditor = setInterval(function() {
                                                        attempts++;
                                                        var editor = tinymce.get('biography');
                                                        if (editor) {
                                                            clearInterval(checkEditor);
                                                            
                                                            // Kurz warten bis Editor voll initialisiert ist
                                                            setTimeout(function() {
                                                                // Content aus dem versteckten Textarea holen
                                                                var textareaContent = $('textarea#biography').val();
                                                                
                                                                // Nur setzen wenn Textarea nicht leer ist
                                                                if (textareaContent && textareaContent.trim() !== '') {
                                                                    editor.setContent(textareaContent);
                                                                }
                                                            }, 200);
                                                        }
                                                        
                                                        // Abbruch nach 50 Versuchen (5 Sekunden)
                                                        if (attempts > 50) {
                                                            clearInterval(checkEditor);
                                                        }
                                                    }, 100);
                                                }
                                                
                                                // TinyMCE Save vor Submit
                                                $('.je-expert-submit').on('click', function(e) {
                                                    if (typeof tinyMCE !== 'undefined') {
                                                        tinyMCE.triggerSave();
                                                    }
                                                });
                                            });
                                            </script>
                                            <?php
                                        } else {
                                            // Kein Addon: Einfaches Textarea
                                            ?>
                                            <label><?php _e('Biografie (erforderlich, mindestens 200 Zeichen)', 'psjb') ?></label>
                                            <?php
                                            $form->text_area('biography', array(
                                                'attributes' => array(
                                                    'class' => 'form-control',
                                                    'placeholder' => __("Erzähle uns von dir...", 'psjb'),
                                                    'rows' => 12,
                                                    'id' => 'biography-editor',
                                                    'required' => true,
                                                    'minlength' => 200,
                                                    'style' => 'min-height: 200px;'
                                                )
                                            ));
                                        }
                                        ?>
                                    </div>
                                    <div id="profile">
                                        <div class="social-skill">
                                            <?php ig_skill()->display($model, 'skills', 'skill-input') ?>
                                            <?php ig_social_wall()->display($model, 'social', 'social-input') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Portfolio Section -->
            <div class="row">
                <div class="col-md-12">
                    <?php ig_uploader()->show_upload_control($model, 'portfolios', false, array(
                        'title' => __("Arbeitsbeispiele oder zusätzliche Informationen", 'psjb')
                    )) ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <br/>

            <!-- Hidden Fields & Buttons -->
            <div class="row" style="margin-bottom: 5px;border-width: 1px;position:relative;">
                <div class="col-md-12" style="margin-left: 0">
                    <?php
                    $form->hidden('id');
                    $form->hidden('company_url');
                    if (empty($model->user_id)) {
                        $form->hidden('user_id', array('value' => get_current_user_id()));
                    } else {
                        $form->hidden('user_id');
                    }
                    $form->hidden('social', array('attributes' => array('id' => 'social-input')));
                    $form->hidden('skills', array('attributes' => array('id' => 'skill-input')));
                    $form->hidden('portfolios');
                    wp_nonce_field('jbp_add_pro', '_wpnonce', true, true);
                    ?>

                    <?php if (je()->settings()->expert_new_expert_status == 'publish'): ?>
                        <button class="submit btn btn-small btn-primary je-expert-submit" name="status" value="publish" type="button">
                            <?php _e('Veröffentlichen', 'psjb') ?>
                        </button>
                    <?php else: ?>
                        <button class="submit btn btn-small btn-primary je-expert-submit" name="status" value="pending" type="button">
                            <?php _e('Zur Überprüfung einreichen', 'psjb') ?>
                        </button>
                    <?php endif; ?>

                    <?php if (je()->settings()->expert_allow_draft == 1): ?>
                        <button class="submit btn btn-small btn-info je-expert-submit" name="status" value="draft" type="button">
                            <?php _e('Entwurf speichern', 'psjb') ?>
                        </button>
                    <?php endif; ?>

                    <button onclick="location.href='<?php echo get_post_type_archive_link('jbp_pro') ?>'" type="button" class="btn btn-default btn-small pull-right">
                        <?php _e('Abbrechen', 'psjb') ?>
                    </button>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <?php $form->close() ?>
        <?php do_action('je_end_expert_form') ?>
    </div>
</div>


<script type="text/javascript">
    jQuery(function ($) {
        // Initialize tabs (vanilla JS, no jQuery UI)
        var tabsContainer = $('#expert-content-tabs');
        var tabLinks = tabsContainer.find('.nav-tabs a');
        var tabPanes = tabsContainer.find('.tab-content > div');
        
        // Add .tab-pane class for Bootstrap CSS
        tabPanes.addClass('tab-pane');
        
        // Show first tab
        if (tabPanes.length > 0) {
            $(tabPanes[0]).addClass('active');
            tabLinks.eq(0).parent().addClass('active');
        }
        
        // Handle tab clicks
        tabLinks.on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            
            // Remove active from all
            tabPanes.removeClass('active');
            tabLinks.parent().removeClass('active');
            
            // Add active to selected
            $(href).addClass('active');
            $(this).parent().addClass('active');
        });

        // Get form and expert validator
        var form = $(".form-horizontal");
        var expertFormValidator = null;

        // Initialize modern form validation after a delay to ensure all editors are loaded
        setTimeout(function() {
            console.log('Form Validation Init check...');
            console.log('typeof FormValidator:', typeof FormValidator);
            console.log('FormValidator:', FormValidator);
            
            if (typeof FormValidator !== 'undefined') {
                // Der richtige Selektor: form.form-horizontal (das parent Form element)
                expertFormValidator = new FormValidator('.form-horizontal', {
                    realTimeValidation: true
                });
                console.log('expertFormValidator created:', expertFormValidator);
                console.log('expertFormValidator.validate:', expertFormValidator.validate);
            } else {
                console.warn('FormValidator not loaded!');
            }
        }, 1000);

        // Handle button clicks for save/publish
        $('.je-expert-submit').on('click', function (e) {
            e.preventDefault();
            var button = $(this);
            var status = button.attr('value'); // 'draft', 'pending', or 'publish'

            // Draft saves without validation
            if (status === 'draft') {
                submitExpertForm(form, status, button);
            } else {
                // Publish/Pending requires validation
                // Use modern FormValidator if available, fallback to custom validation
                if (expertFormValidator && expertFormValidator.validate()) {
                    submitExpertForm(form, status, button);
                } else if (expertFormValidator) {
                    // Errors already displayed by FormValidator
                } else {
                    // Fallback validation if FormValidator not loaded yet
                    if (validateMainFormFields(form)) {
                        submitExpertForm(form, status, button);
                    }
                }
            }
        });

        /**
         * Fallback Validation - for use if FormValidator lib doesn't load
         */
        function validateMainFormFields(form) {
            var isValid = true;
            var errors = [];

            // Check biography field (TinyMCE)
            if (typeof tinyMCE !== 'undefined') {
                var bioEditor = tinyMCE.get('biography');
                if (bioEditor) {
                    var bioContent = bioEditor.getContent().trim();
                    var plainText = bioContent.replace(/<[^>]*>/g, '').trim();
                    if (plainText.length === 0) {
                        errors.push('<?php echo esc_js(__("Die Biografie ist erforderlich")) ?>');
                        isValid = false;
                    } else if (plainText.length < 200) {
                        errors.push('<?php echo esc_js(__("Die Biografie muss mindestens 200 Zeichen lang sein")) ?>');
                        isValid = false;
                    }
                }
            }

            // Check required fields
            form.find('input[required], textarea[required], select[required]').each(function() {
                if ($(this).closest('.skill-form-container, .social-form-container, .avatar-upload-form, .uploader-form-container').length > 0) {
                    return;
                }

                var field = $(this);
                var value = field.val().trim();
                if (value === '') {
                    var label = field.closest('.row').find('label').first().text() || field.attr('placeholder');
                    errors.push(label + ' <?php echo esc_js(__("ist erforderlich")) ?>');
                    isValid = false;
                }
            });

            if (!isValid) {
                alert(errors.join('\n'));
            }

            return isValid;
        }

        /**
         * Submit form via AJAX
         */
        function submitExpertForm(form, status, button) {
            var originalButtonText = button.text();
            button.addClass('disabled').text('<?php echo esc_js(__("wird verarbeitet...")) ?>');

            var formData = form.serialize() + '&status=' + status;

            $.ajax({
                type: 'POST',
                url: form.attr('action') || window.location.href,
                data: formData,
                beforeSend: function() {
                    if (typeof tinyMCE !== 'undefined') {
                        tinyMCE.triggerSave();
                    }
                },
                success: function(response) {
                    var data = response;
                    if (typeof response === 'string') {
                        try {
                            data = JSON.parse(response);
                        } catch(e) {
                            data = null;
                        }
                    }

                    if (data && data.success === true) {
                        if (status === 'draft') {
                            button.removeClass('disabled').text(originalButtonText);
                        } else {
                            if (data.data && data.data.redirect_url) {
                                window.location.href = data.data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }
                    } else if (data && data.success === false) {
                        button.removeClass('disabled').text(originalButtonText);
                        var errorMsg = '<?php echo esc_js(__("Validierung fehlgeschlagen. Bitte überprüfen Sie Ihre Eingaben.")) ?>';
                        if (data.data && data.data.errors) {
                            errorMsg += '\n\nFehler:\n';
                            for (var field in data.data.errors) {
                                if (data.data.errors[field]) {
                                    errorMsg += '- ' + field + ': ' + data.data.errors[field].join(', ') + '\n';
                                }
                            }
                        }
                        alert(errorMsg);
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    button.removeClass('disabled').text(originalButtonText);
                    alert('<?php echo esc_js(__("Es gab einen Fehler beim Speichern. Bitte versuchen Sie es erneut.")) ?>');
                }
            });
        }
    });
</script>

