<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {

	public function __construct(){	
		parent::__construct();
		$this->load->model('Query_model','qm',TRUE);
		$this->load->library('form_validation');
	}
	
	public function index(){
		if($this->session->userdata('educationcv_web_email') != "" && $this->session->userdata('educationcv_web_id') != ""){
			redirect('dashboard');
		}
		if(isset($_POST['login'])){
			$this->form_validation->set_rules('username', 'Username', 'required|valid_email');
        	$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
       		if ($this->form_validation->run() === false) {
       			$this->session->set_flashdata('error', validation_errors());
       			$this->session->set_flashdata('username', $_POST['username']);
       			$this->session->set_flashdata('password', $_POST['password']);
       			redirect('login');
       		}else{
       			$username = $this->input->post('username');
            	$password = $this->input->post('password');
				$where = array ('email_id' => $username, 'password' => md5($password));
				$validate = $this->qm->select_where('tbl_register',$where);
				if(!empty($validate))
				{
					if($validate[0]['status'] == 1){
						if($validate[0]['is_suspend'] == '0'){
							$this->session->set_userdata('educationcv_web_email',$validate[0]['email_id']);
							$this->session->set_userdata('educationcv_web_id',$validate[0]['_id']);
							$this->session->set_flashdata('success', 'Login successful!');
	                		redirect('dashboard');
						}else{
							$this->session->set_flashdata('username', $_POST['username']);
       						$this->session->set_flashdata('password', $_POST['password']);
							$this->session->set_flashdata('error', 'We regret to inform you that your account has been suspended.');
							redirect('login');
						}
					}else{
						$this->session->set_flashdata('username', $_POST['username']);
       					$this->session->set_flashdata('password', $_POST['password']);
						$this->session->set_flashdata('error', 'We regret to inform you that your account has been deactivated.');
						redirect('login');
					}
				}else{
					$this->session->set_flashdata('username', $_POST['username']);
       				$this->session->set_flashdata('password', $_POST['password']);
					$this->session->set_flashdata('error', 'Invalid username or password.');
					redirect('login');
				}
			}
		}else{  
			$this->load->view('web/header');
			$this->load->view('web/login');
			$this->load->view('web/footer');
		}
	}

	public function register(){
		if($this->session->userdata('educationcv_web_email') != "" && $this->session->userdata('educationcv_web_id') != ""){
			redirect('dashboard');
		}
		if(isset($_POST['register'])){
			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('email_id', 'Email', 'required|valid_email|is_unique[tbl_register.email_id]');
			$this->form_validation->set_message('is_unique', 'Email already registered.Try Diffrent.');
        	$this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
       		if ($this->form_validation->run() === false) {
       			$this->session->set_flashdata('error', validation_errors());
       			$this->session->set_flashdata('name', $_POST['name']);
       			$this->session->set_flashdata('email_id', $_POST['email_id']);
       			$this->session->set_flashdata('password', $_POST['password']);
       			redirect('register');
       		}else{
       			$otp = rand(1111,9999);
       			$this->send_otp_mail($_POST['email_id'],$otp);
       			$post_data = array(
					'name' => ucfirst($_POST['name']),
					'email_id' => $_POST['email_id'],
					'password' => md5($_POST['password']),
					'otp' => $otp
				);
				$this->session->set_userdata('educationcv_web_otpcheck',$post_data);
				$this->session->set_flashdata('success', 'Please check your email and enter the one-time-password below.!');
	            redirect('login/verify_otp');
			}
		}else{  
			$this->load->view('web/header');
			$this->load->view('web/register');
			$this->load->view('web/footer');
		}
	}

	public function resend_otp(){
		if($this->session->userdata('educationcv_web_otpcheck') != ""){
			$post_data = $this->session->userdata('educationcv_web_otpcheck');
			$otp = rand(1111,9999);
       		$this->send_otp_mail($post_data['email_id'],$otp);
			$post_data['otp'] = $otp;
			$this->session->set_userdata('educationcv_web_otpcheck',$post_data);
			$this->session->set_flashdata('success', 'Please check again your email and enter the one-time-password below!');
	       	redirect('login/verify_otp');
		}else{
			$this->session->set_flashdata('error', 'Somthing went wrong.');
			redirect('login');
		}
	}

	public function verify_otp(){
		if($this->session->userdata('educationcv_web_otpcheck') != ""){
       		if(isset($_POST['verify'])){
       			$this->form_validation->set_rules('otp', 'otp', 'required|min_length[4]');
	       		if ($this->form_validation->run() === false) {
	       			$this->session->set_flashdata('error', validation_errors());
	       			$this->session->set_flashdata('otp', $_POST['otp']);
	       			redirect('login/verify_otp');
	       		}else{
	       			$check = $this->session->userdata('educationcv_web_otpcheck');
	       			if($_POST['otp'] == $check['otp']){
	       				$post_data = array(
							'name' => ucfirst($check['name']),
							'email_id' => $check['email_id'],
							'password' => $check['password'],
							'status' => 1,
							'created_at' => date('Y-m-d H:i:s'),
							'modified_at' => date('Y-m-d H:i:s')
						);
						$register_id  = $this->qm->ins('tbl_register', $post_data);
						$this->session->set_userdata('educationcv_web_email',$check['email_id']);
						$this->session->set_userdata('educationcv_web_id',$register_id);
						$this->session->unset_userdata('educationcv_web_otpcheck');
						$this->session->set_flashdata('success', 'Login successful!');
			            redirect('dashboard');
	       			}else{
	       				$this->session->set_flashdata('otp', $_POST['otp']);
	       				$this->session->set_flashdata('error', 'Wrong OTP!');
	       				redirect('login/verify_otp');
	       			}
	       		}
	       	}else{  
				$this->load->view('web/header');
				$this->load->view('web/otp_verify');
				$this->load->view('web/footer');
			}
		}else{
			$this->session->set_flashdata('error', 'Somthing went wrong.');
			redirect('login');
		}
	}

	private function send_otp_mail($to_email,$otp) {
        $from_email = "hello@education.cv";
        $this->load->library('email');
        $this->email->from($from_email, 'Education.cv');
        $this->email->to($to_email);
        $this->email->subject('Registration One-Time Password (OTP)');
        $this->email->message('<!DOCTYPE html> <html> <head> <meta charset="UTF-8"> <title>One-Time Password (OTP) Email</title> <style> body { font-family: Arial, sans-serif; line-height: 1.6; } .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; } .header { text-align: center; background-color: #f2f2f2; padding: 10px; } .content { padding: 20px; } .otp-code { font-size: 24px; font-weight: bold; color: #008080; } .note { font-size: 14px; color: #888; } .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }</style> </head> <body> <div class="container"> <div class="header"> <h2> Your Login One-Time Password (OTP)</h2> </div> <div class="content"> <p>Dear User,</p> <p>Here is your login One-Time Password (OTP) is:</p> <p class="otp-code">'.$otp.'</p><p class="note">Note: This OTP is valid for a single use and will expire after a short period of time.</p> </div> <div class="footer"> <p>This email was sent automatically. Please do not reply to this email.</p> </div> </div> </body> </html>');
        if($this->email->send()){
            return "Congragulation Email Send Successfully.";
        }else{
            return "You have encountered an error";
        }
    }

	public function logout(){
		$sess_array = array('educationcv_web_email','educationcv_web_id','educationcv_web_otpcheck','educationcv_web_forgotemail');
        $this->session->unset_userdata($sess_array);
		redirect('login');
	}
}