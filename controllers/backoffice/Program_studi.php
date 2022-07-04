<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Program_studi extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->program_studi();
    }

    public function program_studi()
    {
        $this->data['title'] = 'Program Studi';

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
        $this->form_validation->set_rules('nama_program_studi', 'Nama', 'required');
        $this->form_validation->set_rules('jenis_program_studi', 'Program', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'nama_program_studi' => $this->input->post('nama_program_studi'),
                'jenis_program_studi' => $this->input->post('jenis_program_studi'),
                'active' => $this->input->post('active'),
            ];

            if ($this->Program_studi_model->insert($data)) {
                redirect('backoffice/program_studi', 'refresh');
            }
        }

        $this->data['title'] = 'Program Studi';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'nama_program_studi',
            'label' => 'Nama',
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'jenis_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-', '1' => 'Sarjana', '2' => 'Magister'],
            'value' => set_value('jenis_program_studi'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'active',
            'label' => 'Aktif',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('active'),
            'form_control_class' => 'col-sm-3',
            'control_label_class' => 'col-sm-12 col-form-label',
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
                'onclick' => "window.location.href='" . base_url('backoffice/program_studi') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Program_studi_model->count_all();

        $filter = [];
        $response->rows = $this->Program_studi_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_program_studi)
    {
        $program_studi = $this->Program_studi_model->get($id_program_studi);

        $this->form_validation->set_rules('nama_program_studi', 'Nama', 'required');
        $this->form_validation->set_rules('jenis_program_studi', 'Program', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'nama_program_studi' => $this->input->post('nama_program_studi'),
                'jenis_program_studi' => $this->input->post('jenis_program_studi'),
                'active' => $this->input->post('active'),
            ];

            if ($this->Program_studi_model->update($id_program_studi, $data)) {
                redirect('backoffice/program_studi', 'refresh');
            }
        }

        $this->data['title'] = 'Program Studi';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'nama_program_studi',
            'label' => 'Nama',
            'value' => $program_studi->nama_program_studi,
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'jenis_program_studi',
            'label' => 'Program',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih program-', '1' => 'Sarjana', '2' => 'Magister'],
            'value' => set_value('jenis_program_studi', $program_studi->jenis_program_studi),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'active',
            'label' => 'Aktif',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('active', $program_studi->active),
            'form_control_class' => 'col-sm-3',
            'control_label_class' => 'col-sm-12 col-form-label',
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
                'onclick' => "window.location.href='" . base_url('backoffice/program_studi') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
}
