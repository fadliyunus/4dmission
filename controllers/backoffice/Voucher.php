<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Voucher extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Vouchers_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->voucher();
    }

    public function voucher()
    {
        $this->data['title'] = 'Voucher';

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
        $this->form_validation->set_rules('voucher_name', 'Nama', 'required');
        $this->form_validation->set_rules('voucher_code', 'Kode', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'voucher_name' => $this->input->post('voucher_name'),
                'voucher_code' => strtoupper($this->input->post('voucher_code')),
                'voucher_active' => $this->input->post('voucher_active'),
            ];

            if ($this->Vouchers_model->insert($data)) {
                redirect('backoffice/voucher', 'refresh');
            }
        }

        $this->data['title'] = 'Voucher';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'voucher_name',
            'label' => 'Nama',
            'value' => set_value('voucher_name'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'voucher_code',
            'label' => 'Kode',
            'value' => set_value('voucher_code'),
            'control_label_class' => 'col-sm-12 col-form-label',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'voucher_active',
            'label' => 'Aktif',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('voucher_active'),
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
                'onclick' => "window.location.href='" . base_url('backoffice/voucher') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Vouchers_model->count_all();

        $filter = [];
        $response->rows = $this->Vouchers_model->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_voucher)
    {
        $voucher = $this->Vouchers_model->get($id_voucher);

        $this->form_validation->set_rules('voucher_name', 'Nama', 'required');
        $this->form_validation->set_rules('voucher_code', 'Kode', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'voucher_name' => $this->input->post('voucher_name'),
                'voucher_code' => strtoupper($this->input->post('voucher_code')),
                'voucher_active' => $this->input->post('voucher_active'),
            ];

            if ($this->Vouchers_model->update($id_voucher, $data)) {
                redirect('backoffice/voucher', 'refresh');
            }
        }

        $this->data['title'] = 'Voucher';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'voucher_name',
            'label' => 'Nama',
            'value' => set_value('voucher_name', $voucher->voucher_name),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'voucher_code',
            'label' => 'Kode',
            'value' => set_value('voucher_code', $voucher->voucher_code),
            'control_label_class' => 'col-sm-12 col-form-label',
            'style' => 'text-transform:uppercase',
        ], [
            'id' => 'voucher_active',
            'label' => 'Aktif',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('voucher_active', $voucher->voucher_active),
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
                'onclick' => "window.location.href='" . base_url('backoffice/voucher') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
}
