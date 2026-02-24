<div class="hide" id="biography-template">
    <form class="can-edit-form form-horizontal" style="width: 100%;">
        <div class="form-group">
            <div class="col-lg-12">
                <textarea name="biography" class="form-control input-sm" required minlength="200" style="height: 150px"><?php echo $model->biography ?></textarea>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-xs btn-primary"><?php _e("Speichern", 'psjb') ?></button>
                <button type="button"
                        class="btn btn-xs btn-default can-edit-cancel"><?php _e("Abbrechen", 'psjb') ?></button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>