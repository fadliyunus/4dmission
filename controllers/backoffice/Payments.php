<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payments extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array(
            'Program_studi_model',
            'Payments_model',
            'Seleksi_model',
            'Seleksi_payments_channels_model',
            'Skema_pembayaran_model',
            'Skema_pembayaran_detail_model',
            'Payments_channels_model',
            'Users_discounts_model',
        ));

        if (!$this->ion_auth->logged_in()) {
            redirect('home/login');
        }
    }

    public function index()
    {
        $this->payments();
    }

    public function payments()
    {
        $this->data['title'] = 'Payments';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->load->view('template_backoffice', $this->data);
    }

    public function create()
    {
        $this->form_validation->set_rules('field_name', 'Nama', 'required');
        $this->form_validation->set_rules('field_label', 'Label', 'required');
        $this->form_validation->set_rules('field_type', 'Jenis', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'field_name' => $this->input->post('field_name'),
                'field_label' => $this->input->post('field_label'),
                'field_type' => $this->input->post('field_type'),
                'field_visible' => $this->input->post('field_visible'),
                'field_table' => $this->input->post('field_table'),
            ];

            if ($this->Payments_model->insert($data)) {
                redirect('backoffice/fields', 'refresh');
            }
        }

        $this->data['title'] = 'Payments';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . static::class . '_edit');
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->db->where("field_type IN ('table')");
        $fieldtable_options = ['' => '-Pilih table-'] + $this->Payments_model->dropdown('id_field', 'field_name');

        $this->data['form'] = [[
            'id' => 'field_name',
            'label' => 'Nama',
            'value' => set_value('field_name'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_label',
            'label' => 'Label',
            'value' => set_value('field_label'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_visible',
            'label' => 'Ditampilkan',
            'type' => 'dropdown',
            'options' => ['1' => 'Ya', '0' => 'Tidak'],
            'value' => set_value('field_visible'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_type',
            'label' => 'Jenis',
            'type' => 'dropdown',
            'options' => ['control_static' => 'text', 'input' => 'input', 'dropdown' => 'dropdown', 'radio' => 'radio', 'checkbox' => 'checkbox', 'file' => 'file', 'date' => 'date', 'table' => 'table', 'hidden' => 'hidden'],
            'value' => set_value('field_type'),
            'control_label_class' => 'col-sm-12 col-form-label',
        ], [
            'id' => 'field_table',
            'type' => 'dropdown',
            'options' => $fieldtable_options,
            'value' => set_value('field_table'),
        ], [

            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Submit',
            ], [
                'id' => 'btn-cancel',
                'type' => 'button',
                'label' => 'Cancel',
                'onclick' => "window.location.href='" . base_url('backoffice/fields') . "'",
            ]],
        ]];



        $this->load->view('template_backoffice', $this->data);
    }
    public function data()
    {
        $response  = new stdClass();

        $response->total =  $this->Payments_model->count_by('payment_type > 1');

        $filter = [];
        $response->rows = $this->Payments_model
            ->join('admissions', 'admissions.id_admission = payments.id_admission', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->get_many_by('payment_type > 1');

        echo json_encode($response);
    }

    public function view($id_payment)
    {
        $payment = $this->Payments_model
            ->join('admissions', 'admissions.id_admission = payments.id_admission', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->get($id_payment);
        $this->data['payment'] = $payment;

        $filter = [
            // 'id_seleksi' => $payment->seleksi,
            // 'tgl_seleksi' => $payment->tgl_seleksi,
            'id_skema_pembayaran' => $payment->jenis_pembayaran,
        ];
        $skema_pembayaran = $this->Skema_pembayaran_model
            ->get_by($filter);
        $this->data['skema_pembayaran'] = $skema_pembayaran;

        $this->form_validation->set_rules('payment_status', 'Status Pembayaran', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = [
                'payment_status' => $this->input->post('payment_status'),
                'payment_time' => strtotime($this->input->post('payment_time')),
                'payment_approved_by' => $this->ion_auth->user()->row()->id,
                'payment_approved_on' => time(),
            ];

            $this->db->trans_begin();
            if ($this->Payments_model->update($id_payment, $data)) {
                // if ($payment->payment_installment == $skema_pembayaran->jumlah_angsuran) {
                if ($payment->payment_installment == $payment->jenis_angsuran) {
                    $this->send_welcome_email($id_payment);
                }
                $this->create_payment_receipt($id_payment, 1);
                $this->send_payment_email($id_payment);
            }

            // if ($payment->payment_type == 2) {
            //     if ($this->input->post('payment_status') == '1') {
            //         $this->Admissions_model->update($payment->id_admission, ['status' => 501]);
            //     } else {
            //         $this->Admissions_model->update($payment->id_admission, ['status' => 502]);
            //     }
            // }
            // if ($payment->payment_type == 3) {
            //     if ($this->input->post('payment_status') == '1') {
            //         $this->Admissions_model->update($payment->id_admission, ['status' => 601]);
            //     } else {
            //         $this->Admissions_model->update($payment->id_admission, ['status' => 602]);
            //     }
            // }

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                redirect('backoffice/payments', 'refresh');
            } else {
                $this->db->trans_rollback();
            }
        }
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = 'Payments';

        $dir =  basename(__DIR__) == 'controllers' ? '' :  basename(__DIR__);
        $content = strtolower($dir . '/' . static::class . '/' . __FUNCTION__);
        $this->data['content'] =  $content;
        $this->data['css'] = [];
        $this->data['js_plugins'] = [];
        $this->data['js'] = [
            JS_FUNCTION_DIR . $content . '.js',
        ];

        $this->data['form'] = [[
            'id' => 'id_payment',
            'type' => 'hidden',
            'value' => $payment->id_payment,
        ], [
            'id' => 'id_user',
            'type' => 'hidden',
            'value' => $payment->id_user,
        ], [
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
            'type' => 'control_static',
            'value' => $payment->full_name,
        ], [
            'id' => 'nama_program_studi',
            'label' => 'Program Studi',
            'type' => 'control_static',
            'value' => $payment->nama_program_studi,
        ], [
            'id' => 'payment_type',
            'label' => 'Pembayaran',
            'type' => 'control_static',
            'value' => $payment->payment_type == '2' ? 'Konfirmasi' : 'Angsuran ' . $payment->payment_installment,
        ]];

        $this->load->view('template_backoffice', $this->data);
    }

    public function get_options()
    {
        $response = new stdClass();
        $id_field = $this->input->get('id_field');

        $response->rows = $this->Payments_options_model->get_many_by('id_field', $id_field);
        echo json_encode($response);
    }

    public function send_payment_email($id_payment)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'id_payment' => $id_payment,
        ];

        $payment = $this->Payments_model
            ->join('admissions', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->get_by($filter);
        $this->data['payment'] = $payment;

        // $this->load->view('home/email/ticket.tpl.php', $this->data);


        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/payment_approval.tpl.php', $this->data, true);
        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($payment->email);

        if ($payment->payment_status == 1) {
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Konfirmasi ' . ($payment->payment_type == 2 ? 'LoA' : 'Pembayaran'));
        } elseif ($payment->payment_status == 2) {
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Verifikasi ' . ($payment->payment_type == 2 ? 'LoA' : 'Pembayaran'));
        }
        $this->email->message($message);

        if ($payment->payment_status == 1) {
            if ($payment->payment_type == 2) {
                $attachment_file = FCPATH . DOCUMENTS_FOLDER . $payment->id_admission . '/KWITANSI KONFIRMASI ' . $payment->full_name . '.pdf';
            } elseif ($payment->payment_type == 3) {
                $attachment_file = FCPATH . DOCUMENTS_FOLDER . $payment->id_admission . '/KWITANSI ANGSURAN ' . $payment->full_name . '.pdf';
            }
            if (file_exists($attachment_file)) {
                $this->email->attach($attachment_file);
            }
        }

        if ($this->email->send() == TRUE) {
            return true;
        }
        return false;
    }

    public function send_welcome_email($id_payment)
    {
        // $this->data = $this->sharia->_create_ticket($id_registration);
        $filter = [
            'id_payment' => $id_payment,
        ];

        $payment = $this->Payments_model
            ->join('admissions', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->get_by($filter);
        $this->data['payment'] = $payment;


        $email_config = $this->config->item('email_config', 'ion_auth');

        if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config)) {
            $this->email->initialize($email_config);
        }

        $message = $this->load->view('home/email/welcome.tpl.php', $this->data, true);
        $this->email->clear();
        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($payment->email);
        $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - Welcoming Mahasiswa Baru ' . $this->config->item('site_title', 'ion_auth'));
        $this->email->message($message);


        if ($this->email->send() == TRUE) {
            return true;
        }
        return false;
    }

    public function create_payment_receipt($id_payment, $write = 0)
    {
        $filter = [
            'payments.id_payment' => $id_payment,
            // 'payment_type' => 1,
        ];

        $payment = $this->Payments_model
            ->select('admissions.*, nama_program_studi, nama_seleksi, tgl_seleksi_option, payment,tes_seleksi, biaya, channel_type, channel_name, full_name, email, id_payment, payment_type, payment_installment, payment_code, payment_time, payment_status, payment_receipt, payment_voucher_code, id_payment, tahun_ajaran, payment_amount')
            ->join('admissions', 'payments.id_admission = admissions.id_admission', 'left')
            ->join('users', 'users.id = admissions.id_user', 'left')
            ->join('program_studi', 'program_studi.id_program_studi = admissions.program_studi', 'left')
            ->join('seleksi', 'seleksi.id_seleksi = admissions.seleksi', 'left')
            ->join('jadwal_seleksi', 'jadwal_seleksi.id_jadwal_seleksi = admissions.seleksi', 'left')
            ->join('payments_channels', 'payments_channels.id_payment_channel = payments.payment_channel', 'left')
            ->join('angkatan', 'angkatan.id_angkatan = admissions.id_angkatan', 'left')

            ->get_by($filter);


        if ($payment) {
            $id_admission = $payment->id_admission;
            if ($payment->payment_type == 2) {
                $seleksi = $this->Seleksi_model
                    ->join('program_studi', 'program_studi.id_program_studi = seleksi.id_program_studi', 'left')
                    ->get($payment->seleksi);
                $this->data['seleksi'] = $seleksi;

                if ($payment->program == 1) {
                    $filter = [
                        'id_seleksi' => $payment->seleksi,
                        'pembayaran' => 0,
                    ];
                    if ($payment->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                    }
                    $skema_pembayaran = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);
                } else {
                    $filter = [
                        'id_seleksi' => $payment->id_seleksi,
                        'pembayaran' => 0,
                    ];
                    if ($payment->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                    }

                    $skema_pembayaran = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);
                }

                $payment_channels = $this->Payments_channels_model->get_all();

                $admission_fee = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
                $total_fee = $skema_pembayaran ? $skema_pembayaran->jumlah : 0;
            } elseif ($payment->payment_type == 3) {
                $filter = [
                    'jenis_program_studi' => $payment->program_studi,
                    'discount' => 1,
                    'id_user' => $payment->id_user,
                    'id_admission' => $payment->id_admission,
                ];
                if ($payment->program_studi == MAGISTER) {
                    $filter['id_program_studi'] = $payment->id_program_studi;
                }
                $discounts = $this->Users_discounts_model
                    ->select('SUM(jumlah) jumlah')
                    ->join('biaya', 'biaya.id_biaya = users_discounts.id_biaya', 'left')
                    ->get_by($filter);


                if ($payment->program == 1) {
                    $filter = [
                        'id_seleksi' => $payment->seleksi,
                        'pembayaran' => $payment->payment_installment,
                    ];
                    if ($payment->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                    }
                    $skema_pembayaran = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);

                    if ($skema_pembayaran) {
                        $filter = [
                            'id_seleksi' => $payment->seleksi,
                            'pembayaran' => 0,
                        ];
                        if ($payment->tgl_seleksi_option) {
                            $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                        }
                        $konfirmasi = $this->Skema_pembayaran_detail_model
                            ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                            ->get_by($filter);


                        $total = $skema_pembayaran->jumlah_total - $discounts->jumlah;
                        if ($skema_pembayaran->persentase) {
                            $total_fee = ($skema_pembayaran->persentase / 100) * ($total - $konfirmasi->jumlah);
                        } elseif ($skema_pembayaran->jumlah) {
                            $total_fee  = $skema_pembayaran->jumlah;
                        }
                    }
                } elseif ($payment->program == 2) {
                    $filter = [
                        'id_seleksi' => $payment->seleksi,
                        'pembayaran' => $payment->payment_installment,
                    ];

                    if ($payment->tgl_seleksi_option) {
                        $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                    }

                    $skema_pembayaran = $this->Skema_pembayaran_detail_model
                        ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                        ->get_by($filter);

                    if ($skema_pembayaran) {
                        $filter = [
                            'id_seleksi' => $payment->seleksi,
                            'pembayaran' => 0,
                        ];
                        if ($payment->tgl_seleksi_option) {
                            $filter['tgl_seleksi'] = $payment->tgl_seleksi;
                        }

                        $konfirmasi = $this->Skema_pembayaran_detail_model
                            ->join('skema_pembayaran', 'skema_pembayaran.id_skema_pembayaran = skema_pembayaran_detail.id_skema_pembayaran', 'left')
                            ->get_by($filter);

                        $total = $skema_pembayaran->jumlah_total - $discounts->jumlah;

                        if ($skema_pembayaran->persentase) {
                            $total_fee = ($skema_pembayaran->persentase / 100) * ($total - $konfirmasi->jumlah);
                        } elseif ($skema_pembayaran->jumlah) {
                            $total_fee = $skema_pembayaran->jumlah;
                        }
                    }
                }
            }
        }

        $pdf = new setasign\Fpdi\Fpdi();

        $pageCount = $pdf->setSourceFile(FCPATH . DOCUMENTS_FOLDER . 'bukti_pembayaran.pdf');
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            if ($size['width'] > $size['height']) {
                $pdf->AddPage('L', array($size['width'], $size['height']));
            } else {
                $pdf->AddPage('P', array($size['width'], $size['height']));
            }

            $pdf->useTemplate($templateId);
            $pdf->SetFont('Helvetica', '', 7);

            if ($pageNo == 1) {
                // //nama nasabah
                $pdf->SetXY(83, 36);
                $pdf->Cell(90, 3.5, $payment->id_payment, 0);

                $pdf->SetXY(83, 43);
                $pdf->Cell(90, 3.5, $payment->full_name, 0);

                $pdf->SetXY(83, 49.5);
                $pdf->MultiCell(90, 3.5, ucfirst($this->terbilang($payment->payment_amount) . ' rupiah'), 0);

                $pdf->SetXY(83, 60);
                $pdf->MultiCell(90, 3.5, 'Pembayaran biaya pendaftaran Program ' . $payment->nama_program_studi . ' Angkatan ' . $payment->tahun_ajaran, 0);

                $pdf->SetXY(83, 97.5);
                $pdf->Cell(55, 3.5, number_format($payment->payment_amount, 0, ',', '.'), 0);

                $pdf->SetXY(112, 118);
                $pdf->Cell(26, 3.5, $payment->channel_name, 0, 0, 'C');

                $pdf->SetXY(138.5, 118);
                $pdf->Cell(35, 3.5, $payment->payment_time ? date('d/m/Y', $payment->payment_time) : '', 0, 0, 'C');
            }
        }

        if ($write) {
            if (!file_exists(FCPATH . DOCUMENTS_FOLDER . $id_admission)) {
                mkdir(FCPATH . DOCUMENTS_FOLDER . $id_admission);
            }
            $filename = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/KWITANSI ' . $payment->full_name . '.pdf';
            if ($payment->payment_type == 2) {
                $filename = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/KWITANSI KONFIRMASI ' . $payment->full_name . '.pdf';
            } elseif ($payment->payment_type == 3) {
                $filename = FCPATH . DOCUMENTS_FOLDER . $id_admission . '/KWITANSI ANGSURAN ' . $payment->full_name . '.pdf';
            }
            $pdf->Output($filename, 'F');
        } else {
            $pdf->Output();
        }
    }

    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil;
    }
}
