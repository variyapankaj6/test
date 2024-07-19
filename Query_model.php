<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Query_model extends CI_Model {
	
	public function upload($tbl,$a)
	{

		$this->db->insert($tbl,$a);
        return;
	}
	public function num_row_all($table)
	{
		$result = $this->db->get($table);
		return $result->num_rows();
	}

	public function select_result($data){
 		$query = $this->db->query($data);
 		return $query->result();
	}

	public function select_row($data){
		$query = $this->db->query($data);
 		return $query->row();
	}

	public function sum_where($table,$field,$where)
	{
		$this->db->where($where);
		$this->db->select_sum($field);
		$result = $this->db->get($table);
		return $result->result_array();
	}
	public function sum_where_sngle($table,$where)
	{
		$result = $this->db->get_where($table,$where);
		$data=$result->result_array();
		return $data[0];
	}
	public function sum_with_between($table,$field,$where,$field_between,$start_date,$end_date)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->where($field_between.' >=', $start_date);
		$this->db->where($field_between.' <=', $end_date);
		$this->db->select_sum($field);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function group_by_where($table,$group_by){
		$this->db->from($table);
		$this->db->group_by($group_by);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function group_by_where_with_field($table,$field){
		$this->db->from($table);
		$this->db->group_by($field);
		$result = $this->db->get();
		return $result->result_array();
	}
    public function group_by_where_with_field_result($table,$where,$field){
        $this->db->from($table);
        $this->db->where($where);
        $this->db->group_by($field);
        $result = $this->db->get();
        return $result->result_array();
    }
	public function sum($table,$field)
	{
		$this->db->select_sum($field);
		$result = $this->db->get($table);
		return $result->result_array();
	}
	
	public function num_row($table,$where)
	{
		$result = $this->db->get_where($table,$where);
		return $result->num_rows();
	}
	public function num_row_with_between($table,$where,$field_between,$start_date,$end_date)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->where($field_between.' >=', $start_date);
		$this->db->where($field_between.' <=', $end_date);
		$this->db->group_by('user_id');
		$result = $this->db->get();
		return $result->num_rows();
	}
	public function select_with_between_result($table,$where,$field_between,$start_date,$end_date)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->where($field_between.' >=', $start_date);
		$this->db->where($field_between.' <=', $end_date);
		$this->db->group_by('user_id');
		$result = $this->db->get();
		return $result->result_array();
	}

	public function select_all($table)
	{
		$result = $this->db->get($table);
		return $result->result_array();
	}
	
	
	public function select_where_row($table,$where)
	{
		$this->db->from($table);
		$this->db->where($where);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function SelectAllOrderBy($table,$key,$order_by)
	{
		$this->db->order_by($key,$order_by);
		$result = $this->db->get($table);
		return $result->result_array();
	}

	public function select_where($table,$where)
	{
		$result = $this->db->get_where($table,$where);
		return $result->result_array();
	}
	public function select_where_filter($table,$where,$key,$order_by)
	{
		$this->db->order_by($key,$order_by);
		$result = $this->db->get_where($table,$where);
		return $result->result_array();
	}
	public function ins($table,$where)
	{
		if($this->db->insert($table,$where))
		{
			return $this->db->insert_id();
		}else{
			return false;
		}
	}

	public function updt($table,$what,$where)
	{
		$this->db->where($where);
		$this->db->update($table,$what);
	}

	public function dlt($table,$where)
	{
		$this->db->delete($table,$where);	
	}
	
	function JoinTwoTable($from,$to,$where,$key,$typ)
	{
		$this->db->select('*');
		$this->db->where($where);
		$this->db->from($from);
		$this->db->join($to,$to.$key=$from.$key,$typ);		
		$result=$this->db->get();
		return $result->result_array();
	}

	function send_email($email,$subject,$message){
		$this->load->config('email');
        $this->load->library('email');
		$from = $this->config->item('smtp_user');
        $to = $email;
		$mail_message= $message;
		$this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($mail_message);

        if ($this->email->send()) {
			return 'Mail Send.';
		} else {
			return 'Failed to send Mail, please try again!';
		}    
	}

	function delete_img($data)
    {
        $tbl = $data['tbl'];
        $select_field = $data['select_field'];
        $where_field = $data['where_field'];
        $img_path = $data['img_path'];

        $sql = $this->db->query("SELECT $select_field from $tbl where $where_field");
        $rows = $sql->result();
        foreach ($rows as $p)
        {
            foreach ($img_path as $unlinks)
            {
                $path = $unlinks . '/' . $p->$select_field;
                if (file_exists($path))
                {
                    unlink($path);
                }
            }
        }
        return true;
    }
	function random_str($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    function star_rating($rating)
	{
		$rating_round = round($rating * 2) / 2;
		if ($rating_round == 0) {
			return '<i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 0.5 && $rating_round > 0) {
			return '<i class="fa fa-star-half" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 1 && $rating_round > 0.5) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 1.5 && $rating_round > 1) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star-half" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 2 && $rating_round > 1.5) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 2.5 && $rating_round > 2) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star-half" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 3 && $rating_round > 2.5) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;" ></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 3.5 && $rating_round > 3) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star-half" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 4 && $rating_round > 3.5) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color: #f3f6f9;"></i>';
		}
		if ($rating_round <= 4.5 && $rating_round > 4) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star-half" style="color:#ffa500;"></i>';
		}
		if ($rating_round <= 5 && $rating_round > 4.5) {
			return '<i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i><i class="fa fa-star" style="color:#ffa500;"></i>';
		}

	}

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */