<div class="ig-container">
    <div id="tabs">
        <ul class="nav nav-tabs">
            <li><a href="#my-wallets"><?php _e("Guthaben", 'psjb') ?></a></li>
            <li><a href="#purcharse-history"><?php _e("Kaufverlauf", 'psjb') ?></a></li>
        </ul>
        <div class="tab-content" style="clear: both; padding: 12px 10px 0 10px;">
            <div id="my-wallets">
                <h3><?php _e("Guthabensaldo", 'psjb') ?></h3>

                <p><?php _e("Guthaben", 'psjb') ?> <span class="label label-info">
                    <?php echo User_Credit_Model::get_balance(get_current_user_id()) ?>
                </span>
                </p>

                <p>
                    <a class="btn btn-info" href="<?php echo get_permalink(ig_wallet()->settings()->plans_page) ?>">
                        <?php _e("Guthaben Kaufen", 'psjb') ?>
                    </a>
                </p>
            </div>
            <div id="purcharse-history">
                <?php $logs = User_Credit_Model::get_logs();
                $cats = array();
                foreach ($logs as $key) {
                    $cats[] = $key['category'];
                }
                $cats = array_unique(array_filter($cats));
                if (!empty($cats)) {
                    $cats = array_merge(array(__("Alle", 'psjb')), $cats);
                }
                ?>
                <div class="log-cats text-right">
                    <?php
                    foreach ($cats as $cat) {
                        ?>
                        <button type="button" data-category="<?php echo sanitize_title($cat) ?>"
                                class="btn btn-default btn-xs"><?php echo $cat ?></button>
                    <?php
                    }
                    ?>
                </div>
                <table class="table" id="purchase-log-table">
                    <thead>
                    <tr>
                        <th><?php _e("Datum", 'psjb') ?></th>
                        <th><?php _e("Detail", 'psjb') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($logs) && count($logs)): ?>
                        <?php
                        $date_format = get_option('date_format');
                        $time_format = get_option('time_format');
                        ?>
                        <?php foreach ($logs as $log): ?>
                            <tr data-cat="<?php echo sanitize_title($log['category']) ?>">
                                <td><?php echo date($date_format . ' ' . $time_format, $log['date']) ?></td>
                                <td><?php echo $log['reason'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><?php _e('Keine Daten verfügbar', 'psjb') ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function ($) {
        // Initialize tabs (vanilla JS, no jQuery UI)
        var tabsContainer = $("#tabs");
        var tabLinks = tabsContainer.find('.nav-tabs a[href^="#"]');
        var tabPanes = tabsContainer.find('.tab-content > div');

        tabPanes.hide();
        tabLinks.parent('li').removeClass('active');
        if (tabPanes.length > 0) {
            $(tabPanes[0]).show();
            tabLinks.first().parent('li').addClass('active');
        }

        tabLinks.on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');

            tabLinks.parent('li').removeClass('active');
            $(this).parent('li').addClass('active');
            tabPanes.hide();
            $(target).show();
        });
        $('.log-cats button').on('click', function () {
            var cat = $(this).data('category');

            if (cat == '<?php echo sanitize_title(__('Alle','psjb')) ?>') {
                $('#purchase-log-table').find('tr').show();
            } else {
                var trs = $('#purchase-log-table tbody').find('tr');
                trs.each(function (i, v) {
                    if ($(this).data('cat') != cat) {
                        $(this).hide();
                    }else{
                        $(this).show();
                    }
                })
            }
            $('.log-cats button').removeClass('active');
            $(this).addClass('active');
        })
    });
</script>