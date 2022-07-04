<section id="content">
    <div class="content-wrap">
        <div class="container">
            <?php if (isset($message) && $message != '') : ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <?= form_open_multipart('', ['id' => 'registration_form']) ?>
            <?= form_hidden('id_admission', $admission->id_admission); ?>
            <?= form_fieldset('Pendaftaran'); ?>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Program Studi</label>
                <div class="col-sm-6">
                    <p class="form-control-static mb-0"><?= $admission->nama_program_studi ?></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Jenis Seleksi</label>
                <div class="col-sm-6">
                    <p class="form-control-static mb-0"><?= $admission->nama_seleksi ?></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Jadwal Seleksi</label>
                <div class="col-sm-6">
                    <p class="form-control-static mb-0"><?= date('j F Y', strtotime($admission->tgl_seleksi)) ?></p>
                </div>
            </div>

            <?php if ($admission->program == 1 && $admission->nama_seleksi == 'REGULER') : ?>
                <?= $this->form_builder->build_form_horizontal($form_personal, $user); ?>
            <?php elseif ($admission->program == 2) : ?>
                <?= $this->form_builder->build_form_horizontal($form_personal_1, $user); ?>
            <?php endif; ?>

            <?php if ($admission->program == 2) : ?>
                <?= form_fieldset('Riwayat Pekerjaan'); ?>
                <div id="employment_history_toolbar"></div>
                <table id="employment_history"></table>
            <?php endif; ?>

            <?php if ($admission->program == 1 && $admission->program_studi == '3') : ?>
                <?= form_fieldset('Rencana Biaya Pendidikan'); ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Sumber dana pendidikan</label>
                    <div class="col-sm-6">
                        <?= form_dropdown('education_fund_source', ['' => '-Pilih rencana biaya pendidikan', '1' => 'Pribadi', '2' => 'Perusahaan', '3' => 'Sebagian pribadi, sebagian perusahaan'], [], 'class="form-control"'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($admission->program == 2 && ($admission->nama_program_studi == 'MAGISTER MANAJEMEN EKSEKUTIF' || $admission->nama_program_studi == 'MAGISTER MANAJEMEN EKSEKUTIF MUDA')) : ?>
                <?= form_fieldset('Kontak Perusahaan'); ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama pimpinan/PIC</label>
                    <div class="col-sm-6">
                        <?= form_input('company_pic_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">E-mail</label>
                    <div class="col-sm-6">
                        <?= form_input('company_pic_email', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Phone/Whatsapp Number</label>
                    <div class="col-sm-6">
                        <?= form_input('company_pic_phone', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Alamat</label>
                    <div class="col-sm-6">
                        <?= form_input('company_pic_address', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <?= form_fieldset('Rekomendasi'); ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama pemberi rekomendasi 1</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_name_1', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Jabatan & Nama Perusahaan</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_company_1', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">E-mail</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_email_1', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Phone/Whatsapp Number</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_phone_1', '', 'class="form-control"'); ?>
                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama Pemberi Rekomendasi 2</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_name_2', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Jabatan & Nama Perusahaan</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_company_2', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">E-mail</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_email_2', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Phone/Whatsapp Number</label>
                    <div class="col-sm-6">
                        <?= form_input('recommender_phone_2', '', 'class="form-control"'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($admission->program == 1) : ?>
                <?= form_fieldset('Upload Dokumen'); ?>
                <div class="help-block mb-3">File yang diupload hanya boleh file .pdf, .jpeg, .jpg, .png dengan ukuran maksimum 2MB</div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas X Semester 1</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_x_1']) && $documents['report_x_1'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_x_1']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_x_1', '', 'class="form-control fileupload2" id="report_x_1"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_x_1', '', 'class="form-control fileupload" id="report_x_1"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas X Semester 2</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_x_2']) && $documents['report_x_2'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_x_2']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_x_2', '', 'class="form-control fileupload2" id="report_x_2"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_x_2', '', 'class="form-control fileupload" id="report_x_2"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 1</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_xi_1']) && $documents['report_xi_1'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_xi_1']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_xi_1', '', 'class="form-control fileupload2" id="report_xi_1"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_xi_1', '', 'class="form-control fileupload" id="report_xi_1"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 2</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_xi_2']) && $documents['report_xi_2'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_xi_2']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_xi_2', '', 'class="form-control fileupload2" id="report_xi_2"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_xi_2', '', 'class="form-control fileupload" id="report_xi_2"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 1</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_xii_1']) && $documents['report_xii_1'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_xii_1']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_xii_1', '', 'class="form-control fileupload2" id="report_xii_1"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_xii_1', '', 'class="form-control fileupload" id="report_xii_1"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 2</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['report_xii_2']) && $documents['report_xii_2'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['report_xii_2']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('report_xii_2', '', 'class="form-control fileupload2" id="report_xii_2"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('report_xii_2', '', 'class="form-control fileupload" id="report_xii_2"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($admission->nama_seleksi == 'RAPORT') : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Keterangan Akreditasi Sekolah</label>
                        <div class="col-sm-6">
                            <?php if (isset($documents['full_body_photo']) && $documents['full_body_photo'] != '') : ?>
                                <span class="mr-2">Sudah diupload</span>
                                <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['full_body_photo']) ?>" class="btn btn-primary">Lihat</a>
                                <div style="display:inline-block">
                                    <?= form_upload('full_body_photo', '', 'class="form-control fileupload2" id="full_body_photo"'); ?>
                                </div>
                            <?php else : ?>
                                <span class="mr-2">Belum upload</span>
                                <div style="display:inline-block">
                                    <?= form_upload('full_body_photo', '', 'class="form-control fileupload" id="full_body_photo"'); ?>
                                </div>
                                <div id="errorBlock" class="help-block"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($admission->nama_seleksi == 'UTBK') : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sertifikat Hasil UTBK</label>
                        <div class="col-sm-6">

                            <?php if (isset($documents['utbk_certificate']) && $documents['utbk_certificate'] != '') : ?>
                                <span class="mr-2">Sudah diupload</span>
                                <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['utbk_certificate']) ?>" class="btn btn-primary">Lihat</a>
                                <div style="display:inline-block">
                                    <?= form_upload('utbk_certificate', '', 'class="form-control fileupload2" id="utbk_certificate"'); ?>
                                </div>
                            <?php else : ?>
                                <span class="mr-2">Belum upload</span>
                                <div style="display:inline-block">
                                    <?= form_upload('utbk_certificate', '', 'class="form-control fileupload" id="utbk_certificate"'); ?>
                                </div>
                                <div id="errorBlock" class="help-block"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($admission->program == 2) : ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Transkrip Nilai</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['academic_transcript']) && $documents['academic_transcript'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['academic_transcript']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('academic_transcript', '', 'class="form-control fileupload2" id="academic_transcript"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('academic_transcript', '', 'class="form-control fileupload" id="academic_transcript"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Ijazah</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['school_certificate']) && $documents['school_certificate'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['school_certificate']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('school_certificate', '', 'class="form-control fileupload2" id="school_certificate"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('school_certificate', '', 'class="form-control fileupload" id="school_certificate"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">KTP</label>
                <div class="col-sm-6">

                    <?php if (isset($documents['identity_card']) && $documents['identity_card'] != '') : ?>
                        <span class="mr-2">Sudah diupload</span>
                        <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['identity_card']) ?>" class="btn btn-primary">Lihat</a>
                        <div style="display:inline-block">
                            <?= form_upload('identity_card', '', 'class="form-control fileupload2" id="identity_card"'); ?>
                        </div>
                    <?php else : ?>
                        <span class="mr-2">Belum upload</span>
                        <div style="display:inline-block">
                            <?= form_upload('identity_card', '', 'class="form-control fileupload" id="identity_card"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">KK</label>
                <div class="col-sm-6">
                    <?php if (isset($documents['family_certificate']) && $documents['family_certificate'] != '') : ?>
                        <span class="mr-2">Sudah diupload</span>
                        <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['family_certificate']) ?>" class="btn btn-primary">Lihat</a>
                        <div style="display:inline-block">
                            <?= form_upload('family_certificate', '', 'class="form-control fileupload2" id="family_certificate"'); ?>
                        </div>
                    <?php else : ?>
                        <span class="mr-2">Belum upload</span>
                        <div style="display:inline-block">
                            <?= form_upload('family_certificate', '', 'class="form-control fileupload" id="family_certificate"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($admission->program == 2) : ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">TOEFL/IELTS</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['toefl_certificate']) && $documents['toefl_certificate'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['toefl_certificate']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('toefl_certificate', '', 'class="form-control fileupload2" id="toefl_certificate"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('toefl_certificate', '', 'class="form-control fileupload" id="toefl_certificate"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Pas Photo Terbaru (4x6)</label>
                <div class="col-sm-6">
                    <?php if (isset($documents['photo']) && $documents['photo'] != '') : ?>
                        <span class="mr-2">Sudah diupload</span>
                        <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['photo']) ?>" class="btn btn-primary">Lihat</a>
                        <div style="display:inline-block">
                            <?= form_upload('photo', '', 'class="form-control fileupload2" id="photo"'); ?>
                        </div>
                    <?php else : ?>
                        <span class="mr-2">Belum upload</span>
                        <div style="display:inline-block">
                            <?= form_upload('photo', '', 'class="form-control fileupload" id="photo"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($admission->program == 2) : ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">CV</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['cv']) && $documents['cv'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['cv']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('cv', '', 'class="form-control fileupload2" id="cv"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('cv', '', 'class="form-control fileupload" id="cv"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Karangan Ringkas</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['short_resume']) && $documents['short_resume'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['short_resume']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('short_resume', '', 'class="form-control fileupload2" id="short_resume"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('short_resume', '', 'class="form-control fileupload" id="short_resume"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Full Body Photo</label>
                    <div class="col-sm-6">
                        <?php if (isset($documents['full_body_photo']) && $documents['full_body_photo'] != '') : ?>
                            <span class="mr-2">Sudah diupload</span>
                            <a target="_blank" href="<?= base_url(DOCUMENTS_FOLDER . $admission->id_admission . '/' . $documents['full_body_photo']) ?>" class="btn btn-primary">Lihat</a>
                            <div style="display:inline-block">
                                <?= form_upload('full_body_photo', '', 'class="form-control fileupload2" id="full_body_photo"'); ?>
                            </div>
                        <?php else : ?>
                            <span class="mr-2">Belum upload</span>
                            <div style="display:inline-block">
                                <?= form_upload('full_body_photo', '', 'class="form-control fileupload" id="full_body_photo"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($admission->program == 1 && ($admission->seleksi == 2 || $admission->seleksi == 6)) : ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Sertifikat Organisasi</label>
                    <div class="col-sm-6">
                        <?= form_upload('organization_certificates', '', 'class="form-control fileuploadmultiple" id="organization_certificates" multiple'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Sertifikat Achievement</label>
                    <div class="col-sm-6">
                        <?= form_upload('achievement_certificates', '', 'class="form-control fileuploadmultiple" id="achievement_certificates" multiple'); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group row">
                <div class="col-sm-12">
                    <?= form_checkbox('statement', '1', FALSE, 'class="form_control"'); ?>
                    <label class="col-form-label">Semua informasi yang saya berikan/lampirkan dalam pendaftaran ini adalah benar</label>
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <?= form_close() ?>
        </div>
    </div>
</section><!-- #content end -->
<div class="modal fade" id="education_history_modal" tabindex="-1" role="dialog" aria-labelledby="education_history_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'education_history_form']) ?>
    <?= form_hidden('id_user_education'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="education_history_modal_label">Riwayat Pendidikan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Nama Sekolah/Universitas/College</label>
                    <div class="col-sm-12">
                        <?= form_input('education_school_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">City</label>
                    <div class="col-sm-6">
                        <?= form_input('education_city', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Major</label>
                    <div class="col-sm-6">
                        <?= form_input('education_major', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tahun</label>
                    <div class="col-sm-12">
                        <div class="float-left mr-2">
                            <?= form_dropdown('education_year_from', ['' => '-Pilih tahun-'] + $years, '', 'class="form-control"'); ?>
                        </div>
                        <div class="float-left mr-2">
                            <p class="form-control-static mb-0" style="line-height:34px"> - </p>
                        </div>
                        <div class="float-left">
                            <?= form_dropdown('education_year_to', ['' => '-Pilih tahun-'] + $years, '', 'class="form-control"'); ?>
                        </div>
                    </div>
                </div>
                <?php if ($admission->program == 2) : ?>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label">GPA</label>
                        <div class="col-sm-3">
                            <?= form_input('education_gpa', '', 'class="form-control"'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_education_history" data-table="#education_history_datatable" data-modal="#education_history_modal" data-form="form_education_history">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_education_history" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>


<script type="text/javascript">
    var initialPreview = [
        <?php foreach ($initialPreviews as $initialPreview) : ?>
            <?= "'$initialPreview'," ?>
        <?php endforeach; ?>
    ];
    var initialPreviewConfig = [
        <?php foreach ($initialPreviewConfigs as $initialPreviewConfig) : ?>
            <?= "{" ?>
            <?php foreach ($initialPreviewConfig as $key => $value) : ?>
                <?= "$key:'$value'," ?>
            <?php endforeach; ?>
            <?= "}," ?>
        <?php endforeach; ?>
    ];
</script>