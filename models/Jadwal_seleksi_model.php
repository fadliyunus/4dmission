<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jadwal_seleksi_model extends MY_Model
{

    public $_table = 'jadwal_seleksi';
    public $primary_key = 'id_jadwal_seleksi';


    function __construct()
    {
        parent::__construct();
    }
}
