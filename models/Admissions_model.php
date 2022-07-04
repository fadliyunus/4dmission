<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admissions_model extends MY_Model
{

    public $_table = 'admissions';
    public $primary_key = 'id_admission';

    public $belongs_to = array(
        // 'kota' => array('model' => 'Kota_model', 'primary_key' => 'id_kota', 'foreign_key' => 'kode_kota'),
        // 'hub' => array('model' => 'Kota_model', 'primary_key' => 'id_hub_kota', 'foreign_key' => 'kode_kota'),
    );

    function __construct()
    {
        parent::__construct();
    }

    public function get_skema_pembayaran($id_seleksi, $tgl_seleksi = '') {
        $filter = [
            'id_seleksi' => $id_seleksi
        ];
        if ($tgl_seleksi) {
            $filter['tgl_seleksi'] = $tgl_seleksi;
        }
        return $this->Skema_pembayaran_model->get_many_by($filter);
    }
}
