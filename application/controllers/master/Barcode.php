<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Barcode extends CI_Controller{
	protected $table;
	protected $term;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->table 			= 'barcode_master';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('master/Barcodemdl', 'model');
		$this->load->model('purchase/Purchasemdl');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						$this->load->view('pdfs/barcode_pdf', $record);
					}else{
						$this->load->view('errors/error');
					}
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
	public function add_barcode(){
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
		$year  = date('y');
		$month = date('m');
		$master_data['bm_barcode_year'] 		= date('Y');
		$master_data['bm_barcode_month'] 		= $month;
		$master_data['bm_counter']				= $this->Purchasemdl->generate_barcode();
		$master_data['bm_item_code'] 			= $year.''.$month.''.$master_data['bm_counter'];
		$master_data['bm_design_id']			= $post_data['design_id'];
		$master_data['bm_style_id']				= $post_data['style_id'];
		$master_data['bm_brand_id']				= $post_data['brand_id'];
		// $master_data['bm_age_id']				= $post_data['age_id'];
		$master_data['bm_pt_qty']				= $post_data['qty'];
		$master_data['bm_sp_amt']				= $post_data['rate'];
		$master_data['bm_branch_id']			= $_SESSION['user_branch_id'];
		$master_data['bm_fin_year']				= $_SESSION['fin_year'];
		
		$this->db->trans_begin();
		$id  = $this->db_operations->data_insert($this->table, $master_data);
		if($id < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not added']);
			return;
		}
		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
			return;
	    }
	    $this->db->trans_commit();
	    $data['id'] 	= $id;
	    $data['name'] 	= $master_data['bm_item_code'];
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => '']);
	}
	public function get_barcode_select2(){
		$json = [];
		$data = $this->model->get_barcode_select2();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2(){
		$json = [];
		$data = $this->model->get_select2();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_acc_id(){
		$json = [];
		$data = $this->model->get_select2_acc_id();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_style_id(){
		$json = [];
		$data = $this->model->get_select2_style_id();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_design_id(){
		$json = [];
		$data = $this->model->get_select2_design_id();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_brand_id(){
		$json = [];
		$data = $this->model->get_select2_brand_id();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_age_id(){
		$json = [];
		$data = $this->model->get_select2_age_id();
		foreach ($data as $key => $value) 
		{
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_barcode_data($bm_id){ 
  		if(sessionExist()){
  			$data 	= $this->model->get_barcode_data($bm_id);
  			$msg 	= 'Data fetched successfully.';
  			$flag 	= 1;
  			if(empty($data)){
  				$msg 	= 'Record not found.';
  				$flag 	= 0;
  			}
			echo json_encode(['status' => true, 'flag' => $flag, 'data' => $data, 'msg' => $msg]);
		}else {
			echo json_encode($this->session_expired);
		}
  	}
}
?>
