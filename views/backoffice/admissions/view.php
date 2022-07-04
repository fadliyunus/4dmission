<h1 class="h3 mb-3">Data Pendaftaran</h1>
<div class="row">
    <div class="col-12 d-flex">
        <div class="card flex-fill">
            <!-- <div class="card-header">
                <h5 class="card-title mb-0">Data Pendaftaran</h5>
            </div> -->
            <div class="card-body">
                <?= $this->form_builder->open_form(['action' => '', 'id' => 'approval_form']); ?>
                <?= $this->form_builder->build_form_horizontal($form); ?>
                <?php if ($admission->status >= 102) : ?>
                    <?php if ($admission->payment == 1) : ?>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Biaya Pendaftaran</label>
                            <div class="col-sm-6">
                                <p class="form-control-static mb-0">Rp. <?= number_format($admission->biaya, 0, '', '.') ?></p>
                            </div>
                        </div>
                        <?php if ($admission->payment_voucher_code) : ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Metode Pembayaran</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static mb-0">Voucher</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="payment_receipt" class="col-sm-3 col-form-label bold">Kode Voucher</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static mb-0"><?= $admission->payment_voucher_code ?></p>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Metode Pembayaran</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static mb-0"><?= ($admission->channel_type == 1 ? 'Transfer' : 'Virtual Account') . ' ' . $admission->channel_name ?></p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="payment_receipt" class="col-sm-3 col-form-label bold">Bukti Pembayaran</label>
                                <div class="col-sm-6">
                                    <?php
                                    $receipt_file = pathinfo(FCPATH . RECEIPTS_FOLDER . $admission->payment_receipt);
                                    ?>
                                    <?php if ($receipt_file['extension'] == 'pdf') : ?>
                                        <iframe src="<?= base_url(RECEIPTS_FOLDER . $admission->payment_receipt) ?>" width="100%" height="400"></iframe>
                                    <?php else : ?>
                                        <img src="<?= base_url(RECEIPTS_FOLDER . $admission->payment_receipt) ?>" class="img-fluid">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if ($admission->payment) : ?>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Status <?= $admission->payment ? 'Pembayaran' : 'Pendaftaran' ?></label>
                            <div class="col-sm-6">
                                <?php if ($admission->status == 102) : ?>
                                    <?= form_dropdown('payment_status', ['' => '-Pilih status-', '1' => 'Disetujui', '2' => 'Ditolak'], '', 'class="form-control select2"'); ?>
                                <?php elseif ($admission->status >= 200) : ?>
                                    <p class="form-control-static">
                                        <?= $admission->payment ? 'Pembayaran' : 'Pendaftaran' ?> disetujui
                                    </p>
                                <?php elseif ($admission->status == 103) : ?>
                                    <p class="form-control-static">
                                        <?= $admission->payment ? 'Pembayaran' : 'Pendaftaran' ?> ditolak
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($admission->status == 102) : ?>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Pembayaran User</label>
                            <div class="col-sm-3">
                                <input type="date" name="payment_time" id="payment_time" class="form-control" value="" />
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($admission->status == 300) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Pendaftaran</label>
                        <div class="col-sm-6">
                            <?= form_dropdown('status', ['' => '-Pilih status-', '301' => 'Disetujui', '302' => 'Ditolak'], '', 'class="form-control select2"'); ?>
                        </div>
                    </div>
                    <?php if ($admission->tes_seleksi) : ?>
                        <div class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">Waktu Tes Seleksi</label>
                            <div class="col-sm-3">
                                <?= form_input('waktu_seleksi', date('d/m/Y H:i', strtotime($admission->tgl_seleksi . ' 08:00')), 'class="form-control" id="waktu_seleksi"'); ?>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">Nama aplikasi</label>
                            <div class="col-sm-3">
                                <?= form_input('aplikasi', '', 'class="form-control" id="aplikasi"'); ?>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">Meeting ID</label>
                            <div class="col-sm-3">
                                <?= form_input('zoom_meeting_id', '', 'class="form-control" id="zoom_meeting_id"'); ?>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">Passcode</label>
                            <div class="col-sm-3">
                                <?= form_input('zoom_passcode', '', 'class="form-control" id="zoom_passcode"'); ?>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <label class="col-sm-3 col-form-label">Zoom Link</label>
                            <div class="col-sm-6">
                                <?= form_input('zoom_link', '', 'class="form-control" id="zoom_link"'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php elseif ($admission->status == 301) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Pendaftaran</label>
                        <div class="col-sm-6">
                            <p class="form-control-static">
                                Pendaftaran disetujui
                            </p>
                        </div>
                    </div>
                <?php elseif ($admission->status == 302) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Pendaftaran</label>
                        <div class="col-sm-6">
                            <p class="form-control-static">
                                Pendaftaran ditolak
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="<?= base_url('backoffice/admissions') ?>" class="btn btn-danger">Kembali</a>
                <?= $this->form_builder->close_form(); ?>


                <?php if ($admission->status > 101) : ?>
                    <div class="pl-3">
                        <?php if ($admission->form_1->data_personal_1 || $admission->form_2->data_personal_1) : ?>
                            <div class="mt-4">
                                <h3>Data Personal</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_personal_1, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->social_media || $admission->form_2->social_media) : ?>
                            <?= $this->form_builder->build_display($form_social_media, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->data_personal_2 || $admission->form_2->data_personal_2) : ?>
                            <?= $this->form_builder->build_display($form_personal_2, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->family || $admission->form_2->family) : ?>
                            <div class="mt-4">
                                <h3>Data Keluarga</h3>
                            </div>
                            <table id="family" data-toolbar="#family_toolbar"></table>
                        <?php endif; ?>

                        <?php if ($admission->form_1->last_education || $admission->form_2->last_education) : ?>
                            <div class="mt-4">
                                <h3>Pendidikan Terakhir</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_last_education, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->education_history || $admission->form_2->education_history) : ?>
                            <div class="mt-4">
                                <h3>Riwayat Pendidikan</h3>
                            </div>
                            <div id="education_toolbar">
                            </div>
                            <table id="education_history" data-toolbar="#education_toolbar"></table>
                        <?php endif; ?>

                        <?php if ($admission->form_1->education_history_informal || $admission->form_2->education_history_informal) : ?>
                            <div class="mt-4">
                                <h3>Riwayat Pendidikan (Informal)</h3>
                            </div>
                            <div id="education_informal_toolbar">
                            </div>
                            <table id="education_informal_history" data-toolbar="#education_informal_toolbar"></table>
                        <?php endif; ?>


                        <?php if ($admission->form_1->last_occupation || $admission->form_2->last_occupation) : ?>
                            <div class="mt-4">
                                <h3>Pekerjaan Saat Ini</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_last_occupation, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->employment_history || $admission->form_2->employment_history) : ?>
                            <div class="mt-4">
                                <h3>Riwayat Pekerjaan</h3>
                            </div>
                            <div id="employment_toolbar">
                            </div>
                            <table id="employment_history" data-toolbar="#employment_toolbar"></table>
                        <?php endif; ?>

                        <?php if ($admission->form_1->organization_history || $admission->form_2->organization_history) : ?>
                            <div class="mt-4">
                                <h3>Pengalaman Organisasi</h3>
                            </div>
                            <div id="organization_history_toolbar">
                            </div>
                            <table id="organization_history" data-toolbar="organization_history_toolbar"></table>
                        <?php endif; ?>

                        <?php if ($admission->form_1->achievement || $admission->form_2->achievement) : ?>
                            <div class="mt-4">
                                <h3>Prestasi</h3>
                            </div>
                            <div id="achievement_toolbar">

                            </div>
                            <table id="achievement" data-toolbar="achievement_toolbar"></table>
                        <?php endif; ?>

                        <?php if ($admission->form_1->company_contact_info || $admission->form_2->company_contact_info) : ?>
                            <div class="mt-4">
                                <h3>Kontak Perusahaan</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_company_contact_info, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->recommendation || $admission->form_2->recommendation) : ?>
                            <div class="mt-4">
                                <h3>Rekomendasi</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_recommendation, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->education_fund_source || $admission->form_2->education_fund_source) : ?>
                            <?= $this->form_builder->build_display($form_education_fund_source, $personal_information); ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->score_utbk || $admission->form_2->score_utbk) : ?>
                            <div class="mt-4">
                                <h3>Nilai UTBK</h3>
                            </div>
                            <?= $this->form_builder->build_display($form_score_utbk, $personal_information); ?>
                        <?php endif; ?>


                        <?php if ($admission->form_1->file_upload_1 || $admission->form_1->file_upload_2 || $admission->form_1->file_upload_3 || $admission->form_1->file_upload_4 || $admission->form_2->file_upload_1 || $admission->form_2->file_upload_2 || $admission->form_2->file_upload_3 || $admission->form_2->file_upload_4) : ?>
                            <div class="mt-4">
                                <h3>Dokumen</h3>
                                <a href="<?=base_url('backoffice/admissions/download_documents/'.$admission->id_admission)?>">Download dokumen</a>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas X Semester 1</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_x_1']) && $documents['report_x_1'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_x_1']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas X Semester 2</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_x_2']) && $documents['report_x_2'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_x_2']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 1</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_xi_1']) && $documents['report_xi_1'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xi_1']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 2</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_xi_2']) && $documents['report_xi_2'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xi_2']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 1</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_xii_1']) && $documents['report_xii_1'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xii_1']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 2</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['report_xii_2']) && $documents['report_xii_2'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xii_2']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($admission->form_1->file_upload_2 || $admission->form_2->file_upload_2) : ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Keterangan Akreditasi Sekolah</label>
                                    <div class="col-sm-6">
                                        <?php if (isset($documents['accreditation_certificate']) && $documents['accreditation_certificate'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['accreditation_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($admission->form_1->file_upload_4 || $admission->form_2->file_upload_4) :  ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sertifikat Hasil UTBK</label>
                                    <div class="col-sm-6">

                                        <?php if (isset($documents['utbk_certificate']) && $documents['utbk_certificate'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['utbk_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">KTP</label>
                                <div class="col-sm-6">

                                    <?php if (isset($documents['identity_card']) && $documents['identity_card'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['identity_card']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">KK</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['family_certificate']) && $documents['family_certificate'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['family_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($admission->form_1->file_upload_3 || $admission->form_2->file_upload_3) : ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sertifikat Organisasi</label>
                                    <div class="col-sm-6">
                                        <?php if (isset($documents['organization_certificates']) && $documents['organization_certificates'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['organization_certificates']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sertifikat Achievement</label>
                                    <div class="col-sm-6">
                                        <?php if (isset($documents['achievement_certificates']) && $documents['achievement_certificates'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['achievement_certificates']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Pas Photo Terbaru (4x6)</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['photo']) && $documents['photo'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['photo']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($admission->form_1->file_upload_5 || $admission->form_1->file_upload_6 || $admission->form_1->file_upload_7 || $admission->form_2->file_upload_5 || $admission->form_2->file_upload_6 || $admission->form_2->file_upload_7) : ?>
                            <div class="mt-4">
                                <h3>Dokumen</h3>
                                <a href="<?=base_url('backoffice/admissions/download_documents/'.$admission->id_admission)?>">Download dokumen</a>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Transkrip Nilai</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['academic_transcript']) && $documents['academic_transcript'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['academic_transcript']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ijazah</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['school_certificate']) && $documents['school_certificate'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['school_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">KTP</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['identity_card']) && $documents['identity_card'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['identity_card']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">KK</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['family_certificate']) && $documents['family_certificate'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['family_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">TOEFL/IELTS</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['toefl_certificate']) && $documents['toefl_certificate'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['toefl_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Pas Photo Terbaru (4x6)</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['photo']) && $documents['photo'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['photo']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">CV</label>
                                <div class="col-sm-6">
                                    <?php if (isset($documents['cv']) && $documents['cv'] != '') : ?>
                                        <span class="mr-2">Sudah diupload</span>
                                        <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['cv']) ?>" class="btn btn-primary">Lihat</a>
                                    <?php else : ?>
                                        <span class="mr-2">Belum upload</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($admission->form_1->file_upload_6 || $admission->form_2->file_upload_6) : ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Full Body Photo</label>
                                    <div class="col-sm-6">
                                        <?php if (isset($documents['full_body_photo']) && $documents['full_body_photo'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['full_body_photo']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($admission->form_1->file_upload_7 || $admission->form_2->file_upload_7) : ?>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Karangan Ringkas</label>
                                    <div class="col-sm-6">
                                        <?php if (isset($documents['short_resume']) && $documents['short_resume'] != '') : ?>
                                            <span class="mr-2">Sudah diupload</span>
                                            <a target="_blank" href="<?= base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['short_resume']) ?>" class="btn btn-primary">Lihat</a>
                                        <?php else : ?>
                                            <span class="mr-2">Belum upload</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($admission->form_1->other || $admission->form_2->other) : ?>
                            <?= $this->form_builder->build_display($form_other, $personal_information); ?>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

</div>