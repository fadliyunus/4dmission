<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">

                        <h3>Login to your Account</h3>
                        <p>Don't have an account yet? <a href="<?= base_url('home/register') ?>">Sign Up Here</a></p>

                        <?php if (isset($message)) : ?>
                            <div class="alert alert-danger"><?= $message ?></div>
                        <?php endif; ?>
                        <?= $this->form_builder->open_form(array('id' => 'login-form', 'action' => '', 'class' => 'mb-0')) ?>
                        <?= $this->form_builder->build_form_horizontal($form) ?>
                        <?= $this->form_builder->close_form() ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->