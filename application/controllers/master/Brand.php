<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Brand extends CI_Controller{
	protected $table;
	protected $term;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->table 			= 'brand_master';
		$this->term  			= 'brand';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('master/Brandmdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_master(true);
					$config['base_url'] 	= base_url("master/".$this->term."?search=true");

					foreach ($_GET as $key => $value) 
					{
						if($key != 'search' && $key != 'offset')
						{
							$config['base_url'] .= "&" . $key . "=" .$value;
						}
					}

					$offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
					$this->pagination->initialize($config);

					$record['count']		= $offset;
					$record['total_rows'] 	= $config['total_rows'];
					$record['data']			= $this->model->get_master(false, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/master/'.$this->table, $record);
				}else{
					$this->load->view('errors/error');
				}
			}else{
				$this->load->view('errors/error');
			}
		}else{
			redirect('login/logout');	
		}
	}
	public function add_update($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$post_data = $this->input->post();
		// echo "<pre>"; print_r($post_data); exit;
		if(empty($post_data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Form data is empty.']);
			return;
		}
		$post_data[$this->term.'_name'] 		= trim($post_data[$this->term.'_name']);
		$post_data[$this->term.'_status'] 		= isset($post_data[$this->term.'_status']);
		$post_data[$this->term.'_updated_by'] 	= $_SESSION['user_id'];
		$temp = $this->db_operations->get_record($this->table, [$this->term.'_id !=' => $id, $this->term.'_name' => $post_data[$this->term.'_name']]);
		if(!empty($temp)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => ucfirst($this->term).' already added']);
			return;	
		}
		if($id == 0){
			$post_data[$this->term.'_created_by'] = $_SESSION['user_id'];
			$post_data[$this->term.'_created_at'] = date('Y-m-d H:i:s');
			$id = $this->db_operations->data_insert($this->table, $post_data);
			$msg = 'Added successfully';
			if($id < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
			}
		}else{
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->table, $post_data, $this->term.'_id', $id) < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
			}
		}
		$data['id'] 	= $id;
		$data['name'] 	= strtoupper($post_data[$this->term.'_name']);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record([$this->term.'_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => $this->term.' not found.']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record([$this->term.'_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not Found.']);
			return;	
		}
		$isExist = $this->model->isExist($id);
		if($isExist){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not Allowed.']);
			return;	
		}
		if($this->db_operations->delete_record($this->table, [$this->term.'_id' => $id]) < 1){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not deleted.']);
			return;
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [], 'msg' => 'Deleted successfully']);
	}
	public function get_select2(){
		$json = [];
		$data = $this->model->get_select2();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}

	// public function temp_function1(){  
	// 	$this->db->trans_begin();
	// 	$temp = $this->db_operations->get_record('purchase_master', ['pm_branch_id !='=>1]);
	// 	if(!empty($temp)){
	// 		foreach ($temp as $key => $value) {
	// 			$data['pm_entry_no'] 	= $this->db_operations->get_fin_year_branch_max_id('purchase_master', 'pm_entry_no', 'pm_fin_year', $_SESSION['fin_year'], 'pm_branch_id', 3);
	// 			$data['pm_branch_id'] = 3;

	// 			if($this->db_operations->data_update('purchase_master', $data,'pm_id',$value['pm_id']) < 1){
	// 				$this->db->trans_rollback();
	// 				echo 'PURCHASE NOT UPDATED';
	// 			 	return;
	// 			}
	// 			$bm_data = $this->db_operations->get_record('barcode_master', ['bm_pm_id'=>$value['pm_id']]);
	// 			if(!empty($bm_data)){
	// 				if($bm_data[0]['bm_branch_id']>0){
	// 					if($this->db_operations->data_update('barcode_master',['bm_branch_id'=>3],'bm_pm_id',$value['pm_id']) < 1){
	// 						$this->db->trans_rollback();
	// 						echo 'BARCODE NOT UPDATED';
	// 					 	return;
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	if ($this->db->trans_status() === FALSE){
	// 	    $this->db->trans_rollback();
	// 	    ['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not Updated'];
	// 		return;
	//     }
	//     $this->db->trans_commit();

	//     echo 1;
	// }

	// public function temp_function2(){  
	// 	$this->db->trans_begin();
	// 	$temp = $this->db_operations->get_record('outward_master', ['om_branch !='=>1,'om_branch !='=>3]);
	// 	if(!empty($temp)){
	// 		foreach ($temp as $key => $value) {
	// 			$data['om_entry_no'] = $this->db_operations->get_fin_year_branch_max_id('outward_master', 'om_entry_no', 'om_fin_year', $_SESSION['fin_year'],'om_branch_id',1);
	// 			$data['om_branch'] = 3;
	// 			if($this->db_operations->data_update('outward_master', $data,'om_id',$value['om_id']) < 1){
	// 				$this->db->trans_rollback();
	// 				echo 'OUTWARD NOT UPDATED';
	// 			 	return;
	// 			}
				
	// 		}
	// 	}
	// 	if ($this->db->trans_status() === FALSE){
	// 	    $this->db->trans_rollback();
	// 	    ['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not Updated'];
	// 		return;
	//     }
	//     $this->db->trans_commit();

	//     echo 1;
	// }

	// public function temp_function3(){  
	// 	$this->db->trans_begin();
	// 	$temp = $this->db_operations->get_record('grn_master', ['gm_branch_id !='=>1,'gm_branch_id !='=>3]);
	// 	if(!empty($temp)){
	// 		foreach ($temp as $key => $value) {
				
	// 			$data['gm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id('grn_master', 'gm_entry_no', 'gm_fin_year', $_SESSION['fin_year'], 'gm_branch_id',3);

	// 			$data['gm_branch_id'] = 3;
	// 			if($this->db_operations->data_update('grn_master', $data,'gm_id',$value['gm_id']) < 1){
	// 				$this->db->trans_rollback();
	// 				echo 'OUTWARD NOT UPDATED';
	// 			 	return;
	// 			}
				
	// 		}
	// 	}
	// 	if ($this->db->trans_status() === FALSE){
	// 	    $this->db->trans_rollback();
	// 	    ['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not Updated'];
	// 		return;
	//     }
	//     $this->db->trans_commit();

	//     echo 1;
	// }


}
?>
