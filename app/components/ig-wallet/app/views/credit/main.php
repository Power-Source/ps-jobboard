<div class="wrap">
    <h2><?php _e("Guthabenpakete", 'psjb') ?>
        <a href="" class="add-new-h2"><?php _e("Neues hinzufügen", 'psjb') ?></a></h2>

    <div class="ig-container">
        <?php if ($this->has_flash('plan_save')): ?>
            <div class="alert alert-success">
                <?php echo $this->get_flash('plan_save') ?>
            </div>
        <?php endif; ?>
        <br/>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-hover table-stripe">
                            <thead>
                            <tr>
                                <th><?php _e("Name", 'psjb') ?></th>
                                <th><?php _e("Guthaben", 'psjb') ?></th>
                                <th><?php _e("Kosten", 'psjb') ?></th>
                                <th><?php _e("Verkaufspreis", 'psjb') ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($models)): ?>
                                <?php foreach ($models as $row): ?>
                                    <tr>
                                        <td>
                                            <?php echo $row->title ?>
                                        </td>
                                        <td>
                                            <?php echo $row->credits ?>
                                        </td>
                                        <td>
                                            <?php echo JobsExperts_Helper::format_currency('', $row->cost) ?>
                                        </td>
                                        <td>
                                            <?php echo $row->sale_price ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-xs"
                                           href="<?php echo admin_url(add_query_arg('id', $row->product_id, 'admin.php?page=je-jobboard-settings&tab=wallet_plans')) ?>">

                                            <form style="display: inline;" method="post">
                                                <?php wp_nonce_field('je_delete_plan', 'je_delete_plan_nonce') ?>
                                                <input type="hidden" name="id" value="<?php echo $row->product_id ?>">
                                                <button onclick="return confirm('Bist du sicher?')" type="submit"
                                                        class="btn btn-xs btn-danger">
                                                    <?php _e("Löschen", 'psjb') ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <?php _e("Keine Daten verfügbar", 'psjb') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php $form = new IG_Active_Form($model);
                        $form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
                        <div class="form-group <?php echo $model->has_error("title") ? "has-error" : null ?>">
                            <?php $form->label("title", array("text" => "Titel", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text("title", array("attributes" => array("class" => "form-control"))) ?>
                                <span class="help-block m-b-none error-title"><?php $form->error("title") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("description") ? "has-error" : null ?>">
                            <?php $form->label("description", array("text" => "Beschreibung", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text_area("description", array("attributes" => array("class" => "form-control"))) ?>
                                <span
                                    class="help-block m-b-none error-description"><?php $form->error("description") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("credits") ? "has-error" : null ?>">
                            <?php $form->label("credits", array("text" => "Guthaben", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text("credits", array("attributes" => array("class" => "form-control"))) ?>
                                <span class="help-block m-b-none error-credits"><?php $form->error("credits") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("cost") ? "has-error" : null ?>">
                            <?php $form->label("cost", array("text" => "Preis", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <span
                                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                                    <?php $form->text("cost", array("attributes" => array("class" => "form-control"))) ?>
                                </div>
                                <span class="help-block m-b-none error-cost"><?php $form->error("cost") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("sale_price") ? "has-error" : null ?>">
                            <?php $form->label("sale_price", array("text" => "Angebotspreis", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <span
                                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                                    <?php $form->text("sale_price", array("attributes" => array("class" => "form-control"))) ?>
                                </div>
                                <span class="help-block m-b-none error-cost"><?php $form->error("sale_price") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php $form->hidden('product_id') ?>
                        <hr class="no-margin">
                        <div class="form-group">
                            <div class="checkbox col-md-10 col-md-offset-2">
                                <label>
                                    <?php
                                    $form->hidden('append_credits_info', array(
                                        'value' => 0
                                    ));
                                    $form->checkbox('append_credits_info', array(
                                        'attributes' => array(
                                            'value' => 1
                                        )
                                    )) ?> <?php _e("Füge Guthabeninformationen nach Produktname und Preis hinzu, z. B.: <em>10€ für 20 Guthaben</em>") ?>
                                </label>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button type="submit" name="je_credit_submit"
                                        value="<?php echo wp_create_nonce('ig_wallet_save_plan') ?>"
                                        class="btn btn-primary"><?php _e("Speichern", 'psjb') ?></button>
                                <a href="<?php echo admin_url('admin.php?page=je-jobboard-settings&tab=wallet_plans') ?>"
                                   class="btn btn-default"><?php _e("Formular zurücksetzen", 'psjb') ?></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php $form->close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>