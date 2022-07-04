<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">
                        <div class="fancy-title title-dotted-border title-center">
                            <h3><span>Pembayaran Konfirmasi</span></h3>
                        </div>
                        <?php if (isset($message) && $message != '') : ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message) && $error_message != '') : ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php endif; ?>
                        <p>Pembayaran biaya konfirmasi penerimaan PROGRAM <?= $seleksi->nama_program_studi ?></p>

                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-borderless">
                                    <tr style="border:none">
                                        <td>Nama Lengkap</td>
                                        <td colspan="2"><?= $this->ion_auth->user()->row()->full_name ?></td>
                                    </tr>
                                    <tr style="border:none">
                                        <td>Biaya Konfirmasi</td>
                                        <td>Rp.</td>
                                        <td class="text-right"><?= number_format($admission_fee, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr style="border-top:2px solid #ddd">
                                        <td>Total</td>
                                        <td>Rp.</td>
                                        <td class="text-right"><?= number_format($total_fee, 0, ',', '.') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <p>Silakan melalukan pembayaran melalui channel pembayaran berikut ini:</p>
                        <table class="table table-bordered">
                            <?php foreach ($payment_channels as $channel) : ?>
                                <?php
                                $channel_type = '';
                                switch ($channel->channel_type) {
                                    case '1':
                                        $channel_type = 'Transfer';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td><?= $channel_type ?></td>
                                    <td><?= $channel->channel_name ?></td>
                                    <td><?= $channel->channel_account_no ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
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