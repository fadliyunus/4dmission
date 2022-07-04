<section id="content">
    <div class="content-wrap">
        <div class="container">
            <?php if (isset($message) && $message != '') : ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <?= $this->form_builder->open_form(['action' => '', 'id' => 'registration_form', 'autocomplete' => 'off']) ?>
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
            <?php if ($admission->tgl_seleksi) : ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Jadwal Seleksi</label>
                    <div class="col-sm-6">
                        <p class="form-control-static mb-0"><?= date('j F Y', strtotime($admission->tgl_seleksi)) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($admission->data_personal_1) : ?>
                <?= $this->form_builder->build_form_horizontal($form_personal_1, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->social_media) : ?>
                <?= $this->form_builder->build_form_horizontal($form_social_media, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->data_personal_2) : ?>
                <?= $this->form_builder->build_form_horizontal($form_personal_2, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->family) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Data Keluarga</h3>
                </div>
                <div id="family_toolbar">
                    <a id="btn_add_family" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="family" data-toolbar="#family_toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->last_education) : ?>
                <?= $this->form_builder->build_form_horizontal($form_last_education, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->education_history) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Riwayat Pendidikan</h3>
                </div>
                <div id="education_toolbar">
                    <a id="btn_add_education_history" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="education_history" data-toolbar="#education_toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->education_history_informal) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Riwayat Pendidikan (Informal)</h3>
                </div>
                <div id="education_informal_toolbar">
                    <a id="btn_add_education_informal_history" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="education_informal_history" data-toolbar="#education_informal_toolbar"></table>
            <?php endif; ?>



            <?php if ($admission->last_occupation) : ?>
                <?= $this->form_builder->build_form_horizontal($form_last_occupation, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->employment_history) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Riwayat Pekerjaan</h3>
                </div>
                <div id="employment_toolbar">
                    <a id="btn_add_employment_history" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="employment_history" data-toolbar="#employment_toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->organization_history) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Pengalaman Organisasi</h3>
                </div>
                <div id="organization_history_toolbar">
                    <a id="btn_add_organization_history" href="#" class="btn btn-primary btn-create">Tambah</a>

                </div>
                <table id="organization_history" data-toolbar="organization_history_toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->achievement) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Prestasi</h3>
                </div>
                <div id="achievement_toolbar">
                    <a id="btn_add_achievement" href="#" class="btn btn-primary btn-create">Tambah</a>

                </div>
                <table id="achievement" data-toolbar="achievement_toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->company_contact_info) : ?>
                <?= $this->form_builder->build_form_horizontal($form_company_contact_info, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->recommendation) : ?>
                <?= $this->form_builder->build_form_horizontal($form_recommendation, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->education_fund_source) : ?>
                <?= $this->form_builder->build_form_horizontal($form_education_fund_source, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->score_utbk) : ?>
                <?= $this->form_builder->build_form_horizontal($form_score_utbk, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->file_upload_1 || $admission->file_upload_2 || $admission->file_upload_3 || $admission->file_upload_4) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Upload Dokumen</h3>
                </div>
                <div class="help-block mb-3">File yang diupload hanya boleh file .pdf, .jpeg, .jpg, .png dengan ukuran maksimum 2MB</div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas X Semester 1</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_x_1', '', 'class="form-control fileupload" id="report_x_1"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas X Semester 2</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_x_2', '', 'class="form-control fileupload" id="report_x_2"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 1</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_xi_1', '', 'class="form-control fileupload" id="report_xi_1"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XI Semester 2</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_xi_2', '', 'class="form-control fileupload" id="report_xi_2"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 1</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_xii_1', '', 'class="form-control fileupload" id="report_xii_1"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Raport Kelas XII Semester 2</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('report_xii_2', '', 'class="form-control fileupload" id="report_xii_2"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <?php if ($admission->file_upload_2) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Keterangan Akreditasi Sekolah</label>
                        <div class="col-sm-6">
                            <div style="display:inline-block">
                                <?= form_upload('accreditation_certificate', '', 'class="form-control fileupload" id="accreditation_certificate"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($admission->file_upload_4) :  ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sertifikat Hasil UTBK</label>
                        <div class="col-sm-6">

                            <div style="display:inline-block">
                                <?= form_upload('utbk_certificate', '', 'class="form-control fileupload" id="utbk_certificate"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">KTP</label>
                    <div class="col-sm-6">

                        <div style="display:inline-block">
                            <?= form_upload('identity_card', '', 'class="form-control fileupload" id="identity_card"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">KK</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('family_certificate', '', 'class="form-control fileupload" id="family_certificate"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <?php if ($admission->file_upload_3) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sertifikat Organisasi</label>
                        <div class="col-sm-6">
                            <div style="display:inline-block">
                                <?= form_upload('organization_certificates', '', 'class="form-control fileuploadmultiple" id="organization_certificates" multiple'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sertifikat Achievement</label>
                        <div class="col-sm-6">
                            <div style="display:inline-block">
                                <?= form_upload('achievement_certificates', '', 'class="form-control fileuploadmultiple" id="achievement_certificates" multiple'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Pas Photo Terbaru (4x6)</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('photo', '', 'class="form-control fileupload" id="photo"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($admission->file_upload_5 || $admission->file_upload_6 || $admission->file_upload_7) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Upload Dokumen</h3>
                </div>
                <div class="help-block mb-3">File yang diupload hanya boleh file .pdf, .jpeg, .jpg, .png dengan ukuran maksimum 2MB</div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Transkrip Nilai</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('academic_transcript', '', 'class="form-control fileupload" id="academic_transcript"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Ijazah</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('school_certificate', '', 'class="form-control fileupload" id="school_certificate"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">KTP</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('identity_card', '', 'class="form-control fileupload" id="identity_card"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">KK</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('family_certificate', '', 'class="form-control fileupload" id="family_certificate"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">TOEFL/IELTS</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('toefl_certificate', '', 'class="form-control fileupload" id="toefl_certificate" data-required="false"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Pas Photo Terbaru (4x6)</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('photo', '', 'class="form-control fileupload" id="photo"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">CV</label>
                    <div class="col-sm-6">
                        <div style="display:inline-block">
                            <?= form_upload('cv', '', 'class="form-control fileupload" id="cv"'); ?>
                        </div>
                        <div id="errorBlock" class="help-block"></div>
                    </div>
                </div>
                <?php if ($admission->file_upload_6) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Full Body Photo</label>
                        <div class="col-sm-6">
                            <div style="display:inline-block">
                                <?= form_upload('full_body_photo', '', 'class="form-control fileupload" id="full_body_photo"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($admission->file_upload_7) : ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Karangan Ringkas</label>
                        <div class="col-sm-6">
                            <div style="display:inline-block">
                                <?= form_upload('short_resume', '', 'class="form-control fileupload" id="short_resume"'); ?>
                            </div>
                            <div id="errorBlock" class="help-block"></div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($admission->other) : ?>
                <?= $this->form_builder->build_form_horizontal($form_other, $personal_information); ?>
            <?php endif; ?>

            <div class="form-group row">
                <div class="form-check">
                    <?= form_checkbox('statement', '1', FALSE, 'id="statement" class="form_control"'); ?>
                    <label for="statement" class="col-form-label">Semua informasi yang saya berikan/lampirkan dalam pendaftaran ini adalah benar</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <?= $this->form_builder->close_form() ?>
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
                            <?= form_input('education_year_from', '', 'class="form-control yearpicker"') ?>
                        </div>
                        <div class="float-left mr-2">
                            <p class="form-control-static mb-0" style="line-height:34px"> - </p>
                        </div>
                        <div class="float-left">
                            <?= form_input('education_year_to', '', 'class="form-control yearpicker"') ?>
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
<div class="modal fade" id="employment_history_modal" tabindex="-1" role="dialog" aria-labelledby="employment_history_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'employment_history_form', 'autocomplete' => 'off']) ?>
    <?= form_hidden('id_user_employment'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="employment_history_modal_label">Riwayat Pekerjaan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Nama Perusahaan</label>
                    <div class="col-sm-12">
                        <?= form_input('employment_company_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Alamat Perusahaan</label>
                    <div class="col-sm-12">
                        <?= form_input('employment_company_address', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Jabatan</label>
                    <div class="col-sm-6">
                        <?= form_input('employment_position', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tahun</label>
                    <div class="col-sm-12">
                        <div class="float-left mr-2">
                            <?= form_input('employment_year_from',  '', 'class="form-control yearpicker"'); ?>
                        </div>
                        <div class="float-left mr-2">
                            <p class="form-control-static mb-0" style="line-height:34px"> - </p>
                        </div>
                        <div class="float-left">
                            <?= form_input('employment_year_to', '', 'class="form-control yearpicker"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_employment_history" data-table="#employment_history_datatable" data-modal="#employment_history_modal" data-form="form_employment_history">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_employment_history" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>
<div class="modal fade" id="family_modal" tabindex="-1" role="dialog" aria-labelledby="family_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'family_form', 'autocomplete' => 'off']) ?>
    <?= form_hidden('id_user_family'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="family_modal_label">Data Keluarga</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-12">
                        <?= form_input('family_full_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tgl Lahir</label>
                    <div class="col-sm-6">
                        <?= form_input('family_birth_date', '', 'class="form-control datepicker"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">No Telp</label>
                    <div class="col-sm-6">
                        <?= form_input('family_phone', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">E-mail</label>
                    <div class="col-sm-6">
                        <?= form_input('family_email', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Status</label>
                    <div class="col-sm-6">
                        <?= form_dropdown('family_marital_status', [
                            '' => '-PILIH-',
                            '0' => 'BELUM MENIKAH',
                            '1' => 'SUDAH MENIKAH',
                            '2' => 'BERPISAH/CERAI',
                        ], '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Bekerja</label>
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input type="radio" name="family_working_status" value="1" id="family_working_status_option_1" label="Bekerja" class="form-check-input" data-title="Bekerja">
                            <label class="form-check-label" for="family_working_status_option_1">Bekerja</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="family_working_status" value="0" id="family_working_status_option_2" label="Tidak Bekerja" class="form-check-input" data-title="Tidak Bekerja">
                            <label class="form-check-label" for="family_working_status_option_2">Tidak Bekerja</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row family_working_status d-none">
                    <label class="col-sm-12 col-form-label">Jabatan</label>
                    <div class="col-sm-6">
                        <?= form_input('family_working_position', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row family_working_status d-none">
                    <label class="col-sm-12 col-form-label">Nama Perusahaan</label>
                    <div class="col-sm-6">
                        <?= form_input('family_working_company', '', 'class="form-control"'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_family" data-table="#family_datatable" data-modal="#family_modal" data-form="form_family">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_family" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>
<div class="modal fade" id="organization_history_modal" tabindex="-1" role="dialog" aria-labelledby="organization_history_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'organization_history_form', 'autocomplete' => 'off']) ?>
    <?= form_hidden('id_user_organization'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="organization_history_modal_label">Pengalaman Organisasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Nama Organisasi</label>
                    <div class="col-sm-12">
                        <?= form_input('organization_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Posisi</label>
                    <div class="col-sm-6">
                        <?= form_input('organization_position', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tahun</label>
                    <div class="col-sm-6">
                        <?= form_input('organization_year', '', 'class="form-control yearpicker"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_organization_history" data-table="#organization_history_datatable" data-modal="#organization_history_modal" data-form="form_organization_history">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_organization_history" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>
<div class="modal fade" id="achievement_modal" tabindex="-1" role="dialog" aria-labelledby="achievement_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'achievement_form', 'autocomplete' => 'off']) ?>
    <?= form_hidden('id_user_achievement'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="achievement_modal_label">Prestasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Prestasi</label>
                    <div class="col-sm-12">
                        <?= form_input('achievement_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Penyelenggara</label>
                    <div class="col-sm-6">
                        <?= form_input('achievement_organizer', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tahun</label>
                    <div class="col-sm-6">
                        <?= form_input('achievement_year', '', 'class="form-control yearpicker"'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_achievement" data-table="#achievement_datatable" data-modal="#achievement_modal" data-form="form_achievement">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_achievement" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>
<div class="modal fade" id="education_informal_history_modal" tabindex="-1" role="dialog" aria-labelledby="education_informal_history_modal_label" aria-hidden="true">
    <?= form_open('', ['id' => 'education_informal_history_form', 'autocomplete' => 'off']) ?>
    <?= form_hidden('id_user_education_informal'); ?>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="education_informal_history_modal_label">Riwayat Pendidikan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Nama Kursus/Serifikasi/Training</label>
                    <div class="col-sm-12">
                        <?= form_input('education_informal_school_name', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Penyelenggara</label>
                    <div class="col-sm-6">
                        <?= form_input('education_informal_organizer', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Gelar</label>
                    <div class="col-sm-6">
                        <?= form_input('education_informal_title', '', 'class="form-control"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Tahun</label>
                    <div class="col-sm-12">
                        <div class="float-left mr-2">
                            <?= form_input('education_informal_year_from',  '', 'class="form-control yearpicker"'); ?>
                        </div>
                        <div class="float-left mr-2">
                            <p class="form-control-static mb-0" style="line-height:34px"> - </p>
                        </div>
                        <div class="float-left">
                            <?= form_input('education_informal_year_to', '', 'class="form-control yearpicker"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-submit-modal" id="btn_submit_education_informal_history" data-table="#education_informal_history_datatable" data-modal="#education_informal_history_modal" data-form="form_education_informal_history">Submit</button>
                <button type="button" class="btn btn-secondary" id="btn_cancel_education_informal_history" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
    <?= form_close(); ?>
</div>


<script type="text/javascript">
    // var initialPreview = [
    //     <?php foreach ($initialPreviews as $initialPreview) : ?>
    //         <?= "'$initialPreview'," ?>
    //     <?php endforeach; ?>
    // ];
    var initialPreview = <?= json_encode($initialPreviews) ?>;
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