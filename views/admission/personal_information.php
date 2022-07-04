<section id="content">
    <div class="content-wrap">
        <div class="container">

            <?= $this->form_builder->open_form(array('action' => '', 'id' => 'personal_form', 'autocomplete' => 'off')) ?>
            <?= form_fieldset('Informasi Pendaftaran'); ?>
            <?= form_hidden('id_admission', $admission->id_admission); ?>
            <?= form_hidden('program', $admission->program); ?>
            <?= form_hidden('program_studi', $admission->program_studi); ?>
            <?= form_hidden('seleksi', $admission->seleksi); ?>
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

            <?= $this->form_builder->build_form_horizontal($form_referral, $personal_information); ?>

            <?php if ($admission->education_history) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Riwayat Pendidikan</h3>
                </div>
                <div id="toolbar">
                    <a id="btn_add_education_history" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="education_history" data-toolbar="#toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->score_utbk) : ?>
                <?= $this->form_builder->build_form_horizontal($form_score_utbk, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->last_education) : ?>
                <?= $this->form_builder->build_form_horizontal($form_last_education, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->last_occupation) : ?>
                <?= $this->form_builder->build_form_horizontal($form_last_occupation, $personal_information); ?>
            <?php endif; ?>

            <?php if ($admission->employment_history) : ?>
                <div class="fancy-title title-border mb-0">
                    <h3>Riwayat Pekerjaan</h3>
                </div>
                <div id="toolbar">
                    <a id="btn_add_employment_history" href="#" class="btn btn-primary btn-create">Tambah</a>
                </div>
                <table id="employment_history" data-toolbar="#toolbar"></table>
            <?php endif; ?>

            <?php if ($admission->education_fund_source) : ?>
                <?= $this->form_builder->build_form_horizontal($form_education_fund_source, $personal_information); ?>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Submit</button>
            <?= $this->form_builder->close_form() ?>

            <div class="clear"></div>
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
                        <?= form_input('education_school_name', '', 'class="form-control" style="text-transform:uppercase"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">City</label>
                    <div class="col-sm-6">
                        <?= form_input('education_city', '', 'class="form-control" style="text-transform:uppercase"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Major</label>
                    <div class="col-sm-6">
                        <?= form_input('education_major', '', 'class="form-control" style="text-transform:uppercase"'); ?>
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
                        <?= form_input('employment_company_name', '', 'class="form-control" style="text-transform:uppercase"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Alamat Perusahaan</label>
                    <div class="col-sm-12">
                        <?= form_input('employment_company_address', '', 'class="form-control" style="text-transform:uppercase"'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Jabatan</label>
                    <div class="col-sm-6">
                        <?= form_input('employment_position', '', 'class="form-control" style="text-transform:uppercase"'); ?>
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