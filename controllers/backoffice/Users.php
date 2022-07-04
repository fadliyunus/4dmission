<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array());

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->data['title'] = 'Users';
        $this->data['content'] = 'backoffice/users';
        $this->data['css'] = array();
        $this->data['js_plugins'] = array();
        $this->data['js'] = array(
            'functions/backoffice/users/users.js'
        );

        $this->load->view('template_backoffice', $this->data);
    }

    public function data()
    {
        $response  = new stdClass();

        $response->total = count($this->ion_auth->users('members')->result());
        $response->rows = $this->ion_auth->select('users.id, users.email, full_name, phone')->users('members')->result();

        echo json_encode($response);
    }

    public function delete($id)
    {
        $response = new stdClass();
        $response->status = FALSE;

        if ($this->ion_auth->delete_user($id)) {
            $response->status = TRUE;
        }

        echo json_encode($response);
    }
}
