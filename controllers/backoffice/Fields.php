<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fields extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
            'Fields_model',
            'Fields_options_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->fields();
    }

    public function fields()
    {
        $this->data['title'] = 'Fields';

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
        $this->form_validation->set_rules('field_name', 'Nama', 'required');
        $this->form_validation->set_rules('field_label', 'Label', 'required');
        $this->form_validation->set_rules('field_type', 'Jenis', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'field_name' => $this->input->post('field_name'),
                'field_label' => $this->input->post('field_label'),
                'field_type' => $this->input->post('field_type'),
                'field_visible' => $this->input->post('field_visible'),
                'field_table' => $this->input->post('field_table'),
            ];

            if ($this->Fields_model->insert($data)) {
                redirect('backoffice/fields', 'refresh');
            }
        }

        $this->data['title'] = 'Fields';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where("field_type IN ('table')");
        $fieldtable_options = ['' => '-Pilih table-'] + $this->Fields_model->dropdown('id_field', 'field_name');

        $this->data['form'] = [[
            'id' => 'field_name',
            'label' => 'Nama',
            'value' => set_value('field_name'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_label',
            'label' => 'Label',
            'value' => set_value('field_label'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_visible',
            'label' => 'Ditampilkan',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('field_visible'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_type',
            'label' => 'Jenis',
            'type' => 'dropdown',
            'options' => ['control_static'=>'text','input' => 'input', 'dropdown' => 'dropdown', 'radio' => 'radio', 'checkbox' => 'checkbox', 'file'=>'file','date' => 'date', 'table' => 'table','hidden'=>'hidden'],
            'value' => set_value('field_type'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_table',
            'type' => 'dropdown',
            'options' => $fieldtable_options,
            'value' => set_value('field_table'),
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
                'onclick' => "window.location.href='" . base_url('backoffice/fields') . "'",
            ]],
        ]];

        
        
        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Fields_model->count_all();

        $filter = [];
        $response->rows = $this->Fields_model
            ->select('fields.*, b.field_label table_label, b.field_name table_name')
            ->join('fields b','b.id_field = fields.field_table','left')
            ->get_many_by($filter);

        echo json_encode($response);
    }

    public function edit($id_fields)
    {
        $fields = $this->Fields_model->get($id_fields);

        $this->form_validation->set_rules('field_name', 'Nama', 'required');
        $this->form_validation->set_rules('field_label', 'Label', 'required');
        $this->form_validation->set_rules('field_type', 'Jenis', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id_field_options = $this->input->post('id_field_option');
            $option_texts = $this->input->post('option_text');
            $option_values = $this->input->post('option_value');

            $data = [
                'field_name' => $this->input->post('field_name'),
                'field_label' => $this->input->post('field_label'),
                'field_type' => $this->input->post('field_type'),
                'field_visible' => $this->input->post('field_visible'),
                'field_dependency' => $this->input->post('field_dependency'),
                'field_dependency_value' => $this->input->post('field_dependency_value'),
                'field_table' => $this->input->post('field_table'),
            ];

            $this->db->trans_begin();
            if ($this->Fields_model->update($id_fields, $data)) {
                if ($id_field_options) {
                    foreach ($id_field_options as $i => $id_field_option) {
                        $data = [
                            'id_field' => $id_fields,
                            'option_text' => $option_texts[$i],
                            'option_value' => $option_values[$i],
                        ];

                        if ($id_field_option) {
                            $this->Fields_options_model->update($id_field_option, $data);
                        } else {
                            $this->Fields_options_model->insert($data);
                        }
                    }
                }
            }

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                redirect('backoffice/fields', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->data['title'] = 'Fields';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where("field_type IN ('checkbox','radio','dropdown')");
        $field_dependency_options = ['' => '-Pilih field-'] + $this->Fields_model->dropdown('id_field', 'field_name');

        $this->db->where("id_field", $fields->field_dependency);
        $field_dependency_value_options = ['' => '-Pilih value-'] + $this->Fields_options_model->dropdown('option_value', 'option_text');

        $this->db->where("field_type IN ('table')");
        $fieldtable_options = ['' => '-Pilih table-'] + $this->Fields_model->dropdown('id_field', 'field_name');

        $this->data['form'] = [[
            'id' => 'id_field',
            'type' => 'hidden',
            'value' => $fields->id_field,
        ], [
            'id' => 'field_name',
            'label' => 'Nama',
            'value' => set_value('field_name', $fields->field_name),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_label',
            'label' => 'Label',
            'value' => set_value('field_label', $fields->field_label),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_visible',
            'label' => 'Ditampilkan',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('field_visible', $fields->field_visible),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_dependency',
            'label' => 'Dependency',
            'type' => 'dropdown',
            'options' => $field_dependency_options,
            'value' => set_value('field_dependency', $fields->field_dependency),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_dependency_value',
            'label' => 'Value',
            'type' => 'dropdown',
            'options' => $field_dependency_value_options,
            'value' => set_value('field_dependency_value', $fields->field_dependency_value),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_type',
            'label' => 'Jenis',
            'type' => 'dropdown',
            'options' => ['control_static'=>'text','input' => 'input', 'dropdown' => 'dropdown', 'radio' => 'radio', 'checkbox' => 'checkbox', 'file'=>'file', 'date' => 'date', 'table' => 'table','hidden'=>'hidden'],
            'value' => set_value('field_type', $fields->field_type),
            'control_label_class' => 'col-sm-12 col-form-label',
        ]];

        if ($fields->field_type == 'radio' || $fields->field_type == 'checkbox' || $fields->field_type == 'dropdown') {
            $this->data['form'][] = [
                'id' => 'fields_options',
                'type' => 'html',
                'label' => 'Pilihan field',
                'html' => '<div id="toolbar"><a href="#" class="btn btn-primary" id="btn-add-option">Add Option</a></div><table id="datatable" data-toolbar="#toolbar"></table>',
            ];
        }

        // if ($fields->field_type == 'table_field') {
        $this->data['form'][] = [
            'id' => 'field_table',
            'type' => 'dropdown',
            'options' => $fieldtable_options,
            'value' => set_value('field_table', $fields->field_table),
        ];
        // }

        $this->data['form'][] = [
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
                'onclick' => "window.location.href='" . base_url('backoffice/fields') . "'",
            ]]
        ];

        $this->data['option_text'] = [
            'id' => 'option_text',
            'name' => 'option_text',
            'class' => 'form-control',
        ];

        $this->data['option_value'] = [
            'id' => 'option_value',
            'name' => 'option_value',
            'class' => 'form-control',
        ];

        $this->load->view('template_backoffice', $this->data);
    }

    public function get_options()
    {
        $response = new stdClass();
        $id_field = $this->input->get('id_field');

        $response->rows = $this->Fields_options_model->get_many_by('id_field', $id_field);
        echo json_encode($response);
    }
}
