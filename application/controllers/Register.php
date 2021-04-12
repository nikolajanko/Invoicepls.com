<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function index()
	{
        $user_data = $this->session->userdata('USER_DATA');

        if(!empty($user_data)) {
            $page_data = [
                'content'   => $this->load->view('register', '', true),
                'user_data' => $user_data,
                'js'        => '/assets/js/register.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else
        {
            $page_data = [
                'content'   => $this->load->view('register', '', true),
                'js'        => '/assets/js/register.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
	}

    public function register_user()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $post = $this->input->post();

        $username = $post['username'];
        $email = $post['email'];
        $hashed_password = password_hash($post['password'], PASSWORD_DEFAULT);

        if(isset($username) && empty($username))
            $this->helper->json('Error', 'Username is required');

        if(isset($email) && empty($email))
            $this->helper->json('Error', 'E-mail is required');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->helper->json('Error', 'Invalid e-mail format');
        }
        if(isset($post['password']) && empty($post['password']))
            $this->helper->json('Error', 'Password field cannot be empty.');

        $this->db->select('user_id');
        $this->db->where('username', $username);
        $this->db->or_where('email', $email);
        $user = $this->db->get('users')->row_array();

        if(isset($user['user_id']))
        {
            $this->helper->json('Error', 'User already exists');
        }

        $data = [
            'username'          => $username,
            'email'             => $email,
            'password'          => $hashed_password,
            'date_registered'   => date("Y-m-d H:i:s"),
        ];
        $this->db->insert('users', $data);

        $this->helper->json('Success', 'Successfully registered.');
    }
}
