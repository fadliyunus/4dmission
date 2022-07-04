<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">
                        <div class="fancy-title title-dotted-border title-center">
                            <h3><span>Pendaftaran</span></h3>
                        </div>

                        <?php if (isset($message) && $message != '') : ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        <?php if ($admission->status == 400 || $admission->status == 401) : ?>
                            <?php if ($admission->result_sent) : ?>
                                <div>
                                    <p>Hasil seleksi anda telah dikirim ke email anda.</p>
                                </div>
                            <?php else : ?>
                                <div>
                                    <p>Harap tunggu maksimal 2 x 24 jam, <strong>hasil seleksi pendaftaran</strong> anda akan kami kirimkan melalui e-mail.<br></p>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div>
                                <p>Harap tunggu maksimal 2 x 24 jam, konfirmasi <strong>pendaftaran</strong> anda akan kami kirimkan melalui e-mail</p>
                            </div>
                        <?php endif; ?>
                        <a href="<?= base_url('admission') ?>">Kembali</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->