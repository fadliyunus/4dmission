<script type="text/javascript">
    var program_studi_data = <?php echo json_encode($program_studi_data) ?>;
    var seleksi_data = <?php echo json_encode($seleksi_data) ?>;
    var jadwal_data = <?php echo json_encode($jadwal_data) ?>;
</script>
<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">

                        <?php if (isset($message)) : ?>
                            <div class="alert alert-danger"><?= $message ?></div>
                        <?php endif; ?>
                        <?= $this->form_builder->open_form(array('id' => 'register-form', 'action' => '', 'class' => 'mb-0', 'autocomplete' => 'off')) ?>
                        <?= $this->form_builder->build_form_horizontal($form) ?>
                        <?= $this->form_builder->close_form() ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->