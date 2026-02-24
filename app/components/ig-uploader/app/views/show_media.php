<?php if (is_array($models) && count($models)): ?>
    <!-- Inline Formular Container -->
    <div class="uploader-form-container" style="display:none; margin-bottom: 20px;"></div>
    
    <div class="file-view-port">
        <?php foreach ($models as $model): ?>
            <?php $this->render_partial(apply_filters('igu_icon_file_template', '_single_icon'), array(
                'model' => $model
            )) ?>
            <?php
            $this->footer_model[] = $model;
            add_action('wp_footer', array(&$this, 'footer_modal')) ?>
        <?php endforeach; ?>
    </div>
    <script type="text/javascript">
        jQuery(function ($) {
            $('.igu-media-icon').on('mouseenter', function () {
                $(this).find('.igu-media-info').removeClass('hide');
            }).on('mouseleave', function () {
                $(this).find('.igu-media-info').addClass('hide');
            });
            $('.igu-media-info a').on('click', function (e) {
                e.preventDefault();
            })
        })
    </script>
    <div class="clearfix"></div>
<?php else: ?>
    <p class="no-file"><?php _e("Keine Beispieldatei.", ig_uploader()->domain) ?></p>
<?php endif; ?>