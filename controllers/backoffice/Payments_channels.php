<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payments_channels extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
            'Payments_channels_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->payments_channels();
    }

    public function payments_channels()
    {
        $this->data['title'] = 'Channel Pembayaran';

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
        $this->form_validation->set_rules('channel_type', 'Tipe', 'required');
        $this->form_validation->set_rules('channel_name', 'Nama Bank/Nama Channel', 'required');
        $this->form_validation->set_rules('channel_account_no', 'No Rekening/Kode Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->input->post();
            unset($data['btn-submit']);

            if ($this->Payments_channels_model->insert($data)) {
                redirect('backoffice/payments_channels', 'refresh');
            }
        }

        $this->data['title'] = 'Edit Channel Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');

        $this->data['form'] = [[
            'id' => 'channel_type',
            'label' => 'Tipe',
            'type' => 'dropdown',
            'options' => ['0' => '-Pilih-', '1' => 'Transfer'],
            'value' => set_value('channel_type'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_name',
            'label' => 'Nama Bank/Nama Channel',
            'value' => set_value('channel_name'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_account_no',
            'label' => 'No Rekening/Kode Pembayaran',
            'value' => set_value('channel_account_no'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_account_name',
            'label' => 'Nama Rekening',
            'value' => set_value('channel_account_name'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [

            'id' => 'buttons',
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
                'onclick' => "window.location.href='" . base_url('backoffice/payments_channels') . "'",
            ]],
        ]];



        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Payments_channels_model->count_all();

        $filter = [];
        $response->rows = $this->Payments_channels_model
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_payment_channel)
    {
        $channel = $this->Payments_channels_model->get($id_payment_channel);

        $this->form_validation->set_rules('channel_type', 'Tipe', 'required');
        $this->form_validation->set_rules('channel_name', 'Nama Bank/Nama Channel', 'required');
        $this->form_validation->set_rules('channel_account_no', 'No Rekening/Kode Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->input->post();
            unset($data['btn-submit']);

            if ($this->Payments_channels_model->update($id_payment_channel, $data)) {
                redirect('backoffice/payments_channels', 'refresh');
            }
        }

        $this->data['title'] = 'Edit Channel Pembayaran';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'channel_type',
            'label' => 'Tipe',
            'type' => 'dropdown',
            'options' => ['0' => '-Pilih-', '1' => 'Transfer'],
            'value' => set_value('channel_type', $channel->channel_type),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_name',
            'label' => 'Nama Bank/Nama Channel',
            'value' => set_value('channel_name', $channel->channel_name),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_account_no',
            'label' => 'No Rekening/Kode Pembayaran',
            'value' => set_value('channel_account_no', $channel->channel_account_no),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'channel_account_name',
            'label' => 'Nama Rekening',
            'value' => set_value('channel_account_name', $channel->channel_account_name),
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
                'onclick' => "window.location.href='" . base_url('backoffice/payments_channels') . "'",
            ]]

        ]];

        $this->load->view('template_backoffice', $this->data);
    }

    public function get_options()
    {
        $response = new stdClass();
        $id_field = $this->input->get('id_field');

        $response->rows = $this->Payments_channels_options_model->get_many_by('id_field', $id_field);
        echo json_encode($response);
    }
}
