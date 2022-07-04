<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Skema_pembayaran_model extends MY_Model
{

    public $_table = 'skema_pembayaran';
    public $primary_key = 'id_skema_pembayaran';

    public $belongs_to = array(
        // 'kota' => array('model' => 'Kota_model', 'primary_key' => 'id_kota', 'foreign_key' => 'kode_kota'),
        // 'hub' => array('model' => 'Kota_model', 'primary_key' => 'id_hub_kota', 'foreign_key' => 'kode_kota'),
    );

    function __construct()
    {
        parent::__construct();
    }
}
