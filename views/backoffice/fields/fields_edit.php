<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <?php if (isset($form)) : ?>
                <?= $this->form_builder->open_form(array('action' => '', 'id' => 'form-edit')) ?>
                <?= $this->form_builder->build_form_horizontal($form) ?>
                <?= $this->form_builder->close_form(); ?>
            <?php endif; ?>
        </div>
    </div>

</div>
<div class="modal fade" id="optionModal" tabindex="-1" role="dialog" aria-labelledby="optionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="optionModalLabel">Add option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit-option">
                    <?= form_hidden('index') ?>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label">Text</label>
                        <div class="col-sm-12">
                            <?= form_input($option_text); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label">Value</label>
                        <div class="col-sm-12">
                            <?= form_input($option_value); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-submit-option">Save changes</button>
                <button type="button" class="btn btn-secondary" id="btn-cancel-option" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>