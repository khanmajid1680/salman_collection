<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Login extends CI_Controller {
		public function __construct(){
			parent::__construct();
			$this->load->model('master/Branchmdl');
			$this->load->model('master/Usermdl');
			$this->config->load('extra');
		}
		public function index(){
			if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
				redirect(base_url('/home'));
				return;
			}
			$record['year'] 		= $this->get_year_range();
			$record['title'] 		= $this->config->item('title');
			$record['branch'] 		= $this->Branchmdl->get_record(['branch_status' => true], true);
			// echo "<pre>"; print_r($record); exit();
			$this->load->view('login', $record);
		}
		public function get_fin_year($fin_year, $start = true){
			$explode = explode('-', $fin_year);
			if($start) return $explode[0].'-04-01'; // 2020-04-01
			return $explode[1].'-03-31'; // 2021-03-31
		}
		public function get_year_range()
		{
			// echo "<pre>"; print_r(); exit;	
			$today_date = date('d-m-Y');	
			// echo "<pre>";print_r($today_date);

			$financial_start_date = date('01-04-Y');
			// echo "<pre>"; print_r($financial_start_date); exit;

			if(strtotime($today_date) >= strtotime($financial_start_date))
			{
				$years = range('2025', date('Y')+1);	
			}
			else
			{
				$years = date('Y'); //range('2025', date('Y'));					
			}
			

			// echo "<pre>"; print_r($years);exit;
			$range = array();

			$size = sizeOf($years);

			foreach ($years as $key => $value)
			{
				if($key != $size - 1)			
					$range[$value."-".$years[$key+1]] = $value."-".$years[$key+1];
			}

			return array_reverse($range);
		}
		
		public function login_action() 
		{
			$post_data = $this->input->post();
			$user = $this->Usermdl->get_record(['user_name' => $post_data['user_name'], 'user_branch_id' => $post_data['user_branch_id'], 'user_status' => true]);
			if(empty($user)){
				echo json_encode(['status' => false, 'msg' => 'Invalid Credentials']);
				return ;
			}

    		if($user[0]['user_password'] != md5($post_data['user_password'])){
				echo json_encode(['status' => false, 'msg' => 'Invalid Credentials']);
				return ;
    		}

			$session_data['user_id'] 		= $user[0]['user_id'];
			$session_data['user_role'] 		= $user[0]['user_role'];
			$session_data['user_name'] 		= $user[0]['user_name'];
			$session_data['user_fullname'] 	= $user[0]['user_fullname'];
			$session_data['user_branch_id'] = $user[0]['user_branch_id'];
			$session_data['user_branch'] 	= $this->Branchmdl->get_name(['branch_id' => $user[0]['user_branch_id']]);
			$session_data['fin_year'] 		= $post_data['fin_year'];
			$session_data['start_year'] 	= $this->get_fin_year($post_data['fin_year']);
			$session_data['end_year'] 		= $this->get_fin_year($post_data['fin_year'], false);

			$this->session->set_userdata($session_data);
			$session_id = $this->session->session_id;
			$arr = array(
					'user_token' 		=> $session_id,
					'user_log_status' 	=> true,
					'user_ip' 			=> $_SERVER["REMOTE_ADDR"]
				);

			$this->db_operations->data_update("user_master", $arr, 'user_id', $user[0]['user_id']);
			echo json_encode(['status' => true, 'msg'=>'Login successfully']);
		}
		
		public function logout(){

			$arr = array(
				'user_token' 		=> false,
				'user_log_status' 	=> false,
				'user_ip' 			=> $_SERVER["REMOTE_ADDR"]
			);
			
			$user_id = $this->session->userdata('user_id');
			$this->db_operations->data_update("user_master", $arr, 'user_id', $user_id);

			$this->session->sess_destroy();

			redirect(base_url());
		}
	}
?>