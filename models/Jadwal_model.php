<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jadwal_model extends MY_Model
{

    public $_table = 'jadwal';
    public $primary_key = 'id_jadwal';


    function __construct()
    {
        parent::__construct();
    }
}
