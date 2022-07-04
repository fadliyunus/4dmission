<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger p-2"><?= $message ?></div>
                <?php endif; ?>
                <?php if (isset($form)) : ?>
                    <?= $this->form_builder->open_form(array('action' => '', 'id'=>'skema-form')) ?>
                    <?= $this->form_builder->build_form_horizontal($form) ?>
                    <?= $this->form_builder->build_form_horizontal($form_2) ?>
                    <?= $this->form_builder->close_form(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>