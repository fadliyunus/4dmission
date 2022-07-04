<h1 class="h3 mb-3">Data Pendaftaran</h1>
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

                <?= $this->form_builder->open_form(['action' => '']); ?>
                <?= $this->form_builder->build_form_horizontal($form); ?>
                <div class="form-group row">
                    <label for="discount" class="col-sm-3 col-form-label">Hasil Seleksi</label>
                    <div class="col-sm-3">
                        <?php if ($admission->beasiswa) :  ?>
                            <?= form_dropdown('status', ['' => '-Pilih hasil-', '402' => 'Lulus Tahap 1 (Tes Seleksi)', '403' => 'Lulus Tahap 2 (Psikotest)', '400' => 'Lulus', '401' => 'Tidak Lulus'], set_value('status', $admission->status), 'class="form-control select2"'); ?>
                        <?php else : ?>
                            <?= form_dropdown('status', ['' => '-Pilih hasil-', '400' => 'Lulus', '401' => 'Tidak Lulus'], set_value('status', $admission->status), 'class="form-control select2"'); ?>
                        <?php endif; ?>
                        <?php if ($admission->status == 400) : ?>
                            <a href="<?= base_url('backoffice/admissions/acceptances_letter/' . $admission->id_admission) ?>">Letter of Acceptance</a>
                        <?php elseif ($admission->status == 401) : ?>
                            <a href="<?= base_url('backoffice/admissions/acceptances_letter/' . $admission->id_admission) ?>">Letter of Non-Acceptance</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row accepted <?= $admission->status == 400 || set_value('acceptance_date') != '' ? '' : 'd-none' ?>">
                    <label for="payment_receipt" class="col-sm-3 col-form-label">Tanggal Penerimaan</label>
                    <div class="col-sm-3">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            <?= form_input('acceptance_date', (isset($admission->acceptance_date) ?  date('d/m/Y', strtotime($admission->acceptance_date)) : date('d/m/Y')), 'class="form-control datepicker"'); ?>
                        </div>
                    </div>
                </div>
                <?php if ($admission->beasiswa) : ?>
                    <div class="form-group row accepted <?= $admission->status == 400 || set_value('acceptance_date') != '' ? '' : 'd-none' ?>">
                        <label for="jenis_beasiswa" class="col-sm-3 col-form-label">Jenis Beasiswa</label>
                        <div class="col-sm-3">
                            <div class="input-group mb-2">
                                <?= form_dropdown('jenis_beasiswa', ['' => '-Pilih jenis beasiswa-', '1' => 'Full Scholarship', '2' => 'Partial Scholarship'], isset($admission->scholarship) && $admission->scholarship > 0 ? ($admission->scholarship == 100 ? '1' : '2') : '', 'id="jenis_beasiswa" class="form-control select2"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row <?= isset($admission->scholarship) && $admission->scholarship != 100 ? '' : 'd-none' ?>">
                        <label for="scholarship" class="col-sm-3 col-form-label">Beasiswa</label>
                        <div class="col-sm-3">
                            <div class="input-group mb-2">
                                <?= form_input('scholarship', $admission->scholarship, 'id="scholarship" class="form-control"'); ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="form-group row accepted  <?= $admission->status == 400 || set_value('acceptance_date') != '' ? '' : 'd-none' ?>">
                        <label for="discount" class="col-sm-3 col-form-label">Potongan Grade</label>
                        <div class="col-sm-6">
                            <?= form_dropdown('discount', $grade_discounts, (isset($admission->grade_discount) ?  $admission->grade_discount : ''), 'class="form-control select2"'); ?>
                        </div>
                    </div>
                    <div class="form-group row accepted  <?= $admission->status == 400 || set_value('acceptance_date') != '' ? '' : 'd-none' ?>">
                        <label for="discount" class="col-sm-3 col-form-label">Potongan</label>
                        <div class="col-sm-9">
                            <?php foreach ($discounts as $i => $discount) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="<?= $discount->id_biaya ?>" id="cb_discount_<?= $i ?>" name="id_biaya[]" <?= isset($admission->discounts) && in_array($discount->id_biaya, $admission->discounts) ? 'checked="checked"' : '' ?>>
                                    <label class="form-check-label" for="cb_discount_<?= $i ?>">
                                        <?= $discount->nama_biaya ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>



                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="<?= base_url('backoffice/admissions/acceptances') ?>" class="btn btn-danger">Kembali</a>
                <?= $this->form_builder->close_form(); ?>
            </div>
        </div>
    </div>

</div>