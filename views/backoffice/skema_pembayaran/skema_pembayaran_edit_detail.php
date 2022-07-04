<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <?php if (isset($message)) : ?>
                    <div class="alert alert-danger p-2"><?= $message ?></div>
                <?php endif; ?>
                <?php if (isset($form)) : ?>
                    <?= $this->form_builder->open_form(array('action' => '')) ?>
                    <?= $this->form_builder->build_form_horizontal($form) ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Jumlah</label>
                        <div class="col-sm-3">
                            <label class="form-check">
                                <?= form_radio('cb_jumlah', '1', isset($skema_pembayaran) ? ($skema_pembayaran->jumlah > 0 ? TRUE : FALSE) : set_radio('cb_jumlah', '1', TRUE), 'class="form-check-input"') ?>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <?= form_input('jumlah', set_value('jumlah', isset($skema_pembayaran) ? $skema_pembayaran->jumlah : 0), 'class="form-control"') ?>
                                </div>
                            </label>
                            <label class="form-check">
                                <?= form_radio('cb_jumlah', '2', isset($skema_pembayaran) ? ($skema_pembayaran->persentase > 0 ? TRUE : FALSE) : set_radio('cb_jumlah', '2', FALSE), 'class="form-check-input"') ?>
                                <div class="input-group">
                                    <?= form_input('persentase', set_value('persentase', isset($skema_pembayaran) ? $skema_pembayaran->persentase : 0), 'class="form-control"') ?>
                                    <span class="input-group-text">%</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Waktu</label>
                        <div class="col-sm-3">
                            <label class="form-check">
                                <?= form_radio('cb_waktu', '1', isset($skema_pembayaran) ? ($skema_pembayaran->waktu > 0 ? TRUE : FALSE) : set_radio('cb_waktu', '1', TRUE), 'class="form-check-input"') ?>
                                <div class="input-group">
                                    <span class="input-group-text">H + </span>
                                    <?= form_input('waktu', set_value('waktu', isset($skema_pembayaran) ? $skema_pembayaran->waktu : 0), 'class="form-control"') ?>
                                    <span class="input-group-text">hari kalender</span>
                                </div>
                            </label>
                            <label class="form-check">
                                <?= form_radio('cb_waktu', '2', isset($skema_pembayaran) ? ($skema_pembayaran->waktu > 0 ? FALSE : TRUE) : set_radio('cb_waktu', '2', TRUE), 'class="form-check-input"') ?>
                                <div class="input-group">
                                    <span class="input-group-text">Tanggal</span>
                                    <?= form_input('jatuh_tempo', set_value('jatuh_tempo', isset($skema_pembayaran) && $skema_pembayaran->jatuh_tempo != '0000-00-00' ? date('d/m/Y', strtotime($skema_pembayaran->jatuh_tempo)) : ''), 'class="form-control datepicker"') ?>
                                </div>
                            </label>
                        </div>
                    </div>
                    <?= $this->form_builder->build_form_horizontal($form_2) ?>
                    <?= $this->form_builder->close_form(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>