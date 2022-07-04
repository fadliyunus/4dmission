<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Potongan extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Biaya_model',
            'Program_studi_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->potongan();
    }

    public function potongan()
    {
        $this->data['title'] = 'Potongan';

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
        $this->form_validation->set_rules('jenis_program_studi', 'Program', 'required');
        $this->form_validation->set_rules('nama_biaya', 'Nama', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'jenis_program_studi' => $this->input->post('jenis_program_studi'),
                'id_program_studi' => $this->input->post('id_program_studi'),
                'nama_biaya' => $this->input->post('nama_biaya'),
                'jumlah' => str_replace('.','',$this->input->post('jumlah')),
                'sign' => -1,
                'discount' => 1.
            ];

            if ($this->Biaya_model->insert($data)) {
                redirect('backoffice/potongan', 'refresh');
            }
        }

        $this->data['title'] = 'Potongan';
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $id_program_studi_options = ['' => '-Pilih program studi-'];
        if (set_value('jenis_program_studi')) {
            $this->db->where('jenis_program_studi', set_value('jenis_program_studi'));
            $id_program_studi_options += $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        }

        $this->data['form'] = [[
            'id' => 'jenis_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-', '1' => 'Sarjana', '2' => 'Magister'],
            'value' => set_value('jenis_program_studi'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'class' => 'form-control select2',
        ], [
            'id' => 'id_program_studi',
            'label' => 'Program Studi',
            'type' => 'dropdown',
            'options' => $id_program_studi_options,
            'value' => set_value('id_program_studi'),
            'control_label_class' => 'col-sm-3 col-form-label',
            'class' => 'form-control select2',
        ], [
            'id' => 'nama_biaya',
            'label' => 'Nama',
            'control_label_class' => 'col-sm-3 col-form-label',
        ], [
            'id' => 'jumlah',
            'label' => 'Jumlah',
            'control_label_class' => 'col-sm-3 col-form-label',
        ], [
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
                'onclick' => "window.location.href='" . base_url('backoffice/potongan') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $filter = [
            'discount' => 1,
        ];
        $response->total =  $this->Biaya_model
            ->count_by($filter);

        $response->rows = $this->Biaya_model
            ->select('biaya.*, program_studi.nama_program_studi')
            ->join('program_studi', 'program_studi.id_program_studi = biaya.id_program_studi', 'left')
            ->order_by([
                'jenis_program_studi' => 'asc',
                'id_program_studi' => 'asc',
                'nama_biaya' => 'asc',
            ])
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_biaya)
    {
        $biaya = $this->Biaya_model->get($id_biaya);

        $this->form_validation->set_rules('jenis_program_studi', 'Program', 'required');
        $this->form_validation->set_rules('nama_biaya', 'Nama', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'jenis_program_studi' => $this->input->post('jenis_program_studi'),
                'id_program_studi' => $this->input->post('id_program_studi'),
                'nama_biaya' => $this->input->post('nama_biaya'),
                'jumlah' => str_replace('.','',$this->input->post('jumlah')),
            ];

            if ($this->Biaya_model->update($id_biaya, $data)) {
                redirect('backoffice/potongan', 'refresh');
            }
        }

        $this->data['title'] = 'Potongan';
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $id_program_studi_options = ['' => '-Pilih program studi-'];
        if (set_value('jenis_program_studi', $biaya->jenis_program_studi)) {
            $this->db->where('jenis_program_studi', set_value('jenis_program_studi', $biaya->jenis_program_studi));
            $id_program_studi_options += $this->Program_studi_model->dropdown('id_program_studi', 'nama_program_studi');
        }

        $this->data['form'] = [[
            'id' => 'jenis_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-', '1' => 'Sarjana', '2' => 'Magister'],
            'value' => set_value('jenis_program_studi', $biaya->jenis_program_studi),
            'control_label_class' => 'col-sm-3 col-form-label',
            'class' => 'form-control select2',
        ], [
            'id' => 'id_program_studi',
            'label' => 'Program Studi',
            'type' => 'dropdown',
            'options' => $id_program_studi_options,
            'value' => set_value('id_program_studi', $biaya->id_program_studi),
            'control_label_class' => 'col-sm-3 col-form-label',
            'class' => 'form-control select2',
        ], [
            'id' => 'nama_biaya',
            'label' => 'Nama',
            'control_label_class' => 'col-sm-3 col-form-label',
            'value' => set_value('nama_biaya', $biaya->nama_biaya)
        ], [
            'id' => 'jumlah',
            'label' => 'Jumlah',
            'control_label_class' => 'col-sm-3 col-form-label',
            'value' => set_value('jumlah', $biaya->jumlah)
        ], [
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
                'onclick' => "window.location.href='" . base_url('backoffice/potongan') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }

    public function delete()
    {
        $response = new stdClass();
        $response->status = false;

        $id_biaya = $this->input->post('id_biaya');
        if ($this->Biaya_model->delete($id_biaya)) {
            $response->status = true;
        }

        echo json_encode($response);
    }
}
