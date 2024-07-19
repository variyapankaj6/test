<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Security_model extends CI_Model{

    public function __construct(){  
        parent::__construct();
        $this->load->model('Query_model','qm',TRUE);
    }

    public function is_logged_in(){
        if($this->session->userdata('educationcv_email') != "" && $this->session->userdata('educationcv_id') != ""){
            return true;
        }else{
             $this->session->set_flashdata('error','Please login!');
             redirect('/admin');
        }
    }
    
    public function is_logged_in_web(){
        if($this->session->userdata('educationcv_web_email') != "" && $this->session->userdata('educationcv_web_id') != ""){
            $where = array ('_id' => $this->session->userdata('educationcv_web_id'));
            $validate = $this->qm->select_where('tbl_register',$where);
            if($validate[0]['status'] == 1){
                if($validate[0]['is_suspend'] == '0'){
                    return true;
                }else{
                    $this->session->set_flashdata('error', 'We regret to inform you that your account has been suspended.');
                    $sess_array = array('educationcv_web_email','educationcv_web_id','educationcv_web_otpcheck','educationcv_web_forgotemail');
                    $this->session->unset_userdata($sess_array);
                    redirect('');
                }
            }else{
                $this->session->set_flashdata('error', 'We regret to inform you that your account has been deactivated.');
                $sess_array = array('educationcv_web_email','educationcv_web_id','educationcv_web_otpcheck','educationcv_web_forgotemail');
                $this->session->unset_userdata($sess_array);
                redirect('');
            }
        }else{
             $this->session->set_flashdata('error','Please login!');
             redirect('');
        }
    }
}