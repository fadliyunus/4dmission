<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Admissions_model',
            'Acceptances_model',
            'Payments_model',
            'Seleksi_model',
            'Seleksi_payments_model',
            'Seleksi_payments_channels_model',
            'Skema_pembayaran_model',
            'Skema_pembayaran_detail_model',
            'Biaya_model',
            'Users_discounts_model',
            'Jadwal_seleksi_model',
            'Payments_channels_model',
        ));
        $this->load->model('Vouchers_model');

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    function index()
    {
        $this->payment();
    }

    function payment()
    {
        $filter = [
            'id_user' => $this->ion_auth->user()->row()->id,
            'status' => 400,
        ];
        $admission = $this->Admissions_model
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->get_by($filter);

        if ($admission) {
            $filter = [
                'id_seleksi' => $admission->seleksi,
            ];
            if ($admission->tgl_seleksi) {
                $filter['tgl_seleksi'] = $admission->tgl_seleksi;
            }
            $skema_pembayaran = $this->Skema_pembayaran_model->get_many_by($filter);

            if ($admission->program == 1) {
                if (!$admission->jenis_angsuran) {
                    $master_angsuran = [1, 4, 6, 9, 12];
                    $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

                    $datetime1 = date_create($admission->tgl_seleksi);
                    $datetime2 = date_create('2022-09-01');
                    $interval = date_diff($datetime1, $datetime2);
                    $angsuran_interval =  $interval->format('%m');

                    $angsuran = [];
                    $konfirmasi = [];
                    $angsuran_per_bulan = [];
                    $angsuran_lain = [];

                    $max_angsuran = 1;

                    foreach ($master_angsuran as $i => $jenis_angsuran) {
                        if ($jenis_angsuran <= $angsuran_interval) {
                            $max_angsuran = $jenis_angsuran;
                            $angsuran[] = $jenis_angsuran;
                        }
                    }

                    if (count($angsuran) > 1) {
                        redirect(base_url('payment/type/' . $admission->id_admission), 'refresh');
                    } elseif (count($angsuran) == 1) {
                        $this->Admissions_model->update($admission->id_admission, ['jenis_angsuran' => $angsuran[0]]);
                    }
                }
            } else {
                if (!$admission->jenis_pembayaran) {
                    if (count($skema_pembayaran) > 1) {
                        redirect(base_url('payment/type/' . $admission->id_admission), 'refresh');
                    } elseif (count($skema_pembayaran) == 1) {
                        $this->Admissions_model->update($admission->id_admission, ['jenis_pembayaran' => $skema_pembayaran[0]->id_skema_pembayaran]);
                    }
                }



                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'tgl_seleksi' => $admission->tgl_seleksi,
                ];
                $admission->skema_pembayaran = $this->Skema_pembayaran_model->get_by($filter);
            }

            $filter = [
                'id_admission' => $admission->id_admission,
                'payment_type' => 2,
                'payment_status' => 1,
            ];
            $admission->confirmation = $this->Payments_model->get_by($filter);

            $filter = [
                'id_admission' => $admission->id_admission,
                'payment_type' => 3,
                'payment_status' => 1,
            ];
            $admission->installment = $this->Payments_model->get_many_by($filter);

            $filter = [
                'id_admission' => $admission->id_admission,
                'payment_type' => 3,
                'payment_status' => 0,
            ];
            $admission->installment_validation = $this->Payments_model->get_many_by($filter);



            $this->data['admission'] = $admission;
            $this->data['title'] = 'Pembayaran';

            $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
            $content = strtolower(static::class . '/' . __FUNCTION__);
            $this->data['content'] =  $content;
            $this->data['css'] = [];
            $this->data['js_plugins'] = [];
            $this->data['js'] = [
                JS_FUNCTION_DIR . $content . '.js',
            ];

            $this->load->view('template_home', $this->data);
        } else {
            show_404();
        }
    }

    function data()
    {
        $response = new stdClass();

        $response->rows = [];

        if ($this->ion_auth->logged_in()) {
            // $filter = [
            //     'id_user' => $this->ion_auth->user()->row()->id,
            //     'payment_type' => '  1',
            // ];
            $user_id = $this->ion_auth->user()->row()->id;
            $response->user_id = $user_id;
            $rows = $this->Payments_model
                ->join('admissions', 'payments.id_admission = admissions.id_admission', 'left')
                // ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
                // ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
                // ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.jadwal', 'left')
                // ->join('jadwal', 'jadwal_seleksi.id_jadwal = jadwal.id_jadwal', 'left')
                ->order_by('id_payment')
                ->get_many_by('payment_type > 1 AND id_user = ' . $user_id);

            foreach ($rows as $row) {
                // if ($row->payment_type == 2) {
                //     $row->jumlah = 0;
                // } elseif ($row->payment_type == 3) {
                $row->jumlah = $this->get_installment_amount($row->id_admission, $row->payment_installment);
                // }
            }
            $response->rows = $rows;
        }
        echo json_encode($response);
    }

    function registration($id_admission)
    {
        $this->data['admission_fee'] = 0;
        $this->data['payment_code'] = 0;
        $this->data['total_fee'] = 0;

        $admission = $this->Admissions_model->get($id_admission);

        if ($admission->status > 101 && $admission->status != 103) {
            redirect('payment/status/' . $id_admission, 'refresh');
        }

        $payment_channels = [];

        if ($admission) {
            $payment = $this->Payments_model->get_by('id_admission', $admission->id_admission);
            $seleksi = $this->Seleksi_model
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get($admission->seleksi);
            $this->data['seleksi'] = $seleksi;
            // $seleksi_payment = $this->Seleksi_payments_model->get_by('id_seleksi', $seleksi->id_seleksi);

            $payment_channels = $this->Seleksi_payments_channels_model
                ->join('payments_channels', 'payments_channels.id_payment_channel = seleksi_payments_channels.id_payment_channel', 'left')
                ->get_many_by('id_seleksi', $seleksi->id_seleksi);

            $this->data['payment_channels'] = $payment_channels;

            $this->data['admission_fee'] = $seleksi->biaya;
            $this->data['payment_code'] = $payment->payment_code;
            $this->data['total_fee'] = $seleksi->biaya + $payment->payment_code;
        }

        if ($this->input->post()) {
            $payment_channel_type = $this->input->post('payment_channel_type');
            if ($payment_channel_type == 1) {
                if (empty($_FILES['transfer_receipt']['name'])) {
                    $this->session->set_flashdata('error_message', 'Bukti Transfer belum diisi');
                    redirect(base_url('payment/registration/' . $id_admission), 'refresh');
                } else {
                    $error_upload = false;
                    if (!empty($_FILES) && !empty($_FILES['transfer_receipt']['name'])) {
                        $transfer_receipt = '';

                        $config['upload_path'] = FCPATH . RECEIPTS_FOLDER;
                        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                        $config['overwrite'] = TRUE;
                        $config['file_name'] = 'receipt_' . $admission->id_admission;
                        $config['max_size'] = 2000000;

                        $this->load->library('upload', $config);

                        if (!$this->upload->do_upload('transfer_receipt')) {
                            $error_upload = true;
                            $this->session->set_flashdata('error_message', $this->upload->display_errors());
                        } else {
                            $transfer_receipt = $this->upload->data('file_name');
                        }
                    }

                    if (!$error_upload) {
                        $this->db->trans_begin();

                        $data = array(
                            // 'payment_status' => 1,
                            'payment_type' => $this->input->post('payment_type'),
                            'payment_channel' => $this->input->post('payment_channel'),
                            'payment_receipt' => $transfer_receipt,
                            'payment_upload_time' => strtotime(date('Y-m-d H:i:s')),
                        );
                        if ($this->Payments_model->update($payment->id_payment, $data)) {
                            $this->Admissions_model->update($id_admission, ['status' => 102]);
                        }

                        if ($this->db->trans_status() == TRUE) {
                            $this->db->trans_commit();
                            $this->session->set_flashdata('message', 'Terima kasih telah mengupload bukti pembayaran anda.');
                            redirect(base_url('payment/status/' . $id_admission), 'refresh');
                        } else {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('error_message', 'Upload bukti pembayaran gagal.');
                            redirect(base_url('payment/' . $id_admission), 'refresh');
                        }
                    }
                }
            } elseif ($payment_channel_type == 2) {
                $voucher_code = strtoupper($this->input->post('voucher_code'));
                if ($this->Vouchers_model->get_by('voucher_code', $voucher_code)) {
                    $this->db->trans_begin();

                    $data = array(
                        // 'payment_status' => 1,
                        'payment_type' => $this->input->post('payment_type'),
                        'payment_voucher_code' => $voucher_code,
                        'payment_upload_time' => strtotime(date('Y-m-d H:i:s')),
                    );
                    if ($this->Payments_model->update($payment->id_payment, $data)) {
                        $this->Admissions_model->update($id_admission, ['status' => 102]);
                    }

                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', 'Terima kasih telah melakukan pembayaran biaya pendaftaran.');
                        redirect(base_url('payment/status/' . $id_admission), 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('error_message', 'Upload bukti pembayaran gagal.');
                        redirect(base_url('payment/' . $id_admission), 'refresh');
                    }
                } else {
                    $this->session->set_flashdata('error_message', 'Kode voucher tidak valid.');
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['error_message'] = validation_errors() ? validation_errors() : $this->session->flashdata('error_message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $payment_channel_options = [];
        foreach ($payment_channels as $i => $channel) {
            $channel_type = '';
            switch ($channel->channel_type) {
                case '1':
                    $channel_type = 'Transfer';
                    break;
            }
            $payment_channel_options[] = [
                'id' => 'channel' . $i,
                'value' => $channel->id_payment_channel,
                'label' => $channel_type . ' ' . $channel->channel_name . ' (' . $channel->channel_account_no . ')',
            ];
        }
        $this->data['form'] = [[
            'id' => 'payment_type',
            'type' => 'hidden',
            'value' => 1,
        ], [
            'id' => 'payment_channel',
            'label' => 'Pembayaran melalui:',
            'type' => 'radio',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'options' => $payment_channel_options,
        ], [
            'id' => 'transfer_receipt',
            'label' => 'Bukti Transfer',
            'type' => 'file',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'help' => 'Hanya boleh file .jpeg, .jpg, .png, .pdf dengan ukuran maksimum 2MB',
            'accept' => 'image/png, image/jpeg, application/pdf',
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];

        $this->data['form_voucher'] = [[
            'id' => 'payment_type',
            'type' => 'hidden',
            'value' => 1,
        ], [
            'id' => 'voucher_code',
            'label' => 'Kode voucher',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'style' => "text-transform: uppercase",
            'value' => set_value('voucher_code'),
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];

        $this->load->view('template_home', $this->data);
    }

    function status($id_admission)
    {
        $this->data['admission_fee'] = 0;
        $this->data['payment_code'] = 0;
        $this->data['total_fee'] = 0;

        $admission = $this->Admissions_model->get($id_admission);

        if ($admission) {
            $payment = $this->Payments_model->get_by('id_admission', $admission->id_admission);
            $seleksi = $this->Seleksi_model->get($admission->seleksi);

            $this->data['payment_channels'] = $this->Seleksi_payments_channels_model
                ->join('payments_channels', 'payments_channels.id_payment_channel = seleksi_payments_channels.id_payment_channel', 'left')
                ->get_many_by('id_seleksi', $seleksi->id_seleksi);

            $this->data['payment'] = $payment;
            $this->data['admission'] = $admission;
            $this->data['confirmation'] = $this->Payments_model->get_by(
                array(
                    'id_admission' => $admission->id_admission,
                    'payment_type' => 2,
                )
            );
            $this->data['installment'] = $this->Payments_model->get_by(
                array(
                    'id_admission' => $admission->id_admission,
                    'payment_type' => 3,
                )
            );
        }

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['error_message'] = validation_errors() ? validation_errors() : $this->session->flashdata('error_message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];


        $this->load->view('template_home', $this->data);
    }

    function confirmation($id_admission)
    {
        $this->data['admission_fee'] = 0;
        $this->data['payment_code'] = 0;
        $this->data['total_fee'] = 0;

        $admission = $this->Admissions_model
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->get($id_admission);

        if ($admission->status != 400) {
            redirect('payment/status/' . $id_admission, 'refresh');
        }

        $payment_channels = [];

        if ($admission) {
            $payment = $this->Payments_model->get_by('id_admission', $admission->id_admission);
            $seleksi = $this->Seleksi_model
                ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                ->get($admission->seleksi);
            $this->data['seleksi'] = $seleksi;

            if ($admission->program == 1) {
                // $filter = [
                //     'id_seleksi' => $admission->id_seleksi,
                //     'pembayaran' => 0,
                // ];
                // if ($admission->tgl_seleksi_option) {
                //     $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                // }
                // $skema_pembayaran = $this->Skema_pembayaran_detail_model
                //     ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                //     ->get_by($filter);

                $filter = [
                    'discount' => 0,
                    'jenis_program_studi' => $admission->program,
                ];
                $biaya = $this->Biaya_model->get_many_by($filter);
                $admission->biaya = $biaya;

                $filter = [
                    'jenis_program_studi' => $admission->program,
                    'discount' => 1,
                    'id_user' => $admission->id_user,
                    'id_admission' => $admission->id_admission,
                ];
                $admission->discounts = $this->Users_discounts_model
                    ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                    ->get_many_by($filter);

                $total = 0;
                $development = 0;
                foreach ($admission->biaya as $biaya) {
                    if ($biaya->development) {
                        $development += $biaya->jumlah * $biaya->sign;
                    }
                    $total += $biaya->jumlah * $biaya->sign;
                }

                foreach ($admission->discounts as $biaya) {
                    $total += $biaya->jumlah * $biaya->sign;
                    $development += $biaya->jumlah * $biaya->sign;
                }

                $master_angsuran = [1, 4, 6, 9, 12];
                $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

                $datetime1 = date_create($admission->tgl_seleksi);
                $datetime2 = date_create('2022-09-01');
                $interval = date_diff($datetime1, $datetime2);
                $angsuran_interval =  $interval->format('%m');

                $angsuran = [];
                $konfirmasi = 0;
                $angsuran_per_bulan = 0;
                $angsuran_lain = [];

                $max_angsuran = 1;

                foreach ($master_angsuran as $i => $jenis_angsuran) {
                    if ($jenis_angsuran == $admission->jenis_angsuran) {
                        $max_angsuran = $jenis_angsuran;

                        $angsuran[] = $jenis_angsuran;
                        $konfirmasi = $master_konfirmasi[$i];
                        if ($jenis_angsuran == 1) {
                            // echo "$development - $master_konfirmasi[$i]";
                            $biaya_development = $development;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                    $biaya_development += $biaya->jumlah;
                                }
                            }
                            $angsuran_per_bulan = ($biaya_development - $master_konfirmasi[$i]);
                            $angsuran_lain[] = [];
                        } elseif ($jenis_angsuran == 4) {
                            $angsuran_per_bulan = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 1);

                            $biaya_pembinaan = 0;
                            $biaya_sks = 0;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                    $biaya_pembinaan += $biaya->jumlah;
                                }
                            }
                            $angsuran_lain = [
                                $jenis_angsuran - 1 => $biaya_pembinaan
                            ];
                        } else {
                            $angsuran_per_bulan = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 2);
                            $biaya_pembinaan = 0;
                            $biaya_sks = 0;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1) {
                                    $biaya_pembinaan += $biaya->jumlah;
                                }
                                if ($biaya->sks == 1) {
                                    $biaya_sks += $biaya->jumlah;
                                }
                            }
                            $angsuran_lain = [
                                $jenis_angsuran - 2 => $biaya_pembinaan,
                                $jenis_angsuran - 1 => $biaya_sks
                            ];
                        }
                    }
                }

                // print_r($konfirmasi);
                // print_r($angsuran_per_bulan);
                // print_r($angsuran_lain);

                // $jumlah_angsuran = 0;
                // if ($installment == '0') {
                //     $jumlah_angsuran = $konfirmasi;
                // } else {
                //     if ($admission->jenis_angsuran == 1) {
                //         $jumlah_angsuran = $angsuran_per_bulan;
                //     } elseif ($admission->jenis_angsuran == 4) {
                //         if ($installment <= $admission->jenis_angsuran - 1) {
                //             $jumlah_angsuran = $angsuran_per_bulan;
                //         } else {
                //             $jumlah_angsuran = $angsuran_lain[$installment - 1];
                //         }
                //     } else {
                //         if ($installment <= $admission->jenis_angsuran - 1) {
                //             $jumlah_angsuran = $angsuran_per_bulan;
                //         } else {
                //             $jumlah_angsuran = $angsuran_lain[$installment - 1];
                //         }
                //     }
                // }

                $this->data['admission_fee'] = $konfirmasi;
                $this->data['total_fee'] = $konfirmasi;
            } else {
                $filter = [
                    'id_seleksi' => $admission->id_seleksi,
                    'pembayaran' => 0,
                ];
                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }

                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);
                $this->data['admission_fee'] = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
                $this->data['total_fee'] = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
            }

            $payment_channels = $this->Payments_channels_model->get_all();
            $this->data['payment_channels'] = $this->Payments_channels_model->get_all();
        }
        // $this->data['admission_fee'] = $this-

        if ($this->input->post()) {
            if (empty($_FILES['loa']['name'])) {
                $this->session->set_flashdata('error_message', 'Hasil seleksi belum diisi');
                redirect(base_url('payment/confirmation/' . $id_admission), 'refresh');
            } elseif (empty($_FILES['transfer_receipt']['name'])) {
                $this->session->set_flashdata('error_message', 'Bukti Transfer belum diisi');
                redirect(base_url('payment/confirmation/' . $id_admission), 'refresh');
            } else {
                $error_upload = false;
                $loa = '';
                if (!empty($_FILES) && !empty($_FILES['loa']['name'])) {

                    $config['upload_path'] = FCPATH . LOA_FOLDER;
                    $config['allowed_types'] = 'pdf';
                    $config['overwrite'] = TRUE;
                    $config['file_name'] = 'loa_' . $admission->id_admission;
                    $config['max_size'] = 2000000;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('loa')) {
                        $error_upload = true;
                        $this->session->set_flashdata('error_message', $this->upload->display_errors());
                    } else {
                        $loa = $this->upload->data('file_name');
                    }
                }

                if (!empty($_FILES) && !empty($_FILES['transfer_receipt']['name'])) {
                    $transfer_receipt = '';

                    $config['upload_path'] = FCPATH . RECEIPTS_FOLDER;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                    $config['overwrite'] = TRUE;
                    $config['file_name'] = 'receipt_confirmation_' . $admission->id_admission;
                    $config['max_size'] = 2000000;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('transfer_receipt')) {
                        $error_upload = true;
                        $this->session->set_flashdata('error_message', $this->upload->display_errors());
                    } else {
                        $transfer_receipt = $this->upload->data('file_name');
                    }
                }

                if (!$error_upload) {

                    $this->db->trans_begin();
                    $data = array(
                        // 'payment_status' => 1,
                        'id_admission' => $admission->id_admission,
                        'payment_type' => $this->input->post('payment_type'),
                        'payment_channel' => $this->input->post('payment_channel'),
                        'payment_amount' => $this->input->post('payment_amount'),
                        'payment_receipt' => $transfer_receipt,
                        'payment_loa' => $loa,
                        'payment_upload_time' => strtotime(date('Y-m-d H:i:s')),
                    );
                    if ($this->Payments_model->insert($data)) {
                        // $this->Admissions_model->update($admission->id_admission, ['status' => 500]);
                    }

                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', 'Terima kasih telah mengupload bukti pembayaran anda.');
                        redirect(base_url('payment/status/' . $id_admission), 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('error_message', 'Upload bukti pembayaran gagal.');
                        redirect(base_url('payment/' . $id_admission), 'refresh');
                    }
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['error_message'] = validation_errors() ? validation_errors() : $this->session->flashdata('error_message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $payment_channel_options = [];
        foreach ($payment_channels as $i => $channel) {
            $channel_type = '';
            switch ($channel->channel_type) {
                case '1':
                    $channel_type = 'Transfer';
                    break;
            }
            $payment_channel_options[] = [
                'id' => 'channel' . $i,
                'value' => $channel->id_payment_channel,
                'label' => $channel_type . ' ' . $channel->channel_name . ' (' . $channel->channel_account_no . ')',
            ];
        }
        $this->data['form'] = [[
            'id' => 'payment_type',
            'type' => 'hidden',
            'value' => 2,
        ], [
            'id' => 'payment_amount',
            'type' => 'hidden',
            'value' => $konfirmasi,
        ], [
            'id' => 'payment_channel',
            'label' => 'Pembayaran melalui:',
            'type' => 'radio',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'options' => $payment_channel_options,
        ], [
            'id' => 'loa',
            'label' => 'Hasil Seleksi',
            'type' => 'file',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'help' => 'Hanya boleh file .pdf dengan ukuran maksimum 2MB',
            'accept' => 'application/pdf',
        ], [
            'id' => 'transfer_receipt',
            'label' => 'Bukti Transfer',
            'type' => 'file',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'help' => 'Hanya boleh file .jpeg, .jpg, .png, .pdf dengan ukuran maksimum 2MB',
            'accept' => 'image/png, image/jpeg, application/pdf',
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];

        $this->load->view('template_home', $this->data);
    }

    function installment($id_admission)
    {
        $this->data['admission_fee'] = 0;
        $this->data['payment_code'] = 0;
        $this->data['total_fee'] = 0;

        $admission = $this->Admissions_model
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->get($id_admission);

        // if ($admission->status != 400) {
        //     redirect('payment/status/' . $id_admission, 'refresh');
        // }

        $payment_channels = [];

        if ($admission) {
            if ($admission->program == 1) {
                if (!$admission->jenis_angsuran) {
                    $master_angsuran = [1, 4, 6, 9, 12];
                    $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

                    $datetime1 = date_create($admission->tgl_seleksi);
                    $datetime2 = date_create('2022-09-01');
                    $interval = date_diff($datetime1, $datetime2);
                    $angsuran_interval =  $interval->format('%m');

                    $angsuran = [];
                    $konfirmasi = [];
                    $angsuran_per_bulan = [];
                    $angsuran_lain = [];

                    $max_angsuran = 1;

                    foreach ($master_angsuran as $i => $jenis_angsuran) {
                        if ($jenis_angsuran < $angsuran_interval) {
                            $max_angsuran = $jenis_angsuran;
                            $angsuran[] = $jenis_angsuran;
                        }
                    }

                    if (count($angsuran) > 1) {
                        redirect(base_url('payment/type/' . $admission->id_admission), 'refresh');
                    }
                }
            } else {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                ];
                $skema_pembayaran = $this->Skema_pembayaran_model->get_many_by($filter);
                if (!$admission->jenis_pembayaran) {
                    if (count($skema_pembayaran) > 1) {
                        redirect(base_url('payment/type/' . $admission->id_admission . '/i'), 'refresh');
                    } elseif (count($skema_pembayaran) == 1) {
                        $this->Admissions_model->update($admission->id_admission, ['jenis_pembayaran' => $skema_pembayaran[0]->id_skema_pembayaran]);
                    }
                }
            }


            $payment = $this->Payments_model->get_by('id_admission', $admission->id_admission);
            $seleksi = $this->Seleksi_model->get($admission->seleksi);

            if ($admission->program == 1) {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'pembayaran' => 0,
                ];
                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }
                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);
            } else {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'pembayaran' => 0,
                ];
                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }
                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);
            }

            $payment_channels = $this->Payments_channels_model->get_all();
            $this->data['payment_channels'] = $payment_channels;

            $this->data['admission_fee'] = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
            $this->data['total_fee'] = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
        }
        // $this->data['admission_fee'] = $this-

        if ($this->input->post()) {
            if (empty($_FILES['transfer_receipt']['name'])) {
                $this->session->set_flashdata('error_message', 'Bukti Transfer belum diisi');
                redirect(base_url('payment/installment/' . $id_admission), 'refresh');
            } else {
                $error_upload = false;
                if (!empty($_FILES) && !empty($_FILES['transfer_receipt']['name'])) {
                    $transfer_receipt = '';

                    $config['upload_path'] = FCPATH . RECEIPTS_FOLDER;
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                    $config['overwrite'] = TRUE;
                    $config['file_name'] = 'receipt_installment_' . $admission->id_admission;
                    $config['max_size'] = 2000000;

                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('transfer_receipt')) {
                        $error_upload = true;
                        $this->session->set_flashdata('error_message', $this->upload->display_errors());
                    } else {
                        $transfer_receipt = $this->upload->data('file_name');
                    }
                }

                if (!$error_upload) {
                    $this->db->trans_begin();
                    $data = array(
                        // 'payment_status' => 1,
                        'id_admission' => $admission->id_admission,
                        'payment_type' => $this->input->post('payment_type'),
                        'payment_installment' => $this->input->post('payment_installment'),
                        'payment_channel' => $this->input->post('payment_channel'),
                        'payment_amount' => $this->input->post('jumlah'),
                        'payment_receipt' => $transfer_receipt,
                        'payment_upload_time' => strtotime(date('Y-m-d H:i:s')),
                    );
                    if ($this->Payments_model->insert($data)) {
                        // $this->Admissions_model->update($admission->id_admission, ['status' => 600]);
                    }

                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', 'Terima kasih telah mengupload bukti pembayaran anda.');
                        redirect(base_url('payment/status/' . $id_admission), 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('error_message', 'Upload bukti pembayaran gagal.');
                        redirect(base_url('payment/' . $id_admission), 'refresh');
                    }
                }
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['error_message'] = validation_errors() ? validation_errors() : $this->session->flashdata('error_message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $payment_channel_options = [];
        foreach ($payment_channels as $i => $channel) {
            $channel_type = '';
            switch ($channel->channel_type) {
                case '1':
                    $channel_type = 'Transfer';
                    break;
            }
            $payment_channel_options[] = [
                'id' => 'channel' . $i,
                'value' => $channel->id_payment_channel,
                'label' => $channel_type . ' ' . $channel->channel_name . ' (' . $channel->channel_account_no . ')',
            ];
        }

        $filter = [
            'id_admission' => $admission->id_admission,
            'payment_type' => 3,
            'payment_status' => 1,
        ];
        $payments = $this->Payments_model->get_many_by($filter);

        // print_r($skema_pembayaran);
        $payment_installment_options = ['' => '-Pilih angsuran-'];

        if ($skema_pembayaran) {
            for ($i = 1; $i <= $skema_pembayaran->jumlah_angsuran; $i++) {
                $payment_installment_options[$i] = 'Angsuran ' . $i;
            }
        }

        $payment_installment = $payments ? count($payments) + 1 : 1;
        $jumlah = $this->get_installment_amount($id_admission, $payment_installment);

        $this->data['form'] = [[
            'id' => 'id_admission',
            'type' => 'hidden',
            'value' => $id_admission,
        ], [
            'id' => 'payment_type',
            'type' => 'hidden',
            'value' => 3,
        ], [
            'id' => 'payment_installment',
            'label' => 'Angsuran ke:',
            'value' => $payment_installment,
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'readonly' => 'readonly',
        ], [
            'id' => 'jumlah',
            'label' => 'Jumlah:',
            'value' => $jumlah,
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'readonly' => 'readonly',
        ], [
            'id' => 'payment_channel',
            'label' => 'Pembayaran melalui:',
            'type' => 'radio',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'options' => $payment_channel_options,
        ], [
            'id' => 'transfer_receipt',
            'label' => 'Bukti Transfer',
            'type' => 'file',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'help' => 'Hanya boleh file .jpeg, .jpg, .png dengan ukuran maksimum 2MB'
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];

        $this->load->view('template_home', $this->data);
    }

    public function get_installment_amount($id_admission = '', $installment = '')
    {
        $jumlah_angsuran = 0;

        $admission = $this->Admissions_model
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->get($id_admission);

        if ($admission) {
            $acceptance = $this->Acceptances_model->get_by('id_admission', $admission->id_admission);


            $filter = [
                'jenis_program_studi' => $admission->program_studi,
                'discount' => 1,
                'id_user' => $admission->id_user,
                'id_admission' => $admission->id_admission,
            ];
            if ($admission->program_studi == MAGISTER) {
                $filter['id_program_studi'] = $admission->id_program_studi;
            }
            $discounts = $this->Users_discounts_model
                ->select('SUM(jumlah) jumlah')
                ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                ->get_by($filter);


            if ($admission->program == 1) {
                $filter = [
                    'discount' => 0,
                    'jenis_program_studi' => $admission->program,
                ];
                $biaya = $this->Biaya_model->get_many_by($filter);
                $admission->biaya = $biaya;

                $filter = [
                    'jenis_program_studi' => $admission->program,
                    'discount' => 1,
                    'id_user' => $admission->id_user,
                    'id_admission' => $admission->id_admission,
                ];
                $admission->discounts = $this->Users_discounts_model
                    ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                    ->get_many_by($filter);

                $total = 0;
                $development = 0;
                foreach ($admission->biaya as $biaya) {
                    if ($biaya->development) {
                        $development += $biaya->jumlah * $biaya->sign;
                    }
                    $total += $biaya->jumlah * $biaya->sign;
                }

                foreach ($admission->discounts as $biaya) {
                    $total += $biaya->jumlah * $biaya->sign;
                    $development += $biaya->jumlah * $biaya->sign;
                }

                $master_angsuran = [1, 4, 6, 9, 12];
                $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

                $datetime1 = date_create($admission->tgl_seleksi);
                $datetime2 = date_create('2022-09-01');
                $interval = date_diff($datetime1, $datetime2);
                $angsuran_interval =  $interval->format('%m');

                $angsuran = [];
                $konfirmasi = 0;
                $angsuran_per_bulan = 0;
                $angsuran_lain = [];

                $max_angsuran = 1;

                foreach ($master_angsuran as $i => $jenis_angsuran) {
                    if ($jenis_angsuran == $admission->jenis_angsuran) {
                        $max_angsuran = $jenis_angsuran;

                        $angsuran[] = $jenis_angsuran;
                        $konfirmasi = $master_konfirmasi[$i];
                        if ($jenis_angsuran == 1) {
                            // echo "$development - $master_konfirmasi[$i]";
                            $biaya_development = $development;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                    $biaya_development += $biaya->jumlah;
                                }
                            }
                            $angsuran_per_bulan = ($biaya_development - $master_konfirmasi[$i]);
                            $angsuran_lain[] = [];
                        } elseif ($jenis_angsuran == 4) {
                            $angsuran_per_bulan = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 1);

                            $biaya_pembinaan = 0;
                            $biaya_sks = 0;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1 || $biaya->sks == 1) {
                                    $biaya_pembinaan += $biaya->jumlah;
                                }
                            }
                            $angsuran_lain = [
                                $jenis_angsuran - 1 => $biaya_pembinaan
                            ];
                        } else {
                            $angsuran_per_bulan = ($development - $master_konfirmasi[$i]) / ($jenis_angsuran - 2);
                            $biaya_pembinaan = 0;
                            $biaya_sks = 0;
                            foreach ($admission->biaya as $biaya) {
                                if ($biaya->pembinaan == 1) {
                                    $biaya_pembinaan += $biaya->jumlah;
                                }
                                if ($biaya->sks == 1) {
                                    $biaya_sks += $biaya->jumlah;
                                }
                            }
                            $angsuran_lain = [
                                $jenis_angsuran - 2 => $biaya_pembinaan,
                                $jenis_angsuran - 1 => $biaya_sks
                            ];
                        }
                    }
                }

                // print_r($konfirmasi);
                // print_r($angsuran_per_bulan);
                // print_r($angsuran_lain);

                $jumlah_angsuran = 0;
                if ($installment == '0') {
                    $jumlah_angsuran = $konfirmasi;
                } else {
                    if ($admission->jenis_angsuran == 1) {
                        $jumlah_angsuran = $angsuran_per_bulan;
                    } elseif ($admission->jenis_angsuran == 4) {
                        if ($installment <= $admission->jenis_angsuran - 1) {
                            $jumlah_angsuran = $angsuran_per_bulan;
                        } else {
                            $jumlah_angsuran = $angsuran_lain[$installment - 1];
                        }
                    } else {
                        if ($installment <= $admission->jenis_angsuran - 1) {
                            $jumlah_angsuran = $angsuran_per_bulan;
                        } else {
                            $jumlah_angsuran = $angsuran_lain[$installment - 1];
                        }
                    }
                }
            } elseif ($admission->program == 2) {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'pembayaran' => $installment,
                ];

                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }

                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);

                if ($skema_pembayaran) {
                    $filter = [
                        'id_seleksi' => $admission->seleksi,
                        'pembayaran' => 0,
                    ];
                    if ($admission->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                    }

                    $konfirmasi = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);

                    $total = $skema_pembayaran->jumlah_total - $discounts->jumlah;

                    if ($skema_pembayaran->persentase) {
                        $jumlah_angsuran = ($skema_pembayaran->persentase / 100) * ($total - $konfirmasi->jumlah);
                    } elseif ($skema_pembayaran->jumlah) {
                        $jumlah_angsuran = $skema_pembayaran->jumlah;
                    }
                }
            }
        }


        return $jumlah_angsuran;
    }

    public function get_installment()
    {
        $response = new stdClass();

        $id_admission = $this->input->post('id_admission');
        $installment = $this->input->post('installment');

        $jumlah_angsuran = 0;

        $admission = $this->Admissions_model
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->get($id_admission);

        if ($admission) {
            $acceptance = $this->Acceptances_model->get_by('id_admission', $admission->id_admission);


            $filter = [
                'jenis_program_studi' => $admission->program_studi,
                'discount' => 1,
                'id_user' => $admission->id_user,
                'id_admission' => $admission->id_admission,
            ];
            if ($admission->program_studi == MAGISTER) {
                $filter['id_program_studi'] = $admission->id_program_studi;
            }
            $discounts = $this->Users_discounts_model
                ->select('SUM(jumlah) jumlah')
                ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                ->get_by($filter);


            if ($admission->program == 1) {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'pembayaran' => $installment,
                ];
                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }
                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);

                if ($skema_pembayaran) {
                    $filter = [
                        'id_seleksi' => $admission->seleksi,
                        'pembayaran' => 0,
                    ];
                    if ($admission->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                    }
                    $konfirmasi = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);


                    $total = $skema_pembayaran->jumlah_total - $discounts->jumlah;
                    if ($skema_pembayaran->persentase) {
                        $jumlah_angsuran = ($skema_pembayaran->persentase / 100) * ($total - $konfirmasi->jumlah);
                    } elseif ($skema_pembayaran->jumlah) {
                        $jumlah_angsuran  = $skema_pembayaran->jumlah;
                    }
                }
            } elseif ($admission->program == 2) {
                $filter = [
                    'id_seleksi' => $admission->seleksi,
                    'pembayaran' => $installment,
                ];

                if ($admission->tgl_seleksi_option) {
                    $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                }

                $skema_pembayaran = $this->Skema_pembayaran_detail_model
                    ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                    ->get_by($filter);

                if ($skema_pembayaran) {
                    $filter = [
                        'id_seleksi' => $admission->seleksi,
                        'pembayaran' => 0,
                    ];
                    if ($admission->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $admission->tgl_seleksi;
                    }

                    $konfirmasi = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);

                    $total = $skema_pembayaran->jumlah_total - $discounts->jumlah;

                    if ($skema_pembayaran->persentase) {
                        $jumlah_angsuran = ($skema_pembayaran->persentase / 100) * ($total - $konfirmasi->jumlah);
                    } elseif ($skema_pembayaran->jumlah) {
                        $jumlah_angsuran = $skema_pembayaran->jumlah;
                    }
                }
            }
        }


        $response->filter = $filter;
        $response->id_admission = $id_admission;
        $response->installment = $installment;
        $response->admission = $admission;
        $response->skema_pembayaran = $skema_pembayaran;
        $response->acceptance = $acceptance;
        $response->jumlah_angsuran = $jumlah_angsuran;

        echo json_encode($response);
    }

    public function type($id_admission, $redirect = '')
    {
        if ($redirect == 'i') {
            $redirect = 'installment';
        } elseif ($redirect == 'c') {
            $redirect = 'confirmation';
        } elseif ($redirect == 'r') {
            $redirect = 'registration';
        }
        $admission = $this->Admissions_model->get($id_admission);
        $this->data['adamission'] = $admission;

        $this->form_validation->set_rules('jenis_pembayaran', 'Jenis Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            if ($admission->program == 1) {
                $this->Admissions_model->update($id_admission, ['jenis_angsuran' => $this->input->post('jenis_pembayaran')]);
            } elseif ($admission->program == 2) {
                $this->Admissions_model->update($id_admission, ['jenis_pembayaran' => $this->input->post('jenis_pembayaran')]);
            }
            if ($redirect) {
                redirect(base_url('payment/' . $redirect . '/' . $id_admission), 'refresh');
            } else {
                redirect(base_url('payment'), 'refresh');
            }
        }

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['error_message'] = validation_errors() ? validation_errors() : $this->session->flashdata('error_message');
        $this->data['title'] = '';

        $content = strtolower(static::class . '/' . __FUNCTION__);
        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $dir . '/' . $content . '.js',
        ];

        $jenis_pembayaran_options = ['' => '-Pilih jenis pembayaran-'];

        if ($admission->program == 1) {
            $master_angsuran = [1, 4, 6, 9, 12];
            $master_konfirmasi = [7000000, 7000000, 7000000, 7000000, 3000000];

            $datetime1 = date_create($admission->tgl_seleksi);
            $datetime2 = date_create('2022-09-01');
            $interval = date_diff($datetime1, $datetime2);
            $angsuran_interval =  $interval->format('%m');


            foreach ($master_angsuran as $i => $jenis_angsuran) {
                if ($jenis_angsuran <= $angsuran_interval) {
                    $jenis_pembayaran_options[$jenis_angsuran] = 'ANGSURAN ' . $jenis_angsuran . 'x';
                }
            }
        } elseif ($admission->program == 2) {
            $skema_pembayaran = $this->Admissions_model->get_skema_pembayaran($admission->seleksi, $admission->tgl_seleksi);

            if ($skema_pembayaran) {
                foreach ($skema_pembayaran as $pembayaran) {
                    $jenis_pembayaran = ['', 'TUNAI', 'ANGSURAN'];
                    $jenis_pembayaran_options[$pembayaran->id_skema_pembayaran] = $jenis_pembayaran[$pembayaran->jenis_pembayaran] . ($pembayaran->jumlah_angsuran > 0 ? ' ' . $pembayaran->jumlah_angsuran . 'x' : '');
                }
            }
        }


        $this->data['form'] = [[
            'id' => 'id_admission',
            'type' => 'hidden',
            'value' => $id_admission,
        ], [
            'id' => 'jenis_pembayaran',
            'label' => 'Jenis Pembayaran:',
            'control_label_class' => 'col-sm-12',
            'form_control_class' => 'col-sm-12',
            'type' => 'dropdown',
            'options' => $jenis_pembayaran_options,
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Submit',
            'control_label_class' => 'col-sm-12 hide',
        ]];
        $this->load->view('template_home', $this->data);
    }
}
