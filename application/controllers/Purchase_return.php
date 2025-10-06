<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Purchase_Return extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'purchase_return_master';
		$this->trans 			= 'purchase_return_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('purchase/PurchaseReturnmdl', 'model');
		$this->load->model('master/Accountmdl');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_data(true);
					$config['base_url'] 	= base_url("purchase?search=true");

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
					$record['data']			= $this->model->get_data(false, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/purchase/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$this->load->view('pages/purchase/purchase_return_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$this->load->view('pages/purchase/purchase_return_form', $record);	
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						// echo "<pre>"; print_r($record); exit;
						$this->load->view('pdfs/purchase/purchase_return', $record);
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
		$master_data['prm_entry_no']		= trim($post_data['prm_entry_no']);
		$master_data['prm_entry_date'] 		= date('Y-m-d',strtotime($post_data['prm_entry_date']));	
		$master_data['prm_acc_id'] 			= trim($post_data['prm_acc_id']);
		$master_data['prm_total_qty']		= trim($post_data['prm_total_qty']);
		$master_data['prm_sub_total']		= trim($post_data['prm_sub_total']);
		$master_data['prm_round_off']		= trim($post_data['prm_round_off']);
		$master_data['prm_bill_disc']		= trim($post_data['prm_bill_disc']);
		$master_data['prm_taxable_amt']		= trim($post_data['prm_taxable_amt']);
		$master_data['prm_sgst_amt']		= trim($post_data['prm_sgst_amt']);
		$master_data['prm_cgst_amt']		= trim($post_data['prm_cgst_amt']);
		$master_data['prm_igst_amt']		= trim($post_data['prm_igst_amt']);

		$master_data['prm_gst_amt']			= trim($post_data['prm_gst_amt']);
		$master_data['prm_notes']			= trim($post_data['prm_notes']);
		$master_data['prm_final_amt']		= trim($post_data['prm_final_amt']);
		$master_data['prm_updated_by'] 		= $_SESSION['user_id'];				
		$temp = $this->model->get_record(['prm_id !=' => $id,'prm_entry_no' => $master_data['prm_entry_no'],'prm_fin_year' => $_SESSION['fin_year'],'prm_branch_id' => $_SESSION['user_branch_id']]);
		if(!empty($temp)){
			$master_data['prm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'prm_entry_no', 'prm_fin_year', $_SESSION['fin_year'], 'prm_branch_id', $_SESSION['user_branch_id']);
		}
		if($id == 0){
			$this->db->trans_begin();
			$master_data['prm_created_by'] 	= $_SESSION['user_id'];
			$master_data['prm_created_at'] 	= date('Y-m-d H:i:s');
			$master_data['prm_fin_year'] 	= $_SESSION['fin_year'];
			$master_data['prm_branch_id'] 	= $_SESSION['user_branch_id'];
			$id  = $this->db_operations->data_insert($this->master, $master_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
		    }
		    $this->db->trans_commit();
		}else{
			$prev_data = $this->model->get_record(['prm_id' => $id]);
			if(empty($prev_data)){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Record not found.']);
				return;
			}
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'prm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not updated']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not updated']);
				return;
			}
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Rollback']);
				return;
		    }
		    $this->db->trans_commit();
		}
		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){
		$trans_db_data = $this->db_operations->get_record($this->trans, ['prt_prm_id' => $id]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['prt_id'], $post_data['prt_id'])){
					if($this->db_operations->delete_record($this->trans,array('prt_id' =>$value['prt_id'])) < 1){
						return 0;
					}
				} 
			}
		}
		foreach ($post_data['prt_id'] as $key => $value){
			$trans_data['prt_prm_id'] 		= $id;
			$trans_data['prt_pm_id'] 	 	= $post_data['prt_pm_id'][$key];
			$trans_data['prt_pt_id'] 	 	= $post_data['prt_pt_id'][$key];
			$trans_data['prt_bm_id'] 	 	= $post_data['prt_bm_id'][$key];
			$trans_data['prt_bill_no'] 	 	= $post_data['prt_bill_no'][$key];
			$trans_data['prt_bill_date'] 	= date('Y-m-d', strtotime($post_data['prt_bill_date'][$key]));
			$trans_data['prt_design_id'] 	= $post_data['prt_design_id'][$key];
			$trans_data['prt_style_id'] 	= $post_data['prt_style_id'][$key];
			$trans_data['prt_brand_id'] 	= $post_data['prt_brand_id'][$key];
			$trans_data['prt_qty'] 			= $post_data['prt_qty'][$key];
			$trans_data['prt_rate']			= $post_data['prt_rate'][$key];
			$trans_data['prt_disc']			= $post_data['prt_disc'][$key];
			$trans_data['prt_taxable_amt']	= $post_data['prt_taxable_amt'][$key];
			$trans_data['prt_sgst_per']		= $post_data['prt_sgst_per'][$key];
			$trans_data['prt_sgst_amt']		= $post_data['prt_sgst_amt'][$key];
			$trans_data['prt_cgst_per']		= $post_data['prt_cgst_per'][$key];
			$trans_data['prt_cgst_amt']		= $post_data['prt_cgst_amt'][$key];
			$trans_data['prt_igst_per']		= $post_data['prt_igst_per'][$key];
			$trans_data['prt_igst_amt']		= $post_data['prt_igst_amt'][$key];

			$trans_data['prt_sub_total']	= $post_data['prt_sub_total'][$key];

			if($value == 0){
				$prt_id = $this->db_operations->data_insert($this->trans, $trans_data);
				if($prt_id < 1) return 0;
			}
		}
		return 1;
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['prm_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not found']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['prm_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => ' Already deleted']);
			return;	
		}
		$isExist = $this->model->isExist($id);
		if($isExist){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Already exist.']);
			return;	
		}
		$this->db->trans_begin();
		if($this->db_operations->delete_record($this->trans, ['prt_prm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->master, ['prm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not deleted']);
			return;
		}
		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Rollback']);
			return;
	    }
	    $this->db->trans_commit();
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [], 'msg' => 'Deleted successfully']);
	}
	public function get_select2_entry_no(){
		$json = [];
		$data = $this->model->get_select2_entry_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_acc_id(){
		$json = [];
		$data = $this->model->get_select2_acc_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}

}
?>
