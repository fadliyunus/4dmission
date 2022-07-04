<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vouchers_model extends MY_Model
{

    public $_table = 'vouchers';
    public $primary_key = 'id_voucher';


    function __construct()
    {
        parent::__construct();
    }
}
