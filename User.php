<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/api/REST_Controller.php';
class user extends REST_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('query_model','qm',TRUE);
    }

    function login_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('api_token', 'API Token', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $email = trim($_REQUEST['email']);
            $password = trim($_REQUEST['password']);
            $api_token = trim($_REQUEST['api_token']);
            $check_email = $this->check_user_validate($email,$password);
            if (!empty($check_email)) {
                if($check_email[0]->contact_verify==1)
                {
                    $token['api_token'] = $api_token;
                    $this->db->where('email', $email);
                    $this->db->update('tbl_users', $token); 
                    
                    $business = $this->db->query("SELECT * FROM tbl_business_list where _id = '".$check_email[0]->business_id."'")->result();
                    if(!empty($business)){
                        $business_name = $business[0]->name;
                    }else{
                        $business_name = "";
                    }

                    if($check_email[0]->image != ''){
                        $image = base_url('images/users/').$check_email[0]->image;
                    }else{
                        $image = LOGO;
                    }
                    
                    $data= [
                        'id' => $check_email[0]->_id,
                        'name' => $check_email[0]->name,
                        'email' => $check_email[0]->email,
                        'city' => $check_email[0]->city,
                        'image' => $image,
                        'business' => $business_name,
                        'contact' => $check_email[0]->contact,
                        'contact_verify' => $check_email[0]->contact_verify,
                        'sms_otp' => (string)$check_email[0]->otp,
                        'reset_otp' => (string)$check_email[0]->reset_otp,
                        'plan_start' => (string)$check_email[0]->plan_start,
                        'plan_end' => (string)$check_email[0]->plan_end,
                        'plan_type' => (string)$check_email[0]->plan_type,
                        'image_url' => $image
                    ];
                    $outputjson['result'] = 1;
                    $outputjson['message'] = 'login successfully';
                    $outputjson['api_token'] = $check_email[0]->api_token;
                    $outputjson['record'] = $data;
                }
                else
                {
                    $outputjson['result'] = 0;
                    $outputjson['message'] = 'Inactive User';
                }
            } else {
                $outputjson['result'] = 0;
                $outputjson['message'] = 'invalid email or password';
            }
            $this->response($outputjson);
        }
    }

    public function check_user_validate($email,$password) {
        $check_email = $this->db->query("SELECT * FROM tbl_users where email = '".$email."' and password = '".md5($password)."'")->result();
        return $check_email;
    }

    // function check_otp_post() {
    //     $jsonval = (object)array();
    //     $outputjson = array();
    //     $this->form_validation->set_rules('contact', 'Contact', 'trim|required');
    //     $this->form_validation->set_rules('sms_otp', 'SMS OTP', 'trim|required');
    //     if($this->form_validation->run()==FALSE){
    //         $outputjson['result'] = 0;
    //         $errorString = implode(",",$this->form_validation->error_array());
    //         $outputjson['message'] = $errorString;
    //         $this->response($outputjson);
    //     }else{
    //         $contact = trim($_REQUEST['contact']);
    //         $sms_otp = trim($_REQUEST['sms_otp']);
    //         $check_email = $this->otp_c($contact,$sms_otp);
    //         if ($check_email == 1) {
              
    //             $outputjson['result'] = 1;
    //             $outputjson['contact_verify'] = 1;
    //             $outputjson['message'] = 'sms otp match';
                
    //         } else {
    //             $outputjson['result'] = 0;
    //             $outputjson['contact_verify'] = 0;
    //             $outputjson['message'] = 'sms otp not match';
    //         }
    //         $this->response($outputjson);
    //     }
    // }

    // public function otp_c($email,$sms_otp) {
    //     $check = $this->db->query("SELECT * FROM tbl_users where email = '".$email."' and otp ='".$sms_otp."'");
    //     if ($check->num_rows() > 0) {
    //         return 1;
    //     } else {
    //         return 0;
    //     }
    // }


    public function forget_password_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $email = trim($_REQUEST['email']);
            $check_email = $this->check_register($email);
            if(!empty($check_email)) 
            {
                $newotp = rand(1111,9999);
                $otp['otp'] = $newotp;
                $this->db->where('email', $email);
                $this->db->update('tbl_users', $otp); 
                $this->send_verification_otp($email,$newotp);
                $outputjson['result'] = 1;
                $outputjson['message'] = 'Otp sent successfully';
            } else {
                $outputjson['result'] = 0;
                $outputjson['message'] = 'Email ID not register. Register first.';
            }
            $this->response($outputjson);
        }
    }

    public function send_verification_otp($email,$otp)
    {
        $this->load->config('email');
        $this->load->library('email');
        $from = $this->config->item('smtp_user');
        $to = $email;
        $subject = "Digital Poster Maker Forget Password OTP.";
        $mail_message = "Digital Poster Maker Forget Password OTP is";
        $mail_message .= "<h1>".$otp."</h1>";
        $this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($mail_message);
        if ($this->email->send()) {
            return 'Password sent to your email!';
        } else {
            return $this->email->print_debugger().'Failed to send password, please try again!';
        }  
    }

    public function register_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('city', 'City', 'trim');
        $this->form_validation->set_rules('business', 'Business', 'trim');
        $this->form_validation->set_rules('contact', 'Contact', 'trim|required');
        $this->form_validation->set_rules('api_token', 'API Token', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $name = trim($_REQUEST['name']);
            $email = trim($_REQUEST['email']);
            $password = trim($_REQUEST['password']);
            $city = trim($_REQUEST['city']);
            $business = trim($_REQUEST['business']);
            $contact = trim($_REQUEST['contact']);
            $api_token = trim($_REQUEST['api_token']);
            $check_email = $this->check_register($email);
            if(empty($check_email)) 
            {
                date_default_timezone_set('Asia/kolkata');
                $post_data['name'] = $name;
                $post_data['email'] = $email;
                $post_data['password'] = md5($password);
                $post_data['city'] = $city;
                $post_data['business_id'] = $business;
                $post_data['contact'] = $contact;
                $post_data['api_token'] = $api_token;
                $post_data['status'] = '1';
                $post_data['contact_verify'] = '1';
                $post_data['created_at'] = date('Y-m-d h:i:s');
                $post_data['updated_at'] = date('Y-m-d h:i:s');
                if(isset($_FILES['image'])){
					$file = 'image';
					$path ="images/users";
					$allowedExts = array("jpg", "jpeg", "gif", "png");
					$extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
					$filename=rand() * time().'.'.$extension;
					$extension = strtolower($extension);
					$move_uploaded_file = move_uploaded_file($_FILES[$file]['tmp_name'],$path."/".$filename);
					if($move_uploaded_file){
                        $source_path  = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/'.$filename;
                        $target_path = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/';
                        $config = array(
                            'image_library' => 'gd2',
                            'source_image' => $source_path,
                            'new_image' => $target_path,
                            'maintain_ratio' => TRUE,
                            'create_thumb' => TRUE,
                            'thumb_marker' => '',
                            'width' => 250,
                            // 'height' => 250
                        );
                        $this->load->library('image_lib', $config);
                        $this->image_lib->initialize($config);
                        if (!$this->image_lib->resize()) {
                            $this->image_lib->display_errors();
                        }
                        $this->image_lib->clear();
						$final_image = $filename;
					} else {
						$final_image = "";
					}
					$post_data['image'] = $final_image;
				}else{
                    $post_data['image'] = '';
                }
                $insert=$this->db->insert('tbl_users', $post_data); 
                $user_data = $this->db->query("SELECT * FROM tbl_users where email = '".$post_data['email']."'")->result();
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data = [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => "",
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'User registered successfully';
                $outputjson['api_token'] = $api_token;
                $outputjson['record'] = $data;
            }else{
                $outputjson['result'] = 0;
                $outputjson['message'] = 'The Email has been already taken.';
            }
            $this->response($outputjson);
        }
    }

    public function register_new_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('city', 'City', 'trim');
        $this->form_validation->set_rules('business', 'Business', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('website', 'Website', 'trim');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('contact', 'Contact', 'trim|regex_match[/^[0-9]{10}$/]|is_unique[tbl_users.contact]|required');
        $this->form_validation->set_rules('api_token', 'API Token', 'trim|required');
        if(empty($_FILES['image']['name']))
        {
            $this->form_validation->set_rules('image', 'Image', 'trim|required');
        }
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $name = trim($_REQUEST['name']);
            $email = trim($_REQUEST['email']);
            $password = trim($_REQUEST['password']);
            $city = trim($_REQUEST['city']);
            $business = trim($_REQUEST['business']);
            $contact = trim($_REQUEST['contact']);
            $website = trim($_REQUEST['website']);
            $address = trim($_REQUEST['address']);
            $api_token = trim($_REQUEST['api_token']);
            $check_email = $this->check_register($email);
            if(empty($check_email)) 
            {
                date_default_timezone_set('Asia/kolkata');
                $post_data['name'] = $name;
                $post_data['email'] = $email;
                $post_data['password'] = md5($password);
                $post_data['contact'] = $contact;
                $post_data['api_token'] = $api_token;
                $post_data['status'] = '1';
                $post_data['contact_verify'] = '1';
                $post_data['created_at'] = date('Y-m-d h:i:s');
                $post_data['updated_at'] = date('Y-m-d h:i:s');
                if(isset($_FILES['image'])){
                    $file = 'image';
                    $path ="images/users";
                    $allowedExts = array("jpg", "jpeg", "gif", "png");
                    $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
                    $filename=rand() * time().'.'.$extension;
                    $extension = strtolower($extension);
                    $move_uploaded_file = move_uploaded_file($_FILES[$file]['tmp_name'],$path."/".$filename);
                    if($move_uploaded_file){
                        $source_path  = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/'.$filename;
                        $target_path = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/';
                        $config = array(
                            'image_library' => 'gd2',
                            'source_image' => $source_path,
                            'new_image' => $target_path,
                            'maintain_ratio' => TRUE,
                            'create_thumb' => TRUE,
                            'thumb_marker' => '',
                            'width' => 250,
                            // 'height' => 250
                        );
                        $this->load->library('image_lib', $config);
                        $this->image_lib->initialize($config);
                        if (!$this->image_lib->resize()) {
                            $this->image_lib->display_errors();
                        }
                        $this->image_lib->clear();

                        $file = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/'.$filename;
                        $newfile = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/user_business/'.$filename;
                        $copied = copy($file , $newfile);
                        $final_image = $filename;
                    } else {
                        $final_image = "";
                    }
                    $post_data['image'] = $final_image;
                }else{
                    $final_image = "";
                    $post_data['image'] = $final_image;
                }
                $this->db->insert('tbl_users', $post_data); 
                $user_id = $this->db->insert_id();

                $post_data_new['name'] = trim($name);
                $post_data_new['contact'] = trim($contact);
                $post_data_new['address'] = trim($address);
                $post_data_new['email'] = trim($email);
                if(!empty($website)){
                    $post_data_new['website'] = trim($website);
                }
                $post_data_new['user_id'] = trim($user_id);
                $post_data_new['image'] = $final_image;
                $this->db->insert('tbl_user_business', $post_data_new);

                $user_data = $this->db->query("SELECT * FROM tbl_users where email = '".$post_data['email']."'")->result();
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data = [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => "",
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'User registered successfully';
                $outputjson['api_token'] = $api_token;
                $outputjson['record'] = $data;
            }else{
                $outputjson['result'] = 0;
                $outputjson['message'] = 'The Email has been already taken.';
            }
            $this->response($outputjson);
        }
    }


    public function check_register($email) {
        $check_email = $this->db->query("SELECT * FROM tbl_users where email='".$email."'")->result();
        return $check_email;
    }


    public function reset_password_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('reset_otp', 'Reset OTP', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $email = trim($_REQUEST['email']);
            $reset_otp = trim($_REQUEST['reset_otp']);
            $password = trim($_REQUEST['password']);
            $check_email = $this->check_reset_pass($email,$reset_otp);
            if ($check_email == 1) {
                $pass['password'] = md5($password);
                $pass['otp'] = NULL;
                $this->db->where('contact', $contact);
                $this->db->update('tbl_users', $pass); 
                $outputjson['result'] = 1;
                $outputjson['message'] = 'Reset Password successfully';
            } else {
                $outputjson['result'] = 0;
                $outputjson['message'] = 'Invalid OTP';
            }
            $this->response($outputjson);
        }
    }


    public function check_reset_pass($email,$reset_otp) {
        $check = $this->db->query("SELECT * FROM tbl_users where email = '".$email."'and otp = '".$reset_otp."'");
        if ($check->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function business_list_post()
    {
        $outputjson = array();
		$this->db->from("tbl_business_list");
		$this->db->where('status',1);
		$q = $this->db->get();
		$rows = $q->result_array();
		$data=array();
		foreach($rows as $key)
		{
            if($key['image'])
            {
                $image = IMAGE."business_list/".$key['image'];
            }else{
                $image = NO_USER;
            }
			$data[]= [
				'id' => $key['_id'],
				'name' => $key['name'],
                'details' => $key['details'],
                'image' => $image
			];
		}
		$outputjson['result'] = 1;
		$outputjson['message'] = 'success';
		$outputjson['records'] = $data;
		$this->response($outputjson);
    }

    public function add_business_list_post(){
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('details', 'details', 'trim|required');
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $name = trim($_REQUEST['name']);
            $details = trim($_REQUEST['details']);
            $user_id = trim($_REQUEST['user_id']);
            $check = $this->qm->select_where('tbl_users', array('_id' => $user_id)); 
            if(!empty($check)) 
            {
                $post_data['name'] = trim($name);
                $post_data['details'] = trim($details);
                $post_data['user_id'] = trim($user_id);
                $post_data['status'] = 0;
                if(isset($_FILES['logo'])){
                    $file = 'logo';
                    $path = "images/business_list";
                    $allowedExts = array("jpg","jpeg","png");
                    $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
                    $filename=rand() * time().'.'.$extension;
                    $extension = strtolower($extension);
                    $move_uploaded_file = move_uploaded_file($_FILES[$file]['tmp_name'],$path."/".$filename);
                    if($move_uploaded_file){
                        $source_path  = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/business_list/'.$filename;
                        $target_path = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/business_list/';
                        $config = array(
                            'image_library' => 'gd2',
                            'source_image' => $source_path,
                            'new_image' => $target_path,
                            'maintain_ratio' => TRUE,
                            'create_thumb' => TRUE,
                            'thumb_marker' => '',
                            'width' => 250,
                            // 'height' => 250
                        );
                        $this->load->library('image_lib', $config);
                        $this->image_lib->initialize($config);
                        if (!$this->image_lib->resize()) {
                            $this->image_lib->display_errors();
                        }
                        $this->image_lib->clear();
                        $final_image = $filename;
                    } else {
                        $final_image = "";
                    }
                    $post_data['image'] = $final_image;
                }else{
                    $post_data['image'] = '';
                }
                $business_id = $this->qm->ins('tbl_business_list', $post_data); 
                $data= [
                    'id' => $business_id,
                    'name' => $name,
                    'details' => $details,
                    'image' => base_url('images/business_list/').$post_data['image']
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'Business added successfully';
                $outputjson['record'] = $data;
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }


    public function feedback_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('contact', 'contact', 'trim|required');
        $this->form_validation->set_rules('description', 'description', 'trim|required');
        $this->form_validation->set_rules('api_token', 'api_token', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $name = trim($_REQUEST['name']);
            $contact = trim($_REQUEST['contact']);
            $description = trim($_REQUEST['description']);
            $api_token = trim($_REQUEST['api_token']);
            $check_api_token = $this->check_feedbackapi_token($api_token);
            if (!empty($check_api_token)) 
            {
                $post_data['name'] = trim($name);
                $post_data['contact'] = trim($contact);
                $post_data['description'] = trim($description);
                $post_data['user_id'] = $check_api_token[0]->_id;
                $insert=$this->db->insert('tbl_feedback', $post_data); 
                $user_data = $this->db->query("SELECT * FROM tbl_feedback where user_id = '" . $post_data['user_id']."'")->result();
                $data= [
                    'id' => $user_data[0]->_id,
                    'user_id' => $user_data[0]->user_id,
                    'name' => $user_data[0]->name,
                    'description' => $user_data[0]->description,
                    'contact' => $user_data[0]->contact,
                    'created_at' => $user_data[0]->created_at,
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'feedback successfully';
                $outputjson['record'] = $data;
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }

    public function check_feedbackapi_token($api_token) {
        $check_api_token = $this->db->query("SELECT * FROM tbl_users where api_token = '".$api_token."'")->result();
        return $check_api_token;
    }


    public function profile_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('api_token', 'api_token', 'trim|required');
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        // $this->form_validation->set_rules('city', 'city', 'trim|required');
        // $this->form_validation->set_rules('email', 'email', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $api_token = trim($_REQUEST['api_token']);
            $name = trim($_REQUEST['name']);
            $city = trim($_REQUEST['city']);
            $email = trim($_REQUEST['email']);
            $check_api_token = $this->check_Profile_api_token($api_token);
            if (!empty($check_api_token)) 
            {
                $post_data['name'] = trim($_REQUEST['name']);
                $post_data['city'] = trim($_REQUEST['city']);
                if(isset($_FILES['image'])){
                    if($_FILES['image']['size'] != 0) {
                        $file = 'image';
                        $path ="images/users";
                        $allowedExts = array("jpg", "jpeg", "gif", "png");
                        $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
                        $filename=rand() * time().'.'.$extension;
                        $extension = strtolower($extension);
                        $move_uploaded_file = move_uploaded_file($_FILES[$file]['tmp_name'],$path."/".$filename);
                        if($move_uploaded_file){
                            $source_path  = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/'.$filename;
                            $target_path = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/';
                            $config = array(
                                'image_library' => 'gd2',
                                'source_image' => $source_path,
                                'new_image' => $target_path,
                                'maintain_ratio' => TRUE,
                                'create_thumb' => TRUE,
                                'thumb_marker' => '',
                                'width' => 250,
                                // 'height' => 250
                            );
                            $this->load->library('image_lib', $config);
                            $this->image_lib->initialize($config);
                            if (!$this->image_lib->resize()) {
                                $this->image_lib->display_errors();
                            }
                            $this->image_lib->clear();
                            $final_image = $filename;
                        } else {
                            $final_image = "";
                        }
                        $post_data['image'] = $final_image;
                    }
                }
                $where = array('api_token' => $_REQUEST['api_token']);
                $this->qm->updt('tbl_users', $post_data, $where);
                $user_data = $this->db->query("SELECT * FROM tbl_users where api_token = '".$_REQUEST['api_token']."'")->result();
                $business = $this->db->query("SELECT * FROM tbl_business_list where _id = '" . $user_data[0]->business_id."'")->result();
                if(!empty($business)){
                    $businessname = $business[0]->name;
                }else{
                    $businessname = "";
                }         
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data= [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => $businessname,
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'company' => $user_data[0]->company,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'Profile Update successfully';
                $outputjson['record'] = $data;     
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }

    public function profile_new_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('api_token', 'api_token', 'trim|required');
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('city', 'city', 'trim|required');
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $api_token = trim($_REQUEST['api_token']);
            $user_id =  trim($_REQUEST['user_id']);
            $name = trim($_REQUEST['name']);
            $city = trim($_REQUEST['city']);
            $email = trim($_REQUEST['email']);
            $check = $this->db->query("SELECT * FROM tbl_users where _id = '".$user_id."'")->result();
            if(!empty($check)) 
            {
                $post_data['name'] = trim($_REQUEST['name']);
                $post_data['city'] = trim($_REQUEST['city']);
                if(isset($_FILES['image'])){
                    if($_FILES['image']['size'] != 0) {
                        $file = 'image';
                        $path ="images/users";
                        $allowedExts = array("jpg", "jpeg", "gif", "png");
                        $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
                        $filename=rand() * time().'.'.$extension;
                        $extension = strtolower($extension);
                        $move_uploaded_file = move_uploaded_file($_FILES[$file]['tmp_name'],$path."/".$filename);
                        if($move_uploaded_file){
                            $source_path  = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/'.$filename;
                            $target_path = $_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/images/users/';
                            $config = array(
                                'image_library' => 'gd2',
                                'source_image' => $source_path,
                                'new_image' => $target_path,
                                'maintain_ratio' => TRUE,
                                'create_thumb' => TRUE,
                                'thumb_marker' => '',
                                'width' => 250,
                                // 'height' => 250
                            );
                            $this->load->library('image_lib', $config);
                            $this->image_lib->initialize($config);
                            if (!$this->image_lib->resize()) {
                                $this->image_lib->display_errors();
                            }
                            $this->image_lib->clear();
                            $final_image = $filename;
                        } else {
                            $final_image = "";
                        }
                        $post_data['image'] = $final_image;
                    }
                }
                $where = array('_id' => $user_id);
                $this->qm->updt('tbl_users', $post_data, $where);
                $user_data = $this->db->query("SELECT * FROM tbl_users where _id = '".$user_id."'")->result();
                $business = $this->db->query("SELECT * FROM tbl_business_list where _id = '" . $user_data[0]->business_id."'")->result();
                if(!empty($business)){
                    $businessname = $business[0]->name;
                }else{
                    $businessname = "";
                }         
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data= [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => $businessname,
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'company' => $user_data[0]->company,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'Profile Update successfully';
                $outputjson['record'] = $data;     
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }
    
    public function check_Profile_api_token($api_token) {
        $check_api_token = $this->db->query("SELECT * FROM tbl_users where api_token = '".$api_token."'")->result();
        return $check_api_token;
    }

    public function get_profile_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('api_token', 'api_token', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $api_token = trim($_REQUEST['api_token']);
            $user_data = $this->db->query("SELECT * FROM tbl_users where api_token = '".$api_token."'")->result();
            if(!empty($user_data)) 
            {
                $business = $this->db->query("SELECT * FROM tbl_business_list where _id = '" . $user_data[0]->business_id . "' ")->result();
                if(!empty($business)){
                    $businessname = $business[0]->name;
                }else{
                    $businessname = "";
                }         
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data= [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => $businessname,
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'company' => $user_data[0]->company,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'success';
                $outputjson['record'] = $data;
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }

    public function get_profile_new_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $user_id = trim($_REQUEST['user_id']);
            $user_data = $this->db->query("SELECT * FROM tbl_users where _id = '".$user_id."'")->result();
            if(!empty($user_data)) 
            {
                $business = $this->db->query("SELECT * FROM tbl_business_list where _id = '" . $user_data[0]->business_id . "' ")->result();
                if(!empty($business)){
                    $businessname = $business[0]->name;
                }else{
                    $businessname = "";
                }         
                if($user_data[0]->image != ''){
                    $image = base_url('images/users/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data= [
                    'id' => $user_data[0]->_id,
                    'name' => $user_data[0]->name,
                    'email' => $user_data[0]->email,
                    'email_verified_at' => $user_data[0]->email_varify_at,
                    'city' => $user_data[0]->city,
                    'image' => $image,
                    'business' => $businessname,
                    'contact' => $user_data[0]->contact,
                    'contact_verify' => $user_data[0]->contact_verify,
                    'sms_otp' => $user_data[0]->otp,
                    'reset_otp' => $user_data[0]->reset_otp,
                    'plan_start' => $user_data[0]->plan_start,
                    'plan_end' => $user_data[0]->plan_end,
                    'plan_type' => $user_data[0]->plan_type,
                    'company' => $user_data[0]->company,
                    'image_url' => $image
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'success';
                $outputjson['record'] = $data;
            }else {
                $outputjson['result'] = 404;
                $outputjson['is_expired'] = 1;
                $outputjson['message'] = 'Please try again!';
            }
            $this->response($outputjson);
        }
    }

       
    function get_business_post() {
        $jsonval = (object)array();
        $outputjson = array();
        $this->form_validation->set_rules('user_id', 'user_id', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $outputjson['result'] = 0;
            $errorString = implode(",",$this->form_validation->error_array());
            $outputjson['message'] = $errorString;
            $this->response($outputjson);
        }else{
            $user_id = trim($_REQUEST['user_id']);
            $user_data = $this->db->query("SELECT * FROM tbl_user_business where user_id = '".$user_id."'")->result();
            if(!empty($user_data)) 
            {
                if($user_data[0]->image != ''){
                    $image = base_url('images/user_business/').$user_data[0]->image;
                }else{
                    $image = LOGO;
                }
                $data= [
                    'id' => $user_data[0]->_id,
                    'user_id' => $user_data[0]->user_id,
                    'name' => $user_data[0]->name,
                    'image' => $image,
                    'address' => $user_data[0]->address,
                    'contact' => $user_data[0]->contact,
                    'email' => $user_data[0]->email,
                    'website' => $user_data[0]->website,
                    'image_url' => $image   
                ];
                $outputjson['result'] = 1;
                $outputjson['message'] = 'success';
                $outputjson['record'] = $data;
            }else {
                $outputjson['result'] = 0;
                $outputjson['message'] = 'Business not added';
            }
            $this->response($outputjson);
        }
    }
}
?>