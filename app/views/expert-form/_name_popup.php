<div class="hide" id="name-template">
    <form class="can-edit-form form-horizontal" style="width: 100%;">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php _e("Vorname", 'psjb') ?></label>

            <div class="col-lg-9">
                <input type="text" id="first_name" name="first_name"
                       class="form-control input-sm" required value="<?php echo $model->first_name ?>">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php _e("Nachname", 'psjb') ?></label>

            <div class="col-lg-9">
                <input type="text" name="last_name" class="form-control input-sm" required value="<?php echo $model->last_name ?>">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-xs btn-primary"><?php _e("Speichern", 'psjb') ?></button>
                <button type="button"
                        class="btn btn-xs btn-default can-edit-cancel"><?php _e("Abbrechen", 'psjb') ?></button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>