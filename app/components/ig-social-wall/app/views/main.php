<div class="panel panel-default">
    <div class="panel-heading">
        <?php _e("Social Profile", ig_social_wall()->domain) ?>
        <button type="button"
                class="btn btn-primary btn-xs pull-right add-social"><?php _e('Hinzufügen', ig_social_wall()->domain) ?>
            <i class="glyphicon glyphicon-plus"></i></button>
    </div>
    <div class="panel-body">
        <!-- Inline Formular Container -->
        <div class="social-form-container" style="display:none; margin-bottom: 20px;"></div>
        
        <ul class="jbp-socials">
            <?php foreach ($models as $model) {
                echo '<li>';
                $this->render_partial('_icon', array(
                    'data' => $model->export(),
                    'social' => ig_social_wall()->social($model->name)
                ));
                echo '</li>';
            } ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        var currentEditingSocial = null;

        $('body').on('change', 'select[name="social"]', function () {
            var socials = <?php echo json_encode(ig_social_wall()->get_social_list()) ?>;
            var select = $(this);
            var container = select.closest('.social-add-form');
            var data = socials[select.val()];
            var preview = container.find('.social-preview');

            if (!data) {
                preview.find('h4').text('');
                preview.find('img').removeAttr('src');
                container.find('.note').text('');
                return;
            }

            preview.find('h4').text(data.name);
            preview.find('img').attr('src', data.url);
            container.find('.note').text(capitaliseFirstLetter(data.type));
        });
        
        // Formular inline laden (Hinzufügen)
        $('body').on('click', '.add-social', function () {
            var container = $('.social-form-container');
            var button = $(this);
            
            if (container.is(':visible')) {
                container.slideUp().html('');
                return;
            }
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php?action=social_wall_form&_wpnonce='.wp_create_nonce('social_wall_form')) ?>',
                success: function(data) {
                    container.html(data).slideDown();
                    container.find('select[name="social"]').trigger('change');
                    currentEditingSocial = button;
                }
            });
        });
        
        // Formular inline laden (Bearbeiten)
        $('body').on('click', '.jbp-social', function () {
            var id = $(this).data('id');
            var container = $('.social-form-container');
            var socialElement = $(this);
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php?action=social_wall_form&_wpnonce='.wp_create_nonce('social_wall_form')) ?>&parent_id=<?php echo $parent->id ?>&id=' + id,
                success: function(data) {
                    container.html(data).slideDown();
                    currentEditingSocial = socialElement;
                }
            });
        });

        $('body').on('click', '.hn-save-social', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.social-form-container .social-form').trigger('ig-social-submit');
        });

        $('body').on('ig-social-submit', '.social-form', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            var form = $(this);
            var parent = $('.social-add-form').parent();
            var target = $('#<?php echo $element ?>');
            var container = $('.social-form-container');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                beforeSend: function () {
                    parent.find('.ig-overlay').removeClass('hide');
                    parent.find('.alert').addClass('hide');
                },
                data: {
                    action: 'social_add',
                    name: form.find('select[name="social"]').val(),
                    value: form.find('input[name="value"]').val(),
                    parent_id: '<?php echo $parent->id ?>',
                    _wpnonce: '<?php echo wp_create_nonce('social_add') ?>'
                },
                success: function (data) {
                    parent.find('.ig-overlay').addClass('hide');
                    data = $.parseJSON(data);

                    if (data.status == 0) {
                        parent.find('.alert').html(data.errors).removeClass('hide');
                    } else {
                        var element = $(data.html);
                        if (currentEditingSocial.hasClass('add-social')) {
                            //check does this appear in the pool
                            var exist = $('.jbp-socials').find("[data-id='" + element.data('id') + "']");
                            if (exist.length > 0) {
                                exist.replaceWith(element);
                                element.addClass('animated flash').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                                    element.removeClass('animated flash');
                                });
                            } else {
                                $('.jbp-socials').append($('<li/>').html(data.html));
                            }
                            target.val(target.val() + ',' + element.data('id'));
                        } else {
                            currentEditingSocial.replaceWith(element);
                            target.val(target.val().replace(currentEditingSocial.data('id'), element.data('id')));
                        }
                        container.slideUp().html('');
                        currentEditingSocial = null;
                    }
                }
            })
            return false;
        })
        
        $('body').on('click', '.hn-cancel-social', function () {
            $('.social-form-container').slideUp().html('');
            currentEditingSocial = null;
        });
        
        $('body').on('click', '.hn-delete-social', function () {
            var target = $('#<?php echo $element ?>');
            target.val(target.val().replace(currentEditingSocial.data('id'), ''));
            currentEditingSocial.remove();
            $('.social-form-container').slideUp().html('');
            currentEditingSocial = null;
        })
        
        function capitaliseFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    })
</script>
