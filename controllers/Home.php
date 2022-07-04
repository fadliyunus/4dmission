<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        setlocale(LC_TIME, 'id_ID.UTF-8');
        $this->load->model(array());

        $this->lang->load('auth');

        // if (!$this->ion_auth->logged_in()) {
        //     redirect('home/login');
        // }
    }

    public function index()
    {
        $this->data['title'] = 'Home';
        $this->data['content'] = 'home/index';
        $this->data['css'] = array();
        $this->data['js_plugins'] = array();
        $this->data['js'] = array();

        $this->load->view('template_home', $this->data);
    }

    public function login()
    {
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == TRUE) {

            // check to see if the user is logging in
            // check for "remember me"
            if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), '0')) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                if ($this->ion_auth->is_admin()) {
                    redirect('backoffice/dashboard', 'refresh');
                } else {
                    redirect('dashboard', 'refresh');
                }
            } else {
                $errors = $this->ion_auth->errors_array(false);

                if (in_array('login_unsuccessful_not_active', $errors)) {
                    $this->session->set_flashdata('message', $this->ion_auth->errors() . '<a href="' . base_url('home/resend_activation_email') . '">Kirim Ulang Email Aktivasi</a>');
                    redirect('home/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
                }

                // if the login was un-successful
                // redirect them back to the login page
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect('home/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['form'] = [[
            'id' => 'email',
            'label' => 'E-mail',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'password',
            'label' => 'Password',
            'type' => 'password',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'buttons',
            'type' => 'combine',
            'label' => '',
            // 'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 d-none',
            'form_control_class' => 'col-12',
            'elements' => [[
                'id' => 'btn-submit',
                'type' => 'submit',
                'label' => 'Login',
                'class' => 'button button-3d button-black m-0',
                'input_container_class' => 'col-6',
                'control_label_class' => 'col-12 p-0',
                'form_control_class' => 'col-12 p-0',
            ], [
                'id' => 'forgot-password',
                'type' => 'html',
                'html' => '<div class="float-right"><a href="' . base_url('home/forgot_password') . '" style="line-height:40px">Forgot Password</a></div>',
                'input_container_class' => 'col-6',
            ]]

        ]];

        $this->data['title'] = 'Login';
        $this->data['content'] = 'home/login';
        $this->data['css'] = array();
        $this->data['js_plugins'] = array();
        $this->data['js'] = array();

        $this->load->view('template_home', $this->data);
    }

    public function logout()
    {
        $this->ion_auth->logout();

        redirect('home/login', 'refresh');
    }

    public function register()
    {
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|min_length[8]|matches[password]');
        // $this->form_validation->set_rules('captcha', 'Captcha', 'required|callback_validate_captcha');

        if ($this->form_validation->run() === TRUE) {
            $this->session->unset_userdata('captchacode');
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');

            $additional_data = [
                'full_name' => strtoupper($this->input->post('full_name')),
                'phone' => $this->input->post('phone'),
            ];

            if ($this->ion_auth->register($email, $password, $email, $additional_data)) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("home/login", 'refresh');
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
            }
        } else {
            // redirect("home/register", 'refresh');
        }


        $this->load->helper('captcha');
        $vals = array(
            // 'word'          => 'Random word',
            'img_path'      => FCPATH . 'assets/files/captcha/',
            'img_url'       => base_url() . 'assets/files/captcha/',
            'font_path'     => FCPATH . 'assets/files/captcha/fonts/Roboto-Regular.ttf',
            'img_width'     => 200,
            'img_height'    => 80,
            'word_length'   => 6,
            'font_size'     => 24,
            'img_id'        => 'Imageid',
            // 'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

            // White background and border, black text and red grid
            'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(206, 212, 219),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );

        $cap = create_captcha($vals);
        //fadli rubah 29-06-22
        // $this->session->set_userdata('captchacode', $cap['word']);
        $this->session->set_userdata('captchacode');
        // echo $cap['image'];

        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['form'] = [[
            'id' => 'full_name',
            'label' => 'Nama Lengkap',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
            // 'value' => set_value('full_name'),
        ], [
            'id' => 'email',
            'label' => 'E-mail',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
            'value' => set_value('email'),
        ], [
            'id' => 'phone',
            'label' => 'No HP/WA',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
            'value' => set_value('phone'),
        ], [
            'id' => 'password',
            'label' => 'Password',
            'type' => 'password',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'password_confirm',
            'label' => 'Konfirmasi Password',
            'type' => 'password',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'captcha_image',
            'label' => '',
            'type' => 'html',
            'html' => $cap ? $cap['image'] : '',
            'control_label_class' => 'd-none',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'captcha',
            'label' => '',
            'input_container_class' => 'col-12',
            'control_label_class' => 'd-none',
            'form_control_class' => 'col-12 p-0',
            'value' => '',
        ], [
            'id' => 'cb_agree',
            'label' => '',
            'type' => 'checkbox',
            'options' => array(
                array(
                    'id' => 'checkbox1',
                    'value' => '1',
                    'label' => 'Saya menyatakan bahwa data yang saya isi benar',
                    // If no label is set, the value will be used
                ),
            ),
            'control_label_class' => 'd-none',
            'input_container_class' => 'col-12',
            'form_control_class' => 'col-12 p-0',
        ], [
            'id' => 'btn-submit',
            'type' => 'submit',
            'label' => 'Register',
            'class' => 'button button-3d button-black m-0',
            'input_container_class' => 'col-12',
            'control_label_class' => 'col-12 p-0',
            'form_control_class' => 'col-12 p-0',
        ]];


        $this->data['title'] = 'Register';
        $this->data['content'] = 'home/register';
        $this->data['css'] = array();
        $this->data['js_plugins'] = array();
        $this->data['js'] = array(
            'functions/home/register.js',
        );


        $this->load->view('template_home', $this->data);
    }

    public function validate_captcha()
    {
        if ($this->input->post('captcha') != $this->session->userdata['captchacode']) {
            $this->form_validation->set_message('validate_captcha', 'Captcha code is wrong');
            return false;
        } else {
            return true;
        }
    }

    public function activate($id, $code = FALSE)
    {
        $activation = FALSE;

        if ($code !== FALSE) {
            $activation = $this->ion_auth->activate($id, $code);
        } else if ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            // redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("home/login", 'refresh');
        } else {
            // redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("home/forgot_password", 'refresh');
        }
    }

    public function forgot_password()
    {
        $this->data['title'] = $this->lang->line('forgot_password_heading');

        // setting validation rules by checking whether identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }


        if ($this->form_validation->run() === FALSE) {
            $this->data['type'] = $this->config->item('identity', 'ion_auth');
            // setup the input
            $this->data['identity'] = [
                'name' => 'identity',
                'id' => 'identity',
            ];

            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            // set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['content'] = 'home/forgot_password';

            $this->data['form'] = [[
                'id' => 'identity',
                'label' => 'E-mail',
                'input_container_class' => 'col-12',
                'control_label_class' => 'col-12 p-0',
                'form_control_class' => 'col-12 p-0',
            ], [
                'id' => 'buttons',
                'type' => 'combine',
                'label' => '',
                'elements' => [[
                    'id' => 'btn-submit',
                    'type' => 'submit',
                    'label' => 'Submit',
                    'class' => 'button button-3d button-black m-0',
                    'input_container_class' => 'col-6',
                    'control_label_class' => 'col-12 p-0',
                    'form_control_class' => 'col-12 p-0',
                ], [
                    'id' => 'forgot-password',
                    'type' => 'html',
                    'html' => '<div class="float-right"><a href="' . base_url() . '" style="line-height:40px">Back to home</a></div>',
                    'input_container_class' => 'col-6',
                ]]

            ]];
            $this->load->view('template_home', $this->data);
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

            if (empty($identity)) {

                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                } else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }

                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("home/forgot_password", 'refresh');
            }

            // run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten) {
                // if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("home/login", 'refresh'); //we should display a confirmation page here instead of the login page
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("home/forgot_password", 'refresh');
            }
        }
    }

    public function reset_password($code = NULL)
    {
        if (!$code) {
            show_404();
        }
        // $csrf = $this->_get_csrf_nonce();
        // var_dump($csrf);

        $this->data['title'] = $this->lang->line('reset_password_heading');

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user) {
            // if the code is valid then display the password reset form

            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() === FALSE) {
                // display the form

                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
                // $this->data['new_password'] = [
                //     'name' => 'new',
                //     'id' => 'new',
                //     'type' => 'password',
                //     'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                // ];
                // $this->data['new_password_confirm'] = [
                //     'name' => 'new_confirm',
                //     'id' => 'new_confirm',
                //     'type' => 'password',
                //     'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                // ];
                // $this->data['user_id'] = [
                //     'name' => 'user_id',
                //     'id' => 'user_id',
                //     'type' => 'hidden',
                //     'value' => $user->id,
                // ];
                $this->data['csrf'] = $this->_get_csrf_nonce();
                $this->data['code'] = $code;

                $this->data['form'] = [[
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' =>  $user->id,
                ], [
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',
                    'label' => sprintf(lang('reset_password_new_password_label'), $this->config->item('min_password_length', 'ion_auth')),
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                    'control_label_class' => 'col-12 p-0',
                ], [
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'label' => lang('reset_password_new_password_confirm_label'),
                    'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
                    'control_label_class' => 'col-12 p-0',
                ], [
                    'id' => 'buttons',
                    'type' => 'combine',
                    'label' => '',
                    'elements' => [[
                        'id' => 'btn-submit',
                        'type' => 'submit',
                        'label' => 'Submit',
                        'class' => 'button button-3d button-black m-0',
                        'input_container_class' => 'col-6',
                        'control_label_class' => 'col-12 p-0',
                        'form_control_class' => 'col-12 p-0',
                    ], [
                        'id' => 'forgot-password',
                        'type' => 'html',
                        'html' => '<div class="float-right"><a href="' . base_url() . '" style="line-height:40px">Back to home</a></div>',
                        'input_container_class' => 'col-6',
                    ]]

                ]];

                $this->data['content'] = 'home/reset_password';
                $this->load->view('template_home', $this->data);
            } else {
                $identity = $user->{$this->config->item('identity', 'ion_auth')};

                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

                    // something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($identity);

                    show_error($this->lang->line('error_csrf'));
                } else {
                    // finally change the password
                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change) {
                        // if the password was successfully changed
                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        redirect("home/login", 'refresh');
                    } else {
                        $this->session->set_flashdata('message', $this->ion_auth->errors());
                        redirect('home/reset_password/' . $code, 'refresh');
                    }
                }
            }
        } else {
            // if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("home/forgot_password", 'refresh');
        }
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return [$key => $value];
    }

    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return TRUE;
        }
        return FALSE;
    }

    public function resend_activation_email()
    {
        $this->data['title'] = $this->lang->line('forgot_password_heading');

        // setting validation rules by checking whether identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }


        if ($this->form_validation->run() === FALSE) {
            $this->data['type'] = $this->config->item('identity', 'ion_auth');
            // setup the input
            $this->data['identity'] = [
                'name' => 'identity',
                'id' => 'identity',
            ];

            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            // set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['content'] = 'home/resend_activation_email';

            $this->data['form'] = [[
                'id' => 'identity',
                'label' => 'E-mail',
                'input_container_class' => 'col-12',
                'control_label_class' => 'col-12 p-0',
                'form_control_class' => 'col-12 p-0',
            ], [
                'id' => 'buttons',
                'type' => 'combine',
                'label' => '',
                // 'input_container_class' => 'col-12',
                'control_label_class' => 'col-12 d-none',
                'form_control_class' => 'col-12',
                'elements' => [[
                    'id' => 'btn-submit',
                    'type' => 'submit',
                    'label' => 'Submit',
                    'class' => 'button button-3d button-black m-0',
                    'input_container_class' => 'col-6',
                    'control_label_class' => 'col-12 p-0',
                    'form_control_class' => 'col-12 p-0',
                ]]

            ]];
            $this->load->view('template_home', $this->data);
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $user = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

            if (empty($user)) {

                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                } else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }

                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("home/resend_activation_email", 'refresh');
            }

            $id = $user->id;
            $deactivate = $this->ion_auth->deactivate($id);

            // the deactivate method call adds a message, here we need to clear that
            $this->ion_auth->clear_messages();


            if (!$deactivate) {
                $this->ion_auth->set_error('deactivate_unsuccessful');
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("home/resend_activation_email", 'refresh');
            }

            $activation_code = $this->ion_auth_model->activation_code;
            $identity        = $this->config->item('identity', 'ion_auth');
            $email = $user->email;

            $data = [
                'identity'   => $user->{$identity},
                'full_name'   => $user->full_name,
                'id'         => $user->id,
                'email'      => $email,
                'activation' => $activation_code,
            ];
            if (!$this->config->item('use_ci_email', 'ion_auth')) {
                $this->ion_auth->set_message('activation_email_successful');
                return $data;
            } else {
                $message = $this->load->view($this->config->item('email_templates', 'ion_auth') . $this->config->item('email_activate', 'ion_auth'), $data, true);

                $this->email->clear();
                $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
                $this->email->to($email);
                $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
                $this->email->message($message);

                if ($this->email->send(FALSE) === TRUE) {
                    $this->ion_auth->set_message('activation_email_successful');
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                } else {
                    $this->ion_auth->set_error('activation_email_unsuccessful');
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                    // echo $this->email->print_debugger(array('headers'));
                }
            }
            redirect("home/resend_activation_email", 'refresh'); //we should display a confirmation page here instead of the login page
        }
    }
}
