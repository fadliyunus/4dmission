<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Biaya_model extends MY_Model
{

    public $_table = 'biaya';
    public $primary_key = 'id_biaya';


    function __construct()
    {
        parent::__construct();
    }
}
