<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Personal_informations_model extends MY_Model
{

    public $_table = 'personal_informations';
    public $primary_key = 'id_personal_information';

    public $belongs_to = array(
        // 'kota' => array('model' => 'Kota_model', 'primary_key' => 'id_kota', 'foreign_key' => 'kode_kota'),
        // 'hub' => array('model' => 'Kota_model', 'primary_key' => 'id_hub_kota', 'foreign_key' => 'kode_kota'),
    );

    function __construct()
    {
        parent::__construct();
    }
}
