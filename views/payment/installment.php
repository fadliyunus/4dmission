<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">
                        <div class="fancy-title title-dotted-border title-center">
                            <h3><span>Pembayaran</span></h3>
                        </div>
                        <?php if (isset($message) && $message != '') : ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message) && $error_message != '') : ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php endif; ?>
                       
                        <p>Apabila anda sudah melakukan pembayaran, upload bukti transfer melalui form dibawah ini.</p>
                        <?php echo $this->form_builder->open_form(array('action' => '', 'id' => "payment_form", 'name' => "payment_form", 'class' => "form-horizontal")) ?>
                        <?php echo $this->form_builder->build_form_horizontal($form) ?>
                        <?php echo $this->form_builder->close_form() ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->