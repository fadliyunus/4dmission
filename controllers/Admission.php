<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admission extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Provinsi_model',
            'Kota_model',
            'Admissions_model',
            'Program_studi_model',
            'Seleksi_model',
            'Seleksi_form_model',
            'Forms_fields_model',
            'Fields_model',
            'Fields_options_model',
            'Jadwal_model',
            'Jadwal_seleksi_model',
            'Personal_informations_model',
            'Forms_model',
            'Payments_model',
            'Users_educations_model',
            'Users_educations_informal_model',
            'Users_documents_model',
            'Users_utbk_model',
            'Users_employments_model',
            'Users_families_model',
            'Users_achievements_model',
            'Users_organizations_model',
            'Angkatan_model',
            'Skema_pembayaran_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    function index()
    {
        // $user = $this->ion_auth->user()->row();

        // if ($this->Personal_informations_model->get_by('id_user', $user->id)) {
        $this->dashboard();
        // } else {
        //     $this->personal_information();
        // }
    }

    public function get_kota()
    {
        $response = new stdClass();
        $province = $this->input->post('province');
        $_data = $this->Kota_model->get_many_by(array('kode_provinsi' => $province));
        $data = array();
        foreach ($_data as $d) {
            $data[] = array('id_kota' => $d->kode_kota, 'nama_kota' => $d->nama_kota);
        }
        $response->data = $data;
        echo json_encode($response);
    }

    function dashboard()
    {
        $this->data['title'] = 'Admissions';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower(static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $user = $this->ion_auth->user()->row();
        $personal_information = $this->Personal_informations_model->get_by('id_user', $user->id);

        $this->data['user'] = $user;
        $this->data['personal_information'] = $personal_information;

        $this->load->view('template_home', $this->data);
    }

    public function dashboard_data()
    {
        $response = new stdClass();

        $response->rows = [];

        if ($this->ion_auth->logged_in()) {
            $filter = [
                'id_user' => $this->ion_auth->user()->row()->id,
            ];
            $rows = $this->Admissions_model
                ->select('admissions.*, nama_program_studi, nama_seleksi, payment, payment_status')
                ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
                ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
                ->join('payments', 'payments.id_admission = admissions.id_admission AND payment_type = 1', 'left')
                ->order_by('admissions.created_on')
                ->get_many_by($filter);

            foreach ($rows as $row) {
                $filter = [
                    'id_admission' => $row->id_admission,
                    'payment_type' => 2,
                ];
                $row->confirmation = $this->Payments_model->get_by($filter);
                $filter = [
                    'id_admission' => $row->id_admission,
                    'payment_type' => 3,
                    'payment_installment' => 1,
                ];
                $row->installment = $this->Payments_model->get_by($filter);

                $filter = [
                    'id_seleksi' => $row->seleksi,
                    'tgl_seleksi' => $row->tgl_seleksi,
                ];
                $row->skema_pembayaran = $this->Skema_pembayaran_model->get_by($filter);
            }
            $response->rows = $rows;
        }
        echo json_encode($response);
    }

    function personal_information($id_admission)
    {
        $admission = $this->Admissions_model
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('seleksi_form', 'seleksi_form.id_seleksi = admissions.seleksi AND seleksi_form.type = 1', 'left')
            ->get($id_admission);

        // print_r($admission);

        $this->data['admission'] = $admission;
        $this->data['title'] = '';

        $user = $this->ion_auth->user()->row();

        $this->data['user'] = $user;

        $personal_information = $this->Personal_informations_model
            ->get_by('personal_informations.id_user', $user->id);
        if ($personal_information) {
            $personal_information->mobile_phone = $user->phone;
            $personal_information->whatsapp_number = $user->phone;
            $personal_information->email = $user->email;
            $personal_information->education_fund_source = $admission->education_fund_source;
        }

        $this->data['personal_information'] = $personal_information;

        $this->form_validation->set_rules('full_name', 'Name', 'required');
        if ($this->form_validation->run() == TRUE) {
            $this->db->trans_begin();

            $data = $this->input->post();
            unset($data['utbk_average']);

            $data['id_user'] = $user->id;

            if (isset($data['education_fund_source'])) {
                $data_education = [
                    'education_fund_source' => $data['education_fund_source'],
                ];
                $this->Admissions_model->update($data['id_admission'], $data_education);
            }

            $personal_data = [];
            $fields = $this->db->list_fields('personal_informations');
            foreach ($data as $key => $d) {
                if (in_array($key, $fields)) {
                    $personal_data[$key] = strtoupper($d);
                }
            }

            if ($personal_information = $this->Personal_informations_model->get_by('id_user', $user->id)) {
                $this->Personal_informations_model->update($personal_information->id_personal_information, $personal_data);
            } else {
                $this->Personal_informations_model->insert($personal_data);
            }



            if ($admission->payment) {
                $this->Admissions_model->update($admission->id_admission, ['status' => 101]);
            } else {
                // $this->Admissions_model->update($admission->id_admission, ['status' => 102]);
                $this->Admissions_model->update($admission->id_admission, ['status' => 200]);
            }

            $send_email = $this->send_registration_email($id_admission);

            if ($this->db->trans_status() == TRUE && $send_email) {
                $this->db->trans_commit();
                if ($admission->payment) {
                    redirect('payment/registration/' . $id_admission, 'refresh');
                } else {
                    // redirect('admission/registration_complete', 'refresh');
                    redirect('admission/registration/'.$id_admission, 'refresh');
                }
            } else {
                $this->db->trans_rollback();
            }
        }

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];
        $years = [];
        for ($i = date('Y'); $i >= 1980; $i--) {
            $years[$i] = $i;
        }
        $this->data['years'] = $years;
        $this->data['modals'] = [];

        $city_options = ['' => '-Pilih-'];
        if ($personal_information && $personal_information->province) {
            $this->db->where('kode_provinsi', $personal_information->province);
            $city_options = $this->Kota_model->dropdown('kode_kota', 'nama_kota');
        }

        $this->data['form_personal_1'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Data Personal</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
            'value' => set_value('full_name', $user->full_name),
        ], [
            'id' => 'gender',
            'label' => 'Jenis Kelamin',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'gender_option_1',
                    'value' => 1,
                    'label' => 'LAKI-LAKI',
                    'checked' => $personal_information && $personal_information->gender == 1,
                ], [
                    'id' => 'gender_option_2',
                    'value' => 2,
                    'label' => 'PEREMPUAN',
                    'checked' => $personal_information && $personal_information->gender == 2,
                ]
            ]
        ], [
            'id' => 'birthdate',
            'label' => 'Tanggal Lahir',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'birthplace',
            'label' => 'Tempat Lahir',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'marital_status',
            'label' => 'Status Perkawinan',
            'type' => 'dropdown',
            'options' => [
                '' => '-PILIH-',
                '0' => 'BELUM MENIKAH',
                '1' => 'SUDAH MENIKAH',
                '2' => 'BERPISAH/CERAI',
            ],
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'address_1',
            'label' => 'Alamat',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'address_2',
            'label' => '',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'province',
            'label' => 'Provinsi',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih-'] + $this->Provinsi_model->dropdown('kode_provinsi', 'nama_provinsi'),
            'form_control_class' => 'col-sm-4',
        ], [
            'id' => 'city',
            'label' => 'Kab/Kota',
            'type' => 'dropdown',
            'options' => $city_options,
            'form_control_class' => 'col-sm-4',
        ], [
            'id' => 'postal_code',
            'label' => 'Kode Pos',
            'class' => 'number',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'mobile_phone',
            'label' => 'No. HP',
            'class' => 'number',
            'form_control_class' => 'col-sm-3',
            'value' => set_value('full_name', $user->phone),
        ], [
            'id' => 'whatsapp_number',
            'label' => 'No. Whatsapp',
            'class' => 'number',
            'form_control_class' => 'col-sm-3',
            'value' => set_value('full_name', $user->phone),
        ], [
            'id' => 'email',
            'label' => 'E-mail',
            'value' => set_value('full_name', $user->email),
        ]];

        $this->data['form_referral'] = [[
            'id' => 'referral',
            'label' => 'Pemberi Referensi (jika ada)',
            'style' => 'text-transform:uppercase',
        ]];

        $this->data['form_social_media'] = [[
            'id' => 'social_media',
            'label' => 'Sosial Media',
            'type' => 'dropdown',
            'options' => [
                '' => '-PILIH-',
                '1' => 'INSTAGRAM',
                '2' => 'TIK TOK',
                '3' => 'FACEBOOK',
                '4' => 'TWITTER',
            ],
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'social_media_account',
            'label' => 'Nama Akun Sosial Media',
        ]];

        $this->data['form_personal_2'] = [[
            'id' => 'father_name',
            'label' => 'Nama Ayah',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'father_birthdate',
            'label' => 'Tanggal Lahir Ayah',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'father_phone',
            'label' => 'No. HP/Whatsapp Ayah',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'father_email',
            'label' => 'E-mail Ayah',
        ], [
            'id' => 'father_status',
            'label' => 'Status Ayah',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'father_status_option_1',
                    'value' => 1,
                    'label' => 'Masih hidup',
                    'checked' => $personal_information && $personal_information->father_status == 1,
                ], [
                    'id' => 'father_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal',
                    'checked' => $personal_information && $personal_information->father_status == 2,
                ]
            ]
        ], [
            'id' => 'father_working_status',
            'label' => 'Status Pekerjaan Ayah',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'father_working_status_option_1',
                    'value' => 1,
                    'label' => 'Bekerja',
                    'checked' => $personal_information && $personal_information->father_working_status == 1,
                ], [
                    'id' => 'father_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja',
                    'checked' => $personal_information && $personal_information->father_working_status == 2,
                ]
            ]
        ], [
            'id' => 'father_working_company',
            'label' => 'Nama Perusahaan',
            'input_container_class' => 'father_working_status ' . ($personal_information && $personal_information->father_working_status == 1 ? '' : 'd-none'),
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'father_working_position',
            'label' => 'Jabatan',
            'input_container_class' => 'father_working_status ' . ($personal_information && $personal_information->father_working_status == 1 ? '' : 'd-none'),
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_name',
            'label' => 'Nama Ibu',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_birthdate',
            'label' => 'Tanggal Lahir Ibu',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'mother_phone',
            'label' => 'No. HP/Whatsapp Ibu',
        ], [
            'id' => 'mother_email',
            'label' => 'E-mail Ibu',
        ], [
            'id' => 'mother_status',
            'label' => 'Status',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'mother_status_option_1',
                    'value' => 1,
                    'label' => 'Masih hidup',
                    'checked' => $personal_information && $personal_information->mother_status == 1,
                ], [
                    'id' => 'mother_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal',
                    'checked' => $personal_information && $personal_information->mother_status == 2,
                ]
            ]
        ], [
            'id' => 'mother_working_status',
            'label' => 'Bekerja',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'mother_working_status_option_1',
                    'value' => 1,
                    'label' => 'Bekerja',
                    'checked' => $personal_information && $personal_information->mother_working_status == 1,
                ], [
                    'id' => 'mother_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja',
                    'checked' => $personal_information && $personal_information->mother_working_status == 2,
                ]
            ]
        ], [
            'id' => 'mother_working_company',
            'label' => 'Nama Perusahaan',
            'input_container_class' => 'mother_working_status ' . ($personal_information && $personal_information->mother_working_status == 1 ? '' : 'd-none'),
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_working_position',
            'label' => 'Jabatan',
            'input_container_class' => 'mother_working_status ' . ($personal_information && $personal_information->mother_working_status == 1 ? '' : 'd-none'),
            'style' => 'text-transform:uppercase',
        ]];

        $this->data['form_score_utbk'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Nilai UTBK</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'utbk_penalaran_umum',
            'label' => 'Nilai TPS Kemampuan Penalaran UMUM',
            'class' => 'utbk number text-right',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-2',
        ], [
            'id' => 'utbk_kuantitatif',
            'label' => 'Nilai TPS Kemampuan Kuantitatif *',
            'class' => 'utbk number text-right',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-2',
        ], [
            'id' => 'utbk_pengetahuan',
            'label' => 'Nilai TPS Pengetahuan dan Pemahaman Umum *',
            'class' => 'utbk number text-right',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-2',
        ], [
            'id' => 'utbk_baca_tulis',
            'label' => 'Nilai Kemampuan Memahami Bacaan & Menulis *',
            'class' => 'utbk number text-right',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-2',
        ], [
            'id' => 'utbk_average',
            'label' => 'Rata-rata Nilai',
            'class' => 'text-right',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-2',
            'readonly' => 'readonly',
            'value' => '0',
        ]];

        $this->data['form_last_education'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Pendidikan Terakhir</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'last_education_school_name',
            'label' => 'Nama Universitas',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'last_education_year',
            'label' => 'Tahun',
            'type' => 'combine',
            'elements' => [[
                'id' => 'last_education_year_from',
                'style' => 'width: auto;display:inline-block',
                'class' => 'yearpicker',
            ], [
                'type' => 'html',
                'html' => '<span> - </span>',
            ], [
                'id' => 'last_education_year_to',
                'style' => 'width: auto; margin-left: 5px;display:inline-block',
                'class' => 'yearpicker',
            ]]
        ], [
            'id' => 'last_education_major',
            'label' => 'Fakultas dan Jurusan',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'last_education_title',
            'label' => 'Gelar',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'last_education_gpa',
            'label' => 'IPK',
            'class' => 'decimal',
            'form_control_class' => 'col-sm-2',
        ]];

        $this->data['form_last_occupation'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Pekerjaan Saat Ini</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'last_employment_company_name',
            'label' => 'Nama Perusahaan',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'last_employment_company_address',
            'label' => 'Alamat Perusahaan',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'last_employment_position',
            'label' => 'Jabatan',
            'form_control_class' => 'col-sm-3',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'last_employment_year',
            'label' => 'Tahun',
            'type' => 'combine',
            'elements' => [[
                'id' => 'last_employment_year_from',
                'style' => 'width: auto;display:inline-block',
                'class' => 'yearpicker',
            ], [
                'type' => 'html',
                'html' => '<span> - </span>',
            ], [
                'id' => 'last_employment_year_to',
                'style' => 'width: auto; margin-left: 5px;display:inline-block',
                'class' => 'yearpicker',
            ]]
        ]];

        $this->data['form_education_fund_source'] = [[
            'id' => 'education_fund_source',
            'label' => 'Rencana Biaya Pendidikan',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih rencana biaya pendidikan', '1' => 'Pribadi', '2' => 'Perusahaan', '3' => 'Sebagian pribadi, sebagian perusahaan']
        ]];


        $this->load->view('template_home', $this->data);
    }


    function create()
    {
        $user = $this->ion_auth->user()->row();
        $user->name = $user->full_name;
        $user->mobile_phone = $user->phone;
        $user->whatsapp_number = $user->phone;

        $this->data['user'] = $user;

        $this->form_validation->set_rules('program', 'Program', 'required');
        $this->form_validation->set_rules('program_studi', 'Program Studi', 'required');
        $this->form_validation->set_rules('seleksi', 'Seleksi', 'required');

        if ($this->form_validation->run() == TRUE) {

            $filter = [
                'id_program_studi' => $this->input->post('program_studi'),
                'active' => 1,
            ];
            $angkatan = $this->Angkatan_model->get_by($filter);

            $filter = [
                'program_studi' => $this->input->post('program_studi'),
                'id_user' => $user->id,
                'status !=' => '302',
            ];
            if (!$this->Admissions_model->get_by($filter)) {
                $tgl_seleksi = NULL;
                if ($this->input->post('jadwal')) {
                    $tgl_seleksi = date('Y-m-d', $this->input->post('jadwal'));
                }
                if ($this->input->post('tgl_seleksi')) {
                    if ($tgl = date_create_from_format('d/m/Y', $this->input->post('tgl_seleksi'))) {
                        $tgl_seleksi = date_format($tgl, 'Y-m-d');
                    }
                }
                $admission_data = [
                    'id_angkatan' => $angkatan ? $angkatan->id_angkatan : 0,
                    'program' => $this->input->post('program'),
                    'program_studi' => $this->input->post('program_studi'),
                    'seleksi' => $this->input->post('seleksi'),
                    'tgl_seleksi' => $tgl_seleksi,
                    'id_user' => $user->id,
                    'status' => 100,
                    'created_by' => $user->id,
                ];
                $this->db->trans_begin();

                if ($id_admission = $this->Admissions_model->insert($admission_data)) {
                    $seleksi = $this->Seleksi_model->get($this->input->post('seleksi'));
                    if ($seleksi->payment) {
                        $this->Payments_model->insert([
                            'id_admission' => $id_admission,
                            'payment_code' => $id_admission,
                            'payment_status' => '0',
                        ]);
                    }
                }

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('admission/personal_information/' . $id_admission, 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $program_studi = $this->Program_studi_model->get($this->input->post('program_studi'));
                $this->session->set_flashdata('message', 'Anda telah mendaftar pada program studi ' . $program_studi->nama_program_studi);
            }
        }

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $program_studi_options = ['' => '-Pilih program studi-'];
        if ($this->input->post('program')) {
            $this->db->where('jenis_program_studi', $this->input->post('program'));
            $this->db->where('active', '1');
            $program_studi_options = ['' => '-Pilih program studi-'] + $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        }
        $this->data['program_studi_data'] = $this->Program_studi_model->get_many_by('active','1');

        $seleksi_options = ['' => '-Pilih seleksi-'];
        if ($this->input->post('program_studi')) {
            $this->db->where('id_program_studi', $this->input->post('program_studi'));
            $seleksi_options = ['' => '-Pilih seleksi-'] + $this->Seleksi_model->dropdown('id_seleksi', 'nama_seleksi');
        }
        $this->data['seleksi_data'] = $this->Seleksi_model->get_all();

        $jadwal_options = ['' => '-Pilih seleksi-'];
        if ($this->input->post('seleksi')) {
            $jadwal_options = ['' => '-Pilih jadwal-'];
            $jadwal_seleksi = $this->Jadwal_seleksi_model
                // ->join('jadwal', 'jadwal.id_jadwal = jadwal_seleksi.id_jadwal', 'left')
                ->get_many_by('id_seleksi', $this->input->post('seleksi'));

            foreach ($jadwal_seleksi as $jadwal) {
                $jadwal_options[$jadwal->id_jadwal_seleksi] = date('j F Y', strtotime($jadwal->tgl_seleksi));
            }
        }

        $filter = [
            'tgl_seleksi >= ' => date('Y-m-d'),
        ];

        $this->data['jadwal_data'] =  $this->Jadwal_seleksi_model->get_many_by($filter);

        $this->data['form'] = [[
            'id' => 'program',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-'] + ['1' => 'SARJANA', '2' => 'MAGISTER'],
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'value' => set_value('program'),
        ], [
            'id' => 'program_studi',
            'label' => 'Program Studi',
            'type' => 'dropdown',
            'options' => $program_studi_options,
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'value' => set_value('program_studi'),
        ], [
            'id' => 'seleksi',
            'label' => 'Seleksi',
            'type' => 'dropdown',
            'options' => $seleksi_options,
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'value' => set_value('seleksi'),
        ], [
            'id' => 'jadwal',
            'label' => 'Jadwal Test',
            'type' => 'dropdown',
            'options' => $jadwal_options,
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'value' => set_value('jadwal'),
            'input_container_class' => 'd-none',
        ], [
            'id' => 'tgl_seleksi',
            'label' => 'Jadwal Test',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'value' => set_value('tgl_seleksi'),
            'input_container_class' => 'd-none',
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];

        $this->load->view('template_home', $this->data);
    }

    public function get_program_studi()
    {
        $response = new stdClass();
        $program = $this->input->get('program');

        $filter = ['jenis_program_studi' => $program];
        $program_studi = $this->Program_studi_model->get_many_by($filter);

        $data = [];
        foreach ($program_studi as $ps) {
            $data[$ps->id_program_studi] = $ps->nama_program_studi;
        }
        $response->data = $data;
        echo json_encode($response);
    }

    public function get_seleksi()
    {
        $response = new stdClass();
        $program_studi = $this->input->get('program_studi');

        $filter = ['id_program_studi' => $program_studi];
        $seleksi = $this->Seleksi_model->get_many_by($filter);

        $data = [];
        foreach ($seleksi as $ps) {
            $data[$ps->id_seleksi] = $ps->nama_seleksi;
        }
        $response->data = $data;
        echo json_encode($response);
    }

    public function get_jadwal()
    {
        $response = new stdClass();
        $seleksi = $this->input->get('seleksi');

        $filter = [
            'id_seleksi' => $seleksi,
        ];
        $jadwal = $this->Jadwal_seleksi_model
            ->get_many_by($filter);

        $data = [];
        foreach ($jadwal as $j) {
            $data[strtotime($j->tgl_seleksi)] = date('j F Y', strtotime($j->tgl_seleksi));
        }
        $response->data = $jadwal ? $data : NULL;
        echo json_encode($response);
    }

    public function _rearrange($arr)
    {
        foreach ($arr as $key => $all) {
            foreach ($all as $i => $val) {
                $new[$i][$key] = $val;
            }
        }
        return $new;
    }

    function registration($id_admission)
    {
        $admission = $this->Admissions_model
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('seleksi_form', 'seleksi_form.id_seleksi = admissions.seleksi AND seleksi_form.type = 2', 'left')
            ->get($id_admission);

        if (!$admission) {
            show_404();
        }

        $this->data['admission'] = $admission;

        $user = $this->ion_auth->user()->row();

        $personal_information = $this->Personal_informations_model
            ->get_by('personal_informations.id_user', $user->id);
        if ($personal_information) {
            $personal_information->mobile_phone = $user->phone;
            $personal_information->whatsapp_number = $user->phone;
            $personal_information->email = $user->email;
            $personal_information->education_fund_source = $admission->education_fund_source;

            $personal_information->marketing_hear_from = explode(',', $personal_information->marketing_hear_from);
            $personal_information->marketing_reason = explode(',', $personal_information->marketing_reason);
        }

        $this->data['personal_information'] = $personal_information;

        $this->form_validation->set_rules('statement', 'Statement', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->input->post();
            $this->db->trans_begin();

            $personal_data = [];
            $fields = $this->db->list_fields('personal_informations');
            foreach ($data as $key => $d) {
                if (in_array($key, $fields)) {
                    $personal_data[$key] = strtoupper($d);
                }
            }

            if ($personal_data) {
                $this->Personal_informations_model->update($personal_information->id_personal_information, $personal_data);
            }

            $this->Admissions_model->update($id_admission, ['status' => 300]);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                redirect('admission/registration_complete', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->set_flashdata('message');
        $this->data['title'] = '';


        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];


        $this->data['form_personal_1'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Data Personal</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
        ], [
            'id' => 'gender',
            'label' => 'Jenis Kelamin',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'gender_option_1',
                    'value' => 1,
                    'label' => 'LAKI-LAKI',
                    'checked' => $personal_information && $personal_information->gender == 1,
                ], [
                    'id' => 'gender_option_2',
                    'value' => 2,
                    'label' => 'PEREMPUAN',
                    'checked' => $personal_information && $personal_information->gender == 2,
                ]
            ]
        ], [
            'id' => 'birthdate',
            'label' => 'Tanggal Lahir',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'birthplace',
            'label' => 'Tempat Lahir',
        ], [
            'id' => 'marital_status',
            'label' => 'Status Perkawinan',
            'type' => 'dropdown',
            'options' => [
                '' => '-PILIH-',
                '0' => 'BELUM MENIKAH',
                '1' => 'SUDAH MENIKAH',
                '2' => 'BERPISAH/CERAI',
            ],
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'address_1',
            'label' => 'Alamat',
        ], [
            'id' => 'address_2',
            'label' => '',
        ], [
            'id' => 'province',
            'label' => 'Provinsi',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih-'] + $this->Provinsi_model->dropdown('kode_provinsi', 'nama_provinsi'),
            'form_control_class' => 'col-sm-4',
        ], [
            'id' => 'city',
            'label' => 'Kab/Kota',
            'type' => 'dropdown',
            'options' => [
                '' => '-Pilih-',
            ],
            'form_control_class' => 'col-sm-4',
        ], [
            'id' => 'postal_code',
            'label' => 'Kode Pos',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'mobile_phone',
            'label' => 'No. HP',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'whatsapp_number',
            'label' => 'No. Whatsapp',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'email',
            'label' => 'E-mail',
        ]];

        $this->data['form_referral'] = [[
            'id' => 'referral',
            'label' => 'Pemberi Referensi (jika ada)',
        ]];

        $this->data['form_social_media'] = [[
            'id' => 'social_media',
            'label' => 'Sosial Media',
            'type' => 'dropdown',
            'options' => [
                '' => '-PILIH-',
                '1' => 'INSTAGRAM',
                '2' => 'TIK TOK',
                '3' => 'FACEBOOK',
                '4' => 'TWITTER',
            ],
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'social_media_account',
            'label' => 'Nama Akun Sosial Media',
        ]];

        $this->data['form_personal_2'] = [[
            'id' => 'father_name',
            'label' => 'Nama Ayah',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'father_birthdate',
            'label' => 'Tanggal Lahir Ayah',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'father_phone',
            'label' => 'No. HP/Whatsapp Ayah',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'father_email',
            'label' => 'E-mail Ayah',
        ], [
            'id' => 'father_status',
            'label' => 'Status Ayah',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'father_status_option_1',
                    'value' => 1,
                    'label' => 'Masih hidup',
                    'checked' => $personal_information && $personal_information->father_status == 1,
                ], [
                    'id' => 'father_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal',
                    'checked' => $personal_information && $personal_information->father_status == 2,
                ]
            ]
        ], [
            'id' => 'father_working_status',
            'label' => 'Status Pekerjaan Ayah',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'father_working_status_option_1',
                    'value' => 1,
                    'label' => 'Bekerja',
                    'checked' => $personal_information && $personal_information->father_working_status == 1,
                ], [
                    'id' => 'father_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja',
                    'checked' => $personal_information && $personal_information->father_working_status == 2,
                ]
            ]
        ], [
            'id' => 'father_working_company',
            'label' => 'Nama Perusahaan',
            'input_container_class' => 'father_working_status d-none',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'father_working_position',
            'label' => 'Jabatan',
            'input_container_class' => 'father_working_status d-none',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_name',
            'label' => 'Nama Ibu',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_birthdate',
            'label' => 'Tanggal Lahir Ibu',
            'type' => 'date',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'mother_phone',
            'label' => 'No. HP/Whatsapp Ibu',
        ], [
            'id' => 'mother_email',
            'label' => 'E-mail Ibu',
        ], [
            'id' => 'mother_status',
            'label' => 'Status',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'mother_status_option_1',
                    'value' => 1,
                    'label' => 'Masih hidup',
                    'checked' => $personal_information && $personal_information->mother_status == 1,
                ], [
                    'id' => 'mother_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal',
                    'checked' => $personal_information && $personal_information->mother_status == 2,
                ]
            ]
        ], [
            'id' => 'mother_working_status',
            'label' => 'Bekerja',
            'type' => 'radio',
            'options' => [
                [
                    'id' => 'mother_working_status_option_1',
                    'value' => 1,
                    'label' => 'Bekerja',
                    'checked' => $personal_information && $personal_information->mother_working_status == 1,
                ], [
                    'id' => 'mother_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja',
                    'checked' => $personal_information && $personal_information->mother_working_status == 2,
                ]
            ]
        ], [
            'id' => 'mother_working_company',
            'label' => 'Nama Perusahaan',
            'input_container_class' => 'mother_working_status d-none',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'mother_working_position',
            'label' => 'Jabatan',
            'input_container_class' => 'mother_working_status d-none',
            'style' => 'text-transform:uppercase',
        ]];

        $this->data['form_score_utbk'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Nilai UTBK</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'utbk_penalaran_umum',
            'label' => 'Nilai TPS Kemampuan Penalaran UMUM',
        ], [
            'id' => 'utbk_kuantitatif',
            'label' => 'Nilai TPS Kemampuan Kuantitatif *',
        ], [
            'id' => 'utbk_pengetahuan',
            'label' => 'Nilai TPS Pengetahuan dan Pemahaman Umum *',
        ], [
            'id' => 'utbk_baca_tulis',
            'label' => 'Nilai Kemampuan Memahami Bacaan & Menulis *',

        ]];

        $this->data['form_last_education'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Pendidikan Terakhir</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'last_education_school_name',
            'label' => 'Nama Universitas',
        ], [
            'id' => 'last_employment_year',
            'label' => 'Tahun',
            'type' => 'combine',
            'elements' => [[
                'id' => 'last_employment_year_from',
                'style' => 'width: auto;display:inline-block',
                'class' => 'yearpicker',
            ], [
                'type' => 'html',
                'html' => '<span> - </span>',
            ], [
                'id' => 'last_employment_year_to',
                'style' => 'width: auto; margin-left: 5px;display:inline-block',
                'class' => 'yearpicker',
            ]]
        ], [
            'id' => 'last_education_major',
            'label' => 'Fakultas dan Jurusan',
        ], [
            'id' => 'last_education_title',
            'label' => 'Gelar',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'last_education_gpa',
            'label' => 'IPK',
            'class' => 'decimal',
            'form_control_class' => 'col-sm-2',
        ]];

        $this->data['form_last_occupation'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Pekerjaan Saat Ini</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'last_employment_company_name',
            'label' => 'Nama Perusahaan',
        ], [
            'id' => 'last_employment_company_address',
            'label' => 'Alamat Perusahaan',
        ], [
            'id' => 'last_employment_position',
            'label' => 'Jabatan',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'last_employment_year',
            'label' => 'Tahun',
            'type' => 'combine',
            'elements' => [[
                'id' => 'last_employment_year_from',
                'style' => 'width: auto;display:inline-block',
                'class' => 'yearpicker',
            ], [
                'type' => 'html',
                'html' => '<span> - </span>',
            ], [
                'id' => 'last_employment_year_to',
                'style' => 'width: auto; margin-left: 5px;display:inline-block',
                'class' => 'yearpicker',
            ]]
        ]];

        $this->data['form_company_contact_info'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Kontak Perusahaan</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'company_pic_name',
            'label' => 'Nama Perusahaan',
        ], [
            'id' => 'company_pic_email',
            'label' => 'E-mail',
        ], [
            'id' => 'company_pic_phone',
            'label' => 'No Handphone/Whatsapp',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'company_pic_address',
            'label' => 'Alamat Perusahaan',
        ]];

        $this->data['form_recommendation'] = [[
            'type' => 'html',
            'html' => '<div class="fancy-title title-border mb-0"><h3>Rekomendasi</h3></div>',
            'control_label_class' => '',
            'form_control_class' => 'col-sm-12',
        ], [
            'id' => 'recommender_name_1',
            'label' => 'Nama Pemberi Rekomendasi 1',
        ], [
            'id' => 'recommender_company_1',
            'label' => 'Jabatan dan Nama Perusahaan',
        ], [
            'id' => 'recommender_email_1',
            'label' => 'E-mail',
        ], [
            'id' => 'recommender_phone_1',
            'label' => 'No Handphone/Whatsapp',
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'recommender_name_2',
            'label' => 'Nama Pemberi Rekomendasi 2',
        ], [
            'id' => 'recommender_company_2',
            'label' => 'Jabatan dan Nama Perusahaan',
        ], [
            'id' => 'recommender_email_2',
            'label' => 'E-mail',
        ], [
            'id' => 'recommender_phone_2',
            'label' => 'No Handphone/Whatsapp',
            'form_control_class' => 'col-sm-3',
        ]];

        $this->data['form_education_fund_source'] = [[
            'id' => 'education_fund_source',
            'label' => 'Rencana Biaya Pendidikan',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih rencana biaya pendidikan', '1' => 'Pribadi', '2' => 'Perusahaan', '3' => 'Sebagian pribadi, sebagian perusahaan']
        ]];

        $this->data['form_other'] = [[
            'id' => 'marketing_hear_from',
            'label' => 'Dari mana anda mengenal PPM SoM?',
            'type' => 'checkbox',
            'options' => array(
                array(
                    'id' => 'cb_marketing_hear_from_1',
                    'value' => 1,
                    'label' => 'Teman'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_2',
                    'value' => 2,
                    'label' => 'Keluarga'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_3',
                    'value' => 3,
                    'label' => 'Guru Sekolah'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_4',
                    'value' => 4,
                    'label' => 'Roadshow'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_5',
                    'value' => 5,
                    'label' => 'Surat Kabar'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_6',
                    'value' => 6,
                    'label' => 'Media Sosial'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_7',
                    'value' => 7,
                    'label' => 'Jobfair'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_8',
                    'value' => 8,
                    'label' => 'Radio'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_9',
                    'value' => 9,
                    'label' => 'E-mail'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_10',
                    'value' => 10,
                    'label' => 'Website'
                ),
                array(
                    'id' => 'cb_marketing_hear_from_11',
                    'value' => 11,
                    'label' => 'Lainnya'
                ),
            )
        ], [
            'id' => 'marketing_reason',
            'label' => 'Mengapa anda memilih PPM SoM?',
            'type' => 'checkbox',
            'options' => array(
                array(
                    'id' => 'cb_marketing_reason_1',
                    'value' => 1,
                    'label' => 'Reputasi',
                ),
                array(
                    'id' => 'cb_marketing_reason_2',
                    'value' => 2,
                    'label' => 'Pengajar'
                ),
                array(
                    'id' => 'cb_marketing_reason_3',
                    'value' => 3,
                    'label' => 'Biaya'
                ),
                array(
                    'id' => 'cb_marketing_reason_4',
                    'value' => 4,
                    'label' => 'Roadshow'
                ),
                array(
                    'id' => 'cb_marketing_reason_5',
                    'value' => 5,
                    'label' => 'Fasilitas'
                ),
                array(
                    'id' => 'cb_marketing_reason_6',
                    'value' => 6,
                    'label' => 'Kurikulum'
                ),
                array(
                    'id' => 'cb_marketing_reason_7',
                    'value' => 7,
                    'label' => 'Jejaring ke Perusahaan'
                ),
                array(
                    'id' => 'cb_marketing_reason_8',
                    'value' => 8,
                    'label' => 'Kualitas Alumni'
                ),
                array(
                    'id' => 'cb_marketing_reason_9',
                    'value' => 9,
                    'label' => 'Lainnya'
                ),
            )
        ]];

        $this->data['initialPreviews'] = [];
        $this->data['initialPreviewConfigs'] = [];

        $this->data['documents'] = [];
        $filter = [
            'id_user' => $admission->id_user //$this->ion_auth->user()->row()->id
        ];
        $documents = $this->Users_documents_model->get_many_by($filter);
        foreach ($documents as $document) {
            if (file_exists(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $document->document_filename)) {
                $this->data['documents'][$document->document_type] = $document->document_filename;
                $this->data['initialPreviews'][$document->document_type] = base_url(USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $document->document_filename);
            }
        }

        // if ($admission->program == 1 && $admission->nama_seleksi == 'REGULER') {
        //     $this->data['content'] = 'admission/registration_s1_reguler';
        // } elseif ($admission->program == 1 && $admission->nama_seleksi == 'BEASISWA') {
        //     $this->data['content'] = 'admission/registration_s1_beasiswa';
        // } elseif ($admission->program == 1 && $admission->nama_seleksi == 'RAPORT') {
        //     $this->data['content'] = 'admission/registration_s1_rapor';
        // } elseif ($admission->program == 1 && $admission->nama_seleksi == 'UTBK') {
        //     $this->data['content'] = 'admission/registration_s1_utbk';
        // } elseif ($admission->program == 2 && $admission->nama_program_studi == 'MAGISTER MANAJEMEN WIJAWIYATA MANAJEMEN' && $admission->nama_seleksi == 'REGULER') {
        //     $this->data['content'] = 'admission/registration_s2_wm_reguler';
        // } elseif ($admission->program == 2 && $admission->nama_program_studi == 'MAGISTER MANAJEMEN WIJAWIYATA MANAJEMEN'  && $admission->nama_seleksi == 'BEASISWA') {
        //     $this->data['content'] = 'admission/registration_s2_wm_beasiswa';
        // } elseif ($admission->program == 2 && $admission->nama_program_studi == 'MAGISTER MANAJEMEN EKSEKUTIF') {
        //     $this->data['content'] = 'admission/registration_s2_e';
        // } elseif ($admission->program == 2  && $admission->nama_program_studi == 'MAGISTER MANAJEMEN EKSEKUTIF MUDA') {
        //     $this->data['content'] = 'admission/registration_s2_em';
        // }
        $this->load->view('template_home', $this->data);
    }

    function registration_complete()
    {

        $this->data['title'] = '';

        $this->data['user'] = $this->ion_auth->user()->row();

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $this->load->view('template_home', $this->data);
    }

    public function education_history_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_educations_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function education_history_save()
    {
        $response = new stdClass();
        $response->status = FALSE;


        $id_user_education = $this->input->post('id_user_education');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'education_school_name' => strtoupper($this->input->post('education_school_name')),
            'education_city' => strtoupper($this->input->post('education_city')),
            'education_major' => strtoupper($this->input->post('education_major')),
            'education_year_from' => min($this->input->post('education_year_from'), $this->input->post('education_year_to')),
            'education_year_to' => max($this->input->post('education_year_from'), $this->input->post('education_year_to')),
        ];

        if ($id_user_education) {
            if ($this->Users_educations_model->update($id_user_education, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_educations_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }

    public function education_history_delete($id_user_education)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_educations_model->delete($id_user_education)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function upload_document()
    {
        $response = new stdClass();
        $response->status = FALSE;
        $response->message = '';

        $id = $this->input->post('id');
        $id_admission = $this->input->post('id_admission');
        $multiple = $this->input->post('multiple');

        $id_user = $this->ion_auth->user()->row()->id;

        if ($multiple) {
            $filter = [
                'id_user' => $this->ion_auth->user()->row()->id,
                'document_type' => $id,
            ];
            $last_doc = $this->Users_documents_model->order_by('id_user_document', 'DESC')->get_by($filter);
            $index = $last_doc ? $last_doc->document_index + 1 : 1;
        }

        $response->id = $id;

        $target_dir = FCPATH . USER_DOCUMENTS_FOLDER . $id_user . '/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777);
        }

        $preview = $previewConfig = $errors = $images = $images_path = [];

        if (!empty($_FILES[$id]['name'][0])) {
            $config['upload_path'] = $target_dir;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size'] = '2000000';
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);

            // $filesCount = count($_FILES[$id]['name']);
            // for ($i = 0; $i < $filesCount; $i++) {

            $ext = pathinfo($_FILES[$id]['name'], PATHINFO_EXTENSION);

            $_FILES['fot']['name'] = $multiple ? $id . $index . "." . $ext : $id . "." . $ext;
            // $_FILES['fot']['name'] = $_FILES[$id]['name'];
            $_FILES['fot']['type'] = $_FILES[$id]['type'];
            $_FILES['fot']['tmp_name'] = $_FILES[$id]['tmp_name'];
            $_FILES['fot']['error'] = $_FILES[$id]['error'];
            $_FILES['fot']['size'] = $_FILES[$id]['size'];

            $fileSize = $_FILES[$id]['size'];

            if (!$this->upload->do_upload('fot')) {

                $error_upload = true;
                $errors[] =  $this->upload->display_errors();
                $response->message = $this->upload->display_errors();
            } else {
                $image = $this->upload->data();
                $fileName = $image['file_name'];
                $images_path[] = $image['full_path'];

                $fileId = $image['file_name'];
                $preview[] = base_url(USER_DOCUMENTS_FOLDER . $id_user . '/' . $fileId);
                $previewConfig[] = [
                    'key' => $fileId,
                    'caption' => $fileName,
                    'size' => $fileSize,
                    'downloadUrl' => base_url(USER_DOCUMENTS_FOLDER . $id_user . '/' . $fileId), // the url to download the file
                    'url' => base_url('admission/delete_document'), // server api to delete the file based on key
                ];

                $data = [
                    'id_user' => $this->ion_auth->user()->row()->id,
                    'document_type' => $id,
                    'document_filename' => $fileName,
                ];

                if ($multiple) {
                    $data['document_index'] = $index;
                    $this->Users_documents_model->insert($data);
                } else {
                    $filter = [
                        'id_user' => $this->ion_auth->user()->row()->id,
                        'document_type' => $id,
                    ];
                    if ($user_document = $this->Users_documents_model->get_by($filter)) {
                        $this->Users_documents_model->update($user_document->id_user_document, $data);
                    } else {
                        $this->Users_documents_model->insert($data);
                    }
                }

                $response->status = TRUE;
            }
            // }
        }

        // $this->create_thumbs($images_path);

        $this->session->set_userdata('initialPreview', $preview);
        $this->session->set_userdata('initialPreviewConfig', $previewConfig);

        $response->initialPreview = $preview;
        $response->initialPreviewConfig = $previewConfig;
        $response->initialPreviewAsData = true;
        echo json_encode($response);
    }

    public function registration_form($id_admission, $pdf = 1)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
        ];
        $this->data['admission'] = $this->Admissions_model
            ->select('admissions.*, personal_informations.*, nama_program_studi, nama_seleksi, tanggal, biaya, channel_name, payment_status, payment_receipt')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'personal_informations.id_user = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.jadwal', 'left')
            ->join('jadwal', 'jadwal.id_jadwal = jadwal_seleksi.id_jadwal', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        if ($pdf) {
            $this->data['pdf'] = true;
            $this->load->library(array('pdf'));

            $html = $this->load->view('admission/ticket', $this->data, true);
            $this->pdf->generate($html, "reg-" . $id_admission . ".pdf");
        } else {
            $this->data['pdf'] = false;
            $this->load->view('admission/ticket', $this->data);
        }
    }

    function status($id_admission)
    {
        $this->data['admission'] = $this->Admissions_model->get($id_admission);
        $this->data['title'] = '';

        $this->data['user'] = $this->ion_auth->user()->row();

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $this->load->view('template_home', $this->data);
    }

    public function employment_history_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_employments_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function employment_history_save()
    {
        $response = new stdClass();
        $response->status = FALSE;
        $response->post = $this->input->post();
        $id_user_employment = $this->input->post('id_user_employment');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'employment_company_name' => strtoupper($this->input->post('employment_company_name')),
            'employment_company_address' => strtoupper($this->input->post('employment_company_address')),
            'employment_position' => strtoupper($this->input->post('employment_position')),
            'employment_year_from' => min($this->input->post('employment_year_from'), $this->input->post('employment_year_to')),
            'employment_year_to' => max($this->input->post('employment_year_from'), $this->input->post('employment_year_to')),
        ];

        if ($id_user_employment) {
            if ($this->Users_employments_model->update($id_user_employment, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_employments_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }

    public function employment_history_delete($id_user_employment)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_employments_model->delete($id_user_employment)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function family_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_families_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function family_save()
    {
        $response = new stdClass();
        $response->status = FALSE;

        $id_user_family = $this->input->post('id_user_family');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'family_full_name' => strtoupper($this->input->post('family_full_name')),
            'family_birth_date' => date_format(date_create_from_format('d/m/Y', $this->input->post('family_birth_date')), 'Y-m-d'),
            'family_phone' => strtoupper($this->input->post('family_phone')),
            'family_email' => strtoupper($this->input->post('family_email')),
            'family_marital_status' => $this->input->post('family_marital_status'),
            'family_working_status' => $this->input->post('family_working_status'),
            'family_working_position' => strtoupper($this->input->post('family_working_position')),
            'family_working_company' => strtoupper($this->input->post('family_working_company')),
        ];

        if ($id_user_family) {
            if ($this->Users_families_model->update($id_user_family, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_families_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }

    public function family_delete($id_user_family)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_families_model->delete($id_user_family)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }
    public function achievement_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_achievements_model->get_many_by($filter);

        echo json_encode($response);
    }
    public function achievement_save()
    {
        $response = new stdClass();
        $response->status = FALSE;

        $id_user_achievement = $this->input->post('id_user_achievement');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'achievement_name' => strtoupper($this->input->post('achievement_name')),
            'achievement_organizer' => strtoupper($this->input->post('achievement_organizer')),
            'achievement_year' => strtoupper($this->input->post('achievement_year')),
        ];

        if ($id_user_achievement) {
            if ($this->Users_achievements_model->update($id_user_achievement, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_achievements_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }
    public function achievement_delete($id_user_achievement)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_achievements_model->delete($id_user_achievement)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function organization_history_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_organizations_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function organization_history_save()
    {
        $response = new stdClass();
        $response->status = FALSE;

        $id_user_organization = $this->input->post('id_user_organization');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'organization_name' => strtoupper($this->input->post('organization_name')),
            'organization_position' => strtoupper($this->input->post('organization_position')),
            'organization_year' => strtoupper($this->input->post('organization_year')),
        ];

        if ($id_user_organization) {
            if ($this->Users_organizations_model->update($id_user_organization, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_organizations_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }
    public function organization_history_delete($id_user_organization)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_organizations_model->delete($id_user_organization)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function education_informal_history_data()
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
        ];

        $response->rows = $this->Users_educations_informal_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function education_informal_history_save()
    {
        $response = new stdClass();
        $response->status = FALSE;


        $id_user_education = $this->input->post('id_user_education');
        $data = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'education_school_name' => strtoupper($this->input->post('education_school_name')),
            'education_organizer' => strtoupper($this->input->post('education_organizer')),
            'education_title' => strtoupper($this->input->post('education_title')),
            'education_year_from' => min($this->input->post('education_year_from'), $this->input->post('education_year_to')),
            'education_year_to' => max($this->input->post('education_year_from'), $this->input->post('education_year_to')),
        ];

        if ($id_user_education) {
            if ($this->Users_educations_informal_model->update($id_user_education, $data)) {
                $response->status = TRUE;
            }
        } else {
            if ($this->Users_educations_informal_model->insert($data)) {
                $response->status = TRUE;
            }
        }

        echo json_encode($response);
    }

    public function education_informal_history_delete($id_user_education_informal)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Users_educations_informal_model->delete($id_user_education_informal)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function send_registration_email($id_admission)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, biaya, channel_name, full_name, email, payment_status, payment_receipt')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        $this->data['admission'] = $admission;

        $angkatan = $this->Angkatan_model->get_by('id_program_studi', $admission->program_studi);
        $this->data['angkatan'] = $angkatan;

        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/register.tpl.php', $this->data, true);

        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($admission->email);
        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Pendaftaran');
        $this->email->message($message);


        if ($this->email->send() == TRUE) {
            return true;
        } else {
            echo $this->email->print_debugger(array('headers'));
        }
        return false;
    }
}
