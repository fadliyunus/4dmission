<!-- Content
		============================================= -->
<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">

            <div class="mx-auto mb-0 clearfix" id="form-login" style="max-width: 500px;">

                <div class="card mb-0">
                    <div class="card-body" style="padding: 40px;">
                        <div class="fancy-title title-dotted-border title-center">
                            <h3><span>Pembayaran Biaya Pendaftaran</span></h3>
                        </div>
                        <?php if (isset($message) && $message != '') : ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message) && $error_message != '') : ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php endif; ?>
                        <p>Pembayaran biaya pendaftaran PROGRAM <?= $seleksi->nama_program_studi ?></p>
                        <form>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">NAMA LENGKAP</label>
                                <div class="col-sm-6">
                                    <p class="form-control-plaintext mb-0"><?= $this->ion_auth->user()->row()->full_name ?></p>
                                </div>
                            </div>
                        </form>
                        <?php echo $this->form_builder->open_form(array('action' => '', 'id' => "payment_form", 'name' => "payment_form", 'class' => "form-horizontal")) ?>
                        <div class="form-group row">
                            <label class="col-sm-6 col-form-label">PEMBAYARAN</label>
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_channel_type" id="payment_type_transfer" value="1" <?php echo set_radio('payment_channel_type', '1', TRUE); ?> />
                                    <label class="form-check-label" for="payment_type_transfer">
                                        TRANSFER
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_channel_type" id="payment_type_voucher" value="2" <?php echo set_radio('payment_channel_type', '2'); ?> />
                                    <label class="form-check-label" for="payment_type_voucher">
                                        VOUCHER
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="payment_voucher" <?= $this->input->post('payment_channel_type') == '2' ? '' : 'class="d-none"' ?>>
                            <?php echo $this->form_builder->build_form_horizontal($form_voucher) ?>
                        </div>
                        <div id="payment_transfer" <?= $this->input->post('payment_channel_type') == '2' ? 'class="d-none"' : '' ?>>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-borderless">
                                        <tr style="border:none">
                                            <td>Biaya Pendaftaran</td>
                                            <td>Rp.</td>
                                            <td class="text-right"><?= number_format($admission_fee, 0, ',', '.') ?></td>
                                        </tr>
                                        <tr style="border:none">
                                            <td>Kode Unik</td>
                                            <td>Rp.</td>
                                            <td class="text-right"><?= number_format($payment_code, 0, ',', '.') ?></td>
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
                            <?php echo $this->form_builder->build_form_horizontal($form) ?>
                        </div>
                        <?php echo $this->form_builder->close_form() ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->