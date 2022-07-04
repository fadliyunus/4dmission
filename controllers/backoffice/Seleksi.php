<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Seleksi extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
            'Seleksi_model',
            'Seleksi_form_model',
            'Forms_model',
            'Jadwal_model',
            'Jadwal_seleksi_model',
            'Payments_channels_model',
            'Seleksi_payments_model',
            'Seleksi_payments_channels_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->seleksi();
    }

    public function seleksi()
    {
        $this->data['title'] = 'Seleksi';

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
        $this->form_validation->set_rules('id_program_studi', 'Program Studi', 'required');
        $this->form_validation->set_rules('nama_seleksi', 'Nama', 'required');
        $this->form_validation->set_rules('biaya', 'Biaya', 'required');
        $this->form_validation->set_rules('id_payment_channels[]', 'Metode Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $payment = $this->input->post('payment');
            if ($this->input->post('payment') == 1) {
                $biaya = $this->input->post('biaya');
                $id_payment_channels = $this->input->post('id_payment_channels');
            } else {
                $biaya = 0;
                $id_payment_channels = [];
            }


            $jadwal = explode(',', $this->input->post('jadwal'));
            foreach ($jadwal as $i => $j) {
                $j_date = date_create_from_format('d/m/Y', $j);
                if ($j_date) {
                    $jadwal[$i] = date_format($j_date, 'Y-m-d');
                }
            }

            $id_forms = $this->input->post('id_forms');
            $data = [
                'id_program_studi' => $this->input->post('id_program_studi'),
                'nama_seleksi' => $this->input->post('nama_seleksi'),
                'payment' => $this->input->post('payment'),
                'biaya' => $biaya,
                'tes_seleksi' => $this->input->post('tes_seleksi'),
                'tgl_seleksi_option' => $this->input->post('tgl_seleksi_option'),
            ];

            $this->db->trans_begin();
            if ($id_seleksi = $this->Seleksi_model->insert($data)) {
                if ($jadwal) {
                    foreach ($jadwal as $tgl) {
                        if ($tgl) {
                            $data = [
                                'id_seleksi' => $id_seleksi,
                                'tgl_seleksi' => $tgl
                            ];
                            $this->Jadwal_seleksi_model->insert($data);
                        }
                    }
                }

                if ($id_payment_channels) {
                    foreach ($id_payment_channels as $id) {
                        $data = [
                            'id_seleksi' => $id_seleksi,
                            'id_payment_channel' => $id,
                        ];
                        $this->Seleksi_payments_channels_model->insert($data);
                    }
                }
            }

            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                redirect('backoffice/seleksi', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->data['title'] = 'Seleksi';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $id_form_options = [];
        $forms = $this->Forms_model->get_all();
        foreach ($forms as $i => $form) {
            $id_form_options[] = [
                'id' => 'cb_' . $i,
                'value' => $form->id_form,
                'label' => $form->form_name,
            ];
        }

        $id_payment_channel_options = [];
        $channels = $this->Payments_channels_model->get_all();

        foreach ($channels as $i => $channel) {
            $channel_type = '';
            switch ($channel->channel_type) {
                case '1':
                    $channel_type = 'Transfer';
                    break;
                case '2':
                    $channel_type = 'Virtual Account';
                    break;
            }
            $id_payment_channel_options[] = [
                'id' => 'cb_' . $i,
                'value' => $channel->id_payment_channel,
                'label' => $channel_type . ' ' . $channel->channel_name,
                // 'checked' => in_array($channel->id_payment_channel, $old_id_payment_channels),
            ];
        }


        $this->data['form'] = [[
            'id' => 'id_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-'] + $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi'),
            'value' => set_value('id_program_studi'),
        ], [
            'id' => 'nama_seleksi',
            'label' => 'Nama',
            'value' => set_value('nama_seleksi'),
        ], [
            'id' => 'payment',
            'label' => 'Biaya Pendaftaran',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'payment_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('payment', '1'),
                ),
                array(
                    'id' => 'payment_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('payment', '0'),
                )
            )
        ], [
            'id' => 'biaya',
            'label' => 'Jumlah Biaya',
            'value' => set_value('biaya'),
            'form_control_class' => 'col-sm-3',
            'input_addons' => array(
                'pre' => 'Rp',
            ),
            'input_container_class' => 'biaya ' . (set_value('payment') == 1 ? '' : 'd-none'),
        ], [
            'id' => 'id_payment_channels',
            'name' => 'id_payment_channels[]',
            'label' => 'Channel',
            'type' => 'checkbox',
            'options' => $id_payment_channel_options,
            'input_container_class' => set_value('payment') == 1 ? '' : 'd-none',
        ], [
            'id' => 'tgl_seleksi',
            'label' => 'Tes Seleksi',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'tgl_seleksi_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('tgl_seleksi', '1'),
                ),
                array(
                    'id' => 'tgl_seleksi_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('tgl_seleksi', '0'),
                )
            )
        ], [
            'id' => 'tgl_seleksi_option',
            'label' => 'Jadwal Seleksi',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'tgl_seleksi_option_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('tgl_seleksi_option', '1'),
                ),
                array(
                    'id' => 'tgl_seleksi_option_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('tgl_seleksi_option', '0'),
                )
            )
        ], [
            'id' => 'jadwal',
            'name' => 'jadwal',
            'label' => 'Tanggal Seleksi',
            'value' => set_value('jadwal'),
            'readonly' => 'readonly',
            'input_container_class' => set_value('tes_seleksi') == 1 && set_value('tgl_seleksi_option') == 1 ? '' : 'd-none',
        ], [
            'type' => 'combine',
            'label' => 'none',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/seleksi') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Seleksi_model->count_all();

        $filter = [];
        $response->rows = $this->Seleksi_model
            ->select('seleksi.*, jenis_program_studi, nama_program_studi, nama_seleksi')
            ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
            ->order_by([
                'jenis_program_studi' => 'asc',
                'nama_program_studi' => 'asc'
            ])
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_seleksi)
    {
        $seleksi = $this->Seleksi_model
            ->get_by('seleksi.id_seleksi', $id_seleksi);

        $old_ids = $this->Seleksi_form_model
            ->select('id_seleksi_form')
            ->as_array()
            ->get_many_by('id_seleksi', $id_seleksi);
        $old_id_forms = array_column($old_ids, 'id_form');

        $old_ids_jadwal = $this->Jadwal_seleksi_model
            ->select('tgl_seleksi')
            ->as_array()
            ->get_many_by('id_seleksi', $id_seleksi);
        $old_ids_jadwal = $old_ids_jadwal ? array_column($old_ids_jadwal, 'tgl_seleksi') : [];

        $old_ids_payment = $this->Seleksi_payments_channels_model
            ->select('id_payment_channel')
            ->as_array()
            ->get_many_by('id_seleksi', $id_seleksi);
        $old_id_payment_channels = array_column($old_ids_payment, 'id_payment_channel');

        $this->form_validation->set_rules('id_program_studi', 'Program Studi', 'required');
        $this->form_validation->set_rules('nama_seleksi', 'Nama', 'required');

        if ($this->form_validation->run() == TRUE) {
            if ($this->input->post('payment') == 1) {
                $biaya = $this->input->post('biaya');
                $id_payment_channels = $this->input->post('id_payment_channels');
            } else {
                $biaya = 0;
                $id_payment_channels = [];
            }

            if ($this->input->post('tgl_seleksi_option') == 1) {
                $jadwal = explode(',', $this->input->post('jadwal'));
                foreach ($jadwal as $i => $j) {
                    $j_date = date_create_from_format('d/m/Y', $j);
                    if ($j_date) {
                        $jadwal[$i] = date_format($j_date, 'Y-m-d');
                    }
                }
            } else {
                $jadwal = [];
            }


            $id_forms = $this->input->post('id_forms');

            $data = [
                'id_program_studi' => $this->input->post('id_program_studi'),
                'nama_seleksi' => $this->input->post('nama_seleksi'),
                'biaya' => $biaya,
                'payment' => $this->input->post('payment'),
                'tes_seleksi' => $this->input->post('tes_seleksi'),
                'tgl_seleksi_option' => $this->input->post('tgl_seleksi_option'),
            ];

            $this->db->trans_begin();
            if ($this->Seleksi_model->update($id_seleksi, $data)) {

                if ($id_payment_channels) {
                    $inserted_ids = array_diff($id_payment_channels, $old_id_payment_channels);
                    $deleted_ids = array_diff($old_id_payment_channels, $id_payment_channels);

                    foreach ($deleted_ids as $id) {
                        $this->Seleksi_payments_channels_model->delete_by('id_seleksi_payment_channel', $id);
                    }
                    foreach ($inserted_ids as $id) {
                        $data = [
                            'id_seleksi' => $id_seleksi,
                            'id_payment_channel' => $id,

                        ];
                        $this->Seleksi_payments_channels_model->insert($data);
                    }
                } else {
                    $this->Seleksi_payments_channels_model->delete_by('id_seleksi', $id_seleksi);
                }

                if ($jadwal) {
                    $inserted_ids = array_diff($jadwal, $old_ids_jadwal);
                    $deleted_ids = array_diff($old_ids_jadwal, $jadwal);

                    foreach ($deleted_ids as $tgl) {
                        $filter = [
                            'id_seleksi' => $id_seleksi,
                            'tgl_seleksi' => $tgl,
                        ];
                        $this->Jadwal_seleksi_model->delete_by($filter);
                    }
                    foreach ($inserted_ids as $tgl) {
                        if ($tgl) {
                            $data = [
                                'id_seleksi' => $id_seleksi,
                                'tgl_seleksi' => $tgl,
                            ];
                            $this->Jadwal_seleksi_model->insert($data);
                        }
                    }
                } else {
                    $this->Jadwal_seleksi_model->delete_by('id_seleksi', $id_seleksi);
                }
            }

            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                redirect('backoffice/seleksi', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->data['title'] = 'Seleksi';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');


        $id_payment_channel_options = [];
        $channels = $this->Payments_channels_model->get_all();

        foreach ($channels as $i => $channel) {
            $channel_type = '';
            switch ($channel->channel_type) {
                case '1':
                    $channel_type = 'Transfer';
                    break;
                case '2':
                    $channel_type = 'Virtual Account';
                    break;
            }
            $id_payment_channel_options[] = [
                'id' => 'cb_' . $i,
                'value' => $channel->id_payment_channel,
                'label' => $channel_type . ' ' . $channel->channel_name,
                'checked' => in_array($channel->id_payment_channel, $old_id_payment_channels),
            ];
        }
        $jadwal_options = [];
        foreach ($old_ids_jadwal as $i => $jadwal) {
            $jadwal_options[] = date('d/m/Y', strtotime($jadwal));
        }
        $this->data['form'] = [[
            'id' => 'id_seleksi',
            'type' => 'hidden',
            'value' => $id_seleksi,
        ], [
            'id' => 'id_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-'] + $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi'),
            'value' => set_value('id_program_studi', $seleksi->id_program_studi),
        ], [
            'id' => 'nama_seleksi',
            'label' => 'Nama',
            'value' => set_value('nama_seleksi', $seleksi->nama_seleksi),
        ], [
            'id' => 'payment',
            'label' => 'Biaya Pendaftaran',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'payment_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('payment', '1', $seleksi->payment == '1'),
                ),
                array(
                    'id' => 'payment_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('payment', '0', $seleksi->payment == '0'),
                )
            )
        ], [
            'id' => 'biaya',
            'label' => 'Jumlah Biaya',
            'value' => set_value('biaya', $seleksi->biaya),
            'form_control_class' => 'col-sm-3',
            'input_addons' => array(
                'pre' => 'Rp',
            ),
            'input_container_class' => 'biaya ' . (set_value('payment', $seleksi->payment) == 1 ? '' : 'd-none'),
        ], [
            'id' => 'id_payment_channels',
            'name' => 'id_payment_channels[]',
            'label' => 'Metode Pembayaran',
            'type' => 'checkbox',
            'options' => $id_payment_channel_options,
            'input_container_class' => 'biaya ' . (set_value('payment', $seleksi->payment) == 1 ? '' : 'd-none'),
        ], [
            'id' => 'tes_seleksi',
            'label' => 'Tes Seleksi',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'tes_seleksi_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('tes_seleksi', '1', $seleksi->tes_seleksi == '1'),
                ),
                array(
                    'id' => 'tes_seleksi_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('tes_seleksi', '0', $seleksi->tes_seleksi == '0'),
                )
            )
        ], [
            'id' => 'tgl_seleksi_option',
            'label' => 'Jadwal Seleksi',
            'type' => 'radio',
            'options' => array(
                array(
                    'id' => 'tgl_seleksi_option_1',
                    'value' => '1',
                    'label' => 'Ya',
                    'checked' => set_checkbox('tgl_seleksi_option', '1', $seleksi->tgl_seleksi_option == '1'),
                ),
                array(
                    'id' => 'tgl_seleksi_option_2',
                    'value' => '0',
                    'label' => 'Tidak',
                    'checked' => set_checkbox('tgl_seleksi_option', '0', $seleksi->tgl_seleksi_option == '0'),
                )
            )
        ], [
            'id' => 'jadwal',
            'name' => 'jadwal',
            'label' => 'Tanggal Seleksi',
            'value' => implode(',', $jadwal_options),
            'input_container_class' => set_value('tes_seleksi', $seleksi->tes_seleksi) == 1 && set_value('tgl_seleksi_option', $seleksi->tgl_seleksi_option) == 1 ? '' : 'd-none',
        ], [
            'type' => 'combine',
            'label' => 'none',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/seleksi') . "'",
            ]],
        ]];

        $this->data['form_modal'] = [[
            'id' => 'index',
            'type' => 'hidden',
        ], [
            'id' => 'waktu',
            'label' => 'Waktu',
            'class' => 'datetimepicker',
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'tempat',
            'label' => 'Tempat',
            'control_label_class' => 'col-sm-12 col-form-label',
            // ], [
            //     'id' => 'biaya',
            //     'label' => 'Biaya',
            //     'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'tgl_pembukaan',
            'label' => 'Tgl Pembukaan',
            'class' => 'datepicker',
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'tgl_penutupan',
            'label' => 'Tgl Penutupan',
            'class' => 'datepicker',
            'control_label_class' => 'col-sm-12 col-form-label',
        ]];

        $this->load->view('template_backoffice', $this->data);
    }

    public function get_jadwal()
    {
        $response = new stdClass();
        $id_seleksi = $this->input->get('id_seleksi');

        $response->rows = $this->Jadwal_seleksi_model->get_many_by('id_seleksi', $id_seleksi);

        echo json_encode($response);
    }
}
