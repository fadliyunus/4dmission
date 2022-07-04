<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Skema_pembayaran extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Skema_pembayaran_model',
            'Skema_pembayaran_detail_model',
            'Jadwal_model',
            'Jadwal_seleksi_model',
            'Program_studi_model',
            'Seleksi_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->skema_pembayaran();
    }

    public function skema_pembayaran()
    {
        $this->data['title'] = 'Skema Pembayaran';

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

        // $filter = 'tgl_seleksi >= CURDATE()';
        $filter = '';
        $response->total =  $this->Skema_pembayaran_model->count_all();

        $rows = $this->Skema_pembayaran_model
            ->select('skema_pembayaran.*, seleksi.nama_seleksi, program_studi.nama_program_studi')
            ->join('seleksi', 'seleksi.id_seleksi = skema_pembayaran.id_seleksi', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->order_by(array(
                'id_seleksi' => 'asc',
                'tgl_seleksi' => 'asc',
                'jenis_pembayaran' => 'asc',
            ))
            ->get_all();

        foreach ($rows as $row) {
            $row->details = $this->Skema_pembayaran_detail_model
                ->order_by('pembayaran')
                ->get_many_by('id_skema_pembayaran', $row->id_skema_pembayaran);

            $row->total_details = array_sum(array_column($row->details, 'jumlah'));
        }

        $response->rows = $rows;

        echo json_encode($response);
    }

    public function details_data($id_skema_pembayaran)
    {
        $response  = new stdClass();

        $response->total =  $this->Skema_pembayaran_detail_model->count_all();

        $filter = [
            'skema_pembayaran_detail.id_skema_pembayaran' => $id_skema_pembayaran,
        ];
        $response->rows = $this->Skema_pembayaran_detail_model
            ->select('skema_pembayaran_detail.*, skema_pembayaran.*, program_studi.nama_program_studi')
            ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = skema_pembayaran.id_program_studi', 'left')
            ->order_by(
                [
                    'skema_pembayaran.jenis_program_studi' => 'asc',
                    'skema_pembayaran.id_program_studi' => 'asc',
                    'tgl_seleksi' => 'asc',
                    'pembayaran' => 'asc',
                ]
            )
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function details($id_skema_pembayaran)
    {
        $this->data['id_skema_pembayaran'] = $id_skema_pembayaran;
        $this->data['title'] = 'Skema Pembayaran';

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

    public function create()
    {
        $this->form_validation->set_rules('id_seleksi', 'Seleksi', 'required');
        // $this->form_validation->set_rules('tgl_seleksi', 'Jadwal', 'required');
        $this->form_validation->set_rules('jenis_pembayaran', 'Jenis Pembayaran', 'required');
        $this->form_validation->set_rules('jumlah_total', 'Jumlah', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id_seleksi = $this->input->post('id_seleksi');
            $tgl_seleksi =  $this->input->post('tgl_seleksi') ? date('Y-m-d', $this->input->post('tgl_seleksi')) : NULL;
            $jenis_pembayaran = $this->input->post('jenis_pembayaran');
            $jumlah_total = $this->input->post('jumlah_total');
            $jumlah_angsuran = $this->input->post('jenis_pembayaran') == 1 ?  1 : $this->input->post('jumlah_angsuran');



            $filter = [
                'id_seleksi' => $id_seleksi,
                'tgl_seleksi' => $tgl_seleksi,
                'jenis_pembayaran' => $jenis_pembayaran,
                'jumlah_angsuran' => $jumlah_angsuran,
            ];
            $skema_pembayaran = $this->Skema_pembayaran_model
                ->get_by($filter);

            if ($skema_pembayaran) {
                $this->session->set_flashdata('message', 'Skema pembayaran sudah ada');
            } else {
                $this->db->trans_begin();



                $data = [
                    'id_seleksi' => $id_seleksi,
                    'tgl_seleksi' => $tgl_seleksi,
                    'jenis_pembayaran' => $jenis_pembayaran,
                    'jumlah_total' => $jumlah_total,
                    'jumlah_angsuran' => $jumlah_angsuran,
                ];

                $id_skema_pembayaran = $this->Skema_pembayaran_model->insert($data);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('backoffice/skema_pembayaran', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = 'Skema Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where('jenis_program_studi', 2);
        $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        $jadwal_options = [];
        if (set_value('jenis_program_studi')) {
            $jenis_program_studi = set_value('jenis_program_studi');

            if ($jenis_program_studi == 1) {
                $program_studi_options = [];
            } elseif ($jenis_program_studi == 2) {
                $this->db->where('jenis_program_studi', 2);
                $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
            }

            $filter = [
                'jenis_program_studi' => $jenis_program_studi
            ];
            $jadwal_options_data = $this->Jadwal_seleksi_model
                ->select('DISTINCT(tgl_seleksi)')
                ->join('seleksi', 'seleksi.id_seleksi = jadwal_seleksi.id_seleksi', 'left')
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get_many_by($filter);

            foreach ($jadwal_options_data as $jadwal) {
                $jadwal_options[strtotime($jadwal->tgl_seleksi)] = $jadwal->tgl_seleksi;
            }
        }

        $seleksi_options = [];
        $_seleksi_options = $this->Seleksi_model
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->get_all();
        foreach ($_seleksi_options as $seleksi) {
            $seleksi_options[$seleksi->id_seleksi] = $seleksi->nama_program_studi . ' ' . $seleksi->nama_seleksi;
        }

        $this->data['form'] = [[
            //     'id' => 'jenis_program_studi',
            //     'label' => 'Program',
            //     'type' => 'dropdown',
            //     'options' => ['' => '-Pilih Program-', '1' => 'Sarjana', '2' => 'Magister'],
            //     'value' => set_value('jenis_program_studi'),
            //     'control_label_class' => 'col-sm-3 col-form-label',
            //     'form_control_class' => 'col-sm-3',
            //     'class' => 'select2',
            // ], [
            //     'id' => 'id_program_studi',
            //     'label' => 'Program Studi',
            //     'type' => 'dropdown',
            //     'options' => ['' => '-Pilih program studi-'] + $program_studi_options,
            //     'value' => set_value('id_program_studi'),
            //     'control_label_class' => 'col-sm-3 col-form-label',
            //     'form_control_class' => 'col-sm-3',
            //     'class' => 'select2',
            //     'input_container_class' => 'program_studi',
            // ], [
            'id' => 'id_seleksi',
            'label' => 'Seleksi',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih seleksi-'] + $seleksi_options,
            'value' => set_value('id_seleksi'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-6',
            'class' => 'select2',
        ], [
            'id' => 'tgl_seleksi',
            'label' => 'Jadwal Tes',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih jadwal seleksi-'] + $jadwal_options,
            'value' => set_value('tgl_seleksi'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
            'input_container_class' => 'd-none',
        ], [
            'id' => 'jenis_pembayaran',
            'label' => 'Jenis Pembayaran',
            'type' => 'dropdown',
            'options' => [
                '' => '-Pilih tahap pembayaran-',
                '1' => 'TUNAI',
                '2' => 'ANGSURAN',
            ],
            'value' => set_value('jenis_pembayaran'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
        ], [
            'id' => 'jumlah_total',
            'label' => 'Jumlah Total',
            'value' => set_value('jumlah_total'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
        ], [
            'id' => 'jumlah_angsuran',
            'label' => 'Jumlah Angsuran',
            'value' => set_value('jumlah_angsuran'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
            'input_container_class' => 'd-none',
            // ], [
            //     'id' => 'jatuh_tempo',
            //     'label' => 'Jatuh Tempo',
            //     'type' => 'date',
            //     'value' => set_value('jatuh_tempo'),
            //     'control_label_class' => 'col-sm-3 col-form-label',
            //     'form_control_class' => 'col-sm-3',
            //     'input_container_class' => 'jatuh_tempo d-none',
        ]];

        $this->data['form_2'] = [[
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/skema_pembayaran') . "'",
            ]],
        ]];


        $this->load->view('template_backoffice', $this->data);
    }

    public function create_detail($id_skema_pembayaran)
    {
        $skema_pembayaran = $this->Skema_pembayaran_model
            ->select('skema_pembayaran.*, nama_program_studi')
            ->join('seleksi', 'seleksi.id_seleksi = skema_pembayaran.id_seleksi', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->get($id_skema_pembayaran);

        $this->form_validation->set_rules('id_skema_pembayaran', 'Program', 'required');
        $this->form_validation->set_rules('pembayaran', 'Tahap Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $pembayaran = $this->input->post('pembayaran');
            $jumlah = $this->input->post('jumlah');
            $persentase = $this->input->post('persentase');
            $waktu = $this->input->post('waktu');
            $jatuh_tempo = $this->input->post('jatuh_tempo');
            $cb_jumlah = $this->input->post('cb_jumlah');
            $cb_waktu = $this->input->post('cb_waktu');


            $filter = [
                'id_skema_pembayaran' => $id_skema_pembayaran,
                'pembayaran' => $pembayaran,
            ];
            $skema_pembayaran_detail = $this->Skema_pembayaran_detail_model
                ->get_by($filter);

            if ($skema_pembayaran_detail) {
                $this->session->set_flashdata('message', 'Skema pembayaran sudah ada');
            } else {
                $this->db->trans_begin();

                $data = [
                    'id_skema_pembayaran' => $id_skema_pembayaran,
                    'pembayaran' => $pembayaran,
                ];

                if ($cb_jumlah == 1) {
                    $data['jumlah'] = $jumlah;
                    $data['persentase'] = 0;
                } elseif ($cb_jumlah == 2) {
                    $data['jumlah'] = 0;
                    $data['persentase'] = $persentase;
                }
                if ($cb_waktu == 1) {
                    $data['waktu'] = $waktu;
                } elseif ($cb_waktu == 2) {
                    $data['waktu'] = 0;
                    $data['jatuh_tempo'] = date_format(date_create_from_format('d/m/Y', $jatuh_tempo), 'Y-m-d');
                }
                $this->Skema_pembayaran_detail_model->insert($data);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('backoffice/skema_pembayaran', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = 'Skema Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit_detail');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where('jenis_program_studi', 2);
        $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        $jadwal_options = [];
        if (set_value('jenis_program_studi')) {
            $jenis_program_studi = set_value('jenis_program_studi');

            if ($jenis_program_studi == 1) {
                $program_studi_options = [];
            } elseif ($jenis_program_studi == 2) {
                $this->db->where('jenis_program_studi', 2);
                $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
            }

            $filter = [
                'jenis_program_studi' => $jenis_program_studi
            ];
            $jadwal_options_data = $this->Jadwal_seleksi_model
                ->select('DISTINCT(tgl_seleksi)')
                ->join('seleksi', 'seleksi.id_seleksi = jadwal_seleksi.id_seleksi', 'left')
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get_many_by($filter);

            foreach ($jadwal_options_data as $jadwal) {
                $jadwal_options[strtotime($jadwal->tgl_seleksi)] = $jadwal->tgl_seleksi;
            }
        }

        $program_studi = '';
        if ($skema_pembayaran->nama_program_studi) {
            $program_studi = $skema_pembayaran->nama_program_studi;
        } else {
            if ($skema_pembayaran->jenis_program_studi == 1) {
                $program_studi = 'SARJANA';
            } elseif ($skema_pembayaran->jenis_program_studi == 2) {
                $program_studi = 'MAGISTER';
            }
        }

        $pembayaran_options = ['' => '-Pilih tahap pembayaran-', '0' => 'Konfirmasi'];
        for ($i = 1; $i <= $skema_pembayaran->jumlah_angsuran; $i++) {
            $pembayaran_options[$i] = 'Tahap ' . $i;
        }
        $this->data['form'] = [[
            'id' => 'id_skema_pembayaran',
            'type' => 'hidden',
            'value' => $skema_pembayaran->id_skema_pembayaran,
        ], [
            'id' => 'program_studi',
            'type' => 'control_static',
            'value' => $program_studi,
        ], [
            'id' => 'tgl_seleksi',
            'type' => 'control_static',
            'value' => $skema_pembayaran->tgl_seleksi ? $skema_pembayaran->tgl_seleksi : '-',
        ], [
            'id' => 'pembayaran',
            'label' => 'Tahap Pembayaran',
            'type' => 'dropdown',
            'options' => $pembayaran_options,
            'value' => set_value('pembayaran'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
        ]];

        $this->data['form_2'] = [[
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/skema_pembayaran') . "'",
            ]],
        ]];


        $this->load->view('template_backoffice', $this->data);
    }

    public function edit($id_skema_pembayaran)
    {
        $skema_pembayaran = $this->Skema_pembayaran_model
            ->join('seleksi', 'seleksi.id_seleksi = skema_pembayaran.id_seleksi', 'left')
            ->get($id_skema_pembayaran);
        $this->data['skema_pembayaran'] = $skema_pembayaran;

        $skema_pembayaran_detail = $this->Skema_pembayaran_detail_model
            ->get_by('id_skema_pembayaran', $id_skema_pembayaran);

        $this->form_validation->set_rules('id_seleksi', 'Seleksi', 'required');
        $this->form_validation->set_rules('jenis_pembayaran', 'Jenis Pembayaran', 'required');
        $this->form_validation->set_rules('jumlah_total', 'Jumlah', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id_seleksi = $this->input->post('id_seleksi');
            $tgl_seleksi = $this->input->post('tgl_seleksi') ? date('Y-m-d', $this->input->post('tgl_seleksi')) : NULL;
            $jenis_pembayaran = $this->input->post('jenis_pembayaran');
            $jumlah_total = $this->input->post('jumlah_total');
            $jumlah_angsuran = $this->input->post('jenis_pembayaran') == 1 ? 1 : $this->input->post('jumlah_angsuran');

            $filter = [
                'id_seleksi' => $id_seleksi,
                'tgl_seleksi' => $tgl_seleksi,
                'jenis_pembayaran' => $jenis_pembayaran,
                'jumlah_angsuran' => $jumlah_angsuran,
                'id_skema_pembayaran !=' => $id_skema_pembayaran,
            ];
            $skema_pembayaran_exist = $this->Skema_pembayaran_model
                ->get_by($filter);

            if ($skema_pembayaran_exist) {
                $this->session->set_flashdata('message', 'Skema pembayaran sudah ada');
            } else {
                $this->db->trans_begin();

                $data = [
                    'id_seleksi' => $id_seleksi,
                    'tgl_seleksi' => $tgl_seleksi,
                    'jenis_pembayaran' => $jenis_pembayaran,
                    'jumlah_total' => $jumlah_total,
                    'jumlah_angsuran' => $jumlah_angsuran,
                ];

                if ($this->Skema_pembayaran_model->update($id_skema_pembayaran, $data)) {
                    // if ($jenis_pembayaran == 1) {
                    //     if ($this->Skema_pembayaran_detail_model->get_by('id_skema_pembayaran',$id_skema_pembayaran)) {
                    //         $data = [
                    //             'pembayaran' => $pembayaran,
                    //             'jumlah' => $jumlah_total,
                    //             'jatuh_tempo' => $jatuh_tempo,
                    //         ];
                    //         $this->Skema_pembayaran_detail_model->update_by(['id_skema_pembayaran' => $id_skema_pembayaran], $data);
                    //     } else {
                    //         $data = [
                    //             'id_skema_pembayaran' => $id_skema_pembayaran,
                    //             'pembayaran' => 1,
                    //             'jumlah' => $jumlah_total,
                    //             'persentase' => 0,
                    //             'waktu' => 0,
                    //             'jatuh_tempo' => $jatuh_tempo,
                    //         ];

                    //         $this->Skema_pembayaran_detail_model->insert($data);
                    //     }
                    // }
                }


                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('backoffice/skema_pembayaran', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $this->data['title'] = 'Skema Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $program_studi_options = [];
        $jadwal_options = [];
        if (set_value('id_seleksi') || $skema_pembayaran) {
            $id_seleksi = set_value('id_seleksi', $skema_pembayaran->id_seleksi);

            //     if ($jenis_program_studi == 1) {
            //         $program_studi_options = [];
            //     } elseif ($jenis_program_studi == 2) {
            //         $this->db->where('jenis_program_studi', 2);
            //         $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
            //     }

            $filter = [
                'id_seleksi' => $id_seleksi
            ];
            $jadwal_options_data = $this->Jadwal_seleksi_model
                ->select('DISTINCT(tgl_seleksi)')
                ->get_many_by($filter);

            foreach ($jadwal_options_data as $jadwal) {
                $jadwal_options[strtotime($jadwal->tgl_seleksi)] = date('j F Y', strtotime($jadwal->tgl_seleksi));
            }
        }

        $seleksi_options = [];
        $_seleksi_options = $this->Seleksi_model
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->get_all();
        foreach ($_seleksi_options as $seleksi) {
            $seleksi_options[$seleksi->id_seleksi] = $seleksi->nama_program_studi . ' ' . $seleksi->nama_seleksi;
        }
        $this->data['form'] = [[
            'id' => 'id_seleksi',
            'label' => 'Seleksi',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program studi-'] + $seleksi_options,
            'value' => set_value('id_seleksi', $skema_pembayaran->id_seleksi),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-6',
            'class' => 'select2',
        ], [
            'id' => 'tgl_seleksi',
            'label' => 'Jadwal Tes',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih jadwal seleksi-'] + $jadwal_options,
            'value' => set_value('tgl_seleksi', strtotime($skema_pembayaran->tgl_seleksi)),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2 ',
            'input_container_class' => ($skema_pembayaran->tgl_seleksi_option ? '' : 'd-none'),
        ], [
            'id' => 'jenis_pembayaran',
            'label' => 'Jenis Pembayaran',
            'type' => 'dropdown',
            'options' => [
                '' => '-Pilih tahap pembayaran-',
                '1' => 'Tunai',
                '2' => 'Angsuran',
            ],
            'value' => set_value('jenis_pembayaran', $skema_pembayaran->jenis_pembayaran),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
        ], [
            'id' => 'jumlah_total',
            'label' => 'Jumlah Total',
            'value' => set_value('jumlah_total', $skema_pembayaran->jumlah_total),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
        ], [
            'id' => 'jumlah_angsuran',
            'label' => 'Jumlah Angsuran',
            'value' => set_value('jumlah_angsuran', $skema_pembayaran->jumlah_angsuran),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
            'input_container_class' => $skema_pembayaran->jenis_pembayaran == 1 ? 'd-none' : '',
        ]];

        $this->data['form_2'] = [[
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/skema_pembayaran') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }

    public function edit_detail($id_skema_pembayaran_detail)
    {
        $skema_pembayaran = $this->Skema_pembayaran_detail_model
            ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = skema_pembayaran.id_seleksi', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->get($id_skema_pembayaran_detail);

        $this->data['skema_pembayaran'] = $skema_pembayaran;

        $this->form_validation->set_rules('id_skema_pembayaran_detail', 'Program', 'required');
        $this->form_validation->set_rules('pembayaran', 'Tahap Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $pembayaran = $this->input->post('pembayaran');
            $jumlah = $this->input->post('jumlah');
            $persentase = $this->input->post('persentase');
            $waktu = $this->input->post('waktu');
            $jatuh_tempo = $this->input->post('jatuh_tempo');
            $cb_jumlah = $this->input->post('cb_jumlah');
            $cb_waktu = $this->input->post('cb_waktu');


            $filter = [
                'pembayaran' => $pembayaran,
                'id_skema_pembayaran_detail !=' => $id_skema_pembayaran_detail,
                'id_skema_pembayaran' => $skema_pembayaran->id_skema_pembayaran,
            ];
            $skema_pembayaran_detail = $this->Skema_pembayaran_detail_model
                ->get_by($filter);

            if ($skema_pembayaran_detail) {
                $this->session->set_flashdata('message', 'Skema pembayaran sudah ada');
            } else {
                $this->db->trans_begin();

                $data = [
                    'pembayaran' => $pembayaran,
                ];

                if ($cb_jumlah == 1) {
                    $data['jumlah'] = $jumlah;
                    $data['persentase'] = 0;
                } elseif ($cb_jumlah == 2) {
                    $data['jumlah'] = 0;
                    $data['persentase'] = $persentase;
                }
                if ($cb_waktu == 1) {
                    $data['waktu'] = $waktu;
                } elseif ($cb_waktu == 2) {
                    $data['waktu'] = 0;
                    $data['jatuh_tempo'] = date_format(date_create_from_format('d/m/Y', $jatuh_tempo), 'Y-m-d');
                }
                $this->Skema_pembayaran_detail_model->update($id_skema_pembayaran_detail, $data);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('backoffice/skema_pembayaran', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = 'Skema Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit_detail');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where('jenis_program_studi', 2);
        $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        $jadwal_options = [];
        if (set_value('jenis_program_studi')) {
            $jenis_program_studi = set_value('jenis_program_studi');

            if ($jenis_program_studi == 1) {
                $program_studi_options = [];
            } elseif ($jenis_program_studi == 2) {
                $this->db->where('jenis_program_studi', 2);
                $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
            }

            $filter = [
                'jenis_program_studi' => $jenis_program_studi
            ];
            $jadwal_options_data = $this->Jadwal_seleksi_model
                ->select('DISTINCT(tgl_seleksi)')
                ->join('seleksi', 'seleksi.id_seleksi = jadwal_seleksi.id_seleksi', 'left')
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get_many_by($filter);

            foreach ($jadwal_options_data as $jadwal) {
                $jadwal_options[strtotime($jadwal->tgl_seleksi)] = $jadwal->tgl_seleksi;
            }
        }

        $program_studi = '';
        if ($skema_pembayaran->nama_program_studi) {
            $program_studi = $skema_pembayaran->nama_program_studi;
        } else {
            if ($skema_pembayaran->jenis_program_studi == 1) {
                $program_studi = 'SARJANA';
            } elseif ($skema_pembayaran->jenis_program_studi == 2) {
                $program_studi = 'MAGISTER';
            }
        }
        $this->data['form'] = [[
            'id' => 'id_skema_pembayaran_detail',
            'type' => 'hidden',
            'value' => $skema_pembayaran->id_skema_pembayaran_detail,
        ], [
            'id' => 'program_studi',
            'type' => 'control_static',
            'value' => $program_studi,
        ], [
            'id' => 'tgl_seleksi',
            'type' => 'control_static',
            'value' => $skema_pembayaran->tgl_seleksi,
        ]];

        $pembayaran_options = [
            '' => '-Pilih tahap pembayaran-',
            '0' => 'Konfirmasi',
        ];

        for ($i = 1; $i <= $skema_pembayaran->jumlah_angsuran; $i++) {
            $pembayaran_options[$i] = 'Tahap ' . $i;
        }

        $this->data['form'][] = [
            'id' => 'pembayaran',
            'label' => 'Tahap Pembayaran',
            'type' => 'dropdown',
            'options' => $pembayaran_options,
            'value' => set_value('pembayaran', $skema_pembayaran->pembayaran),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
        ];

        $this->data['form_2'] = [[
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/skema_pembayaran') . "'",
            ]],
        ]];


        $this->load->view('template_backoffice', $this->data);
    }

    public function get_jadwal()
    {
        $response  = new stdClass();

        $id_seleksi = $this->input->post('id_seleksi');

        $filter = [
            'id_seleksi' => $id_seleksi,
            'tgl_seleksi_option' => 1,
        ];
        $response->tgl_seleksi = $this->Seleksi_model->count_by($filter);

        $filter = [
            'id_seleksi' => $id_seleksi
        ];
        $response->data = $this->Jadwal_seleksi_model
            ->select('DISTINCT(tgl_seleksi)')
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function delete()
    {
        $response = new stdClass();
        $response->status = false;

        $id_skema_pembayaran = $this->input->post('id_skema_pembayaran');
        if ($this->Skema_pembayaran_model->delete($id_skema_pembayaran)) {
            $response->status = true;
        }

        echo json_encode($response);
    }

    public function delete_detail()
    {
        $response = new stdClass();
        $response->status = false;

        $id_skema_pembayaran_detail = $this->input->post('id_skema_pembayaran_detail');
        if ($this->Skema_pembayaran_detail_model->delete($id_skema_pembayaran_detail)) {
            $response->status = true;
        }

        echo json_encode($response);
    }

    public function copy($id_skema_pembayaran)
    {
        $skema_pembayaran = $this->Skema_pembayaran_model
            ->join('seleksi', 'seleksi.id_seleksi = skema_pembayaran.id_seleksi', 'left')
            ->get($id_skema_pembayaran);
        $this->data['skema_pembayaran'] = $skema_pembayaran;

        $skema_pembayaran_detail = $this->Skema_pembayaran_detail_model
            ->get_many_by('id_skema_pembayaran', $id_skema_pembayaran);

        $this->form_validation->set_rules('id_seleksi', 'Seleksi', 'required');
        $this->form_validation->set_rules('jenis_pembayaran', 'Jenis Pembayaran', 'required');
        $this->form_validation->set_rules('jumlah_total', 'Jumlah', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id_seleksi = $this->input->post('id_seleksi');
            $tgl_seleksi = $this->input->post('tgl_seleksi') ? date('Y-m-d', $this->input->post('tgl_seleksi')) : NULL;
            $jenis_pembayaran = $this->input->post('jenis_pembayaran');
            $jumlah_total = $this->input->post('jumlah_total');
            $jumlah_angsuran = $this->input->post('jenis_pembayaran') == 1 ? 1 : $this->input->post('jumlah_angsuran');

            $filter = [
                'id_seleksi' => $id_seleksi,
                'tgl_seleksi' => $tgl_seleksi,
                'jenis_pembayaran' => $jenis_pembayaran,
                'jumlah_angsuran' => $jumlah_angsuran,
            ];

            if ($this->Skema_pembayaran_model->get_by($filter)) {
                $this->session->set_flashdata('message', 'Skema pembayaran sudah ada');
            } else {
                $this->db->trans_begin();

                $data = [
                    'id_seleksi' => $id_seleksi,
                    'tgl_seleksi' => $tgl_seleksi,
                    'jenis_pembayaran' => $jenis_pembayaran,
                    'jumlah_total' => $jumlah_total,
                    'jumlah_angsuran' => $jumlah_angsuran,
                ];

                if ($new_id = $this->Skema_pembayaran_model->insert($data)) {
                    foreach ($skema_pembayaran_detail as $detail) {
                        unset($detail->id_skema_pembayaran_detail);
                        $detail->id_skema_pembayaran = $new_id;
                        $this->Skema_pembayaran_detail_model->insert($detail);
                    }
                }


                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    redirect('backoffice/skema_pembayaran', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $this->data['title'] = 'Skema Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $program_studi_options = [];
        $jadwal_options = [];
        if (set_value('id_seleksi') || $skema_pembayaran) {
            $id_seleksi = set_value('id_seleksi', $skema_pembayaran->id_seleksi);

            //     if ($jenis_program_studi == 1) {
            //         $program_studi_options = [];
            //     } elseif ($jenis_program_studi == 2) {
            //         $this->db->where('jenis_program_studi', 2);
            //         $program_studi_options = $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
            //     }

            $filter = [
                'id_seleksi' => $id_seleksi
            ];
            $jadwal_options_data = $this->Jadwal_seleksi_model
                ->select('DISTINCT(tgl_seleksi)')
                ->get_many_by($filter);

            foreach ($jadwal_options_data as $jadwal) {
                $jadwal_options[strtotime($jadwal->tgl_seleksi)] = date('j F Y', strtotime($jadwal->tgl_seleksi));
            }
        }

        $seleksi_options = [];
        $_seleksi_options = $this->Seleksi_model
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->get_all();
        foreach ($_seleksi_options as $seleksi) {
            $seleksi_options[$seleksi->id_seleksi] = $seleksi->nama_program_studi . ' ' . $seleksi->nama_seleksi;
        }
        $this->data['form'] = [[
            'id' => 'id_seleksi',
            'label' => 'Seleksi',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program studi-'] + $seleksi_options,
            'value' => set_value('id_seleksi', $skema_pembayaran->id_seleksi),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-6',
            'class' => 'select2',
        ], [
            'id' => 'tgl_seleksi',
            'label' => 'Jadwal Tes',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih jadwal seleksi-'] + $jadwal_options,
            'value' => set_value('tgl_seleksi', strtotime($skema_pembayaran->tgl_seleksi)),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
            'input_container_class' => ($skema_pembayaran->tgl_seleksi_option ? '' : 'd-none'),
        ], [
            'id' => 'jenis_pembayaran',
            'label' => 'Jenis Pembayaran',
            'type' => 'dropdown',
            'options' => [
                '' => '-Pilih tahap pembayaran-',
                '1' => 'Tunai',
                '2' => 'Angsuran',
            ],
            'value' => set_value('jenis_pembayaran', $skema_pembayaran->jenis_pembayaran),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'select2',
        ], [
            'id' => 'jumlah_total',
            'label' => 'Jumlah Total',
            'value' => set_value('jumlah_total', $skema_pembayaran->jumlah_total),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
        ], [
            'id' => 'jumlah_angsuran',
            'label' => 'Jumlah Angsuran',
            'value' => set_value('jumlah_angsuran', $skema_pembayaran->jumlah_angsuran),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
            'class' => 'number',
            'input_container_class' => $skema_pembayaran->jenis_pembayaran == 1 ? 'd-none' : '',
        ]];

        $this->data['form_2'] = [[
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/skema_pembayaran') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
}
