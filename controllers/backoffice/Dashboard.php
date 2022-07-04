<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->data['title'] = 'Dashboard';
        $this->data['content'] = 'backoffice/backoffice';
        $this->data['css'] = array(
        );
        $this->data['js_plugins'] = array(
        );
        $this->data['js'] = array(
            'functions/backoffice/dashboard/backoffice.js'
        );

        $this->load->view('template_backoffice', $this->data);
    }
}
