<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Jadwal_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->jadwal();
    }

    public function jadwal()
    {
        $this->data['title'] = 'Jadwal';

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
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'tanggal' => date_format(date_create_from_format('d/m/Y', $this->input->post('tanggal')), 'Y-m-d'),
            ];

            if ($this->Jadwal_model->insert($data)) {
                redirect('backoffice/jadwal', 'refresh');
            }
        }

        $this->data['title'] = 'Jadwal';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'tanggal',
            'label' => 'Tanggal',
            'class' => 'datepicker',
            'value' => set_value('tanggal'),
            'control_label_class' => 'col-sm-12 col-form-label',
            'form_control_class' => 'col-sm-3',
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
                'onclick' => "window.location.href='" . base_url('backoffice/jadwal') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Jadwal_model->count_all();

        $filter = [];
        $response->rows = $this->Jadwal_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_jadwal)
    {
        $jadwal = $this->Jadwal_model->get($id_jadwal);

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'tanggal' => date_format(date_create_from_format('d/m/Y', $this->input->post('tanggal')), 'Y-m-d'),
            ];

            if ($this->Jadwal_model->update($id_jadwal, $data)) {
                redirect('backoffice/jadwal', 'refresh');
            }
        }

        $this->data['title'] = 'Jadwal';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'tanggal',
            'label' => 'Tanggal',
            'class' => 'datepicker',
            'value' => set_value('tanggal', date('d/m/Y', strtotime($jadwal->tanggal))),
            'control_label_class' => 'col-sm-3 col-form-label',
            'form_control_class' => 'col-sm-3',
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
                'onclick' => "window.location.href='" . base_url('backoffice/jadwal') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }

}
