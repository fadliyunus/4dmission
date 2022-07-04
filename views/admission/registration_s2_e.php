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

            <?= $this->form_builder->build_form_horizontal($form_personal_1, $user); ?>


            <div class="fancy-title title-border mb-0">
                <h3>Riwayat Pendidikan</h3>
            </div>
            <div id="education_history_toolbar">
                <a id="btn_add_education_history" href="#" class="btn btn-primary btn-create">Tambah</a>

            </div>
            <table id="education_history"></table>

            <div class="fancy-title title-border mb-0">
                <h3>Kontak Perusahaan</h3>
            </div>
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

            <div class="fancy-title title-border mb-0">
                <h3>Rekomendasi</h3>
            </div>
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
    <?= form_open('', ['id' => 'education_history_form', 'autocomplete' => 'off']) ?>
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
                            <?= form_input('education_year_from',  '', 'class="form-control yearpicker"'); ?>
                        </div>
                        <div class="float-left mr-2">
                            <p class="form-control-static mb-0" style="line-height:34px"> - </p>
                        </div>
                        <div class="float-left">
                            <?= form_input('education_year_to', '', 'class="form-control yearpicker"'); ?>
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
                        <?= form_input('family_birth_date', '', 'class="form-control datetimepicker-input datepicker" data-toggle="datetimepicker" data-target=".datepicker"'); ?>
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
                            '' => '-Pilih-',
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
                        <?= form_input('family_company_name', '', 'class="form-control"'); ?>
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