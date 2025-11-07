<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class user_model extends my_model{
	public function __construct(){ parent::__construct(); } 
	
   public function get_user($user_name){
        $query="SELECT *
                FROM user_master 
                WHERE user_name = '$user_name'";
        return $this->db->query($query)->result_array();
    }
    public function get_session_by_date($date){
        $query="SELECT usm.usm_id,
                DATE_FORMAT(usm.created_at, '%Y-%m-%d') as created_at
                FROM user_session_master usm
                WHERE 1
                HAVING created_at <= '$date'";
        return $this->db->query($query)->result_array();
    }
    public function get_session_by_user($user_id){ 
        $query="SELECT usm.usm_id
                FROM user_session_master usm
                WHERE usm.usm_user_id = '$user_id'";
        return $this->db->query($query)->result_array();
    }
    

}
?>
