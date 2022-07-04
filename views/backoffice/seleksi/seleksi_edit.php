<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                <?php endif; ?>

                <?php if (isset($form)) : ?>
                    <?= $this->form_builder->open_form(array('action' => '', 'id' => 'form-edit')) ?>
                    <?= $this->form_builder->build_form_horizontal($form) ?>
                    <?= $this->form_builder->close_form(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="jadwalModal" tabindex="-1" role="dialog" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jadwalModalLabel">Add Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (isset($form_modal)) : ?>
                    <?= $this->form_builder->open_form(array('action' => '', 'id' => 'form-edit-jadwal')) ?>
                    <?= $this->form_builder->build_form_horizontal($form_modal) ?>
                    <?= $this->form_builder->close_form(); ?>
                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-submit-jadwal">Save changes</button>
                <button type="button" class="btn btn-secondary" id="btn-cancel-jadwal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>