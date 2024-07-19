<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller {

	public function __construct(){	
		parent::__construct();
		$this->load->model('Query_model','qm',TRUE);
		$this->load->library('form_validation');
		$this->load->model('security_model');
		$this->security_model->is_logged_in_web();
	}
	
	public function index(){
		$user_id = $this->session->userdata('educationcv_web_id');
		$view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
		$view_data['comments'] = $this->qm->num_row('tbl_comments', array('register_id' => $user_id));
		$this->load->view('web/header_user');
		$this->load->view('web/dashboard',$view_data);
		$this->load->view('web/footer_user');
	}
}