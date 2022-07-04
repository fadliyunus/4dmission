<section id="content">
    <div class="content-wrap">
        <div class="container clearfix">
            <form>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Program Studi</label>
                    <div class="col-sm-9">
                        <input type="text" readonly class="form-control-plaintext" value="<?= $admission->nama_program_studi ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Jenis Seleksi</label>
                    <div class="col-sm-9">
                        <input type="text" readonly class="form-control-plaintext" value="<?= $admission->nama_seleksi ?>">
                    </div>
                </div>
                <?php if ($admission->tgl_seleksi) : ?>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tgl Seleksi</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" value="<?= strftime('%e %B %Y', strtotime($admission->tgl_seleksi)) ?>">
                        </div>
                    </div>
                <?php endif; ?>
            </form>
            <div id="toolbar">
                <?php if (!$admission->confirmation) : ?>
                    <a href="<?= base_url('payment/confirmation/' . $admission->id_admission) ?>" class="btn btn-primary">Pembayaran Konfirmasi</a>
                <?php endif; ?>
                <?php if ($admission->confirmation && $admission->confirmation->payment_status == 1) : ?>
                    <?php if ($admission->program == 1) : ?>
                        <?php if ($admission->installment_validation || count($admission->installment) == $admission->jenis_angsuran) : ?>
                        <?php else : ?>
                            <a href="<?= base_url('payment/installment/' . $admission->id_admission) ?>" class="btn btn-primary">Pembayaran Angsuran</a>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ($admission->installment_validation || count($admission->installment) == $admission->skema_pembayaran->jumlah_angsuran) : ?>
                        <?php else : ?>
                            <a href="<?= base_url('payment/installment/' . $admission->id_admission) ?>" class="btn btn-primary">Pembayaran Angsuran</a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <table id="datatable" data-toolbar="#toolbar"></table>

        </div>
    </div>
</section>