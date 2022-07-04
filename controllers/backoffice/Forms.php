<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Forms extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
            'Forms_model',
            'Forms_fields_model',
            'Fields_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->forms();
    }

    public function forms()
    {
        $this->data['title'] = 'Forms';

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
        $this->form_validation->set_rules('form_name', 'Nama', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'form_name' => $this->input->post('form_name'),
            ];

            if ($this->Forms_model->insert($data)) {
                redirect('backoffice/forms', 'refresh');
            }
        }

        $this->data['title'] = 'Forms';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'form_name',
            'label' => 'Nama',
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
                'onclick' => "window.location.href='" . base_url('backoffice/forms') . "'",
            ]],
        ]];

        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Forms_model->count_all();

        $filter = [];
        $response->rows = $this->Forms_model
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_forms)
    {
        $forms = $this->Forms_model->get($id_forms);
        $this->form_validation->set_rules('form_name', 'Nama', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id_form_fields = $this->input->post('id_form_field');
            $id_fields = $this->input->post('id_field');

            $data = [
                'form_name' => $this->input->post('form_name'),
            ];

            $this->db->trans_begin();
            if ($this->Forms_model->update($id_forms, $data)) {
                if ($id_fields) {
                    $forms_fields = $this->Forms_fields_model->select('id_field')->as_array()->get_many_by('id_form', $id_forms);
                    $forms_fields_ids = array_column($forms_fields, 'id_field');
                    $deleted_ids = array_diff($forms_fields_ids, $id_fields);

                    foreach ($deleted_ids as $i => $id_field) {
                        $filter = [
                            'id_form' => $id_forms,
                            'id_field' => $id_field,
                        ];
                        $this->Forms_fields_model->delete_by($filter);
                    }

                    foreach ($id_fields as $i => $id_field) {
                        $id_form_field = $id_form_fields[$i];
                        $data = [
                            'id_form' => $id_forms,
                            'id_field' => $id_field,
                        ];

                        if ($id_form_field == '') {
                            $this->Forms_fields_model->insert($data);
                        } else {
                            $this->Forms_fields_model->update($id_form_field, $data);
                        }
                    }
                } else {
                    $filter = [
                        'id_form' => $id_forms,
                    ];
                    $this->Forms_fields_model->delete_by($filter);
                }
            }

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                redirect('backoffice/forms', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->data['title'] = 'Forms';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'id_form',
            'type' => 'hidden',
            'value' => set_value('id_form', $forms->id_form),
        ], [
            'id' => 'form_name',
            'label' => 'Nama',
            'value' => set_value('form_name', $forms->form_name),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'form_fields',
            'label' => 'Fields',
            'type' => 'html',
            'html' => '<div id="toolbar"><a href="#" class="btn btn-primary" id="btn-add-field">Add Field</a></div><table id="datatable" data-toolbar="toolbar"></table>',
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
                'onclick' => "window.location.href='" . base_url('backoffice/forms') . "'",
            ]],
        ]];

        $this->data['fields'] = [
            'id' => 'id_field',
            'name' => 'id_field',
            'type' => 'dropdown',
            'options' => ['' => '-Pilih field-'], // + $this->Fields_model->dropdown('id_field', 'field_label'),
            'class' => 'form-control select2',
        ];

        $this->load->view('template_backoffice', $this->data);
    }

    public function fields_data()
    {
        $response  = new stdClass();
        $id_form = $this->input->get('id_form');

        $response->total =  $this->Fields_model->count_all();

        $response->rows = $this->Fields_model
            ->select('fields.*, forms_fields.id_form_field')
            ->join("forms_fields", "fields.id_field = forms_fields.id_field AND id_form = $id_form", "left")
            ->get_all();

        echo json_encode($response);
    }
}
