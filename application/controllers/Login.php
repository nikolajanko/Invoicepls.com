<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
        $user_data = $this->session->userdata('USER_DATA');

        if(!empty($user_data)){
            $page_data = [
                'content'   => $this->load->view('login', '', true),
                'user_data' => $user_data,
                'js'        => '/assets/js/login.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else
        {
            $page_data = [
                'content'   => $this->load->view('login', '', true),
                'js'        => '/assets/js/login.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
	}

    public function user_login()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $post = $this->input->post();

        $username = $post['username'];
        $password = $post['password'];

        if(empty($username))
            $this->helper->json('Error', 'Username is required');

        if(empty($password))
            $this->helper->json('Error', 'Password cannot be empty');

        $this->db->select('*');
        $this->db->where("email = '$username' OR username = '$username'");
        $user_check = $this->db->get('users')->row_array();

        if(isset($user_check['user_id']))
        {
            $hash = $user_check['password'];
            $password_check = password_verify($password, $hash);

            if($password_check)
            {
                unset($user_check['password']);
                $this->session->set_userdata('USER_DATA', $user_check);
                $this->helper->json('Success', 'Successfully logged in.');
            }
            else
            {
                $this->helper->json('Error', 'Wrong credentials, try again');
            }
        }
        else
        {
            $this->helper->json('Error', 'Wrong credentials, try again');
        }
    }

    public function log_out()
    {
        $this->session->unset_userdata('USER_DATA');
        header("Location: /invoice");
        die();
    }

}
