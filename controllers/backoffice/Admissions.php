<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admissions extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Admissions_model',
            'Acceptances_model',
            'Payments_model',
            'Biaya_model',
            'Skema_pembayaran_model',
            'Skema_pembayaran_detail_model',
            'Jadwal_seleksi_model',
            'Users_discounts_model',
            'Provinsi_model',
            'Kota_model',
            'Personal_informations_model',
            'Seleksi_form_model',
            'Users_educations_model',
            'Users_educations_informal_model',
            'Users_documents_model',
            'Users_utbk_model',
            'Users_employments_model',
            'Users_families_model',
            'Users_achievements_model',
            'Users_organizations_model',
            'Users_seleksi_model',
            'Angkatan_model',
            'Seleksi_model',
            'Seleksi_payments_channels_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->admissions();
    }

    public function admissions()
    {
        $this->data['title'] = 'Admissions';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->load->view('template_backoffice', $this->data);
    }

    public function data()
    {
        $response  = new stdClass();

        $response->total = $this->Admissions_model->count_all();
        $response->rows = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, full_name, payment')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->order_by('admissions.created_on')
            ->get_all();

        echo json_encode($response);
    }

    public function _convert_personal_values($personal_information)
    {
        $data = new stdClass();

        $date_fields = ['birthdate', 'father_birthdate', 'mother_birthdate'];

        foreach ($personal_information as $key => $value) {
            if (is_array($value)) {
                if ($key == 'marketing_hear_from') {
                    $options = [
                        '0' => '',
                        '1' => 'TEMAN',
                        '2' => 'KELAURGA',
                        '3' => 'GURU SEKOLAH',
                        '4' => 'ROADSHOW',
                        '5' => 'SURAT KABAR',
                        '6' => 'MEDIA SOSIAL',
                        '7' => 'JOBFAIR',
                        '8' => 'RADIO',
                        '9' => 'E-MAIL',
                        '10' => 'WEBSITE',
                        '11' => 'LAINNYA',
                    ];
                    $values = [];
                    foreach ($value as $v) {
                        $values[] = $options[$v];
                    }
                    $data->{$key} = implode(',', $values);
                } elseif ($key == 'marketing_reason') {
                    $options = [
                        '0' => '',
                        '1' => 'REPUTASI',
                        '2' => 'PENGAJAR',
                        '3' => 'BIAYA',
                        '4' => 'ROADSHOW',
                        '5' => 'FASILITAS',
                        '6' => 'KURIKULUM',
                        '7' => 'JEJARING KE PERUSAHAAN',
                        '8' => 'KUALITAS ALUMNI',
                        '9' => 'LAINNYA',
                    ];
                    $values = [];
                    foreach ($value as $v) {
                        $values[] = $options[$v];
                    }
                    $data->{$key} = implode(',', $values);
                }
                // echo $key;
                // print_r($value);
            } else {
                if ($key == 'gender') {
                    $options =  [
                        '0' => '',
                        '1' => 'LAKI-LAKI',
                        '2' => 'PEREMPUAN',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'marital_status') {
                    $options =  [
                        '0' => 'BELUM MENIKAH',
                        '1' => 'SUDAH MENIKAH',
                        '2' => 'BERPISAH/CERAI',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'social_media') {
                    $options =  [
                        '0' => '',
                        '1' => 'INSTAGRAM',
                        '2' => 'TIK TOK',
                        '3' => 'FACEBOOK',
                        '4' => 'TWITTER',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'father_status') {
                    $options =  [
                        '0' => '',
                        '1' => 'MASIH HIDUP',
                        '2' => 'MENINGGGAL',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'father_working_status') {
                    $options =  [
                        '0' => '',
                        '1' => 'BEKERJA',
                        '2' => 'TIDAK BEKERJA',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'mother_status') {
                    $options =  [
                        '0' => '',
                        '1' => 'MASIH HIDUP',
                        '2' => 'MENINGGGAL',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'mother_working_status') {
                    $options =  [
                        '0' => '',
                        '1' => 'BEKERJA',
                        '2' => 'TIDAK BEKERJA',
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'education_fund_source') {
                    $options = [
                        '0' => '-',
                        '1' => 'PRIBADI',
                        '2' => 'PERUSAHAAN',
                        '3' => 'SEBAGIAN PRIBADI, SEBAGIAN PERUSAHAAN'
                    ];
                    $data->{$key} = $options[$value];
                } elseif ($key == 'province') {
                    $province = $this->Provinsi_model->get_by('kode_provinsi', $value);
                    $data->{$key} = $province ? $province->nama_provinsi : '-';
                } elseif ($key == 'city') {
                    $city = $this->Kota_model->get_by('kode_kota', $value);
                    $data->{$key} = $city ? $city->nama_kota : '-';
                } else {
                    if (in_array($key, $date_fields)) {
                        $dateTime = DateTime::createFromFormat('Y-m-d', $value);
                        if ($dateTime instanceof DateTime && $dateTime->format('Y-m-d') == $value) {
                            $data->{$key} = strtoupper(strftime('%e %B %Y', strtotime($value)));
                        } else {
                            $data->{$key} = '-';
                        }
                    } elseif ($value == '0' || $value == '') {
                        $data->{$key} = '-';
                    } else {
                        $data->{$key} = $value;
                    }
                }
            }
        }
        return $data;
    }

    public function view($id_admission)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, payment,tes_seleksi, biaya, channel_type, channel_name, full_name, email, payment_status, payment_receipt, payment_voucher_code, id_payment')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission and payments.payment_type = 1', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        $admission->skema_pembayaran = $this->Skema_pembayaran_model->get($admission->jenis_pembayaran);

        $admission->form_1 = $this->Seleksi_form_model->get_by(['id_seleksi' => $admission->seleksi, 'type' => 1]);
        $admission->form_2 = $this->Seleksi_form_model->get_by(['id_seleksi' => $admission->seleksi, 'type' => 2]);

        $this->data['admission'] = $admission;


        $user = $this->ion_auth->user($admission->id_user)->row();

        $personal_information = $this->Personal_informations_model
            ->get_by('personal_informations.id_user', $user->id);

        if ($personal_information) {
            $personal_information->mobile_phone = $user->phone;
            $personal_information->whatsapp_number = $user->phone;
            $personal_information->email = $user->email;
            $personal_information->education_fund_source = $admission->education_fund_source;

            $personal_information->marketing_hear_from = explode(',', $personal_information->marketing_hear_from);
            $personal_information->marketing_reason = explode(',', $personal_information->marketing_reason);

            $this->data['personal_information'] = $this->_convert_personal_values($personal_information);
        }



        $this->data['documents'] = [];
        $filter = [
            'id_user' => $admission->id_user,
        ];
        $documents = $this->Users_documents_model->get_many_by($filter);

        foreach ($documents as $document) {
            if (file_exists(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $document->document_filename)) {
                $this->data['documents'][$document->document_type] = $document->document_filename;
            }
        }

        $this->form_validation->set_rules('id_admission', 'Admission', 'required');
        if ($this->form_validation->run() == TRUE) {
            if ($admission->status == 300) {
                $status = $this->input->post('status');
                if ($admission->tes_seleksi) {
                    $waktu_seleksi = date_format(date_create_from_format('d/m/Y H:i', $this->input->post('waktu_seleksi')), 'Y-m-d H:i');
                    $aplikasi = $this->input->post('aplikasi');
                    $zoom_meeting_id = $this->input->post('zoom_meeting_id');
                    $zoom_passcode = $this->input->post('zoom_passcode');
                    $zoom_link = $this->input->post('zoom_link');
                }

                if ($status) {
                    $this->Admissions_model->update($id_admission, ['status' => $status]);

                    if ($admission->tes_seleksi && $status == '301') {
                        $user_seleksi_data = [
                            'id_admission' => $admission->id_admission,
                            'waktu_seleksi' => $waktu_seleksi,
                            'aplikasi' => $aplikasi,
                            'zoom_meeting_id' => $zoom_meeting_id,
                            'zoom_passcode' => $zoom_passcode,
                            'zoom_link' => $zoom_link,
                        ];
                        $this->Users_seleksi_model->insert($user_seleksi_data);
                    }
                    $this->send_ticket_email($id_admission);
                    redirect('backoffice/admissions', 'refresh');
                } else {
                    $this->data['message'] = 'Update pendaftaran Gagal';
                }
            } else if ($admission->status == 102) {
                $payment_status = $this->input->post('payment_status');
                $payment_time = strtotime($this->input->post('payment_time'));

                if ($admission->payment) {
                    // if ($admission->payment_status == 1) {
                    if ($this->Payments_model->update($admission->id_payment, ['payment_status' => $payment_status, 'payment_time' => $payment_time])) {
                        $update_data = [];
                        if ($payment_status == 1) {
                            $update_data = ['status' => 200];
                        } elseif ($payment_status == 2) {
                            $update_data = ['status' => 103];
                        }
                        if ($this->Admissions_model->update($id_admission, $update_data)) {
                            $this->create_payment_receipt($id_admission, 1);
                            $this->send_payment_email($id_admission);

                            redirect('backoffice/admissions', 'refresh');
                        }
                    } else {
                        $this->data['message'] = 'Update pendaftaran Gagal';
                    }
                } else {
                    if ($this->Admissions_model->update($id_admission, ['status' => 200])) {
                        $this->send_registration_validation_email($id_admission);
                        redirect('backoffice/admissions', 'refresh');
                    } else {
                        $this->data['message'] = 'Update pendaftaran Gagal';
                    }
                }
            }
            // }
        }

        // print_r($admission);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $status_pendaftaran = '';
        switch ($admission->status) {
            case '100':
                $status_pendaftaran = 'Belum upload bukti pembayaran';
                break;
            case '101':
                $status_pendaftaran = 'Sudah upload bukti pembayaran';
                break;
            case '102':
                $status_pendaftaran = 'Pembayaran menunggu validasi';
                break;
        }

        // $bukti_pembayaran = '';
        // if ($admission->payment_receipt) {
        //     $bukti_pembayaran = '<img src="' . base_url(RECEIPTS_FOLDER . $admission->payment_receipt) . '">';
        // }


        $this->data['form'] = [[
            'id' => 'id_admission',
            'type' => 'hidden',
            'value' => $admission->id_admission,
        ], [
            'id' => 'id_user',
            'type' => 'hidden',
            'value' => $admission->id_user,
        ], [
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
            'type' => 'control_static',
            'value' => $admission->full_name,
        ], [
            'id' => 'email',
            'label' => 'E-mail',
            'type' => 'control_static',
            'value' => $admission->email,
        ], [
            'id' => 'nama_program_studi',
            'label' => 'Program Studi',
            'type' => 'control_static',
            'value' => $admission->nama_program_studi,
        ], [
            'id' => 'nama_seleksi',
            'label' => 'Jenis Seleksi',
            'type' => 'control_static',
            'value' => $admission->nama_seleksi,
        ]];

        $this->data['form_personal_1'] = [[
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
                    'label' => 'Laki-laki'
                ], [
                    'id' => 'gender_option_2',
                    'value' => 2,
                    'label' => 'Perempuan'
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
                '' => '-Pilih-',
                '0' => 'Belum Menikah',
                '1' => 'Sudah Menikah',
                '2' => 'Berpisah/Cerai',
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
                '' => '-Pilih-',
                '1' => 'Instagram',
                '2' => 'Tik Tok',
                '3' => 'Facebook',
                '4' => 'Twitter',
            ],
            'form_control_class' => 'col-sm-3',
        ], [
            'id' => 'social_media_account',
            'label' => 'Nama Akun Sosial Media',
        ]];

        $this->data['form_personal_2'] = [[
            'id' => 'father_name',
            'label' => 'Nama Ayah',
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
                    'label' => 'Masih hidup'
                ], [
                    'id' => 'father_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal'
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
                    'label' => 'Bekerja'
                ], [
                    'id' => 'father_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja'
                ]
            ]
        ], [
            'id' => 'father_working_company',
            'label' => 'Nama Perusahaan',
            // 'input_container_class' => 'father_working_status d-none',
        ], [
            'id' => 'father_working_position',
            'label' => 'Jabatan',
            // 'input_container_class' => 'father_working_status d-none',
        ], [
            'id' => 'mother_name',
            'label' => 'Nama Ibu',
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
                    'id' => 'father_status_option_1',
                    'value' => 1,
                    'label' => 'Masih hidup'
                ], [
                    'id' => 'father_status_option_2',
                    'value' => 2,
                    'label' => 'Meninggal'
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
                    'label' => 'Bekerja'
                ], [
                    'id' => 'mother_working_status_option_2',
                    'value' => 2,
                    'label' => 'Tidak Bekerja'
                ]
            ]
        ], [
            'id' => 'mother_working_company',
            'label' => 'Nama Perusahaan',
            // 'input_container_class' => 'mother_working_status d-none',
        ], [
            'id' => 'mother_working_position',
            'label' => 'Jabatan',
            // 'input_container_class' => 'mother_working_status d-none',
        ]];

        $this->data['form_score_utbk'] = [[
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


        $this->load->view('template_backoffice', $this->data);
    }



    public function send_ticket_email($id_admission)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, personal_informations.*, nama_program_studi, nama_seleksi, tes_seleksi, biaya, channel_name, payment_status, payment_receipt')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'users.id = personal_informations.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        $angkatan = $this->Angkatan_model->get_by('id_program_studi', $admission->program_studi);
        $this->data['angkatan'] = $angkatan;

        $user_seleksi = $this->Users_seleksi_model->get_by('id_admission', $id_admission);
        $this->data['user_seleksi'] = $user_seleksi;

        $this->data['admission'] = $admission;

        // $this->load->view('home/email/ticket.tpl.php', $this->data);

        if ($admission->status == 301 || $admission->status == 302) {
            if ($admission->status == 301) {
                $this->load->library(array('pdf'));

                $this->data['pdf'] = true;
                $html = $this->pdf->load_view('admission/ticket', $this->data, true);
                $this->pdf->render();

                $output = $this->pdf->output();
                if (!file_exists(FCPATH . DOCUMENTS_FOLDER . $id_admission)) {
                    mkdir(FCPATH . DOCUMENTS_FOLDER . $id_admission);
                }
                $output_file = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/' . $admission->full_name . ".pdf";
                file_put_contents($output_file, $output);
            }


            $email_config = $this->config->item('email_config', 'ion_auth');

            if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
                $this->email->initialize($email_config);
            }

            $message = $this->load->view('home/email/ticket.tpl.php', $this->data, true);
            $this->email->clear();
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($admission->email);
            if ($admission->status == 301) {
                if ($admission->tes_seleksi) {
                    $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Undangan Tes Seleksi');
                } else {
                    $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Jalur ' . $admission->nama_seleksi);
                }
            } else {
                $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Verifikasi Pendaftaran');
            }
            $this->email->message($message);

            if ($admission->status == 301) {
                $this->email->attach($output_file);
                $this->email->attach(FCPATH . DOCUMENTS_FOLDER  . '/S1 - Panduan Registrasi Seleksi online program Sarjana PPM School of Management.pdf');
            }

            $user_email = $this->email->send();

            if ($admission->status == 301) {
                $parent_email = $admission->father_email ? $admission->father_email : ($admission->mother_email ? $admission->mother_email : '');

                if ($parent_email) {
                    $message = $this->load->view('home/email/ticket_parent.tpl.php', $this->data, true);
                    $this->email->clear();
                    $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
                    $this->email->to($parent_email);

                    if ($admission->tes_seleksi) {
                        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Undangan Tes Seleksi');
                    } else {
                        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Jalur ' . $admission->nama_seleksi);
                    }
                    $this->email->message($message);
                    $this->email->attach($output_file);

                    $parent_email = $this->email->send();
                }
            }


            if ($user_email == TRUE) {
                return true;
            }
        }

        return false;
    }

    public function delete($id_admission)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->Admissions_model->delete($id_admission)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }

    public function send_payment_email($id_admission)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'admissions.id_admission' => $id_admission,
            'payment_type' => 1,
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

        // $this->load->view('home/email/ticket.tpl.php', $this->data);


        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/payment.tpl.php', $this->data, true);
        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($admission->email);
        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Verifikasi Pembayaran');
        $this->email->message($message);

        if ($admission->status == 200) {
            $attachment_file = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/KWITANSI PENDAFTARAN ' . $admission->full_name . '.pdf';
            if (file_exists($attachment_file)) {
                $this->email->attach($attachment_file);
            }
        }

        if ($this->email->send() == TRUE) {
            return true;
        }
        return false;
    }

    public function send_registration_validation_email($id_admission)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
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

        // $this->load->view('home/email/ticket.tpl.php', $this->data);


        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/register_raport.tpl.php', $this->data, true);
        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($admission->email);
        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Verifikasi Pendaftaran');
        $this->email->message($message);

        if ($this->email->send() == TRUE) {
            return true;
        }
        return false;
    }

    public function acceptances()
    {
        $this->data['title'] = 'Penerimaan';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->load->view('template_backoffice', $this->data);
    }

    public function acceptances_view($id_admission)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];
        $admission = $this->Admissions_model
            ->select('admissions.*, jenis_program_studi, nama_program_studi, nama_seleksi, beasiswa, biaya, channel_name, full_name, email, payment_status, payment_receipt, id_payment')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        $acceptance = $this->Acceptances_model->get_by('id_admission', $id_admission);
        if ($acceptance) {
            $admission->acceptance_date = $acceptance->acceptance_date;
            $admission->scholarship = $acceptance->scholarship;

            $filter = [
                'id_admission' => $id_admission,
                'jenis_program_studi' => $admission->jenis_program_studi,
                'grade != ' => 0,
                'discount' => 1,
                'id_user' => $admission->id_user,
            ];
            if ($admission->jenis_program_studi == MAGISTER) {
                $filter['id_program_studi'] = $admission->program_studi;
            }
            $grade_discount = $this->Users_discounts_model
                ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                ->get_by($filter);
            $admission->grade_discount = $grade_discount ? $grade_discount->id_biaya : '';

            $filter = [
                'id_admission' => $id_admission,
                'jenis_program_studi' => $admission->jenis_program_studi,
                'grade' => 0,
                'discount' => 1,
                'id_user' => $admission->id_user,
            ];
            if ($admission->jenis_program_studi == MAGISTER) {
                $filter['id_program_studi'] = $admission->program_studi;
            }
            $adm_discounts = $this->Users_discounts_model
                ->select('users_discounts.id_biaya')
                ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                ->as_array()
                ->get_many_by($filter);
            $admission->discounts = $adm_discounts ? array_column($adm_discounts, 'id_biaya') : [];
        }

        $this->data['admission'] = $admission;

        $old_ids_biaya = $this->Users_discounts_model
            ->select('id_biaya')
            ->as_array()
            ->get_many_by(['id_user' => $admission->id_user, 'id_admission' => $admission->id_admission]);
        $old_ids_biaya = $old_ids_biaya ? array_column($old_ids_biaya, 'id_biaya') : [];

        $this->data['documents'] = [];
        $filter = [
            'id_user' => $admission->id_user,
        ];
        $documents = $this->Users_documents_model->get_many_by($filter);

        foreach ($documents as $document) {
            $this->data['documents'][$document->document_type] = $document->document_filename;
        }

        $this->form_validation->set_rules('id_admission', 'Admission', 'required');
        $this->form_validation->set_rules('status', 'Hasil seleksi', 'required');

        // if ($this->input->post('status') == 400) {
        //     $this->form_validation->set_rules('discount', 'Grade', 'required');
        // }
        if ($this->form_validation->run() == TRUE) {

            $id_biaya = $this->input->post('id_biaya') ? $this->input->post('id_biaya') : [];
            if ($this->input->post('discount')) {
                $id_biaya[] = $this->input->post('discount');
            }

            $this->db->trans_begin();
            if ($this->Admissions_model->update($id_admission, ['status' => $this->input->post('status')])) {
                if (!$admission->beasiswa) {
                    if ($id_biaya) {
                        $inserted_ids = array_diff($id_biaya, $old_ids_biaya);
                        $deleted_ids = array_diff($old_ids_biaya, $id_biaya);

                        foreach ($deleted_ids as $idb) {
                            $filter = [
                                'id_admission' => $id_admission,
                                'id_user' => $admission->id_user,
                                'id_biaya' => $idb,
                            ];
                            $this->Users_discounts_model->delete_by($filter);
                        }
                        foreach ($inserted_ids as $idb) {
                            $data = [
                                'id_admission' => $id_admission,
                                'id_user' => $admission->id_user,
                                'id_biaya' => $idb,
                            ];
                            $this->Users_discounts_model->insert($data);
                        }
                    }
                }


                $data = [
                    'id_admission' => $id_admission,
                    'acceptance_date' => date_format(date_create_from_format('d/m/Y', $this->input->post('acceptance_date')), 'Y-m-d'),
                ];
                if ($admission->beasiswa) {
                    $data['scholarship'] = $this->input->post('jenis_beasiswa') == 1 ? 100 : $this->input->post('scholarship');
                }
                if ($acceptance = $this->Acceptances_model->get_by('id_admission', $id_admission)) {
                    $this->Acceptances_model->update($acceptance->id_acceptance, $data);
                } else {
                    $this->Acceptances_model->insert($data);
                }
            }
            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                redirect('backoffice/admissions/acceptances', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        // print_r($admission);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $status_pendaftaran = '';
        switch ($admission->status) {
            case '100':
                $status_pendaftaran = 'Belum upload bukti pembayaran';
                break;
            case '101':
                $status_pendaftaran = 'Sudah upload bukti pembayaran';
                break;
            case '102':
                $status_pendaftaran = 'Pembayaran menunggu validasi';
                break;
        }

        $grade_discounts = ['' => '-Pilih grade-'];
        $filter = [
            'jenis_program_studi' => $admission->jenis_program_studi,
            'grade != ' => 0,
            'discount' => 1,
        ];

        if ($admission->jenis_program_studi == MAGISTER) {
            $filter['id_program_studi'] = $admission->program_studi;
        }
        $_grade_discounts = $this->Biaya_model->get_many_by($filter);
        foreach ($_grade_discounts as $discount) {
            $grade_discounts[$discount->id_biaya] = $discount->nama_biaya;
        }
        $this->data['grade_discounts'] = $grade_discounts;

        $filter = [
            'jenis_program_studi' => $admission->jenis_program_studi,
            'grade' => 0,
            'discount' => 1,
        ];
        if ($admission->jenis_program_studi == MAGISTER) {
            $filter['id_program_studi'] = $admission->program_studi;
        }
        $this->data['discounts'] = $this->Biaya_model->get_many_by($filter);

        $this->data['form'] = [[
            'id' => 'id_admission',
            'type' => 'hidden',
            'value' => $admission->id_admission,
        ], [
            'id' => 'id_user',
            'type' => 'hidden',
            'value' => $admission->id_user,
        ], [
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
            'type' => 'control_static',
            'value' => $admission->full_name,
        ], [
            'id' => 'email',
            'label' => 'E-mail',
            'type' => 'control_static',
            'value' => $admission->email,
        ], [
            'id' => 'nama_program_studi',
            'label' => 'Program Studi',
            'type' => 'control_static',
            'value' => $admission->nama_program_studi,
        ], [
            'id' => 'nama_seleksi',
            'label' => 'Jenis Seleksi',
            'type' => 'control_static',
            'value' => $admission->nama_seleksi,
        ]];

        if ($admission->tgl_seleksi) {
            $this->data['form'][] = [
                'id' => 'tanggal',
                'label' => 'Jadwal Tes',
                'type' => 'control_static',
                'value' => date('j F Y', strtotime($admission->tgl_seleksi)),
            ];
        }


        $this->load->view('template_backoffice', $this->data);
    }

    public function acceptances_data()
    {
        $response  = new stdClass();

        $filter =  'admissions.status = 301 OR admissions.status >= 400';
        $response->total = $this->Admissions_model->count_by($filter);

        $filter =  '(admissions.status = 301 OR admissions.status >= 400)';
        $response->rows = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, full_name, payment')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->order_by('admissions.created_on')
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function acceptances_letter($id_admission, $write = 0)
    {
        // echo $id_admission;
        $filter = [
            'admissions.id_admission' => $id_admission,
            'angkatan.active' => 1,
        ];
        $admission = $this->Admissions_model
            ->select('admissions.*, users.*, personal_informations.*, nama_program_studi, jenis_program_studi, nama_seleksi, nama_kota, employment_company_name, employment_position, tgl_seleksi_option, angkatan.*')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'users.id = personal_informations.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
            ->join('users_employments', 'users_employments.id_user = users.id', 'left')
            ->join('angkatan', 'angkatan.id_angkatan = admissions.id_angkatan', 'left')
            ->get_by($filter);
        // var_dump($admission);

        if ($admission) {
            $admission->salutation = $admission->gender == 0 ? 'Saudara' : 'Saudari';
            $admission->salutation_short = $admission->gender == 0 ? 'Sdr.' : 'Sdri.';

            $acceptance = $this->Acceptances_model->get_by('id_admission', $id_admission);
            if ($acceptance) {
                $admission->acceptance_date = $acceptance->acceptance_date;

                $filter = [
                    'jenis_program_studi' => $admission->jenis_program_studi,
                    'grade != ' => 0,
                    'discount' => 1,
                    'id_user' => $admission->id_user,
                    'id_admission' => $admission->id_admission,
                ];
                if ($admission->jenis_program_studi == MAGISTER) {
                    $filter['id_program_studi'] = $admission->id_program_studi;
                }
                $admission->grade_discount = $this->Users_discounts_model
                    ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                    ->get_by($filter);

                $filter = [
                    'jenis_program_studi' => $admission->jenis_program_studi,
                    'discount' => 1,
                    'id_user' => $admission->id_user,
                    'id_admission' => $admission->id_admission,
                ];
                if ($admission->jenis_program_studi == MAGISTER) {
                    $filter['id_program_studi'] = $admission->id_program_studi;
                }
                $admission->discounts = $this->Users_discounts_model
                    ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                    ->get_many_by($filter);

                $admission->scholarship = $acceptance->scholarship;
            }


            $pdf = FALSE;
            if ($admission->status == 400) {

                if ($admission->program == SARJANA) {
                    $filter = [
                        'discount' => 0,
                        'jenis_program_studi' => $admission->program,
                    ];
                    $biaya = $this->Biaya_model->get_many_by($filter);
                    $admission->biaya = $biaya;

                    // $filter = [
                    //     'id_seleksi' => $admission->seleksi,
                    // ];

                    // if ($admission->tgl_seleksi_option) {
                    //     $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                    // }

                    // $admission->skema = $this->Skema_pembayaran_model->get_many_by($filter);

                    // if ($admission->skema) {
                    //     foreach ($admission->skema as $skema) {
                    //         // $jumlah_angsuran = $this->Skema_pembayaran_detail_model->count_by('id_skema_pembayaran', $skema->id_skema_pembayaran);
                    //         // $skema->jumlah_angsuran = $jumlah_angsuran ? $jumlah_angsuran - 1 : 0;

                    //         $filter['skema_pembayaran.id_skema_pembayaran'] = $skema->id_skema_pembayaran;
                    //         $skema->detail = $this->Skema_pembayaran_detail_model
                    //             ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    //             ->order_by('pembayaran')
                    //             ->get_many_by($filter);

                    //         if ($skema->detail) {
                    //             foreach ($admission->discounts as $disc) {
                    //                 $skema->jumlah_total -= $disc->jumlah;
                    //             }
                    //             // } else {
                    //             //     show_error('Skema pembayaran belum ada');
                    //         }
                    //     }

                    //     if ($admission->nama_seleksi == 'BEASISWA') {
                    //         $pdf = $this->create_scholarship_acceptance_letter($admission);
                    //     } else {
                    //         $pdf = $this->create_acceptance_letter($admission);
                    //     }
                    // } else {
                    //     show_error('Skema pembayaran belum ada');
                    // }

                    if ($admission->nama_seleksi == 'BEASISWA') {
                        $pdf = $this->create_scholarship_acceptance_letter($admission);
                    } else {
                        $pdf = $this->create_acceptance_letter($admission);
                    }
                } elseif ($admission->program == MAGISTER) {
                    $filter = [
                        'discount' => 0,
                        'jenis_program_studi' => $admission->program,
                        'id_program_studi' => $admission->program_studi,
                    ];

                    $biaya = $this->Biaya_model->get_many_by($filter);
                    $admission->biaya = $biaya;


                    $filter = [
                        'id_seleksi' => $admission->seleksi,
                    ];

                    if ($admission->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                    }

                    $admission->skema = $this->Skema_pembayaran_model->get_many_by($filter);

                    // if ($admission->skema) {
                    //     foreach ($admission->skema as $skema) {
                    //         // $jumlah_angsuran = $this->Skema_pembayaran_detail_model->count_by('id_skema_pembayaran', $skema->id_skema_pembayaran);
                    //         // $skema->jumlah_angsuran = $jumlah_angsuran ? $jumlah_angsuran - 1 : 0;

                    //         $filter['skema_pembayaran.id_skema_pembayaran'] = $skema->id_skema_pembayaran;
                    //         $skema->detail = $this->Skema_pembayaran_detail_model
                    //             ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    //             ->order_by('pembayaran')
                    //             ->get_many_by($filter);

                    //         if ($skema->detail) {
                    //             foreach ($admission->discounts as $disc) {
                    //                 $skema->jumlah_total -= $disc->jumlah;
                    //             }
                    //         } else {
                    //             show_error('Skema pembayaran belum ada');
                    //         }
                    //     }
                    $pdf = $this->create_acceptance_letter_s2($admission);
                    // } else {
                    //     show_error('Skema pembayaran belum ada');
                    // }
                }
            } else {
                $pdf = $this->create_non_acceptance_letter($admission);
            }
            // print_r($pdf);
            // die();
            if ($pdf) {
                if ($write) {
                    if (!file_exists(FCPATH . DOCUMENTS_FOLDER . $id_admission)) {
                        mkdir(FCPATH . DOCUMENTS_FOLDER . $id_admission);
                    }

                    $filename = 'SL - ' . $admission->full_name;
                    if ($admission->status != 400) {
                        $filename = 'STL - ' . $admission->full_name;
                    }
                    $pdf->Output(DOCUMENTS_FOLDER . $id_admission . '/' . $filename . '.pdf', 'F');
                } else {
                    // $pdf->Output();//fadli
                    $filename = $admission->full_name;
                    $pdf->Output('I', $filename .'.pdf', true);
                }
            }
        }
    }
    public function create_loa_sarjana_discount($admission)
    {
        $pdf = new setasign\Fpdi\Fpdi();
        $pdf->setMargins(31.75, 45.72, 31.75);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        if ($admission->nama_seleksi == 'REGULER') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_reguler.pdf');
        } elseif ($admission->nama_seleksi == 'RAPORT') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_rapor.pdf');
        } elseif ($admission->nama_seleksi == 'UTBK') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_utbk.pdf');
        }
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['width'] > $size['height']) {
                $pdf->AddPage('L', array($size['width'], $size['height']));
            } else {
                $pdf->AddPage('P', array($size['width'], $size['height']));
            }

            $pdf->useTemplate($templateId);

            if ($pageNo == 1) {
                $pdf->SetXY(31.75, 59.4);
                $pdf->Cell(0, 4, 'Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)), 0, 0, 'R');

                $pdf->SetXY(44.5, 63.5);
                $pdf->Cell(0, 4, '     /SMB/STM-PPM/VIII/20');

                $pdf->SetFont('Calibri', 'B', 10);
                $pdf->SetXY(31, 76.6);
                $pdf->Cell(0, 4, $admission->full_name);

                $pdf->SetXY(31, 80.6);
                $pdf->Cell(0, 4, $admission->address_1);

                $pdf->SetXY(31, 84.6);
                $pdf->Cell(0, 4, $admission->address_2);

                $pdf->SetFont('Calibri', '', 10);
                $pdf->SetXY(101.4, 110.6);
                $pdf->Cell(0, 4, $admission->nama_program_studi);
            } elseif ($pageNo == 2) {
                $y = 72.3;
                $line_height = 4.3;

                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->full_name);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->address_1);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->nama_kota);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->nama_program_studi);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->father_name);
            }
        }

        return $pdf;
    }
    public function create_loa_sarjana($admission)
    {
        $pdf = new setasign\Fpdi\Fpdi();
        $pdf->setMargins(31.75, 45.72, 31.75);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        if ($admission->nama_seleksi == 'REGULER') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_reguler.pdf');
        } elseif ($admission->nama_seleksi == 'RAPORT') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_rapor.pdf');
        } elseif ($admission->nama_seleksi == 'UTBK') {
            $pageCount = $pdf->setSourceFile(FCPATH . 'assets/files/documents/loa_s1_utbk.pdf');
        }
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['width'] > $size['height']) {
                $pdf->AddPage('L', array($size['width'], $size['height']));
            } else {
                $pdf->AddPage('P', array($size['width'], $size['height']));
            }

            $pdf->useTemplate($templateId);

            if ($pageNo == 1) {
                $pdf->SetXY(31.75, 59.4);
                $pdf->Cell(0, 4, 'Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)), 0, 0, 'R');

                $pdf->SetXY(44.5, 63.5);
                $pdf->Cell(0, 4, '     /SMB/STM-PPM/VIII/20');

                $pdf->SetFont('Calibri', 'B', 10);
                $pdf->SetXY(31, 76.6);
                $pdf->Cell(0, 4, $admission->full_name);

                $pdf->SetXY(31, 80.6);
                $pdf->Cell(0, 4, $admission->address_1);

                $pdf->SetXY(31, 84.6);
                $pdf->Cell(0, 4, $admission->address_2);

                $pdf->SetFont('Calibri', '', 10);
                $pdf->SetXY(101.4, 110.6);
                $pdf->Cell(0, 4, $admission->nama_program_studi);
            } elseif ($pageNo == 2) {
                $y = 72.3;
                $line_height = 4.3;

                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->full_name);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->address_1);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->nama_kota);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->nama_program_studi);

                $y += $line_height;
                $pdf->SetXY(68, $y);
                $pdf->Cell(0, 4, $admission->father_name);
            }
        }

        return $pdf;
    }

    public function create_acceptance_letter($admission)
    {
        $this->load->library('PDF_WriteTag');

        $pdf = new PDF_WriteTag();
        // $pdf = new setasign\Fpdi\Fpdi();

        $pdf->setMargins(25, 45.72, 25);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        $pdf->AddPage();

        // Stylesheet
        $pdf->SetStyle("b", "Calibri", "B", 0, "0,0,0");
        $pdf->SetStyle("p", "Calibri", "N", 10, "0,0,0");

        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

        if ($admission->status == 400) {
            $pdf->WriteTag(0, 4, '<p><b>LETTER OF ACCEPTANCE (LoA)</b></p>', 0, "C");
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p><b>Academic Year 2020/2021</b>', 0, "C");
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)) . '</p>', 0);
            $pdf->Ln(4);
            if ($admission->nama_program_studi == 'SARJANA AKUNTANSI BISNIS') {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SAB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            } else {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SMB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            }
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Kepada Yth.</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->full_name</b></p>");
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->address_1</b></p>");
            if ($admission->address_2) {
                $pdf->Ln();
                $pdf->WriteTag(0, 4, "<p><b>$admission->address_2</b></p>");
            }
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Dengan hormat,</p>');
            $pdf->Ln();
            $txt = "<p><b>Selamat atas keberhasilan Anda</b> dan selamat bergabung dalam keluarga besar PPM School of Management (PPM SoM). Berdasarkan ";
            if ($admission->nama_seleksi == 'REGULER') {
                $txt .= "Hasil Tes Seleksi";
            } elseif ($admission->nama_seleksi == 'RAPORT') {
                $txt .= "Hasil Tes Seleksi Nilai Raport";
            } elseif ($admission->nama_seleksi == 'UTBK') {
                $txt .= "Hasil Seleksi Skor UTBK";
            }

            if ($admission->grade_discount) {
                $kategori = '';
                switch ($admission->grade_discount->grade) {
                    case 1:
                        $kategori = 'KATEGORI A';
                        break;
                    case 2:
                        $kategori = 'KATEGORI B';
                        break;
                    case 3:
                        $kategori = 'KATEGORI C';
                        break;
                }
                $txt .= ", Saudara/i dinyatakan LULUS SELEKSI masuk PPM School of Management dan mendapatkan <b>'BEASISWA $kategori'</b>.</p>";
            } else {
                $txt .= ", Saudara/i dinyatakan LULUS SELEKSI masuk PPM School of Management.</p>";
            }
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();

            $pdf->SetXY(25, 110);
            $pdf->WriteTag(0, 4, '<p>Program Studi</p>');
            $pdf->SetXY(67, 110);
            $pdf->WriteTag(0, 4, '<p>:</p>');
            $pdf->SetXY(70, 110);
            $pdf->WriteTag(0, 4, '<p>' . $admission->nama_program_studi . '</p>');
            if ($admission->grade_discount) {
                $pdf->Ln();


                $kategori = '';
                switch ($admission->grade_discount->grade) {
                    case 1:
                        $kategori = 'Kategori A, mendapatkan Beasiswa Potongan Biaya Pengembangan sebesar Rp. 30.000.000,-';
                        break;
                    case 2:
                        $kategori = 'Kategori B, mendapatkan Beasiswa Potongan Biaya Pengembangan sebesar Rp. 20.000.000,-';
                        break;
                    case 3:
                        $kategori = 'Kategori C, mendapatkan Beasiswa Potongan Biaya Pengembangan sebesar Rp. 10.000.000,-';
                        break;
                }

                $pdf->WriteTag(0, 4, '<p>Beasiswa</p>');
                $pdf->SetXY(67, 114);
                $pdf->WriteTag(0, 4, '<p>:</p>');
                $pdf->SetXY(70, 114);
                $pdf->WriteTag(0, 4, '<p>' . $kategori . '</p>');
            }
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p><b>I. BIAYA PENDIDIKAN SEMESTER PERTAMA</b></p>');
            $pdf->Ln();

            $pdf->Cell(87, 4, 'KOMPONEN BIAYA', 1, 0, 'C');
            $pdf->Cell(0, 4, 'JUMLAH (RP)', 1, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Calibri', '', 10);

            $total = 0;
            $development = 0;
            foreach ($admission->biaya as $biaya) {
                if ($biaya->development) {
                    $development += $biaya->jumlah * $biaya->sign;
                }
                $total += $biaya->jumlah * $biaya->sign;
                $biaya_txt = number_format($biaya->jumlah, 0, ',', '.');
                if ($biaya->sign == -1) {
                    $biaya_txt = number_format($biaya->jumlah, 0, ',', '.');
                }
                $pdf->Cell(87, 4, $biaya->nama_biaya, 1);
                $pdf->Cell(0, 4, $biaya_txt, 1, 0, 'R');
                $pdf->Ln();
            }

            foreach ($admission->discounts as $biaya) {
                $total += $biaya->jumlah * $biaya->sign;
                $development += $biaya->jumlah * $biaya->sign;
                $biaya_txt = number_format($biaya->jumlah, 0, ',', '.');
                if ($biaya->sign == -1) {
                    $biaya_txt = number_format($biaya->jumlah, 0, ',', '.');
                }
                $pdf->Cell(87, 4, $biaya->nama_biaya, 1);
                $pdf->Cell(0, 4, $biaya_txt, 1, 0, 'R');
                $pdf->Ln();
            }

            $pdf->SetFont('Calibri', 'B', 10);

            $pdf->Cell(87, 4, 'Total Biaya Yang Dibayarkan', 1);
            $pdf->Cell(0, 4, number_format($total, 0, ',', '.'), 1, 0, 'R');
            $pdf->Ln(4);
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p><b>II.	CARA PEMBAYARAN</b></p>');
            $pdf->Ln();

            $pdf->SetFont('Calibri', 'B', 10);
            $widths = [30];
            $aligns = ['C'];
            $titles = ['CARA PEMBAYARAN'];

            $master_angsuran = [1, 4, 6, 9, 12];
            $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

            $datetime1 = date_create($admission->tgl_seleksi);
            $datetime2 = date_create('2022-09-01');
            $interval = date_diff($datetime1, $datetime2);
            $angsuran_interval =  $interval->format('%m');

            $angsuran = [];
            $konfirmasi = [];
            $angsuran_per_bulan = [];
            $angsuran_lain = [];

            $max_angsuran = 1;

            foreach ($master_angsuran as $i => $jenis_angsuran) {
                if ($jenis_angsuran <= $angsuran_interval) {
                    $max_angsuran = $jenis_angsuran;

                    $angsuran[] = $jenis_angsuran;
                    $konfirmasi[] = $master_konfirmasi[$i];
                    if ($jenis_angsuran == 1) {
                        // echo "$development - $master_konfirmasi[$i]";
                        $biaya_development = $development;
                        foreach ($admission->biaya as $biaya) {
                            if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                $biaya_development += $biaya->jumlah;
                            }
                        }
                        $angsuran_per_bulan[] = ($biaya_development - $master_konfirmasi[$i]);
                        $angsuran_lain[] = [];
                    } elseif ($jenis_angsuran == 4) {
                        $angsuran_per_bulan[] = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 1);

                        $biaya_pembinaan = 0;
                        $biaya_sks = 0;
                        foreach ($admission->biaya as $biaya) {
                            if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                $biaya_pembinaan += $biaya->jumlah;
                            }
                        }
                        $angsuran_lain[] = [
                            $jenis_angsuran - 1 => $biaya_pembinaan
                        ];
                    } else {
                        $angsuran_per_bulan[] = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 2);
                        $biaya_pembinaan = 0;
                        $biaya_sks = 0;
                        foreach ($admission->biaya as $biaya) {
                            if ($biaya->pembinaan == 1) {
                                $biaya_pembinaan += $biaya->jumlah;
                            }
                            if ($biaya->sks == 1) {
                                $biaya_sks += $biaya->jumlah;
                            }
                        }
                        $angsuran_lain[] = [
                            $jenis_angsuran - 2 => $biaya_pembinaan,
                            $jenis_angsuran - 1 => $biaya_sks
                        ];
                    }
                }
            }
            // print_r($angsuran_per_bulan);

            foreach ($master_angsuran as $i => $jenis_angsuran) {
                if ($jenis_angsuran <= $angsuran_interval) {
                    $widths[] = 70 / count($angsuran);
                    $aligns[] = 'C';
                    $titles[] = 'ANGSURAN ' . $jenis_angsuran . 'x';
                }
            }
            // foreach ($admission->skema as $skema) {
            //     $widths[] = 50 / count($admission->skema);
            //     $aligns[] = 'C';
            //     $titles[] = $skema->jenis_pembayaran == 1 ? 'TUNAI' : 'ANGSURAN ' . $skema->jumlah_angsuran . 'x';
            // }
            $widths[] = 60;
            $aligns[] = 'C';
            $titles[] = 'BATAS WAKTU';

            $pdf->SetWidths($widths);
            $pdf->SetAligns($aligns);
            $pdf->Row($titles);

            $confirmation_row = [
                'Konfirmasi',
            ];
            foreach ($konfirmasi as $biaya_konfirmasi) {
                $confirmation_row[] = number_format($biaya_konfirmasi, '2', ',', '.');
            }
            $confirmation_row[] = '';
            $pdf->Row($confirmation_row);


            $first_date_find = strtotime(date("Y-m-d", strtotime($admission->tgl_seleksi)) . ", first day of this month");
            $first_date = date("Y-m-d", $first_date_find);

            for ($i = 1; $i <= $max_angsuran; $i++) {
                $rows = ['Angsuran ' . $i];


                for ($j = 0; $j < count($angsuran); $j++) {
                    if ($angsuran[$j] == 1) {
                        if ($i == 1) {
                            $rows[] = number_format($angsuran_per_bulan[$j], '2', ',', '.');
                        } else {
                            $rows[] = '';
                        }
                    } elseif ($angsuran[$j] == 4) {
                        if ($i == 1 || $i <= $angsuran[$j] - 1) {
                            $rows[] = number_format($angsuran_per_bulan[$j], '2', ',', '.');
                        } elseif ($i > $angsuran[$j] - 1 && $i <= $angsuran[$j]) {
                            $rows[] = number_format($angsuran_lain[$j][$i - 1], '2', ',', '.');
                        } else {
                            $rows[] = '';
                        }
                    } else {
                        if ($i == 1 || $i <= $angsuran[$j] - 2) {
                            $rows[] = number_format($angsuran_per_bulan[$j], '2', ',', '.');
                        } elseif ($i > $angsuran[$j] - 2 && $i <= $angsuran[$j]) {
                            $rows[] = number_format($angsuran_lain[$j][$i - 1], '2', ',', '.');
                        } else {
                            $rows[] = '';
                        }
                    }
                }
                $first_date = date("Y-m-d", strtotime($first_date . " +1 month"));
                $rows[] = date("d-m-Y", strtotime($first_date));


                $pdf->Row($rows);
            }
            // $aligns = ['L'];
            // $total_angsuran = [];

            // foreach ($admission->skema as $skema) {
            //     $aligns[] = 'R';
            //     $total_angsuran[] = 0;
            // }
            // $aligns[] = 'C';
            // $pdf->SetAligns($aligns);
            // $pdf->SetFont('Calibri', '', 10);

            // $rows = max(array_column($admission->skema, 'jumlah_angsuran'));

            // $konfirmasi = 0;

            // for ($i = 0; $i <= $rows; $i++) {
            //     $skema = FALSE;
            //     if (isset($admission->skema[0]->detail[$i])) {
            //         $skema = $admission->skema[0]->detail[$i];
            //     } elseif (isset($admission->skema[1]->detail[$i])) {
            //         $skema = $admission->skema[1]->detail[$i];
            //     }

            //     if ($skema) {
            //         if ($i == 0) {
            //             $pembayaran = 'Konfirmasi';
            //             $waktu = date('j F Y', strtotime($admission->tgl_seleksi . ' + ' . $skema->waktu . ' days'));
            //         } elseif ($i == 1) {
            //             $pembayaran = 'Angsuran ' . $i;

            //             if ($skema->waktu) {
            //                 $waktu = date('j F Y', strtotime($admission->tgl_seleksi . ' + ' . $skema->waktu . ' days'));
            //             } else {
            //                 $waktu = date('j F Y', strtotime($skema->jatuh_tempo));
            //             }
            //         } else {
            //             $pembayaran = 'Angsuran ' . $i;
            //             if ($skema->waktu) {
            //                 $waktu = date('j F Y', strtotime($admission->tgl_seleksi . ' + ' . $skema->waktu . ' days'));
            //             } else {
            //                 $waktu = date('j F Y', strtotime($skema->jatuh_tempo));
            //             }
            //         }
            //         $content = [
            //             $pembayaran,
            //         ];

            //         foreach ($admission->skema as $j => $sk) {
            //             if (isset($sk->detail[$i])) {
            //                 if ($i == 0) {
            //                     $konfirmasi = $sk->detail[$i]->jumlah;

            //                     $jumlah = 0;
            //                     if ($sk->detail[$i]->jumlah) {
            //                         $jumlah = $sk->detail[$i]->jumlah;
            //                     } elseif ($sk->detail[$i]->persentase) {
            //                         $jumlah = ($sk->detail[$i]->persentase / 100) * ($sk->jumlah_total);
            //                     }
            //                 } else {
            //                     $jumlah = 0;
            //                     if ($sk->jenis_pembayaran == 1) {
            //                         $jumlah = $sk->jumlah_total - $konfirmasi;
            //                     } else {
            //                         if ($sk->detail[$i]->jumlah) {
            //                             $jumlah = $sk->detail[$i]->jumlah ;
            //                         } elseif ($sk->detail[$i]->persentase) {
            //                             $jumlah = ($sk->detail[$i]->persentase / 100) * ($sk->jumlah_total - $konfirmasi);
            //                         }
            //                     }
            //                 }
            //                 $content[] = number_format($jumlah, 0, ',', '.');
            //                 $total_angsuran[$j] += $jumlah ? $jumlah : 0;
            //                 // echo $jumlah." ".$total_angsuran[$j]."\n";
            //             } else {
            //                 $content[] = '';
            //             }
            //         }
            //         $content[] = $waktu;
            //         // print_r($content);
            //         $pdf->Row($content);
            //     }
            // }

            // // print_r($total_angsuran);
            // $widths = [50];
            // $aligns = ['R'];
            // $content = ['TOTAL BIAYA'];

            // foreach ($admission->skema as $i => $sk) {
            //     $widths[] = 50 / count($admission->skema);
            //     $aligns[] = 'R';
            //     $content[] =  number_format($total_angsuran[$i], 0, ',', '.');
            // }
            // $widths[] = 47;
            // $aligns[] = 'R';
            // $content[] = '';

            // $pdf->SetWidths($widths);
            // $pdf->SetAligns($aligns);
            // $pdf->Row($content);

            //Fadli
            $pdf->SetFont('Calibri', '', 8);
            $pdf->Cell(0, 4, 'Keterangan:(*) No.1-5 untuk pembayaran angsuran biaya pengembangan. ');
            $pdf->Ln();
            $pdf->Cell(0, 4, '                       No.6-8 untuk pembayaran angsuran biaya pembinaan, biaya orientasi dan SKS semester 1.');
            $pdf->Ln(4);
            //Fadli

            $pdf->Ln(4);

            $txt = "<p>Pembayaran dapat melalui transfer ke rekening:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();
            $pdf->Cell(7, 4, '1.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank Mandiri</b> dengan nomor rekening <b>1030085288583</b> atas nama <b>Yayasan Pendidikan & Pembinaan Manajemen</b>, atau</p>', 0);
            $pdf->Ln();

            $pdf->Cell(7, 4, '2.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank BCA</b> dengan nomor rekening <b>6860038801</b> atas nama <b>Yayasan PPM</b>.</p>', 0);
            $pdf->Ln(4);

            $txt = "<p>Harap mengirimkan soft copy: (1) Letter Of Acceptance & Surat Perjanjian Calon Mahasiswa yang sudah ditandatangani via email ke smb-ppm@ppm-manajemen.ac.id , (2) Bukti Transfer via email ke  tet@ppm-manajemen.ac.id atau kasir_stm@ppm-manajemen.ac.id dan cc ke email smb-ppm@ppm-manajemen.ac.id.</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();

            $txt = "<p>Kami mengucapkan selamat atas keberhasilan Saudara dan selamat bergabung dalam keluarga besar Sekolah Tinggi Manajemen PPM.</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);

            $pdf->Cell(89.4, 4, 'Hormat Kami,');
            $pdf->MultiCell(0, 4, 'Mengetahui dan Menyetujui, Orang Tua / Wali Calon Mahasiswa', 0, 'R');
            $pdf->Ln();
            // $pdf->Cell(89.4, 4, '');
            //fadli
            $pdf->Image(FCPATH . 'assets/backoffice/img/ratna2.png', 25, 246);
            //fadli
            $pdf->MultiCell(0, 4, '', 0, 'R');
            $pdf->Ln();
            $pdf->Cell(89.4, 4, 'Martdian Ratna Sari, S.E., M.Sc');
            $pdf->MultiCell(0, 4, '(....................................................)', 0, 'R');
            $pdf->Cell(89.4, 4, 'Koordinator Kemahasiswaan');
            $pdf->Ln();

            $pdf->setMargins(31.75, 45.72, 25);
            $pdf->AddPage();
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

            $pdf->WriteTag(0, 4, '<p><b>SURAT PERJANJIAN CALON MAHASISWA</b></p>', 0, "C");
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Kami yang bertanda tangan di bawah ini :</p>');
            $pdf->Ln(4);
            $pdf->Cell(42, 4, 'Nama Calon Mahasiswa', 0);
            $pdf->Cell(0, 4, ': ' . $admission->full_name, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Alamat', 0);
            $pdf->Cell(0, 4, ': ' . $admission->address_1, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Kota', 0);
            $pdf->Cell(0, 4, ': ' . $admission->nama_kota, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Program', 0);
            $pdf->Cell(0, 4, ': ' . $admission->nama_program_studi, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Orang Tua/ Wali:', 0);
            $pdf->Cell(0, 4, ': ' . $admission->father_name, 0);
            $pdf->Ln(4);
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p><b>A. KETENTUAN PENGEMBALIAN DANA UNTUK CALON MAHASISWA YANG MENGUNDURKAN DIRI</b></p>');
            $pdf->Ln();

            $txt = "<p>Bagi calon mahasiswa yang telah melakukan pembayaran (lunas) biaya pendidikan dapat mengajukan pengembalian dana biaya pendidikan dengan persyaratan:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);
            $pdf->Cell(7, 4, '1.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Surat Pernyataan pengunduran diri dengan materai Rp.6.000, selambatnya H+1 pengumuman hasil SBMPTN ' . date('Y') . ' atau SNMPTN ' . date('Y'), 0);
            $pdf->Cell(7, 4, '2.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Melampirkan fotokopi surat penerimaan dan pembayaran dari universitas yang dipilih.', 0);
            $pdf->Cell(7, 4, '3.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Melampirkan bukti pembayaran yang telah dilakukan ke PPM School Of Management (PPM SoM) Pengembalian biaya akan dilakukan selambatnya 1 (satu) bulan setelah surat pernyataan pengunduran diri dan kelengkapannya diterima.');
            $pdf->Cell(7, 4, '4.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Surat Pernyataan pengunduran diri ditujukan kepada Ketua PPM SoM dan dikirimkan ke Bagian Admisi', 0);
            $pdf->Cell(7, 4, '5.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Bila persyaratan diatas tidak terpenuhi, maka biaya yang telah dibayarkan tidak dapat dikembalikan.', 0);
            $pdf->Cell(7, 4, '6.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Bila pengunduran diri dilakukan setelah H+1 pengumuman hasil SBMPTN ' . date('Y') . ' atau SNMPTN ' . date('Y') . ', PPM SoM hanya mengembalikan pembayaran biaya semester 1 yang telah dibayarkan, yang terdiri dari Biaya Orientasi, Biaya SKS dan Biaya Pembinaan', 0);
            $pdf->Cell(7, 4, '7.', 0, 'R');
            $pdf->MultiCell(0, 4, 'Besarnya Biaya yang dikembalikan mengikuti kriteria pengunduran diri dibawah ini', 0);
            $pdf->Ln();

            // $pdf->Cell(11.68, 4, 'NO', 1, 0, 'C');
            // $pdf->Cell(50.78, 4, 'KETENTUAN', 1, 0, 'C');
            // $pdf->Cell(50, 4, 'PENGEMBALIAN DANA', 1, 0, 'C');
            // $pdf->Cell(0, 4, 'BIAYA ADMINISTRASI', 1, 0, 'C');
            // $pdf->Ln();
            $pdf->SetXY(32, 160);
            $pdf->MultiCell(10, 12, 'NO', 1, 'C');

            $pdf->SetXY(42, 160);
            $pdf->MultiCell(44, 12, 'KETENTUAN', 1, 'C');

            $pdf->SetXY(86, 160);
            $pdf->MultiCell(66, 4, 'PENGEMBALIAN DANA', 1, 'C');

            $pdf->SetXY(86, 164);
            $pdf->MultiCell(28, 4, 'BIAYA PENGEMBANGAN', 1, 'C');

            $pdf->SetXY(114, 164);
            $pdf->MultiCell(38, 4, 'BIAYA PEMBINAAN, SKS SEM.1 & ORIENTASI', 1, 'C');

            $pdf->SetXY(152, 160);
            $pdf->MultiCell(0, 4, 'BIAYA ADMINISTRASI (TIDAK DIKEMBALIKAN)', 1, 'C');
            $pdf->Ln();

            $y = 172;
            $pdf->SetXY(32, $y);
            $pdf->MultiCell(10, 24, '1', 1, 'C');

            $pdf->SetXY(42, $y);
            $pdf->MultiCell(44, 4, 'Calon Mahasiswa ikut serta Ujian Nasional dan dinyatakan TIDAK LULUS Ujian Nasional dan atau TIDAK LULUS SMA dan sederajat', 1);

            $pdf->SetXY(86, $y);
            $pdf->MultiCell(28, 24, '100%', 1, 'C');

            $pdf->SetXY(114, $y);
            $pdf->MultiCell(38, 24, '100%', 1, 'C');

            $pdf->SetXY(152, $y);
            $pdf->MultiCell(0, 24, 'Rp. 7.000.000,-', 1, 'C');
            $pdf->Ln();

            $y += 24;
            $pdf->SetXY(32, $y);
            $pdf->MultiCell(10, 32, '2', 1, 'C');

            $pdf->SetXY(42, $y);
            $pdf->MultiCell(44, 4, 'Calon Mahasiswa diterima di Perguruan Tinggi Negeri (PTN) dibawah Kementerian Pendidikan Nasional RI jenjang Strata 1 (S1) dengan jalur SNMPTN (undangan) atau SBMPTN', 1);

            $pdf->SetXY(86, $y);
            $pdf->MultiCell(28, 32, '100%', 1, 'C');

            $pdf->SetXY(114, $y);
            $pdf->MultiCell(38, 32, '100%', 1, 'C');

            $pdf->SetXY(152, $y);
            $pdf->MultiCell(0, 32, 'Rp. 7.000.000,-', 1, 'C');
            $pdf->Ln();

            $y += 32;
            $pdf->SetXY(32, $y);
            $pdf->MultiCell(10, 24, '3', 1, 'C');

            $pdf->SetXY(42, $y);
            $pdf->MultiCell(44, 4, 'Calon Mahasiswa ikut serta Ujian Nasional dan dinyatakan TIDAK LULUS Ujian Nasional dan atau TIDAK LULUS SMA dan sederajat', 1);

            $pdf->SetXY(86, $y);
            $pdf->MultiCell(28, 24, '100%', 1, 'C');

            $pdf->SetXY(114, $y);
            $pdf->MultiCell(38, 24, '100%', 1, 'C');

            $pdf->SetXY(152, $y);
            $pdf->MultiCell(0, 24, 'Rp. 7.000.000,-', 1, 'C');
            $pdf->Ln();

            $y += 24;
            $pdf->SetXY(32, $y);
            $pdf->MultiCell(10, 24, '4', 1, 'C');

            $pdf->SetXY(42, $y);
            $pdf->MultiCell(44, 4, 'Calon Mahasiswa ikut serta Ujian Nasional dan dinyatakan TIDAK LULUS Ujian Nasional dan atau TIDAK LULUS SMA dan sederajat', 1);

            $pdf->SetXY(86, $y);
            $pdf->MultiCell(66, 12, 'Tidak ada pengembalian dalam bentuk apapun', 1, 'C');


            $pdf->SetXY(152, $y);
            $pdf->MultiCell(0, 24, 'Rp. 7.000.000,-', 1, 'C');
            $pdf->Ln();

            $pdf->AddPage();
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);
            $pdf->WriteTag(0, 4, '<p><b>B.	KEASLIAN DOKUMEN KELULUSAN SMA/SEDERAJAT</b></p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>Dengan ini menyatakan bahwa saya adalah benar sedang menjalani pendidikan jenjang SMA /sederajat atau telah menyelesaikan pendidikan jenjang SMA/Sederajat, sehingga apabila diketahui bahwa dokumen kelulusan SMA/Sederajat atas nama saya tidak benar, maka saya bersedia menerima keputusan dari pihak PPM School of Management untuk membatalkan penerimaan mahasiswa baru atas nama saya, serta saya berjanji untuk tidak menarik kembali pembayaran yang sudah dilakukan.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p><b>C. KEPATUHAN TERHADAP ATURAN YANG BERLAKU DI PPM SoM</b></p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>Dengan ini menyatakan bahwa pada saat saya dinyatakan sebagai mahasiswa PPM SoM maka saya bersedia menaati Tata Tertib Kehidupan Kampus, Aturan Umum Bidang Akademik, dan semua peraturan yang berlaku di lingkungan PPM SoM.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Bahwa saya memahami ketentuan dan kebijakan yang tercantum di atas dan apabila saya melanggarnya, saya secara sadar untuk menerima sanksi-sanksi yang telah ditentukan menurut kebijakan diatas, termasuk dikeluarkan dari PPM SoM tanpa menuntut apapun termasuk pengembalian atau pengalihan dana yang sudah dibayarkan.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Surat Perjanjian ini kami buat dalam keadaan sehat jasmani dan rohani tanpa terpengaruh atau paksaan dari pihak manapun, dan dapat diberlakukan selama saya menjadi mahasiswa PPM SoM dan bersedia menerima sanksi apapun bila melanggar perjanjian diatas.</p>');
            $pdf->Ln(4);

            $pdf->Cell(89.4, 4, 'Mengetahui Orang Tua / Wali');
            $pdf->MultiCell(0, 4, 'Yang Membuat Pernyataan', 0, 'R');
            $pdf->Cell(89.4, 4, 'Calon Mahasiswa');
            $pdf->MultiCell(0, 4, 'Calon Mahasiswa', 0, 'R');
            $pdf->Cell(89.4, 16, '');
            $pdf->MultiCell(0, 16, 'Meterai Rp. 6.000,-', 0, 'R');
            $pdf->Cell(89.4, 4, '(....................................................)');
            $pdf->MultiCell(0, 4, '(....................................................)', 0, 'R');
            $pdf->Ln(8);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->Cell(0, 4, 'Mengetahui', 0, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(0, 4, 'KETUA SEKOLAH TINGGI MANAJEMEN PPM', 0, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Calibri', '', 10);
            // $pdf->Cell(0, 4, 'Prof. Bramantyo Djohanputro, M.B.A. Ph.D', 0, 0, 'C');
            $pdf->Cell(0, 4, 'Dr. Pepey Riawati Kurnia, M.M., CPM, CAC, CODP', 0, 0, 'C');
            $pdf->Ln(8);
            $pdf->Cell(0, 4, '* Mohon lembaran ini di fotokopi sebanyak 2 lembar setelah ditanda-tangani di atas meterai ');
            $pdf->Ln();
            $pdf->Cell(0, 4, '** Mohon simpan bukti ini sebagai bukti.');
        }

        return $pdf;
    }

    public function create_non_acceptance_letter($admission)
    {
        $this->load->library('PDF_WriteTag');

        $pdf = new PDF_WriteTag();
        // $pdf = new setasign\Fpdi\Fpdi();

        $pdf->setMargins(31.75, 45.72, 31.75);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('times', '', 12);
        $pdf->AddPage();

        // Stylesheet
        $pdf->SetStyle("b", "times", "B", 0, "0,0,0");
        $pdf->SetStyle("p", "times", "N", 12, "0,0,0");

        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

        $line_height = 6;
        // Text
        $pdf->WriteTag(0, $line_height, '<p>Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)) . '</p>');
        $pdf->Ln($line_height);
        if ($admission->nama_program_studi == 'SARJANA AKUNTANSI BISNIS') {
            $pdf->WriteTag(0, $line_height, '<p>No     : ' . $admission->id_admission . '/SAB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
        } else {
            $pdf->WriteTag(0, $line_height, '<p>No     : ' . $admission->id_admission . '/SMB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
        }
        $pdf->Ln($line_height);
        $pdf->WriteTag(0, $line_height, '<p>Kepada Yth.</p>');
        $pdf->Ln();
        $pdf->WriteTag(0, $line_height, "<p><b>$admission->full_name</b></p>");
        $pdf->Ln();
        $pdf->WriteTag(0, $line_height, "<p><b>$admission->address_1</b></p>");
        if ($admission->address_2) {
            $pdf->Ln();
            $pdf->WriteTag(0, $line_height, "<p><b>$admission->address_2</b></p>");
        }
        $pdf->Ln($line_height);

        $pdf->WriteTag(0, $line_height, '<p>Dengan hormat,</p>');
        $pdf->Ln($line_height);
        $txt = "<p>Setelah mempertimbangkan hasil seleksi dan data yang Saudara berikan, dengan sangat menyesal kami beritahukan bahwa Saudara belum bisa kami terima untuk mengikuti Program $admission->nama_program_studi.";
        $pdf->WriteTag(0, $line_height, $txt, 0, "J");
        $pdf->Ln($line_height);

        $txt = "<p>Atas perhatian yang besar terhadap Program $admission->nama_program_studi, kami ucapkan terima kasih. Semoga Saudara sukses berkarir ditempat lain.</p>";
        $pdf->WriteTag(0, $line_height, $txt, 0, "J");
        $pdf->Ln($line_height);

        $pdf->WriteTag(0, $line_height, '<p>Hormat Kami,</p>');
        $pdf->Ln(12);
        $pdf->WriteTag(0, $line_height, '<p>....................................................</p>');
        $pdf->Ln();
        $pdf->WriteTag(0, $line_height, '<p>Koordinator Kemahasiswaan</p>');
        $pdf->Ln();



        return $pdf;
    }

    public function create_scholarship_acceptance_letter($admission)
    {
        $this->load->library('PDF_WriteTag');

        $pdf = new PDF_WriteTag();
        // $pdf = new setasign\Fpdi\Fpdi();

        $pdf->setMargins(31.75, 45.72, 31.75);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        $pdf->AddPage();

        // Stylesheet
        $pdf->SetStyle("b", "Calibri", "B", 0, "0,0,0");
        $pdf->SetStyle("p", "Calibri", "N", 10, "0,0,0");

        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

        if ($admission->status == 400) {
            // Text
            $pdf->WriteTag(0, 4, '<p><b>LETTER OF ACCEPTANCE (LoA)</b></p>', 0, "C");
            $pdf->Ln();
            if ($admission->scholarship == 100) {
                $pdf->WriteTag(0, 4, '<p><b>Full Scholarship</b>', 0, "C");
            } else {
                $pdf->WriteTag(0, 4, '<p><b>Partial Scholarship</b>', 0, "C");
            }
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)) . '</p>', 0, 'R');
            $pdf->Ln(4);
            if ($admission->nama_program_studi == 'SARJANA AKUNTANSI BISNIS') {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SAB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            } else {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SMB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            }
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Kepada Yth.</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->full_name</b></p>");
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->address_1</b></p>");
            if ($admission->address_2) {
                $pdf->Ln();
                $pdf->WriteTag(0, 4, "<p><b>$admission->address_2</b></p>");
            }
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Dengan hormat,</p>');
            $pdf->Ln(4);
            $txt = "<p>Setelah mengkaji hasil wawancara, panitia penerimaan Program Sarjana Sekolah Tinggi Manajemen PPM memutuskan untuk memberikan Saudara  beasiswa dengan rincian sebagai berikut:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);

            $pdf->Cell(42.41, 4, 'NAMA MAHASISWA', 1, 0, 'C');
            $pdf->Cell(55.12, 4, 'ASAL SEKOLAH', 1, 0, 'C');
            $pdf->Cell(0, 4, 'BEASISWA', 1, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('Calibri', '', 10);


            $pdf->Cell(42.41, 4, $admission->full_name, 1, 0, 'C');
            $pdf->Cell(55.12, 4, '', 1, 0, 'C');
            $pdf->Cell(0, 4, '', 1, 0, 'J');

            $pdf->Ln(8);

            if ($admission->scholarship == 100) {
                $pdf->WriteTag(0, 4, '<p><b>RINCIAN BEASISWA</b></p>');
                $pdf->Ln();

                $pdf->Cell(110, 4, 'KOMPONEN BIAYA', 1, 0, 'C');
                $pdf->Cell(0, 4, 'JUMLAH (RP)', 1, 0, 'C');
                $pdf->Ln();

                $pdf->SetFont('Calibri', '', 10);

                $biaya_potongan = [[
                    'komponen' => 'Biaya Pengembangan',
                    'jumlah' => 55000000,
                ], [
                    'komponen' => 'Biaya Pembinaan + Biaya SKS (8 Semester)',
                    'jumlah' => 107100000,
                ]];
                $total = 0;
                foreach ($biaya_potongan as $i => $biaya) {
                    $total += $biaya['jumlah'];
                    $pdf->Cell(110, 4, $biaya['komponen'], 1);
                    $pdf->Cell(0, 4, $biaya['jumlah'], 1, 0, 'R');
                    $pdf->Ln();
                }


                $pdf->SetFont('Calibri', 'B', 10);

                $pdf->Cell(110, 4, 'Total Biaya Yang Dibayarkan ', 1);
                $pdf->Cell(0, 4, number_format($total, 0, ',', '.'), 1, 0, 'R');
                $pdf->Ln();
                $pdf->Ln(4);


                $txt = "<p>Harap mengirimkan soft copy: (1) Letter Of Acceptance & Surat Perjanjian Calon Mahasiswa yang sudah ditandatangani via email ke smb-ppm@ppm-manajemen.ac.id</p>";
                $pdf->WriteTag(0, 4, $txt, 0, "J");
            } else {
                $pdf->WriteTag(0, 4, '<p><b>I. RINCIAN POTONGAN BIAYA STUDI (BEASISWA PARSIAL ' . $admission->scholarship . '%)</b></p>');
                $pdf->Ln();

                $pdf->Cell(110, 4, 'KOMPONEN BIAYA', 1, 0, 'C');
                $pdf->Cell(0, 4, 'JUMLAH (RP)', 1, 0, 'C');
                $pdf->Ln();

                $pdf->SetFont('Calibri', '', 10);

                $biaya_potongan = [[
                    'komponen' => 'Biaya Pengembangan',
                    'jumlah' => 55000000,
                ], [
                    'komponen' => 'Biaya Orientasi',
                    'jumlah' => 1500000,
                ], [
                    'komponen' => 'Biaya Pembinaan Semester 1 s/d Semester 4 @ Rp.6.000.000/Semester',
                    'jumlah' => 24000000,
                ]];
                $total = 0;
                foreach ($biaya_potongan as $i => $biaya) {
                    $total += $biaya['jumlah'];
                    $pdf->Cell(110, 4, $biaya['komponen'], 1);
                    $pdf->Cell(0, 4, $biaya['jumlah'], 1, 0, 'R');
                    $pdf->Ln();
                }


                $pdf->SetFont('Calibri', 'B', 10);

                $pdf->Cell(110, 4, 'Total Beasiswa', 1);
                $pdf->Cell(0, 4, number_format($total, 0, ',', '.'), 1, 0, 'R');
                $pdf->Ln(4);
                $pdf->Ln(4);

                $pdf->WriteTag(0, 4, '<p><b>II. RINCIAN BIAYA STUDI YANG HARUS DIBAYAR :</b></p>');
                $pdf->Ln();

                $pdf->Cell(110, 4, 'KOMPONEN BIAYA', 1, 0, 'C');
                $pdf->Cell(0, 4, 'JUMLAH (RP)', 1, 0, 'C');
                $pdf->Ln();

                $pdf->SetFont('Calibri', '', 10);

                $biaya_potongan = [[
                    'komponen' => 'Biaya Pembinaaan Semester 5 s/d Semester 8 @Rp.6.000.000/semestern',
                    'jumlah' => 24000000,
                ], [
                    'komponen' => 'Biaya SKS (144 SKS @Rp.400.000/SKS)',
                    'jumlah' => 57600000,
                ]];
                $total = 0;
                foreach ($biaya_potongan as $i => $biaya) {
                    $total += $biaya['jumlah'];
                    $pdf->Cell(110, 4, $biaya['komponen'], 1);
                    $pdf->Cell(0, 4, $biaya['jumlah'], 1, 0, 'R');
                    $pdf->Ln();
                }


                $pdf->SetFont('Calibri', 'B', 10);

                $pdf->Cell(110, 4, 'Total Biaya Yang Dibayarkan ', 1);
                $pdf->Cell(0, 4, number_format($total, 0, ',', '.'), 1, 0, 'R');
                $pdf->Ln();
                $txt = "<p>Note : Tambahan biaya Lain yaitu : Biaya HER (Jika melewati masa studi), Biaya Ujian Ulang (Jika melakukan ujian ulang), Biaya Toga dan Biaya Foto Prosesi Wisuda.</b></p>";
                $pdf->WriteTag(0, 4, $txt, 0, "J");

                $pdf->Ln(4);

                $pdf->WriteTag(0, 4, '<p><b>III. BIAYA STUDI SEMESTER I YANG HARUS DIBAYAR</b></p>');
                $pdf->Ln();

                $pdf->Cell(110, 4, 'KOMPONEN BIAYA', 1, 0, 'C');
                $pdf->Cell(0, 4, 'JUMLAH (RP)', 1, 0, 'C');
                $pdf->Ln();

                $pdf->SetFont('Calibri', '', 10);

                $biaya_potongan = [[
                    'komponen' => 'Biaya Pengembangan',
                    'jumlah' => 0,
                ], [
                    'komponen' => 'Biaya Pembinaan',
                    'jumlah' => 0,
                ], [
                    'komponen' => 'Biaya SKS Semester 1 (19 sks@400.000/sks)',
                    'jumlah' => 7600000,
                ]];
                $total = 0;
                foreach ($biaya_potongan as $i => $biaya) {
                    $total += $biaya['jumlah'];
                    $pdf->Cell(110, 4, $biaya['komponen'], 1);
                    $pdf->Cell(0, 4, $biaya['jumlah'], 1, 0, 'R');
                    $pdf->Ln();
                }


                $pdf->SetFont('Calibri', 'B', 10);

                $pdf->Cell(110, 4, 'Total Biaya Yang Dibayarkan ', 1);
                $pdf->Cell(0, 4, number_format($total, 0, ',', '.'), 1, 0, 'R');
                $pdf->Ln(4);
                $pdf->Ln(4);


                $pdf->SetFont('Calibri', '', 10);



                $txt = "<p>Pembayaran Biaya Studi Semester I harus dibayarkan maksimal H+14 setelah menerima Letter of Acceptance. Metode pembayaran hanya melalui transfer ke rekening <b>Bank Mandiri</b> dengan nomor rekening <b>1030085288583</b> atas nama <b>Yayasan Pendidikan & Pembinaan Manajemen</b></p>";
                $pdf->WriteTag(0, 4, $txt, 0, "J");
                $pdf->Ln(4);


                $txt = "<p>Harap mengirimkan soft copy: (1) Letter Of Acceptance & Surat Perjanjian Calon Mahasiswa yang sudah ditandatangani via email ke smb-ppm@ppm-manajemen.ac.id , (2) Bukti Transfer via email ke  tet@ppm-manajemen.ac.id atau kasir_stm@ppm-manajemen.ac.id dan cc ke email smb-ppm@ppm-manajemen.ac.id.</p>";
                $pdf->WriteTag(0, 4, $txt, 0, "J");
            }
            $pdf->Ln(4);

            $txt = "<p>Kami mengucapkan selamat atas keberhasilan Saudara dan selamat bergabung dalam keluarga besar Sekolah Tinggi Manajemen PPM.</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);

            $pdf->Cell(89.4, 4, 'Hormat Kami,');
            $pdf->MultiCell(0, 4, 'Mengetahui dan Menyetujui, Orang Tua / Wali Calon Mahasiswa', 0, 'R');
            $pdf->Ln();
            $pdf->Cell(89.4, 4, '');
            $pdf->MultiCell(0, 4, '', 0, 'R');
            $pdf->Ln();
            $pdf->Cell(89.4, 4, '....................................................');
            $pdf->MultiCell(0, 4, '(....................................................)', 0, 'R');
            $pdf->Cell(89.4, 4, 'Koordinator Kemahasiswaan');
            $pdf->Ln();

            $pdf->AddPage();
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

            $pdf->WriteTag(0, 4, '<p><b>SURAT PERJANJIAN CALON MAHASISWA</b></p>', 0, "C");
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Kami yang bertanda tangan di bawah ini :</p>');
            $pdf->Ln(4);
            $pdf->Cell(42, 4, 'Nama Calon Mahasiswa', 0);
            $pdf->Cell(0, 4, ': ' . $admission->full_name, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Alamat', 0);
            $pdf->Cell(0, 4, ': ' . $admission->address_1, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Kota', 0);
            $pdf->Cell(0, 4, ': ' . $admission->nama_kota, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Program', 0);
            $pdf->Cell(0, 4, ': ' . $admission->nama_program_studi, 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Orang Tua/ Wali:', 0);
            $pdf->Cell(0, 4, ':', 0);
            $pdf->Ln(4);
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p><b>A. KETENTUAN PENGEMBALIAN DANA UNTUK CALON MAHASISWA YANG MENGUNDURKAN DIRI</b></p>');
            $pdf->Ln();

            $txt = "<p>Bagi calon mahasiswa yang telah melakukan pembayaran (lunas) biaya pendidikan dapat mengajukan pengembalian dana biaya pendidikan dengan persyaratan:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);
            $pdf->Cell(7, 4, '-', 0, 'R');
            $pdf->MultiCell(0, 4, 'Konfirmasi sebagai penerima beasiswa maksimal dilakukan 7 Hari setelah surat ini diterima.', 0);
            $pdf->Cell(7, 4, '-', 0, 'R');
            $pdf->MultiCell(0, 4, 'Untuk mengkonfirmasikan diri sebagai penerima beasiswa dapat dilakukan dengan cara mengirimkan soft copy: (1) Letter of Acceptance & Surat Perjanjian Calon Mahasiswa yang sudah ditandatangani via email ke smb-ppm@ppm-manajemen.ac.id', 0);
            $pdf->Cell(7, 4, '-', 0, 'R');
            $pdf->MultiCell(0, 4, 'Surat Perjanjian Mahasiswa Penerima Beasiswa akan dijelaskan dan ditandatangani mendekati Pembukaan Kelas Program Sarjana PPM School of Management yaitu di bulan September 2020');
            $pdf->Ln();

            $pdf->WriteTag(0, 4, '<p><b>B. KETENTUAN UNTUK CALON MAHASISWA PENERIMA BEASISWA YANG SUDAH KONFIRMASI DAN MENGUNDURKAN DIRI</b></p>');
            $pdf->Ln();

            $txt = "<p>Bagi calon mahasiswa yang telah melakukan konfirmasi sebagai penerima beasiswa parsial, jika mengajukan pengunduran diri maka akan dikenakan biaya administrasi Rp.10.000.000,- dan biaya studi semester I yang sudah dibayar tidak akan dikembalikan</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);


            $pdf->WriteTag(0, 4, '<p><b>C.	KEASLIAN DOKUMEN KELULUSAN SMA/SEDERAJAT</b></p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>Dengan ini menyatakan bahwa saya adalah benar sedang menjalani pendidikan jenjang SMA /sederajat atau telah menyelesaikan pendidikan jenjang SMA/Sederajat, sehingga apabila diketahui bahwa dokumen kelulusan SMA/Sederajat atas nama saya tidak benar, maka saya bersedia menerima keputusan dari pihak PPM School of Management untuk membatalkan penerimaan mahasiswa baru atas nama saya, serta saya berjanji untuk tidak menarik kembali pembayaran yang sudah dilakukan.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p><b>D. KEPATUHAN TERHADAP ATURAN YANG BERLAKU DI PPM SoM</b></p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>Dengan ini menyatakan bahwa pada saat saya dinyatakan sebagai mahasiswa PPM SoM maka saya bersedia menaati Tata Tertib Kehidupan Kampus, Aturan Umum Bidang Akademik, dan semua peraturan yang berlaku di lingkungan PPM SoM.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Bahwa saya memahami ketentuan dan kebijakan yang tercantum di atas dan apabila saya melanggarnya, saya secara sadar untuk menerima sanksi-sanksi yang telah ditentukan menurut kebijakan diatas, termasuk dikeluarkan dari PPM SoM tanpa menuntut apapun termasuk pengembalian atau pengalihan dana yang sudah dibayarkan.</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Surat Perjanjian ini kami buat dalam keadaan sehat jasmani dan rohani tanpa terpengaruh atau paksaan dari pihak manapun, dan dapat diberlakukan selama saya menjadi mahasiswa PPM SoM dan bersedia menerima sanksi apapun bila melanggar perjanjian diatas.</p>');
            $pdf->Ln(4);

            $pdf->Cell(89.4, 4, 'Mengetahui Orang Tua / Wali');
            $pdf->MultiCell(0, 4, 'Yang Membuat Pernyataan', 0, 'R');
            $pdf->Cell(89.4, 4, 'Calon Mahasiswa');
            $pdf->MultiCell(0, 4, 'Calon Mahasiswa', 0, 'R');
            $pdf->Cell(89.4, 16, '');
            $pdf->MultiCell(0, 16, 'Meterai Rp. 6.000,-', 0, 'R');
            $pdf->Cell(89.4, 4, '(....................................................)');
            $pdf->MultiCell(0, 4, '(....................................................)', 0, 'R');
            $pdf->Ln(8);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->Cell(0, 4, 'Mengetahui', 0, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(0, 4, 'KETUA SEKOLAH TINGGI MANAJEMEN PPM', 0, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Calibri', '', 10);
            // $pdf->Cell(0, 4, 'Prof. Bramantyo Djohanputro, M.B.A. Ph.D', 0, 0, 'C');
            $pdf->Cell(0, 4, 'Dr. Pepey Riawati Kurnia, M.M., CPM, CAC, CODP', 0, 0, 'C');
            $pdf->Ln(8);
            $pdf->Cell(0, 4, '* Mohon lembaran ini di fotokopi sebanyak 2 lembar setelah ditanda-tangani di atas meterai ');
            $pdf->Ln();
            $pdf->Cell(0, 4, '** Mohon simpan bukti ini sebagai bukti.');
        }

        return $pdf;
    }

    public function create_acceptance_letter_s2($admission)
    {
        // print_r($admission);
        $total = 0;
        $total_potongan = 0;
        $total_angsuran = 0;
        $development = 0;

        foreach ($admission->biaya as $biaya) {
            $total += $biaya->jumlah * $biaya->sign;
        }

        foreach ($admission->discounts as $biaya) {
            $total += $biaya->jumlah * $biaya->sign;
            $total_potongan +=  $biaya->jumlah;
        }


        $this->load->library('PDF_WriteTag');

        $pdf = new PDF_WriteTag();
        // $pdf = new setasign\Fpdi\Fpdi();

        $pdf->setMargins(31.75, 45.72, 31.75);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        $pdf->AddPage();

        // Stylesheet
        $pdf->SetStyle("b", "Calibri", "B", 0, "0,0,0");
        $pdf->SetStyle("i", "Times", "I", 0, "0,0,0");
        $pdf->SetStyle("p", "Calibri", "N", 10, "0,0,0");

        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

        if ($admission->status == 400) {
            $pdf->WriteTag(0, 4, '<p>Jakarta, ' . date('j F Y', strtotime($admission->acceptance_date)) . '</p>');
            $pdf->Ln(4);
            if ($admission->nama_program_studi == 'SARJANA AKUNTANSI BISNIS') {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SAB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            } else {
                $pdf->WriteTag(0, 4, '<p>No     : ' . $admission->id_admission . '/SMB/STM-PPM/' . numberToRoman(date('n')) . '/' . date('y') . '</p>');
            }
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p>Kepada Yth.</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->salutation_short $admission->full_name</b></p>");
            if ($admission->employment_position) {
                $pdf->Ln();
                $pdf->WriteTag(0, 4, "<p><b>$admission->employment_position</b></p>");
            }
            if ($admission->employment_company_name) {
                $pdf->Ln();
                $pdf->WriteTag(0, 4, "<p><b>$admission->employment_company_name</b></p>");
            }
            $pdf->Ln();
            $pdf->WriteTag(0, 4, "<p><b>$admission->address_1</b></p>");
            if ($admission->address_2) {
                $pdf->Ln();
                $pdf->WriteTag(0, 4, "<p><b>$admission->address_2</b></p>");
            }
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Dengan hormat,</p>');
            $pdf->Ln();
            $txt = "<p>Setelah mengkaji hasil seleksi, Panitia Penerimaan Program $admission->nama_program_studi PPM School of Management memutuskan untuk menerima $admission->salutation sebagai peserta program $admission->nama_program_studi PPM School of Management Angkatan $admission->nama_angkatan tahun ajaran $admission->tahun_ajaran yang akan dimulai bulan $admission->mulai_kuliah $admission->tahun.";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);

            if ($admission->program_studi != 5) {
                $txt = "<p>Berdasarkan hasil tes seleksi, Saudara $admission->full_name mendapatkan beasiswa prestasi sebesar Rp " . number_format($total_potongan, 0, ',', '.');
                $pdf->WriteTag(0, 4, $txt, 0, "J");
                $pdf->Ln(4);
            }

            $txt = "<p>Kami berharap keikutsertaan $admission->salutation dapat membantu usaha pengembangan diri dalam bidang Manajemen melalui program $admission->nama_program_studi PPM School of Management.  Untuk kelengkapan administrasi, kami mohon agar mengisi surat konfirmasi keikutsertaan terlampir secepatnya setelah tanggal pengumuman kelulusan.";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);

            $txt = "<p>Kami ucapkan selamat atas keberhasilan $admission->salutation yang telah lulus proses seleksi dan terima kasih atas kerjasamanya.";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(8);

            $txt = "<p>Hormat kami,</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(16);//fadli
            $pdf->Image(FCPATH . 'assets/backoffice/img/ratna2.png', 32, 140);

            $txt = "<p>Martdian Ratna Sari, S.E., M.Sc</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();
            $txt = "<p>Koordinator Kemahasiswaan</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln(4);


            $pdf->AddPage();
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

            $pdf->WriteTag(0, 4, '<p><b>PELAKSANAAN PROGRAM</b></p>', 0);

            // $pdf->WriteTag(0, 4, '<p></p>');
            $pdf->Ln(4);
            $pdf->SetFont('Calibri', '', 10);
            $pdf->Cell(42, 4, 'Pembukaan Kelas', 0);
            $pdf->Cell(0, 4, ': ', 0);
            $pdf->Ln();
            $pdf->Cell(42, 4, 'Waktu Kuliah', 0);
            $pdf->Cell(0, 4, ': ', 0);
            $pdf->Ln(8);




            $pdf->WriteTag(0, 4, '<p><b>BIAYA & PEMBAYARAN</b></p>', 0);
            $pdf->Ln();

            $pdf->SetFont('Calibri', 'B', 10);
            $widths = [25];
            $aligns = ['C'];
            $titles = ['CARA PEMBAYARAN'];

            if ($admission->nama_program_studi == 'MAGISTER MANAJEMEN WIJAWIYATA MANAJEMEN') {
                $master_angsuran = [1, 4];
                $master_konfirmasi = [10000000, 10000000];
                $master_tambahan = [0, 6000000];
            } else {
                $master_angsuran = [1, 4, 6];
                $master_konfirmasi = [10000000, 10000000, 10000000];
                $master_tambahan = [0, 5000000, 10000000];
            }

            $datetime1 = date_create($admission->tgl_seleksi);
            $datetime2 = date_create('2022-09-01');
            $interval = date_diff($datetime1, $datetime2);
            $angsuran_interval =  $interval->format('%m');

            $angsuran = [];
            $konfirmasi = [];
            $angsuran_per_bulan = [];
            $total_angsuran = [];

            $max_angsuran = 1;

            foreach ($master_angsuran as $i => $jenis_angsuran) {
                $max_angsuran = $jenis_angsuran;

                $angsuran[] = $jenis_angsuran;
                $konfirmasi[] = $master_konfirmasi[$i];
                if ($jenis_angsuran == 1) {
                    $angsuran_per_bulan[] = ($total - $master_konfirmasi[$i]);
                } else {
                    $angsuran_per_bulan[] = ($total + $master_tambahan[$i] - $master_konfirmasi[$i]) / $jenis_angsuran;
                }
            }

            foreach ($master_angsuran as $i => $jenis_angsuran) {
                // if ($jenis_angsuran <= $angsuran_interval) {
                $widths[] = 80 / count($angsuran);
                $aligns[] = 'C';
                $titles[] = 'ANGSURAN ' . $jenis_angsuran . 'x';
                // }
            }
            $widths[] = 47;
            $aligns[] = 'C';
            $titles[] = 'BATAS WAKTU';

            $pdf->SetWidths($widths);
            $pdf->SetAligns($aligns);
            $pdf->Row($titles);

            $confirmation_row = [
                'Konfirmasi',
            ];
            foreach ($konfirmasi as $biaya_konfirmasi) {
                $confirmation_row[] = number_format($biaya_konfirmasi, '2', ',', '.');
            }
            $confirmation_row[] = '';
            $pdf->Row($confirmation_row);


            // $acceptance_date = date('Y-m-d', strtotime($admission->acceptance_date));
            $acceptance_date = date('Y-m-d', strtotime('2022-07-03'));
            $first_intake = date('Y-m-d', mktime(0, 0, 0, 3, 31, date('Y')));
            $second_intake = date('Y-m-d', mktime(0, 0, 0, 10, 31, date('Y')));
            $loop = true;

            $first_date = '';

            do {
                if ($acceptance_date <= $first_intake) {
                    $loop = false;
                    $first_date = $first_intake;
                } elseif ($acceptance_date <= $second_intake) {
                    $loop = false;
                    $first_date = $second_intake;
                } else {
                    $first_intake = date("Y-m-d", strtotime($first_intake . " +1 year"));
                    $second_intake = date("Y-m-d", strtotime($second_intake . " +1 year"));
                }
            } while ($loop);

            $first_date = date('Y-m-01', strtotime($first_date));

            for ($i = 1; $i <= $max_angsuran; $i++) {
                $rows = ['Angsuran ' . $i];


                for ($j = 0; $j < count($angsuran); $j++) {
                    if ($angsuran[$j] == 1) {
                        if ($i == 1) {
                            $rows[] = number_format($angsuran_per_bulan[$j], '2', ',', '.');
                        } else {
                            $rows[] = '';
                        }
                    } else {
                        if ($i == 1 || $i <= $angsuran[$j]) {
                            $rows[] = number_format($angsuran_per_bulan[$j], '2', ',', '.');
                        } else {
                            $rows[] = '';
                        }
                    }
                }
                if ($first_date) {
                    $rows[] = date("t-m-Y", strtotime($first_date));
                    $first_date = date("Y-m-01", strtotime($first_date . " +4 month"));
                } else {
                    $rows[] = '';
                }


                $pdf->Row($rows);
            }

            $total_rows = [''];
            foreach ($master_angsuran as $i => $jenis_angsuran) {
                if ($jenis_angsuran == 1) {
                    $total_rows[] = number_format($total, '2', ',', '.');
                    $total_angsuran[] = $total;
                } else {
                    $total_rows[] = number_format($total + $master_tambahan[$i], '2', ',', '.');
                    $total_angsuran[] = $total + $master_tambahan[$i];
                }
            }
            $total_rows[] = '';
            $pdf->Row($total_rows);


            //old

            // $widths = [30, 30];
            // $aligns = ['C', 'C'];
            // $titles = ['KETERANGAN', 'WAKTU PEMBAYARAN'];
            // foreach ($admission->skema as $skema) {
            //     $widths[] = 90 / count($admission->skema);
            //     $aligns[] = 'R';
            //     $titles[] = $skema->jenis_pembayaran == 1 ? 'TUNAI' : 'ANGSURAN ' . $skema->jumlah_angsuran . 'x';
            // }

            // $pdf->SetWidths($widths);
            // $pdf->SetAligns($aligns);
            // $pdf->Row($titles);

            // $aligns = ['L', 'C'];
            // $total_angsuran = [];

            // foreach ($admission->skema as $skema) {
            //     $aligns[] = 'R';
            //     $total_angsuran[] = 0;
            // }
            // $pdf->SetAligns($aligns);
            // $pdf->SetFont('Calibri', '', 10);

            // // print_r($admission->skema);
            // $rows = max(array_column($admission->skema, 'jumlah_angsuran'));

            // $konfirmasi = [];

            // for ($i = 0; $i <= $rows; $i++) {
            //     if (isset($admission->skema[0]->detail[$i]) || isset($admission->skema[1]->detail[$i])) {
            //         $skema = isset($admission->skema[0]->detail[$i]) ? $admission->skema[0]->detail[$i] : $admission->skema[1]->detail[$i];
            //         if ($i == 0) {
            //             $pembayaran = 'Konfirmasi';
            //             $waktu = 'Maksimal ' . $skema->waktu . ' hari setelah menerima Surat Hasil Seleksi';
            //         } elseif ($i == 1) {
            //             $pembayaran = 'Angsuran ' . $i;

            //             if ($skema->waktu) {
            //                 $waktu = date('j F Y', strtotime($admission->tgl_seleksi . ' + ' . $skema->waktu . ' days'));
            //             } else {
            //                 $waktu = date('j F Y', strtotime($skema->jatuh_tempo));
            //             }
            //         } else {
            //             $pembayaran = 'Angsuran ' . $i;
            //             if ($skema->waktu) {
            //                 $waktu = date('j F Y', strtotime($admission->tgl_seleksi . ' + ' . $skema->waktu . ' days'));
            //             } else {
            //                 $waktu = date('j F Y', strtotime($skema->jatuh_tempo));
            //             }
            //         }
            //         $content = [
            //             $pembayaran,
            //             $waktu,
            //         ];

            //         foreach ($admission->skema as $j => $sk) {
            //             if (isset($sk->detail[$i])) {
            //                 if ($i == 0) {
            //                     $konfirmasi[$j] = $sk->detail[$i]->jumlah;
            //                     $jumlah = 0;
            //                     if ($sk->detail[$i]->jumlah) {
            //                         $jumlah = $sk->detail[$i]->jumlah;
            //                     } elseif ($sk->detail[$i]->persentase) {
            //                         $jumlah = ($sk->detail[$i]->persentase / 100) * ($sk->jumlah_total);
            //                     }
            //                 } else {
            //                     // echo "$konfirmasi ";
            //                     $jumlah = 0;
            //                     if ($sk->jenis_pembayaran == 1) {
            //                         $jumlah = $sk->jumlah_total - $konfirmasi[$j];
            //                     } else {
            //                         if ($sk->detail[$i]->jumlah) {
            //                             $jumlah = $sk->detail[$i]->jumlah - $konfirmasi[$j];
            //                         } elseif ($sk->detail[$i]->persentase) {
            //                             $jumlah = ($sk->detail[$i]->persentase / 100) * ($sk->jumlah_total - $konfirmasi[$j]);
            //                         }
            //                     }
            //                 }
            //                 $content[] = number_format($jumlah, 0, ',', '.');
            //                 $total_angsuran[$j] += $jumlah;
            //             } else {
            //                 $content[] = '';
            //             }
            //         }
            //         $pdf->Row($content);
            //     }
            // }


            // $widths = ['60'];
            // $aligns = ['R'];
            // $content = ['TOTAL BIAYA'];

            // foreach ($admission->skema as $i => $sk) {
            //     $widths[] = 90 / count($admission->skema);;
            //     $aligns[] = 'R';
            //     $content[] =  number_format($total_angsuran[$i], 0, ',', '.');
            // }

            // $pdf->SetWidths($widths);
            // $pdf->SetAligns($aligns);
            // $pdf->Row($content);
            // }

            $pdf->Ln(8);
            $pdf->WriteTag(0, 4, '<p>Biaya di atas meliputi</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- E-Book/Modul</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- Biaya pendidikan/bimbingan <b>(termasuk ujian, supervisi, tesis)</b></p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- Ijazah dan Transkrip</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- Keanggotaan Perpustakaan dan Laboratorium Komputer selama studi</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- Makan dan snack</p>');
            $pdf->Ln(4);

            $txt = "<p>Pembayaran dapat melalui transfer ke rekening:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();
            $pdf->Cell(7, 4, '1.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank Mandiri</b> dengan nomor rekening <b>1030085288583</b> atas nama <b>Yayasan Pendidikan & Pembinaan Manajemen</b>, atau</p>', 0);
            $pdf->Ln();

            $pdf->Cell(7, 4, '2.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank BCA</b> dengan nomor rekening <b>6860038801</b> atas nama <b>Yayasan PPM</b>.</p>', 0);
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Bukti pembayaran mohon dapat dikirimkan melalui email ke Adimisi PPM dan Keuangan PPM (Ibu Hilda dan Sdri.Febi) dengan mencantumkan nama peserta dan program yang diikuti:</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- mm-ppm@ppm-manajemen.ac.id</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- tet@ppm-manajemen.ac.id</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- kasir_stm@ppm-manajemen.ac.id</p>');
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Catatan: <i>Keterlambatan pembayaran angsuran dari jadwal di atas dikenakan denda 1,5% per bulan atas sisa angsuran dan tidak diperkenankan mengikuti perkuliahan</i></p>');
            $pdf->Ln();

            $pdf->AddPage();
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-school.png', 45, 20);
            $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

            $pdf->WriteTag(0, 4, '<p><b>SURAT KONFIRMASI</b></p>', 0, 'C');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p><b>' . $admission->nama_program_studi . '</b></p>', 0, 'C');
            $pdf->Ln(4);
            $pdf->SetFont('Calibri', '', 10);
            $pdf->Cell(30, 4, 'Nama', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, 'Jabatan', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, 'Nama Perusahaan', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, '', 0);
            $pdf->Cell(5, 4, '', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, 'Alamat Perusahaan', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, 'Telepon', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Cell(30, 4, 'E-mail', 0);
            $pdf->Cell(5, 4, ':', 0);
            $pdf->Cell(0, 4, '', 'B');
            $pdf->Ln();
            $pdf->Ln(8);

            $pdf->WriteTag(0, 4, '<p>bersedia menjadi peserta Program ' . $admission->nama_program_studi . ' PPM School of Management Angkatan 24 tahun ajaran 2020-2022, dengan memenuhi segala ketentuan-ketentuan yang berlaku</p>');
            $pdf->Ln(4);
            $pdf->WriteTag(0, 4, '<p><b>Biaya program harap ditagihkan secara (Pilih salah satu dengan melingkari) :</b></p>');
            $pdf->Ln(4);

            $pdf->SetFont('Calibri', '', 10);
            // $pdf->Cell(5, 4, '1.', 0);
            // $pdf->Cell(30, 4, 'Tunai', 0);
            // $pdf->Cell(5, 4, 'Rp. ' . number_format($total_tunai, 0, ',', '.'), 0);
            // $pdf->Ln(6);

            // if (count($admission->skema) == 1) {
            //     $pdf->Cell(5, 4, '2.', 0);
            //     $pdf->Cell(30, 4, 'Angsuran 4 kali', 0);
            //     $pdf->Cell(5, 4, 'Rp. ' . number_format($total_angsuran, 0, ',', '.'), 0);
            //     $pdf->Ln(6);
            // } else {
            // foreach ($admission->skema as $i => $skema) {
            //     $pdf->Cell(5, 4, ($i + 1) . '.', 0);
            //     $pdf->Cell(30, 4, $skema->jenis_pembayaran == 1 ? 'Tunai' : 'Angsuran ' . $skema->jumlah_angsuran . ' kali', 0);
            //     $pdf->Cell(5, 4, 'Rp. ' . number_format($total_angsuran[$i], 0, ',', '.'), 0);
            //     $pdf->Ln(6);
            // }

            foreach ($master_angsuran as $i => $jenis_angsuran) {
                // if ($jenis_angsuran <= $angsuran_interval) {
                // $widths[] = 80 / count($angsuran);
                // $aligns[] = 'C';
                // $titles[] = 'ANGSURAN ' . $jenis_angsuran . 'x';

                $pdf->Cell(5, 4, ($i + 1) . '.', 0);
                $pdf->Cell(30, 4, 'ANGSURAN ' . $jenis_angsuran . 'x', 0);
                $pdf->Cell(5, 4, 'Rp. ' . number_format($total_angsuran[$i], 0, ',', '.'), 0);
                $pdf->Ln(6);
            }
            // }
            $pdf->WriteTag(0, 4, '<p>Sebagai konfirmasi keikutsertaan program ini saya bersedia dikenakan biaya sebesar <b>Rp 10.000.000,-</b> (sepuluh juta rupiah) yang akan diperhitungkan ke dalam biaya program tersebut. </p>');
            $pdf->Ln(4);
            $txt = "<p>Pembayaran dapat melalui transfer ke rekening:</p>";
            $pdf->WriteTag(0, 4, $txt, 0, "J");
            $pdf->Ln();
            $pdf->Cell(7, 4, '1.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank Mandiri</b> dengan nomor rekening <b>1030085288583</b> atas nama <b>Yayasan Pendidikan & Pembinaan Manajemen</b>, atau</p>', 0);
            $pdf->Ln();

            $pdf->Cell(7, 4, '2.', 0, 'R');
            $pdf->WriteTag(0, 4, '<p><b>Bank BCA</b> dengan nomor rekening <b>6860038801</b> atas nama <b>Yayasan PPM</b>.</p>', 0);
            $pdf->Ln(4);

            $pdf->WriteTag(0, 4, '<p>Bukti pembayaran mohon dapat dikirimkan melalui email ke Adimisi PPM dan Keuangan PPM (Ibu Hilda dan Sdr. Febi) dengan mencantumkan nama peserta dan program yang diikuti:</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- mm-ppm@ppm-manajemen.ac.id</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- tet@ppm-manajemen.ac.id</p>');
            $pdf->Ln();
            $pdf->WriteTag(0, 4, '<p>- kasir_stm@ppm-manajemen.ac.id</p>');
            $pdf->Ln(4);

            $pdf->Cell(89.4, 4, 'Mengetahui *)');
            $pdf->MultiCell(0, 4, 'Jakarta, ..........................................', 0, 'C');
            $pdf->Cell(89.4, 4, 'Pejabat Perusahaan');
            $pdf->MultiCell(0, 4, 'Yang Menyatakan', 0, 'C');
            $pdf->Cell(89.4, 16, '');
            $pdf->MultiCell(0, 16, '', 0, 'R');
            $pdf->Cell(89.4, 4, '(....................................................)');
            $pdf->MultiCell(0, 4, '(....................................................)', 0, 'C');
            $pdf->Ln(8);
            $pdf->Cell(0, 4, '*) Apabila dibiayai Perusahaan');
        }

        return $pdf;
    }

    public function acceptances_letter_send($id_admission, $write = 0)
    {
        $this->acceptances_letter($id_admission, 1);

        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, users.*, personal_informations.*, nama_program_studi, jenis_program_studi, nama_seleksi, beasiswa, nama_kota, employment_company_name, employment_position, tgl_seleksi_option, angkatan.*')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'users.id = personal_informations.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
            ->join('users_employments', 'users_employments.id_user = users.id', 'left')
            ->join('angkatan', 'angkatan.id_angkatan = admissions.id_angkatan', 'left')
            ->get_by($filter);
        $this->data['admission'] = $admission;

        $acceptance = $this->Acceptances_model->get_by('id_admission', $id_admission);
        $this->data['acceptance'] = $acceptance;

        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/result.tpl.php', $this->data, true);
        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($admission->email);
        $this->email->message($message);

        if ($admission->beasiswa) {
            if ($admission->status == 400 || $admission->status == 401) {
                $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Hasil Seleksi PPM School of Management ');

                $filename = 'SL - ' . $admission->full_name;
                if ($admission->status != 400) {
                    $filename = 'STL - ' . $admission->full_name;
                }

                $output_file = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/' . $filename . ".pdf";
                $this->email->attach($output_file);
            } elseif ($admission->status == 402) {
                $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Lulus Tahap 1 ');
            } elseif ($admission->status == 403) {
                $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Lulus Tahap 2');
            }
        } else {
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Hasil Seleksi PPM School of Management ');

            $filename = 'SL - ' . $admission->full_name;
            if ($admission->status != 400) {
                $filename = 'STL - ' . $admission->full_name;
            }

            $output_file = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/' . $filename . ".pdf";
            $this->email->attach($output_file);
        }

        $user_email = $this->email->send();

        $parent_email = $admission->father_email ? $admission->father_email : ($admission->mother_email ? $admission->mother_email : '');

        if ($parent_email) {
            $message = $this->load->view('home/email/result_parent.tpl.php', $this->data, true);
            $this->email->clear();
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($parent_email);
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Hasil Seleksi PPM School of Management ');
            $this->email->message($message);

            $filename = 'SL - ' . $admission->full_name;
            if ($admission->status != 400) {
                $filename = 'STL - ' . $admission->full_name;
            }

            $output_file = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/' . $filename . ".pdf";
            $this->email->attach($output_file);

            $parent_email = $this->email->send();
        }


        if ($user_email == TRUE) {
            $this->Admissions_model->update($id_admission, ['result_sent' => 1]);
            // return true;
        }
        redirect('backoffice/admissions/acceptances', 'refresh');
    }

    public function education_history_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_educations_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function employment_history_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_employments_model->get_many_by($filter);

        echo json_encode($response);
    }


    public function family_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_families_model->get_many_by($filter);

        echo json_encode($response);
    }


    public function achievement_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_achievements_model->get_many_by($filter);

        echo json_encode($response);
    }


    public function organization_history_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_organizations_model->get_many_by($filter);

        echo json_encode($response);
    }


    public function education_informal_history_data($id_user)
    {
        $response = new stdClass();

        $filter = [
            'id_user' => $id_user,
        ];

        $response->rows = $this->Users_educations_informal_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function create_payment_receipt($id_admission, $write = 0)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, payment,tes_seleksi, biaya, channel_type, channel_name, full_name, email, id_payment, payment_code, payment_status, payment_time, payment_receipt, payment_voucher_code, id_payment, tahun_ajaran')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission and payments.payment_type = 1', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->join('angkatan', 'angkatan.id_angkatan = admissions.id_angkatan', 'left')
            ->get_by($filter);

        if ($admission) {
            $payment = $this->Payments_model->get_by('id_admission', $admission->id_admission);
            $seleksi = $this->Seleksi_model
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get($admission->seleksi);
            $this->data['seleksi'] = $seleksi;
            // $seleksi_payment = $this->Seleksi_payments_model->get_by('id_seleksi', $seleksi->id_seleksi);

            $payment_channels = $this->Seleksi_payments_channels_model
                ->join('payments_channels', 'payments_channels.id_payment_channel = seleksi_payments_channels.id_payment_channel', 'left')
                ->get_many_by('id_seleksi', $seleksi->id_seleksi);

            $this->data['payment_channels'] = $payment_channels;

            $admission_fee = $seleksi->biaya;
            $payment_code = $payment->payment_code;
            $total_fee = $seleksi->biaya + $payment->payment_code;
        }

        $pdf = new setasign\Fpdi\Fpdi();

        $pageCount = $pdf->setSourceFile(FCPATH . DOCUMENTS_FOLDER . 'bukti_pembayaran.pdf');
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['width'] > $size['height']) {
                $pdf->AddPage('L', array($size['width'], $size['height']));
            } else {
                $pdf->AddPage('P', array($size['width'], $size['height']));
            }

            $pdf->useTemplate($templateId);
            $pdf->SetFont('Helvetica', '', 7);

            if ($pageNo == 1) {
                // //nama nasabah
                $pdf->SetXY(83, 36);
                $pdf->Cell(90, 3.5, $admission->id_payment, 0);

                $pdf->SetXY(83, 43);
                $pdf->Cell(90, 3.5, $admission->full_name, 0);

                $pdf->SetXY(83, 49.5);
                $pdf->MultiCell(90, 3.5, ucfirst($this->terbilang($total_fee) . ' rupiah'), 0);

                $pdf->SetXY(83, 60);
                $pdf->MultiCell(90, 3.5, 'Pembayaran biaya pendaftaran Program ' . $admission->nama_program_studi . ' Angkatan ' . $admission->tahun_ajaran, 0);

                $pdf->SetXY(83, 97.5);
                $pdf->Cell(55, 3.5, number_format($total_fee, 0, ',', '.'), 0);

                $pdf->SetXY(112, 118);
                $pdf->Cell(26, 3.5, $admission->channel_name, 0, 0, 'C');

                $pdf->SetXY(138.5, 118);
                $pdf->Cell(35, 3.5, $admission->payment_time ? date('d/m/Y', $admission->payment_time) : '', 0, 0, 'C');
            }
        }

        if ($write) {
            if (!file_exists(FCPATH . DOCUMENTS_FOLDER . $id_admission)) {
                mkdir(FCPATH . DOCUMENTS_FOLDER . $id_admission);
            }
            $pdf->Output(FCPATH . DOCUMENTS_FOLDER . $id_admission . '/KWITANSI PENDAFTARAN ' . $admission->full_name . '.pdf', 'F');
        } else {
            $pdf->Output();
        }
    }

    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil;
    }

    function download()
    {
        $columns = [
            ['title' => 'NO.'],
            ['title' => 'NAMA', 'column' => 'full_name'],
            ['title' => 'SUMBER', 'column' => 'CONCAT(nama_seleksi," ",IF(tgl_seleksi != "", tgl_seleksi,"")) sumber', 'alias' => 'sumber'],
            ['title' => 'L/P', 'column' => 'IF(gender=1,"L","P") gender_text', 'alias' => 'gender_text'],
            ['title' => 'NO HP', 'column' => 'mobile_phone'],
            ['title' => 'EMAIL', 'column' => 'email'],
            ['title' => 'TEMPAT LAHIR', 'column' => 'birthplace'],
            ['title' => 'TANGGAL LAHIR', 'column' => 'birthdate'],
            ['title' => 'PROGRAM', 'column' => 'nama_program_studi'],
            ['title' => 'ALAMAT RUMAH', 'column' => 'address_1'],
            ['title' => 'KOTA', 'column' => 'nama_kota'],
            ['title' => 'PROVINSI', 'column' => 'nama_provinsi'],
            ['title' => 'KODE POS', 'column' => 'postal_code'],
            ['title' => 'TANGGAL TES TAHAP 1', 'column' => 'tgl_seleksi'],
            ['title' => 'SEKOLAH', 'column' => 'education_school_name'],
            ['title' => 'ALAMAT SEKOLAH', 'column' => 'education_city'],
            ['title' => 'KONSENTRASI', 'column' => 'education_major'],
            ['title' => 'TAHUN LULUS', 'column' => 'education_year_to'],
            ['title' => 'NAMA AYAH', 'column' => 'father_name'],
            ['title' => 'NO HP', 'column' => 'father_phone'],
            ['title' => 'PEKERJAAN', 'column' => 'IF (father_working_status = 1, father_working_company, "-") father_occupation', 'alias' => 'father_occupation'],
            ['title' => 'E-MAIL', 'column' => 'father_email'],
            ['title' => 'JABATAN', 'column' => 'IF (father_working_status = 1, father_working_position, "-") father_position', 'alias' => 'father_position'],
            ['title' => 'NAMA IBU', 'column' => 'mother_name'],
            ['title' => 'NO HP', 'column' => 'mother_phone'],
            ['title' => 'PEKERJAAN', 'column' => 'IF (mother_working_status = 1, mother_working_company, "-") mother_occupation', 'alias' => 'mother_occupation'],
            ['title' => 'E-MAIL', 'column' => 'mother_email'],
            ['title' => 'JABATAN', 'column' => 'IF (mother_working_status = 1, mother_working_position, "-") mother_position', 'alias' => 'mother_position'],
        ];
        $select = implode(',', array_column($columns, 'column'));
        $filter = [
            // 'program_studi' => 1,
            // 'seleksi' => 1,
        ];
        $rows = $this->Admissions_model
            ->select($select)
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('personal_informations', 'personal_informations.id_user = admissions.id_user', 'left')
            ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
            ->join('provinsi', 'provinsi.kode_provinsi = personal_informations.province', 'left')
            ->join('users_educations', 'users_educations.id_user = admissions.id_user', 'left')
            ->get_many_by($filter);
        // print_r($rows);
        // die();

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        foreach ($columns as $i => $column) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $column['title']);
        }
        foreach ($rows as $i => $row) {
            $row_index = $i + 2;
            foreach ($columns as $j => $column) {
                $value = '';
                if ($j == 0) {
                    $value = $i + 1;
                } else {
                    if (isset($column['alias'])) {
                        $value = $row->{$column['alias']};
                    } elseif (isset($column['column'])) {
                        $value = $row->{$column['column']};
                    }
                }
                $col_index = $j + 1;
                $col_string = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_index);
                $sheet->setCellValueByColumnAndRow($col_index, $row_index, $value);
                $sheet->getColumnDimension($col_string)->setAutoSize(true);
                // $sheet->getStyle($col_string . $row_index)
                //     ->getNumberFormat()
                //     ->setFormatCode(
                //         \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
                //     );
            }
        }

        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'PMB-PPM';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file
    }

    function download_acceptances()
    {
        $columns = [
            ['title' => 'NO.'],
            ['title' => 'NAMA', 'column' => 'full_name'],
            ['title' => 'STATUS', 'column' => 'IF (status >= 400, "LULUS", "-") status', 'alias' => 'status'],
            ['title' => 'SUMBER', 'column' => 'CONCAT(nama_seleksi," ",IF(tgl_seleksi != "", tgl_seleksi,"")) sumber', 'alias' => 'sumber'],
            ['title' => 'L/P', 'column' => 'IF(gender=1,"L","P") gender_text', 'alias' => 'gender_text'],
            ['title' => 'NO HP', 'column' => 'mobile_phone'],
            ['title' => 'EMAIL', 'column' => 'email'],
            ['title' => 'TEMPAT LAHIR', 'column' => 'birthplace'],
            ['title' => 'TANGGAL LAHIR', 'column' => 'birthdate'],
            ['title' => 'PROGRAM', 'column' => 'nama_program_studi'],
            ['title' => 'ALAMAT RUMAH', 'column' => 'address_1'],
            ['title' => 'KOTA', 'column' => 'nama_kota'],
            ['title' => 'PROVINSI', 'column' => 'nama_provinsi'],
            ['title' => 'KODE POS', 'column' => 'postal_code'],
            ['title' => 'TANGGAL TES TAHAP 1', 'column' => 'tgl_seleksi'],
            ['title' => 'SEKOLAH', 'column' => 'education_school_name'],
            ['title' => 'ALAMAT SEKOLAH', 'column' => 'education_city'],
            ['title' => 'KONSENTRASI', 'column' => 'education_major'],
            ['title' => 'TAHUN LULUS', 'column' => 'education_year_to'],
            ['title' => 'NAMA AYAH', 'column' => 'father_name'],
            ['title' => 'NO HP', 'column' => 'father_phone'],
            ['title' => 'PEKERJAAN', 'column' => 'IF (father_working_status = 1, father_working_company, "-") father_occupation', 'alias' => 'father_occupation'],
            ['title' => 'E-MAIL', 'column' => 'father_email'],
            ['title' => 'JABATAN', 'column' => 'IF (father_working_status = 1, father_working_position, "-") father_position', 'alias' => 'father_position'],
            ['title' => 'NAMA IBU', 'column' => 'mother_name'],
            ['title' => 'NO HP', 'column' => 'mother_phone'],
            ['title' => 'PEKERJAAN', 'column' => 'IF (mother_working_status = 1, mother_working_company, "-") mother_occupation', 'alias' => 'mother_occupation'],
            ['title' => 'E-MAIL', 'column' => 'mother_email'],
            ['title' => 'JABATAN', 'column' => 'IF (mother_working_status = 1, mother_working_position, "-") mother_position', 'alias' => 'mother_position'],
        ];
        $select = implode(',', array_column($columns, 'column'));
        $filter = [
            // 'program_studi' => 1,
            // 'seleksi' => 1,
        ];

        $filter =  '(admissions.status = 301 OR admissions.status >= 400)';
        $rows = $this->Admissions_model
            ->select($select)
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('personal_informations', 'personal_informations.id_user = admissions.id_user', 'left')
            ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
            ->join('provinsi', 'provinsi.kode_provinsi = personal_informations.province', 'left')
            ->join('users_educations', 'users_educations.id_user = admissions.id_user', 'left')
            ->order_by('admissions.created_on')
            ->get_many_by($filter);

        // $rows = $this->Admissions_model
        //     ->select($select)
        //     ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
        //     ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
        //     ->join('personal_informations', 'personal_informations.id_user = admissions.id_user', 'left')
        //     ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
        //     ->join('provinsi', 'provinsi.kode_provinsi = personal_informations.province', 'left')
        //     ->join('users_educations', 'users_educations.id_user = admissions.id_user', 'left')
        //     ->get_many_by($filter);
        // print_r($rows);
        // die();

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        foreach ($columns as $i => $column) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $column['title']);
        }
        foreach ($rows as $i => $row) {
            $row_index = $i + 2;
            foreach ($columns as $j => $column) {
                $value = '';
                if ($j == 0) {
                    $value = $i + 1;
                } else {
                    if (isset($column['alias'])) {
                        $value = $row->{$column['alias']};
                    } elseif (isset($column['column'])) {
                        $value = $row->{$column['column']};
                    }
                }
                $col_index = $j + 1;
                $col_string = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_index);
                $sheet->setCellValueByColumnAndRow($col_index, $row_index, $value);
                $sheet->getColumnDimension($col_string)->setAutoSize(true);
                // $sheet->getStyle($col_string . $row_index)
                //     ->getNumberFormat()
                //     ->setFormatCode(
                //         \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
                //     );
            }
        }

        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'PMB-PPM-Penerimaan';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file
    }

    public function ticket($id_admission)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, personal_informations.*, nama_program_studi, nama_seleksi, tes_seleksi, biaya, channel_name, payment_status, payment_receipt')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'users.id = personal_informations.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);

        $angkatan = $this->Angkatan_model->get_by('id_program_studi', $admission->program_studi);
        $this->data['angkatan'] = $angkatan;

        $user_seleksi = $this->Users_seleksi_model->get_by('id_admission', $id_admission);
        $this->data['user_seleksi'] = $user_seleksi;

        $this->data['admission'] = $admission;
        $this->data['pdf'] = 0;

        $this->load->view('admission/ticket', $this->data);

        // $this->load->view('home/email/ticket.tpl.php', $this->data);


    }

    function download_admission($id_admission)
    {
        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, users.*, personal_informations.*, users.full_name, nama_program_studi, jenis_program_studi, nama_seleksi, nama_provinsi, nama_kota, employment_company_name, employment_position, tgl_seleksi_option, angkatan.*')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('personal_informations', 'users.id = personal_informations.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->join('provinsi', 'provinsi.kode_provinsi = personal_informations.province', 'left')
            ->join('kota', 'kota.kode_kota = personal_informations.city', 'left')
            ->join('users_employments', 'users_employments.id_user = users.id', 'left')
            ->join('angkatan', 'angkatan.id_angkatan = admissions.id_angkatan', 'left')
            ->get_by($filter);


        if ($admission) {
            $admission->educations = $this->Users_educations_model
                ->order_by('education_year_from', 'desc')
                ->get_many_by('id_user', $admission->id);

            $admission->organizations = $this->Users_organizations_model
                ->order_by('organization_year', 'desc')
                ->get_many_by('id_user', $admission->id);

            $admission->achievements = $this->Users_achievements_model
                ->order_by('achievement_year', 'desc')
                ->get_many_by('id_user', $admission->id);

            $admission->families = $this->Users_families_model
                ->get_many_by('id_user', $admission->id);

            $admission->educations_informal = $this->Users_educations_informal_model
                ->order_by('education_year_from', 'desc')
                ->get_many_by('id_user', $admission->id);

            $admission->employments = $this->Users_employments_model
                ->order_by('employment_year_from', 'desc')
                ->get_many_by('id_user', $admission->id);
        }


        // print_r($admission);

        $pdf = $this->create_admission_form($admission);


        if ($pdf) {
            // if ($write) {
            //     if (!file_exists(FCPATH . DOCUMENTS_FOLDER . $id_admission)) {
            //         mkdir(FCPATH . DOCUMENTS_FOLDER . $id_admission);
            //     }

            //     $filename = 'SL - ' . $admission->full_name;
            //     if ($admission->status != 400) {
            //         $filename = 'STL - ' . $admission->full_name;
            //     }
            //     $pdf->Output(DOCUMENTS_FOLDER . $id_admission . '/' . $filename . '.pdf', 'F');
            // } else {
            $pdf->Output();
            // }
        }
    }

    function create_admission_form($admission)
    {
        // print_r($admission);
        $filter = [
            'id_admission' => $admission->id_admission,
        ];
        $form_1 = $this->Admissions_model
            ->select('seleksi_form.*')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('seleksi_form', 'seleksi_form.id_seleksi = admissions.seleksi AND seleksi_form.type = 1', 'left')
            ->get($admission->id_admission);

        // print_r($form_1);
        $filter = [
            'id_admission' => $admission->id_admission,
        ];
        $form_2 = $this->Admissions_model
            ->select('seleksi_form.*')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('seleksi_form', 'seleksi_form.id_seleksi = admissions.seleksi AND seleksi_form.type = 2', 'left')
            ->get($admission->id_admission);
        // print_r($form_2);


        $forms = [];
        foreach ([$form_1, $form_2] as $a) {
            foreach ($a as $key => $value) {
                $forms[$key] = $value + ($forms[$key] ?? 0);
            }
        }
        $forms = (object) $forms;
        // $forms = (object) array_merge((array) $form_1, (array) $form_2);
        // print_r($forms);

        $this->load->library('PDF_WriteTag');

        $pdf = new PDF_WriteTag();
        // $pdf = new setasign\Fpdi\Fpdi();

        $margin_left = 25.75;
        $bullet_left = $margin_left - 6;

        $line_height = 6;
        $field_font_size = 10;
        $value_font_size = 9;

        $pdf->setMargins($margin_left, 45.72, 25);
        $pdf->AddFont('Calibri', '', 'calibri.php');
        $pdf->AddFont('Calibri', 'B', 'calibrib.php');


        $pdf->SetFont('Calibri', '', 10);
        $pdf->AddPage();

        // Stylesheet
        $pdf->SetStyle("b", "Calibri", "B", 0, "0,0,0");
        $pdf->SetStyle("p", "Calibri", "N", 10, "0,0,0");


        $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm-form.png', $margin_left, 10, 0, 15);
        // $pdf->Image(FCPATH . 'assets/backoffice/img/logo-ppm.png', 120, 22);

        // $pdf->SetX(60);
        $pdf->SetXY(90, 15);
        $pdf->SetFont('Calibri', 'B', 16);
        $pdf->MultiCell(100, 6, $admission->nama_program_studi . ' - ' . $admission->nama_angkatan, 0, "R");

        $pdf->Ln(20);
        $pdf->SetDrawColor(5, 52, 104);
        $pdf->SetFont('Calibri', 'BU', 16);
        $pdf->SetTextColor(5, 52, 104);
        $pdf->Cell(0, 4, 'FORMULIR PENDAFTARAN', 0, 1, "C");
        $pdf->Ln(4);

        // $pdf->Rect(155, 40, 30, 45);

        // $pdf->SetFillColor(255, 204, 0);
        // $pdf->SetX($bullet_left);
        // $pdf->Cell(5, 6, '', 0, 0, 'C', true);
        // $pdf->SetX($margin_left);
        // $pdf->SetFont('Calibri', 'BU', 14);
        // $pdf->SetTextColor(5, 52, 104);
        // $pdf->Cell(120, 6, 'PILIHAN PROGRAM STUDI');
        // $pdf->Ln(6);

        // $pdf->SetFont('Calibri', 'B', 10);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->Cell(120, 4, 'Mohon diisi sesuai dengan pilihan jurusan dibawah ini', 0, 0);
        // $pdf->Ln(6);

        // $pdf->SetFillColor(5, 52, 104);
        // $pdf->SetTextColor(255, 255, 255);
        // $pdf->Cell(50, 12, 'Program Studi', 1, 0, 'C', true);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->Cell(10, 4, $admission->id_program_studi == 1 ? 'V' : '', 1, 0, 'C');
        // $pdf->Cell(60, 4, 'Sarjana Manajemen Bisnis', 1);
        // $pdf->Ln(4);
        // $pdf->SetX($margin_left + 50);
        // $pdf->Cell(10, 4, $admission->id_program_studi == 2 ? 'V' : '', 1, 0, 'C');
        // $pdf->Cell(60, 4, 'Sarjana Akuntansi Bisnis', 1);
        // $pdf->Ln(4);
        // $pdf->SetX($margin_left + 50);
        // $pdf->Cell(10, 4, $admission->id_program_studi == 3 ? 'V' : '', 1, 0, 'C');
        // $pdf->Cell(60, 4, 'Sarjana Manajemen Bisnis Profesional', 1);
        // $pdf->Ln(8);

        $pdf->SetFillColor(255, 204, 0);
        $pdf->SetX($bullet_left);
        $pdf->Cell(5, 6, '', 0, 0, 'C', true);
        $pdf->SetX($margin_left);
        $pdf->SetFont('Calibri', 'BU', 14);
        $pdf->SetTextColor(5, 52, 104);
        $pdf->Cell(120, 6, 'DATA PERSONAL');
        $pdf->Ln(8);

        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->SetFillColor(5, 52, 104);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, $line_height, 'Nama Lengkap', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(120, $line_height, $admission->full_name, 1, 1);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'Tempat Tanggal Lahir', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        if ($admission->birthdate && $admission->birthplace) {
            $pdf->Cell(40, $line_height, $admission->birthplace . ', ' . strtoupper(date('j F Y', strtotime($admission->birthdate))), 1);
        } else {
            $pdf->Cell(40, $line_height, '', 1);
        }

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(30, $line_height, 'Jenis Kelamin', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);

        if ($admission->gender == 1) {
            $pdf->Cell(50, $line_height,  'LAKI-LAKI', 1, 1);
        } elseif ($admission->gender == 2) {
            $pdf->Cell(50, $line_height,  'PEREMPUAN', 1, 1);
        } else {
            $pdf->Cell(50, $line_height,  '', 1, 1);
        }

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'No Handphone', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(40, $line_height, $admission->mobile_phone, 1);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(30, $line_height, 'Telepon Rumah', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(50, $line_height, $admission->phone, 1, 1);


        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'E-mail', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(120, $line_height, $admission->email, 1, 1);


        // $pdf->SetTextColor(255, 255, 255);
        // $pdf->SetFont('Calibri', 'B', 10);
        // $pdf->Cell(40, $line_height, 'Agama', 1, 0, 'L', true);
        // $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetFont('Calibri', '', $value_font_size);
        // $pdf->Cell(40, $line_height, '', 1);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'Status', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(120, $line_height, '', 1, 0);

        $y = $pdf->GetY();
        $pdf->SetXY(67, $y + 0.75);
        $pdf->Cell(5, 0.75 * $line_height, '', 1, 0);
        $pdf->SetXY($pdf->GetX(), $y);
        $pdf->Cell(33,  $line_height, 'Menikah', 0, 0);

        $pdf->SetXY($pdf->GetX(), $y + 0.75);
        $pdf->Cell(5,  0.75 * $line_height, '', 1, 0);
        $pdf->SetXY($pdf->GetX(), $y);
        $pdf->Cell(40, $line_height, 'Belum Menikah', 0, 1);

        if ($admission->marital_status == 1) {
            $pdf->SetFont('ZapfDingbats', '', 12);

            $pdf->SetXY(67, $y);
            $pdf->Cell(0, $line_height, chr(52), 0, 1);

            $pdf->SetFont('Calibri', '', $value_font_size);
        } else {
            $pdf->SetFont('ZapfDingbats', '', 12);

            $pdf->SetXY(105, $y);
            $pdf->Cell(0, $line_height, chr(52), 0, 1);

            $pdf->SetFont('Calibri', '', $value_font_size);
        }

        // $pdf->Ln($line_height);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, 8, 'Alamat Rumah', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->MultiCell(120, 8, $admission->address_1, 1);
        // $pdf->Ln(4);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'Kota', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(120, $line_height, $admission->nama_kota, 1, 1);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(40, $line_height, 'Provinsi', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(40, $line_height, $admission->nama_provinsi, 1);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(30, $line_height, 'Kode Pos', 1, 0, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(50, $line_height, $admission->postal_code, 1);
        $pdf->Ln(2 * $line_height);


        $pdf->SetFillColor(255, 204, 0);
        $pdf->SetX($bullet_left);
        $pdf->Cell(5, 6, '', 0, 0, 'C', true);
        $pdf->SetX($margin_left);
        $pdf->SetFont('Calibri', 'BU', 14);
        $pdf->SetTextColor(5, 52, 104);
        $pdf->Cell(120, 6, 'DATA KELUARGA');
        $pdf->Ln(1.5 * $line_height);

        $pdf->SetFont('Calibri', 'B', $field_font_size);
        $pdf->SetFillColor(5, 52, 104);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(20, $line_height, '', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, 'Nama', 1, 0, 'C', true);
        $pdf->Cell(20, $line_height, 'Usia', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, 'Pekerjaan', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, 'E-mail', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, 'HP', 1, 1, 'C', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Calibri', '', $value_font_size);
        $pdf->Cell(20, $line_height, 'AYAH', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->father_name, 1, 0, '', true);
        $pdf->Cell(20, $line_height, date_diff(date_create($admission->father_birthdate), date_create('today'))->y, 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->father_working_status == 1 ? $admission->father_working_position : '-', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->father_phone, 1, 0, 'C', true);
        $pdf->Cell(30, $line_height,  $admission->father_email, 1, 1, '', true);

        $pdf->Cell(20, $line_height, 'IBU', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->mother_name, 1, 0, '', true);
        $pdf->Cell(20, $line_height, date_diff(date_create($admission->mother_birthdate), date_create('today'))->y, 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->mother_working_status == 1 ? $admission->mother_working_position : '-', 1, 0, 'C', true);
        $pdf->Cell(30, $line_height, $admission->mother_phone, 1, 0, 'C', true);
        $pdf->Cell(30, $line_height,  $admission->mother_email, 1, 1, '', true);

        if ($forms->family) {
            foreach ($admission->families as $family) {
                $pdf->Cell(20, $line_height, 'SAUDARA', 1, 0, 'C', true);
                $pdf->Cell(30, $line_height, $family->family_full_name, 1, 0, '', true);
                $pdf->Cell(20, $line_height, date_diff(date_create($family->family_birth_date), date_create('today'))->y, 1, 0, 'C', true);
                $pdf->Cell(30, $line_height, $family->family_working_status == 1 ? $family->family_working_position : '-', 1, 0, 'C', true);
                $pdf->Cell(30, $line_height, $family->family_phone, 1, 0, 'C', true);
                $pdf->Cell(30, $line_height,  $family->family_email, 1, 1, '', true);
            }
        }
        $pdf->Ln(1.5 * $line_height);

        if ($forms->education_history) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'DATA PENDIDIKAN');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', $field_font_size);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Nama Sekolah', 1, 0, 'C', true);
            $pdf->Cell(40, $line_height, 'Lokasi/Kota', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Masuk', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Keluar', 1, 0, 'C', true);
            $pdf->Cell(30, $line_height, 'Bidang Studi', 1, 1, 'C', true);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Calibri', '', $value_font_size);
            foreach ($admission->educations as $education) {
                $pdf->Cell(40, $line_height, $education->education_school_name, 1, 0, 'C', true);
                $pdf->Cell(40, $line_height, $education->education_city, 1, 0, 'C', true);
                $pdf->Cell(25, $line_height, $education->education_year_from, 1, 0, 'C', true);
                $pdf->Cell(25, $line_height, $education->education_year_to, 1, 0, 'C', true);
                $pdf->Cell(30, $line_height, $education->education_major, 1, 1, 'C', true);
            }
            $pdf->Ln(1.5 * $line_height);
        }

        if ($forms->education_history_informal) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'DATA PENDIDIKAN NON-FORMAL');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', $field_font_size);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(50, $line_height, 'Nama Kursus/Training/Sertifikat', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Masuk', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Keluar', 1, 0, 'C', true);
            $pdf->Cell(30, $line_height, 'Penyelenggara', 1, 0, 'C', true);
            $pdf->Cell(30, $line_height, 'Gelar', 1, 1, 'C', true);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Calibri', '', $value_font_size);
            if ($admission->educations_informal) {
                foreach ($admission->educations_informal as $education) {
                    $pdf->Cell(40, $line_height, $education->education_school_name, 1, 0, 'C', true);
                    $pdf->Cell(25, $line_height, $education->education_year_from, 1, 0, 'C', true);
                    $pdf->Cell(25, $line_height, $education->education_year_to, 1, 0, 'C', true);
                    $pdf->Cell(40, $line_height, $education->education_organizer, 1, 0, 'C', true);
                    $pdf->Cell(30, $line_height, $education->education_title, 1, 1, 'C', true);
                }
            } else {
                $pdf->Cell(160, $line_height, '', 1, 0, 'C', true);
            }

            $pdf->Ln(1.5 * $line_height);
        }


        if ($forms->organization_history) {
            $pdf->SetFont('Calibri', 'B', $field_font_size);
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PENGALAMAN ORGANISASI');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(100, $line_height, 'Nama Organisasi', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun', 1, 0, 'C', true);
            $pdf->Cell(35, $line_height, 'Posisi', 1, 1, 'C', true);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if ($admission->organizations) {
                foreach ($admission->organizations as $organization) {
                    $pdf->Cell(100, $line_height, $organization->organization_name, 1, 0, '', true);
                    $pdf->Cell(25, $line_height, $organization->organization_year, 1, 0, 'C', true);
                    $pdf->Cell(35, $line_height, $organization->organization_position, 1, 1, 'C', true);
                }
            } else {
                $pdf->Cell(160, $line_height, '', 1, 0, 'C', true);
            }
            $pdf->Ln(8);
        }

        if ($forms->achievement) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PRESTASI');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(100, $line_height, 'Nama Penghargaan', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun', 1, 0, 'C', true);
            $pdf->Cell(35, $line_height, 'Penyelenggara', 1, 1, 'C', true);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if ($admission->achievements) {
                foreach ($admission->achievements as $achievement) {
                    $pdf->Cell(100, $line_height, $achievement->achievement_name, 1, 0, 'C', true);
                    $pdf->Cell(25, $line_height, $achievement->achievement_year, 1, 0, 'C', true);
                    $pdf->Cell(35, $line_height, $achievement->achievement_organizer, 1, 1, 'C', true);
                }
            } else {
                $pdf->Cell(160, 4, '', 1, 0, 'C', true);
            }
            $pdf->Ln(1.5 * $line_height);
        }


        if ($forms->employment_history) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PENGALAMAN KERJA');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', $field_font_size);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(80, $line_height, 'Perusahaan', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Masuk', 1, 0, 'C', true);
            $pdf->Cell(25, $line_height, 'Tahun Keluar', 1, 0, 'C', true);
            $pdf->Cell(30, $line_height, 'Posisi', 1, 1, 'C', true);

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Calibri', '', $value_font_size);
            if ($admission->employments) {
                foreach ($admission->employments as $employment) {
                    $pdf->Cell(80, $line_height, $employment->employment_company_name, 1, 0, 'C', true);
                    $pdf->Cell(25, $line_height, $employment->employment_year_from, 1, 0, 'C', true);
                    $pdf->Cell(25, $line_height, $employment->employment_year_to, 1, 0, 'C', true);
                    $pdf->Cell(30, $line_height, $employment->employment_position, 1, 1, 'C', true);
                }
            } else {
                $pdf->Cell(160, $line_height, '', 1, 0, 'C', true);
            }

            $pdf->Ln(1.5 * $line_height);
        }


        if ($forms->last_occupation) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PEKERJAAN SAAT INI');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Nama Perusahaan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->last_employment_company_name, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Jabatan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->last_employment_position, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Alamat Perusahaan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->last_employment_company_address, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Telepon', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->last_employment_company_phone, 1, 1);
            $pdf->Ln(1.5 * $line_height);
        }


        if ($forms->education_fund_source) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'RENCANA PEMBIAYAAN PENDIDIKAN');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->Cell(5, $line_height, '', 1);
            $pdf->Cell(40, $line_height, 'Pribadi');
            $pdf->Cell(5, $line_height, '', 1);
            $pdf->Cell(40, $line_height, 'Perusahaan');
            $pdf->Cell(5, $line_height, '', 1);
            $pdf->Cell(50, $line_height, 'Sebagian pribadi, sebagian perusahaan');

            if ($admission->education_fund_source == 1) {
                $pdf->SetFont('ZapfDingbats', '', 12);

                $pdf->SetX($margin_left);
                $pdf->Cell(0, $line_height, chr(52), 0, 0);

                $pdf->SetFont('Calibri', '', $value_font_size);
            } elseif ($admission->education_fund_source == 2) {
                $pdf->SetFont('ZapfDingbats', '', 12);

                $pdf->SetX($margin_left + 45);
                $pdf->Cell(0, $line_height, chr(52), 0, 0);

                $pdf->SetFont('Calibri', '', $value_font_size);
            } elseif ($admission->education_fund_source == 3) {
                $pdf->SetFont('ZapfDingbats', '', 12);

                $pdf->SetX($margin_left + 90);
                $pdf->Cell(0, $line_height, chr(52), 0, 1);

                $pdf->SetFont('Calibri', '', $value_font_size);
            }

            $pdf->Ln(1.5 * $line_height);
        }

        if ($forms->company_contact_info) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'KONTAK PERUSAHAAN');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Nama PIC/Pimpinan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->company_pic_name, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Alamat Perusahaan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->company_pic_address, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Telepon', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->company_pic_phone, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'E-mail', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->company_pic_email, 1, 1);
            $pdf->Ln(1.5 * $line_height);
        }

        if ($forms->recommendation) {
            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PEMBERI REKOMENDASI 1');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Nama', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_name_1, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Jabatan & Perusahaan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_company_1, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Alamat', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, '', 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Telepon', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_phone_1, 1, 1);
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFillColor(255, 204, 0);
            $pdf->SetX($bullet_left);
            $pdf->Cell(5, 6, '', 0, 0, 'C', true);
            $pdf->SetX($margin_left);
            $pdf->SetFont('Calibri', 'BU', 14);
            $pdf->SetTextColor(5, 52, 104);
            $pdf->Cell(120, 6, 'PEMBERI REKOMENDASI 2');
            $pdf->Ln(1.5 * $line_height);

            $pdf->SetFont('Calibri', 'B', 10);
            $pdf->SetFillColor(5, 52, 104);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Nama', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_name_2, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Jabatan & Perusahaan', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_company_2, 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Alamat', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, '', 1, 1);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, $line_height, 'Telepon', 1, 0, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(120, $line_height, $admission->recommender_phone_2, 1, 1);
            $pdf->Ln(1.5 * $line_height);
        }


        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->MultiCell(0, $line_height, 'Darimana Anda mengetahui PPM School of Management');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Teman');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'Jobfair', 0);

        if ($admission->marketing_hear_from == 1) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_hear_from == 7) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Keluarga');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'Radio');

        if ($admission->marketing_hear_from == 2) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_hear_from == 8) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);


        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Guru Sekolah');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'E-mail');

        if ($admission->marketing_hear_from == 3) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_hear_from == 9) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);


        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Roadshow');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'Website');

        if ($admission->marketing_hear_from == 4) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_hear_from == 10) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Surat Kabar');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'Lainnya');

        if ($admission->marketing_hear_from == 5) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_hear_from == 11) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Media Sosial');
        if ($admission->marketing_hear_from == 6) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        $pdf->Ln(1.5 * $line_height);

        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->MultiCell(0, $line_height, 'Apa alasan Anda memilih PPM School of Management');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Reputasi');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Kurikulum');

        if ($admission->marketing_reason == 1) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_reason == 6) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Pengajar');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Jejaring ke Perusahaan');

        if ($admission->marketing_reason == 2) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_reason == 7) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Biaya');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Kualitas Alumni');

        if ($admission->marketing_reason == 3) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_reason == 8) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(75, $line_height, 'Roadshow');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(25, $line_height, 'Lainnya');
        $pdf->Cell(50, $line_height, '');

        if ($admission->marketing_reason == 4) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        if ($admission->marketing_reason == 9) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left + 80);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }
        $pdf->Ln($line_height);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(20, $line_height, 'Fasilitas');

        if ($admission->marketing_reason == 5) {
            $pdf->SetFont('ZapfDingbats', '', 12);
            $pdf->SetX($margin_left);
            $pdf->Cell(0, $line_height, chr(52), 0, 0);
            $pdf->SetFont('Calibri', 'B', $field_font_size);
        }

        $pdf->Ln(1.5 * $line_height);

        $pdf->SetX(140);
        $pdf->Cell(40, 60, 'Foto 4 x 6', 1, 0, 'C');
        $pdf->SetX($margin_left);
        $pdf->MultiCell(110, $line_height, 'Dengan ini saya menyatakan kebenaran seluruh informasi yang saya berikan dalam formulir pendaftaran ini.', 0, 'J');
        $pdf->Ln(6);

        $pdf->Cell(110, 4, 'Jakarta, ' . date('j F Y'), 0, 1, 'C');
        $pdf->Cell(110, 30, 'TTD', 0, 1, 'C');
        $pdf->Cell(110, 4, $admission->full_name, 0, 1, 'C');
        $pdf->Ln(2 * $line_height);

        $pdf->SetFont('Calibri', '', 8);
        $pdf->Cell(0, $line_height, 'Diisi oleh tim penerimaan mahasiswa baru PPM', 0, 1);

        $pdf->SetFont('Calibri', 'B', 10);
        $pdf->Cell(0, $line_height, 'Kelengkapan Persyaratan', 0, 1);

        $pdf->SetFont('Calibri', '', 8);
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Fotokopi transkrip terlegalisir');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Fotokopi Sertifikat TOEFL/IELTS');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 1');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 3', 0, 1);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Fotokopi ijazah terlegalisir');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Fotocopy KTP');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 2');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 4', 0, 1);


        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Riwayat hidup (CV)');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Fotokopi Kartu Keluarga');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 3');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Sertifikat Hasil UTBK', 0, 1);

        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Pas foto warna 4 x 6 : 1 lbr');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Biaya seleksi Rp 200.000,-');
        $pdf->Cell(5, 0.75 * $line_height, '', 1);
        $pdf->Cell(38, $line_height, 'Raport Kelas X Semester 4');
        // $pdf->Cell(5, 0.75 * $line_height, '', 1);
        // $pdf->Cell(30, $line_height, 'Sertifikat Hasil UTBK', 0, 1);


        return $pdf;
    }

    function download_documents($id_admission)
    {
        $this->load->helper('download');

        $filter = [
            'admissions.id_admission' => $id_admission,
            // 'payment_type' => 1,
        ];

        $admission = $this->Admissions_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, payment,tes_seleksi, biaya, channel_type, channel_name, full_name, email, payment_status, payment_receipt, payment_voucher_code, id_payment')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('payments', 'payments.id_admission = admissions.id_admission and payments.payment_type = 1', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->get_by($filter);


        $admission->form_1 = $this->Seleksi_form_model->get_by(['id_seleksi' => $admission->seleksi, 'type' => 1]);
        $admission->form_2 = $this->Seleksi_form_model->get_by(['id_seleksi' => $admission->seleksi, 'type' => 2]);

        $documents = [];
        $filter = [
            'id_user' => $admission->id_user,
        ];
        $documents_filenames = $this->Users_documents_model->get_many_by($filter);

        foreach ($documents_filenames as $document) {
            if (file_exists(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $document->document_filename)) {
                $documents[$document->document_type] = $document->document_filename;
            }
        }

        print_r($documents);

        $this->load->library('zip');

        if ($admission->form_1->file_upload_1 || $admission->form_1->file_upload_2 || $admission->form_1->file_upload_3 || $admission->form_1->file_upload_4 || $admission->form_2->file_upload_1 || $admission->form_2->file_upload_2 || $admission->form_2->file_upload_3 || $admission->form_2->file_upload_4) {
            if (isset($documents['report_x_1']) && $documents['report_x_1'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_x_1']);
            }

            if (isset($documents['report_x_2']) && $documents['report_x_2'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_x_2']);
            }

            if (isset($documents['report_xi_1']) && $documents['report_xi_1'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xi_1']);
            }

            if (isset($documents['report_xi_2']) && $documents['report_xi_2'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xi_2']);
            }

            if (isset($documents['report_xii_1']) && $documents['report_xii_1'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xii_1']);
            }

            if (isset($documents['report_xii_2']) && $documents['report_xii_2'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['report_xii_2']);
            }

            if ($admission->form_1->file_upload_2 || $admission->form_2->file_upload_2) {
                if (isset($documents['accreditation_certificate']) && $documents['accreditation_certificate'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['accreditation_certificate']);
                }
            }

            if ($admission->form_1->file_upload_4 || $admission->form_2->file_upload_4) {
                if (isset($documents['utbk_certificate']) && $documents['utbk_certificate'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['utbk_certificate']);
                }
            }

            if (isset($documents['identity_card']) && $documents['identity_card'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['identity_card']);
            }

            if (isset($documents['family_certificate']) && $documents['family_certificate'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['family_certificate']);
            }

            if ($admission->form_1->file_upload_3 || $admission->form_2->file_upload_3) {
                if (isset($documents['organization_certificates']) && $documents['organization_certificates'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['organization_certificates']);
                }

                if (isset($documents['achievement_certificates']) && $documents['achievement_certificates'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['achievement_certificates']);
                }
            }

            if (isset($documents['photo']) && $documents['photo'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['photo']);
            }
        }

        if ($admission->form_1->file_upload_5 || $admission->form_1->file_upload_6 || $admission->form_1->file_upload_7 || $admission->form_2->file_upload_5 || $admission->form_2->file_upload_6 || $admission->form_2->file_upload_7) {
            if (isset($documents['academic_transcript']) && $documents['academic_transcript'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['academic_transcript']);
            }

            if (isset($documents['school_certificate']) && $documents['school_certificate'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['school_certificate']);
            }

            if (isset($documents['identity_card']) && $documents['identity_card'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['identity_card']);
            }

            if (isset($documents['family_certificate']) && $documents['family_certificate'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['family_certificate']);
            }

            if (isset($documents['toefl_certificate']) && $documents['toefl_certificate'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['toefl_certificate']);
            }

            if (isset($documents['photo']) && $documents['photo'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['photo']);
            }

            if (isset($documents['cv']) && $documents['cv'] != '') {
                $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['cv']);
            }

            if ($admission->form_1->file_upload_6 || $admission->form_2->file_upload_6) {
                if (isset($documents['full_body_photo']) && $documents['full_body_photo'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['full_body_photo']);
                }
            }

            if ($admission->form_1->file_upload_7 || $admission->form_2->file_upload_7) {
                if (isset($documents['short_resume']) && $documents['short_resume'] != '') {
                    $this->zip->read_file(FCPATH . USER_DOCUMENTS_FOLDER . $admission->id_user . '/' . $documents['short_resume']);
                }
            }
        }

        $zip_filename = FCPATH . USER_DOCUMENTS_FOLDER . $id_admission . ' - ' . strtoupper($admission->full_name) . '.zip';
        $this->zip->archive($zip_filename);
        force_download($zip_filename, NULL);
    }
}
