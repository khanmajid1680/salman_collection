<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_Return extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'sales_return_master';
		$this->trans 			= 'sales_return_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('sales/SalesReturnmdl', 'model');
		$this->load->model('purchase/Purchasemdl');
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
					$config['base_url'] 	= base_url("sales_return?search=true");

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
					
					$this->load->view('pages/sales/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$this->load->view('pages/sales/sales_return_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$this->load->view('pages/sales/sales_return_form', $record);	
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						$this->load->view('pdfs/sales_return_pdf', $record);
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
		$master_data['srm_entry_no']		= trim($post_data['srm_entry_no']);
		$master_data['srm_entry_date'] 		= date('Y-m-d',strtotime($post_data['srm_entry_date']));	
		$master_data['srm_acc_id'] 			= trim($post_data['srm_acc_id']);
		$master_data['srm_total_qty']		= trim($post_data['srm_total_qty']);
		$master_data['srm_sub_total']		= trim($post_data['srm_sub_total']);
		$master_data['srm_total_disc']		= trim($post_data['srm_total_disc']);
		$master_data['srm_round_off']		= trim($post_data['srm_round_off']);
		$master_data['srm_bill_disc']		= trim($post_data['srm_bill_disc']);
		$master_data['srm_taxable_amt']		= trim($post_data['srm_taxable_amt']);
		$master_data['srm_sgst_amt']		= trim($post_data['srm_sgst_amt']);
		$master_data['srm_cgst_amt']		= trim($post_data['srm_cgst_amt']);
		$master_data['srm_igst_amt']		= trim($post_data['srm_igst_amt']);

		$master_data['srm_final_amt']		= trim($post_data['srm_final_amt']);
		$master_data['srm_amt_paid']		= trim($post_data['srm_amt_paid']);
		$master_data['srm_notes']			= trim($post_data['srm_notes']);
		$master_data['srm_updated_by'] 		= $_SESSION['user_id'];				
		$temp = $this->model->get_record(['srm_id !=' => $id,'srm_entry_no' => $master_data['srm_entry_no'],'srm_fin_year' => $_SESSION['fin_year'],'srm_branch_id' => $_SESSION['user_branch_id']]);
		if(!empty($temp)){
			$master_data['srm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'srm_entry_no', 'srm_fin_year', $_SESSION['fin_year'], 'srm_branch_id', $_SESSION['user_branch_id']);
		}
		if($id == 0){
			$this->db->trans_begin();
			$master_data['srm_created_by'] 	= $_SESSION['user_id'];
			$master_data['srm_created_at'] 	= date('Y-m-d H:i:s');
			$master_data['srm_fin_year'] 	= $_SESSION['fin_year'];
			$master_data['srm_branch_id'] 	= $_SESSION['user_branch_id'];
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
			$prev_data = $this->model->get_record(['srm_id' => $id]);
			if(empty($prev_data)){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Record not found.']);
				return;
			}
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'srm_id', $id) < 1){
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
		$trans_db_data = $this->db_operations->get_record($this->trans, ['srt_srm_id' => $id]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['srt_id'], $post_data['srt_id'])){
					if($this->db_operations->delete_record($this->trans,array('srt_id' =>$value['srt_id'])) < 1){
						return 0;
					}
				} 
			}
		}
		foreach ($post_data['srt_id'] as $key => $value){
			$trans_data['srt_srm_id'] 		= $id;
			$trans_data['srt_sm_id'] 	 	= $post_data['srt_sm_id'][$key];
			$trans_data['srt_st_id'] 	 	= $post_data['srt_st_id'][$key];
			$trans_data['srt_bm_id'] 	 	= $post_data['srt_bm_id'][$key];
			$trans_data['srt_user_id'] 	 	= $post_data['srt_user_id'][$key];
			$trans_data['srt_bill_no'] 	 	= $post_data['srt_bill_no'][$key];
			$trans_data['srt_bill_date'] 	= date('Y-m-d', strtotime($post_data['srt_bill_date'][$key]));
			$trans_data['srt_style_id'] 	= $post_data['srt_style_id'][$key];
			$trans_data['srt_brand_id'] 	= $post_data['srt_brand_id'][$key];
			$trans_data['srt_qty'] 			= $post_data['srt_qty'][$key];
			$trans_data['srt_rate']			= $post_data['srt_rate'][$key];
			$trans_data['srt_sub_total']	= $post_data['srt_sub_total'][$key];
			$trans_data['srt_disc_amt']		= $post_data['srt_disc_amt'][$key];
			$trans_data['srt_taxable_amt']	= $post_data['srt_taxable_amt'][$key];
			$trans_data['srt_sgst_per']		= $post_data['srt_sgst_per'][$key];
			$trans_data['srt_sgst_amt']		= $post_data['srt_sgst_amt'][$key];
			$trans_data['srt_cgst_per']		= $post_data['srt_cgst_per'][$key];
			$trans_data['srt_cgst_amt']		= $post_data['srt_cgst_amt'][$key];
			$trans_data['srt_igst_per']		= $post_data['srt_igst_per'][$key];
			$trans_data['srt_igst_amt']		= $post_data['srt_igst_amt'][$key];
			
			$trans_data['srt_total_amt']	= $post_data['srt_total_amt'][$key];
			$trans_data['srt_pt_rate']		= $this->Purchasemdl->get_purchase_rate($post_data['srt_bm_id'][$key]);

			if($value == 0){
				$srt_id = $this->db_operations->data_insert($this->trans, $trans_data);
				if($srt_id < 1) return 0;
			}
		}
		return 1;
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['srm_id' => $id]);
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
		$data = $this->model->get_record(['srm_id' => $id]);
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
		if($this->db_operations->delete_record($this->trans, ['srt_srm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->master, ['srm_id' => $id]) < 1){
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
	public function get_select2_user_id(){
		$json = [];
		$data = $this->model->get_select2_user_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_bm_id(){
		$json = [];
		$data = $this->model->get_select2_bm_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_style_id(){
		$json = [];
		$data = $this->model->get_select2_style_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_gender_id(){
		$json = [];
		$data = $this->model->get_select2_gender_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_brand_id(){
		$json = [];
		$data = $this->model->get_select2_brand_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_age_id(){
		$json = [];
		$data = $this->model->get_select2_age_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
