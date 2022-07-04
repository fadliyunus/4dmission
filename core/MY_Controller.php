<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $data = array();

    public function __construct()
    {
        parent::__construct();

        // $this->load->config('rfq');
        // $this->load->library(array('form_builder'));
        $this->load->helper('admission');
        $this->load->model(array(
            'Admissions_model',
        ));

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        if ($this->ion_auth->logged_in()) {
            $filter = [
                'id_user' => $this->ion_auth->user()->row()->id,
                'status' => 400,
            ];
            $this->data['admission_accepted'] = $this->Admissions_model->get_by($filter);
        }
    }
}
