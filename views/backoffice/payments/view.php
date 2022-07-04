<h1 class="h3 mb-3">Data Pembayaran</h1>
<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <!-- <div class="card-header">
                <h5 class="card-title mb-0">Data Pendaftaran</h5>
            </div> -->
            <div class="card-body">
                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger p-2"><?= $message ?></div>
                <?php endif; ?>
                <?= $this->form_builder->open_form(['action' => '', 'id' => 'payment_form']); ?>
                <?= $this->form_builder->build_form_horizontal($form); ?>
                <?php if ($payment->payment_type == 2) : ?>
                    <div class="form-group row">
                        <label for="payment_receipt" class="col-sm-3 col-form-label bold">Hasil Seleksi</label>
                        <div class="col-sm-6">
                            <?php if ($payment->payment_loa) : ?>
                                <?php
                                $loa_file = pathinfo(FCPATH . LOA_FOLDER . $payment->payment_loa);
                                ?>
                                <iframe src="<?= base_url(LOA_FOLDER . $payment->payment_loa) ?>" width="100%" height="400"></iframe>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($payment->payment_receipt) : ?>
                    <div class="form-group row">
                        <label for="payment_receipt" class="col-sm-3 col-form-label bold">Bukti Pembayaran</label>
                        <div class="col-sm-6">
                            <?php
                            $receipt_file = pathinfo(FCPATH . RECEIPTS_FOLDER . $payment->payment_receipt);
                            ?>
                            <?php if ($receipt_file['extension'] == 'pdf') : ?>
                                <iframe src="<?= base_url(RECEIPTS_FOLDER . $payment->payment_receipt) ?>" width="100%" height="400"></iframe>
                            <?php else : ?>
                                <img src="<?= base_url(RECEIPTS_FOLDER . $payment->payment_receipt) ?>" class="img-fluid">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Status Pembayaran</label>
                    <div class="col-sm-6">
                        <?= form_dropdown('payment_status', ['' => '-Pilih status-', '1' => 'Disetujui', '2' => 'Ditolak'], $payment->payment_status, 'class="form-control select2"'); ?>
                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Tanggal Pembayaran User</label>
                    <div class="col-sm-3">
                        <input type="date" name="payment_time" id="payment_time" class="form-control" value="" />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="<?= base_url('backoffice/payments') ?>" class="btn btn-danger">Kembali</a>
                <?= $this->form_builder->close_form(); ?>
            </div>
        </div>
    </div>

</div>