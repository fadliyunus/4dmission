<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <?php if (isset($form)) : ?>
                    <?= $this->form_builder->open_form(array('action' => '')) ?>
                    <?= $this->form_builder->build_form_horizontal($form) ?>
                    <?= $this->form_builder->close_form(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>