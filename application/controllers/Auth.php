<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function register()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('password2', 'Konfirmasi Password ya', 'required|matches[password]');

        if ($this->form_validation->run() === false) {
            $this->load->view('layout/header');
            $this->load->view('layout/register');
            $this->load->view('layout/footer');
        } else {
            $this->user_model->insert_user();//save user
            $this->send_email_verification($this->input->post('email'), $_SESSION['token']); //verifikasi email
            redirect('login');
        }
    }

    public function send_email_verification($email, $token)
    {
        $this->load->library('email');
        $this->email->from('dediapudin@yahoo.com', 'Dedi Apudin');
        $this->email->to($email);
        $this->email->subject('ini subjectnya register aplikasi auth local');
        #$this->email->message("confrm email <a href='http://localhost/pastibisa/verify/$email/$token'>Konfirmasi Email</a>");
        $this->email->message($this->getEmailBody($email, $token));
        $this->email->set_mailtype('html');
        $this->email->send();
    }

     public function getEmailBody($email, $token)
    {
        $data = array('email'=>$email,'token'=>$token);
        $msg = $this->load->view('templates/email_registrasi', $data, true);
        return $msg;
    }

      public function verify_register($email, $token)
    {
        $user = $this->user_model->get_user('email',$email);// key = email, value = data di field row email
        #$user = $this->user_model->get_user('id',$id);// key = email, value = data di field row email
        # cek email ada atau tidak
        if (!$user)
            die('email no exists');

        if ($user['token'] !== $token)
            die('token not match'); 

        # update user role menjadi nilai 1 (aktiv)
        $this->user_model->update_role($user['id'], 1);

        //set session
          $_SESSION['user_id']   = $user['id'];
          $_SESSION['logged_in'] = true;

        # redirect profile
        redirect('profile');
    }


    public function login()
    {

        if ($this->user_model->is_LoggedIn()) {
            redirect('dashboard');
        }
        $this->form_validation->set_rules('email', 'Email', 'required|callback_checkEmail');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_checkPassword');

        if ($this->form_validation->run() === false) {
            $this->load->view('layout/header');
            $this->load->view('auth/login');
            $this->load->view('layout/footer');
        } else {

            $user = $this->user_model->get_user('email',$this->input->post('email'));
           
            $newdata = array(
                    'user_id'     => $user['id'],
                    'logged_in' => TRUE
            );

            $this->session->set_userdata($newdata);
            
            redirect('profile');
        }
    }

    /*public function checkEmail($email) //validasi checkEmail
    {
        if (!$this->user_model->get_user('email', $email)) { // jika email engga ada
            $this->form_validation->set_message('checkEmail','Email tidak terdaftar');
            return false;
        }

            return true; // jika email nya terdaftar
    }*/

    public function checkEmail($email)
    {
        if (!$this->user_model->get_user('email', $email)) {
            $this->form_validation->set_message('checkEmail', 'email is not on database callback_checkEmail');
            return false;
        }

        return true;
    }

  

      public function isEmailExist($email="")
    {
        $checkemail=$this->db->get_where('users',array('email'=>urldecode($email)));
        //cek validasi waktu register
        if($checkemail->num_rows()>0)
        {
            echo "EXIST";
            //return TRUE;
        }
        else
        {
            echo "NOTEXIST";
            //return FALSE;
        }
    }

   


    public function checkPassword($password) //validasi checkPassword
    {
        $user = $this->user_model->get_user('email',$this->input->post('email'));
   
        // $password = input yang di masukan user
        if (!$this->user_model->checkPassword($user['email'], $password)) { // jika password salah
            $this->form_validation->set_message('checkPassword','Password nya tidak benar callback_checkPassword');
            # $this->session->set_flashdata('msgL','Email atau password yang Anda masukkan salah, silahkan coba kembali.');
            #die('password salah');
            return false;
        }
            #die('password bener');
            return true; // jika password benar
    }


    public function logout()
    {
      unset($_SESSION['user_id'], $_SESSION['logged_in']); // hapus session user_id & logged_in
      redirect('auth/login');

    }




}
