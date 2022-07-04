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
                        <?php if ($admission->status == 102) : ?>
                            <div>
                                <p>Harap tunggu maksimal 2 x 24 jam, konfirmasi <strong>pembayaran biaya pendaftaran</strong> Anda akan kami kirimkan melalui email.</p>
                            </div>
                        <?php elseif ($admission->status == 103) : ?>
                            <p>Pembayaran anda ditolak. Harap periksa pembayaran anda dan upload kembali bukti pembayaran anda. </p>
                        <?php elseif ($admission->status == 400) : ?>
                            <?php if ($confirmation) : ?>
                                <?php if ($confirmation->payment_status == 0) : ?>
                                    <div>
                                        <p>Pembayaran konfirmasi anda sedang diproses.<br>Harap tunggu maksimal 2 x 24 jam.</p>
                                    </div>
                                <?php else : ?>
                                    <?php if ($installment && $installment->payment_status == 0) : ?>
                                        <div>
                                            <p>Pembayaran angsuran anda sedang diproses.<br>Harap tunggu maksimal 2 x 24 jam.</p>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif ($admission->status == 2) : ?>
                            <div>
                                <?php if ($payment->payment_status == 1) : ?>
                                    <p>Pembayaran anda telah disetujui. Anda dapat melanjutkan proses pendaftaran anda.</p>
                                <?php elseif ($payment->payment_status == 2) : ?>
                                    <p>Pembayaran anda ditolak. Harap periksa pembayaran anda dan upload kembali bukti pembayaran anda. </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <!-- <div>
                            <?php if ($user->payment == 1) : ?>
                                <a href="<?= base_url('events/1') ?>" class="btn btn-primary">Lanjutkan</a>
                            <?php elseif ($user->payment == 2) : ?>
                                <a href="<?= base_url('account/payment_preregister') ?>" class="btn btn-primary">Ulangi upload bukti pembayaran</a>
                            <?php else : ?>
                                <a href="<?= base_url('account/payment_status_preregister') ?>" class="btn btn-primary">Cek Status Pembayaran</a>
                            <?php endif; ?>
                        </div> -->
                        <a href="<?= base_url('admission') ?>">Kembali</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->