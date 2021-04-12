<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->user_data = $this->session->userdata('USER_DATA');
        $this->user_id = $this->user_data['user_id'];
    }

	public function index()
	{
        $this->db->select('*');
        $this->db->where('user_id', $this->user_id);
        $user = $this->db->get('users')->row_array();

        $this->db->select('id');
        $this->db->where('user_id', $this->user_id);
        $count = count($this->db->get('invoices')->result_array());

        if(!empty($this->user_data)){
            $page_data = [
                'content'   => $this->load->view('settings', ['user' => $user], true),
                'user_data' => $this->user_data,
                'count'     => $count,
                'js'        => '/assets/js/settings.js',
                'css'       => '/assets/css/invoice.css'
            ];
            $this->load->view('master/master_template', $page_data);
        }
        else{
            show_404();
        }
	}

	public function user_data()
    {
        $post = $this->input->post();

        $this->db->where('user_id', $this->user_id);
        $this->db->update('users', [
            'first_name'    => $post['first_name'],
            'last_name'     => $post['last_name'],
            'address'       => $post['address'],
            'company'       => $post['company'],
            'currency'      => $post['currency'],
            'logo'          => $post['file_name'],
            'notes'         => $post['notes'],
            'terms'         => $post['terms']
        ]);

        $this->helper->json('Success', 'Successfully updated your profile.');
    }
}
