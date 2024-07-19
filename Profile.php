<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends CI_Controller {

	public function __construct(){	
		parent::__construct();
		$this->load->model('security_model');
        $this->load->library('form_validation');
		$this->security_model->is_logged_in_web();
		$this->load->model('query_model','qm',TRUE);
	}
	
	public function index(){
        $user_id = $this->session->userdata('educationcv_web_id');
        if (isset($_POST['website_submit'])) {
            $this->form_validation->set_rules('website_name', 'Website Name', 'required|valid_url');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                redirect('profile');
            }else{
                date_default_timezone_set('Asia/Kolkata');
                $post_data1['website_name'] = $_POST['website_name'];
                $post_data1['register_id'] = $user_id;
                $post_data1['created_at'] = date('Y-m-d H:i:s');
                $this->qm->ins('tbl_websites', $post_data1);
                $this->session->set_flashdata('success', 'Website Added Successfully.');
                redirect('profile');
            }
        }else if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[100]');
            // $this->form_validation->set_rules('mobile_no', 'Mobile Number', 'required|numeric|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('profession', 'Profession', 'required|max_length[50]');
            $this->form_validation->set_rules('location', 'Location', 'required|max_length[100]');
            $this->form_validation->set_rules('about_us', 'About Us', 'required|max_length[1000]');
            $this->form_validation->set_rules('facebook_link', 'Facebook Link', 'valid_url');
            $this->form_validation->set_rules('twitter_link', 'Twitter Link', 'valid_url');
            $this->form_validation->set_rules('instagram_link', 'Instagram Link', 'valid_url');
            $this->form_validation->set_rules('linkedin_link', 'LinkedIn Link', 'valid_url');
            $this->form_validation->set_rules('youtube_link', 'YouTube Link', 'valid_url');
            $this->form_validation->set_rules('pinterest_link', 'Pinterest Link', 'valid_url');
            $this->form_validation->set_rules('snapchat_link', 'Snapchat Link', 'valid_url');
            $this->form_validation->set_rules('whatsapp_link', 'WhatsApp Link', 'valid_url');
            $this->form_validation->set_rules('threads_link', 'Threads Link', 'valid_url');
            $this->form_validation->set_rules('tiktok_link', 'Tiktok Link', 'valid_url');
            $this->form_validation->set_rules('others_link', 'Others Link', 'valid_url');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                redirect('profile');
            }else{
                date_default_timezone_set('Asia/Kolkata');
                if (isset($_FILES['photo']['name']) && ($_FILES['photo']['name']) != "") {
                    $data['tbl'] = 'tbl_register';
                    $data['select_field'] = 'photo';
                    $data['where_field'] = "_id='".$user_id."'";
                    $imgpath = 'images/user';
                    $data['img_path'] = glob($imgpath.'*');
                    $this->qm->delete_img($data);

                    $type = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $photo = rand(1111, 9999) . time() . "." . $type;
                    $config['file_name'] = $photo;
                    $config['upload_path'] = "images/user/";
                    $config['allowed_types'] = "jpg|jpeg|png";
                    $this->upload->initialize($config);
                    $this->upload->do_upload('photo');
                    $post_data['photo'] = $photo;
                }
                $post_data['name'] = ucfirst($_POST['name']);
                // $post_data['mobile_no'] = $_POST['mobile_no'];
                $post_data['profession'] = $_POST['profession'];
                $post_data['location'] = $_POST['location'];
                $post_data['about_us'] = $_POST['about_us'];
                $post_data['modified_at'] = date('Y-m-d H:i:s');
                $this->qm->updt('tbl_register', $post_data,array('_id' => $user_id));

                $social['faceboook_link'] = !empty($_POST['faceboook_link']) ? $_POST['faceboook_link'] : null; 
                $social['twitter_link'] = !empty($_POST['twitter_link']) ? $_POST['twitter_link'] : null; 
                $social['instagram_link'] = !empty($_POST['instagram_link']) ? $_POST['instagram_link'] : null; 
                $social['linkedin_link'] = !empty($_POST['linkedin_link']) ? $_POST['linkedin_link'] : null; 
                $social['youtube_link'] = !empty($_POST['youtube_link']) ? $_POST['youtube_link'] : null; 
                $social['pinterest_link'] = !empty($_POST['pinterest_link']) ? $_POST['pinterest_link'] : null; 
                $social['snapchat_link'] = !empty($_POST['snapchat_link']) ? $_POST['snapchat_link'] : null; 
                $social['whatsapp_link'] = !empty($_POST['whatsapp_link']) ? $_POST['whatsapp_link'] : null; 
                $social['threads_link'] =  !empty($_POST['threads_link']) ? $_POST['threads_link'] : null; 
                $social['tiktok_link'] =  !empty($_POST['tiktok_link']) ? $_POST['tiktok_link'] : null; 
                $social['others_link'] =  !empty($_POST['others_link']) ? $_POST['others_link'] : null; 
                $checklink = $this->qm->select_where('tbl_social_media', array('register_id' => $user_id));
                if(empty($checklink)){
                    $social['register_id'] = $user_id;
                    $this->qm->ins('tbl_social_media', $social);
                }else{
                    $this->qm->updt('tbl_social_media', $social,array('register_id' => $user_id));
                }

                $this->session->set_flashdata('success', 'Profile Update Successfully.');
                redirect('profile');
            }
        }else{
            $view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
            $view_data['social_media'] = $this->qm->select_where('tbl_social_media', array('register_id' => $user_id));
            $view_data['websites'] = $this->qm->select_where('tbl_websites', array('register_id' => $user_id));
            $this->load->view('web/header_user');
            $this->load->view('web/profile',$view_data);
            $this->load->view('web/footer_user');
        }
    }

    private function validateUsername($username) {
        if (empty($username)) {
            return "Username cannot be empty.";
        }

        $minLength = 4;
        $maxLength = 20;
        $usernameLength = strlen($username);
        if ($usernameLength < $minLength || $usernameLength > $maxLength) {
            return "Username must be between $minLength and $maxLength characters long.";
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return "Username can only contain letters, numbers, and underscores.";
        }

        $reservedUsernames = array("default_controller", "404_override", "translate_uri_dashes", "register", "about_us", "faqs", "privacy_policy", "terms_and_conditions", "admin", "admin/dashboard", "admin/change_pswd", "admin/logout", "changepassword", "comments", "comments/getLists", "contact_us", "dashboard", "discover", "discover/view", "followers", "followers/getLists_following", "followers/getLists", "following", "home", "login", "login/register", "login/logout", "login/verify_otp", "login/resend_otp", "forgot_password", "forgot_password/change", "profile", "profile/delete_education_higher", "profile/education_higher", "profile/delete_graduate_degrees", "profile/graduate_degrees", "profile/delete_school", "profile/school", "profile/delete_websites", "profile/delete_non_award_program", "profile/non_award_program", "sup_admin/cms_pages", "sup_admin/cms_pages/getLists", "sup_admin/cms_pages/add_cms_pages", "sup_admin/cms_pages/delete", "sup_admin/contactus", "sup_admin/contactus/getLists", "sup_admin/contactus/delete", "sup_admin/dashboard", "sup_admin/registers", "sup_admin/registers/active_deactive", "sup_admin/registers/active_suspend", "sup_admin/registers/getLists", "sup_admin/registers/delete");

        if (in_array(strtolower($username), $reservedUsernames)) {
            return "This username is not allowed.";
        }

        return true;
    }

    public function process_profile_ajax() {
        $url = $this->input->post('url');
        $user_id = $this->session->userdata('educationcv_web_id');
        $validationResult = $this->validateUsername($url);
        if ($validationResult === true) {
            $check = $this->qm->select_where('tbl_register', array('profile_url' => $url,'_id !=' => $user_id));
            if (empty($check)) {
                $post_data['profile_url'] = $url;
                $post_data['modified_at'] = date('Y-m-d H:i:s');
                $this->qm->updt('tbl_register', $post_data,array('_id' => $user_id));
                echo "Profile URL Updated";
            } else {
                echo "<b style='color:red;'>This name already used try diffrent<b>";
            }
        }else{
            echo $validationResult;
        }
    }

    public function delete_education_higher($id)
    {
        $user_id = $this->session->userdata('educationcv_web_id');
        $check = $this->qm->select_where('tbl_education_higher', array('_id' => $id,'register_id' => $user_id));
        if(!empty($check)){
            $where=array('_id'=>$id);
            $this->qm->dlt("tbl_education_higher",$where);   
        }
        $this->session->set_flashdata('success', 'Higher Education Delete Successfully.');
        redirect('profile/education_higher');
    }

    function word_count($str) {
        $word_count = str_word_count($str);
        $max_words = 1000;
        if ($word_count > $max_words) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function education_higher(){
        $user_id = $this->session->userdata('educationcv_web_id');
        $update_id = $this->uri->segment(3);
        if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('institute', 'Institute', 'required|max_length[255]');
            $this->form_validation->set_rules('website_link', 'Website Link', 'max_length[255]');
            $this->form_validation->set_rules('start_year', 'Start Year', 'required');
            $this->form_validation->set_rules('end_year', 'End Year', 'required');
            // $this->form_validation->set_rules('spent', 'Spent', 'required|numeric');
            $this->form_validation->set_rules('comments', 'Comments', 'required|callback_word_count',
                array('word_count' => 'The comments field should not exceed 1000 words.')
            );
            $this->form_validation->set_rules('status', 'Status', 'required|numeric');
            $this->form_validation->set_rules('campus', 'Campus', 'required');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                if ($update_id != "") {
                    redirect('profile/education_higher/'.$update_id);
                }else{
                    redirect('profile/education_higher');
                }
            }else{
                date_default_timezone_set('Asia/Kolkata');
                $post_data1['name'] = $_POST['name'];
                $post_data1['institute'] = $_POST['institute'];
                $post_data1['website_link'] = $_POST['website_link'];
                $post_data1['start_year'] = $_POST['start_year'];
                $post_data1['end_year'] = $_POST['end_year'];
                $post_data1['spent'] = '0';//$_POST['spent'];
                $post_data1['campus'] = $_POST['campus'];
                $post_data1['comments'] = $_POST['comments'];
                $post_data1['status'] = $_POST['status'];
                $post_data1['register_id'] = $user_id;
                if ($update_id != "") {
                    $this->qm->updt('tbl_education_higher', $post_data1,array('_id' => $update_id));
                    $this->session->set_flashdata('success', 'Higher Education Update Successfully.');
                }else{
                    $post_data1['created_at'] = date('Y-m-d H:i:s');
                    $this->qm->ins('tbl_education_higher', $post_data1);
                    $this->session->set_flashdata('success', 'Higher Education Added Successfully.');
                }
                redirect('profile/education_higher');
            }
        }else{
            $view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
            $view_data['education_higher'] = $this->qm->select_where('tbl_education_higher', array('register_id' => $user_id));
            if ($update_id != "") {
                $view_data['education_higher_update'] = $this->qm->select_where('tbl_education_higher', array('_id' => $update_id,'register_id' => $user_id));
                if(empty($view_data['education_higher_update'])){
                    redirect('profile/education_higher');
                }
            }
            $this->load->view('web/header_user');
            $this->load->view('web/education_higher',$view_data);
            $this->load->view('web/footer_user');
        }
    }

    public function delete_graduate_degrees($id)
    {
        $user_id = $this->session->userdata('educationcv_web_id');
        $check = $this->qm->select_where('tbl_graduate_degrees', array('_id' => $id,'register_id' => $user_id));
        if(!empty($check)){
            $where=array('_id'=>$id);
            $this->qm->dlt("tbl_graduate_degrees",$where);   
        }
        $this->session->set_flashdata('success', 'Graduate degrees Delete Successfully.');
        redirect('profile/graduate_degrees');
    }

    public function graduate_degrees(){
        $user_id = $this->session->userdata('educationcv_web_id');
        $update_id = $this->uri->segment(3);
        if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('institute', 'Institute', 'required|max_length[255]');
            $this->form_validation->set_rules('website_link', 'Website Link', 'max_length[255]');
            $this->form_validation->set_rules('start_year', 'Start Year', 'required');
            $this->form_validation->set_rules('end_year', 'End Year', 'required');
            // $this->form_validation->set_rules('spent', 'Spent', 'required|numeric');
            $this->form_validation->set_rules('comments', 'Comments', 'required|callback_word_count',
                array('word_count' => 'The comments field should not exceed 1000 words.')
            );
            $this->form_validation->set_rules('status', 'Status', 'required|numeric');
            $this->form_validation->set_rules('campus', 'Campus', 'required');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                if ($update_id != "") {
                    redirect('profile/graduate_degrees/'.$update_id);
                }else{
                    redirect('profile/graduate_degrees');
                }
            }else{
                date_default_timezone_set('Asia/Kolkata');
                $post_data1['name'] = $_POST['name'];
                $post_data1['institute'] = $_POST['institute'];
                $post_data1['website_link'] = $_POST['website_link'];
                $post_data1['start_year'] = $_POST['start_year'];
                $post_data1['end_year'] = $_POST['end_year'];
                $post_data1['spent'] = '0';//$_POST['spent'];
                $post_data1['campus'] = $_POST['campus'];
                $post_data1['comments'] = $_POST['comments'];
                $post_data1['status'] = $_POST['status'];
                $post_data1['register_id'] = $user_id;
                if ($update_id != "") {
                    $this->qm->updt('tbl_graduate_degrees', $post_data1,array('_id' => $update_id));
                    $this->session->set_flashdata('success', 'Graduate degrees Update Successfully.');
                }else{
                    $post_data1['created_at'] = date('Y-m-d H:i:s');
                    $this->qm->ins('tbl_graduate_degrees', $post_data1);
                    $this->session->set_flashdata('success', 'Graduate degrees Added Successfully.');
                }
                redirect('profile/graduate_degrees');
            }
        }else{
            $view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
            $view_data['graduate_degrees'] = $this->qm->select_where('tbl_graduate_degrees', array('register_id' => $user_id));
            if ($update_id != "") {
                $view_data['graduate_degrees_update'] = $this->qm->select_where('tbl_graduate_degrees', array('_id' => $update_id,'register_id' => $user_id));
                if(empty($view_data['graduate_degrees_update'])){
                    redirect('profile/graduate_degrees');
                }
            }
            $this->load->view('web/header_user');
            $this->load->view('web/graduate_degrees',$view_data);
            $this->load->view('web/footer_user');
        }
    }

    public function delete_school($id)
    {
        $user_id = $this->session->userdata('educationcv_web_id');
        $check = $this->qm->select_where('tbl_school', array('_id' => $id,'register_id' => $user_id));
        if(!empty($check)){
            $where=array('_id'=>$id);
            $this->qm->dlt("tbl_school",$where);   
        }
        $this->session->set_flashdata('success', 'School Delete Successfully.');
        redirect('profile/school');
    }

    public function school(){
        $user_id = $this->session->userdata('educationcv_web_id');
        $update_id = $this->uri->segment(3);
        if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('website_link', 'Website Link', 'max_length[255]');
            $this->form_validation->set_rules('start_year', 'Start Year', 'required');
            $this->form_validation->set_rules('end_year', 'End Year', 'required');
            // $this->form_validation->set_rules('spent', 'Spent', 'required|numeric');
            $this->form_validation->set_rules('comments', 'Comments', 'required|callback_word_count',
                array('word_count' => 'The comments field should not exceed 1000 words.')
            );
            $this->form_validation->set_rules('status', 'Status', 'required|numeric');
            $this->form_validation->set_rules('campus', 'Campus', 'required');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                if ($update_id != "") {
                    redirect('profile/school/'.$update_id);
                }else{
                    redirect('profile/school');
                }
            }else{
                date_default_timezone_set('Asia/Kolkata');
                $post_data1['name'] = $_POST['name'];
                $post_data1['website_link'] = $_POST['website_link'];
                $post_data1['start_year'] = $_POST['start_year'];
                $post_data1['end_year'] = $_POST['end_year'];
                $post_data1['spent'] = '0';//$_POST['spent'];
                $post_data1['campus'] = $_POST['campus'];
                $post_data1['comments'] = $_POST['comments'];
                $post_data1['status'] = $_POST['status'];
                $post_data1['register_id'] = $user_id;
                if ($update_id != "") {
                    $this->qm->updt('tbl_school', $post_data1,array('_id' => $update_id));
                    $this->session->set_flashdata('success', 'School Update Successfully.');
                }else{
                    $post_data1['created_at'] = date('Y-m-d H:i:s');
                    $this->qm->ins('tbl_school', $post_data1);
                    $this->session->set_flashdata('success', 'School Added Successfully.');
                }
                redirect('profile/school');
            }
        }else{
            $view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
            $view_data['school'] = $this->qm->select_where('tbl_school', array('register_id' => $user_id));
            if ($update_id != "") {
                $view_data['school_update'] = $this->qm->select_where('tbl_school', array('_id' => $update_id,'register_id' => $user_id));
                if(empty($view_data['school_update'])){
                    redirect('profile/school');
                }
            }
            $this->load->view('web/header_user');
            $this->load->view('web/school',$view_data);
            $this->load->view('web/footer_user');
        }
    }

    public function delete_websites($id)
    {
        $user_id = $this->session->userdata('educationcv_web_id');
        $check = $this->qm->select_where('tbl_websites', array('_id' => $id,'register_id' => $user_id));
        if(!empty($check)){
            $where=array('_id'=>$id);
            $this->qm->dlt("tbl_websites",$where);   
        }
        $this->session->set_flashdata('success', 'Website Delete Successfully.');
        redirect('profile');
    }

    public function delete_non_award_program($id)
    {
        $user_id = $this->session->userdata('educationcv_web_id');
        $check = $this->qm->select_where('tbl_non_award_program', array('_id' => $id,'register_id' => $user_id));
        if(!empty($check)){
            $where=array('_id'=>$id);
            $this->qm->dlt("tbl_non_award_program",$where);   
        }
        $this->session->set_flashdata('success', 'Non Award Program Delete Successfully.');
        redirect('profile/non_award_program');
    }

    public function non_award_program(){
        $user_id = $this->session->userdata('educationcv_web_id');
        $update_id = $this->uri->segment(3);
        if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('institute', 'Institute', 'required|max_length[255]');
            $this->form_validation->set_rules('website_link', 'Website Link', 'max_length[255]');
            $this->form_validation->set_rules('start_year', 'Start Year', 'required');
            $this->form_validation->set_rules('end_year', 'End Year', 'required');
            // $this->form_validation->set_rules('spent', 'Spent', 'required|numeric');
            $this->form_validation->set_rules('comments', 'Comments', 'required|callback_word_count',
                array('word_count' => 'The comments field should not exceed 1000 words.')
            );
            $this->form_validation->set_rules('status', 'Status', 'required|numeric');
            $this->form_validation->set_rules('campus', 'Campus', 'required');
            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                $this->session->set_flashdata('post_error', $_POST);
                if ($update_id != "") {
                    redirect('profile/non_award_program/'.$update_id);
                }else{
                    redirect('profile/non_award_program');
                }
            }else{
                date_default_timezone_set('Asia/Kolkata');
                $post_data1['name'] = $_POST['name'];
                $post_data1['institute'] = $_POST['institute'];
                $post_data1['website_link'] = $_POST['website_link'];
                $post_data1['start_year'] = $_POST['start_year'];
                $post_data1['end_year'] = $_POST['end_year'];
                $post_data1['spent'] = '0';//$_POST['spent'];
                $post_data1['campus'] = $_POST['campus'];
                $post_data1['comments'] = $_POST['comments'];
                $post_data1['status'] = $_POST['status'];
                $post_data1['register_id'] = $user_id;
                if ($update_id != "") {
                    $this->qm->updt('tbl_non_award_program', $post_data1,array('_id' => $update_id));
                    $this->session->set_flashdata('success', 'Non Award Program Update Successfully.');
                }else{
                    $post_data1['created_at'] = date('Y-m-d H:i:s');
                    $this->qm->ins('tbl_non_award_program', $post_data1);
                    $this->session->set_flashdata('success', 'Non Award Program Added Successfully.');
                }
                redirect('profile/non_award_program');
            }
        }else{
            $view_data['records'] = $this->qm->select_where('tbl_register', array('_id' => $user_id));
            $view_data['non_award_program'] = $this->qm->select_where('tbl_non_award_program', array('register_id' => $user_id));
            if ($update_id != "") {
                $view_data['non_award_program_update'] = $this->qm->select_where('tbl_non_award_program', array('_id' => $update_id,'register_id' => $user_id));
                if(empty($view_data['non_award_program_update'])){
                    redirect('profile/non_award_program');
                }
            }
            $this->load->view('web/header_user');
            $this->load->view('web/non_award_program',$view_data);
            $this->load->view('web/footer_user');
        }
    }
}