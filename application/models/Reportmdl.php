<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Reportmdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Usermdl');
			$this->load->model('purchase/Purchasemdl');
			$this->load->model('purchase/PurchaseReturnmdl');
			$this->load->model('sales/SalesReturnmdl');
			$this->load->model('sales/Salesmdl');
			$this->load->model('master/Barcodemdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Brandmdl');
			$this->load->model('voucher/Vouchermdl');
			$this->load->model('voucher/Paymentmdl');
		}
		/****************** BALANCE SHEET *********************/
			public function get_assets(){
				$record = [];
				$extra1 = '';
				$extra2 = '';
				$zero_check  = (isset($_GET['zero_check'])) ? $_GET['zero_check'] : "";
				$date_start = date('2017-04-01');
    			$date_end 	= date('Y-m-t');
				if((!empty($_GET['date_start'])) && (!empty($_GET['date_end']))){
					$date_start = date("Y-m-d", strtotime($_GET['date_start']));
					$date_end = date("Y-m-d", strtotime($_GET['date_end']));
					
					$extra1 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
				}else{
					$extra1 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
				}
				$query = "
							SELECT account_id, account_open_bal, account_group_id, account_name
							FROM account_master 
							WHERE account_group_id IN(SELECT grp_id FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
							ORDER BY account_name
						";
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				foreach ($data as $key => $value) {
					$acc_cr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
						FROM account_master 
						WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
						AND account_drcr = 'CR' 
						AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];

					// echo "<pre>"; print_r($acc_cr_op_amt);

					$acc_dr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
						FROM account_master 
						WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
						AND account_drcr = 'DR' 
						AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
					// echo "<pre>"; print_r($acc_dr_op_amt);

					if($value['account_group_id'] == 9){
						$voucher_cr_op_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_type = 'PAYMENT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];
						// echo "<pre>"; print_r($voucher_cr_op_amt);

						$voucher_dr_op_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_type = 'RECEIPT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];
						// echo "<pre>"; print_r($voucher_dr_op_amt);

						$voucher_cr_clo_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_type = 'PAYMENT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];
						// echo "<pre>"; print_r($voucher_cr_clo_amt);

						$voucher_dr_clo_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_type = 'RECEIPT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];
						// echo "<pre>"; print_r($voucher_dr_clo_amt);
					}else{
						$voucher_cr_op_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
							AND vm.vm_type = 'PAYMENT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];

						$voucher_dr_op_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
							AND vm.vm_type = 'RECEIPT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];

						$voucher_cr_clo_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
							AND vm.vm_type = 'PAYMENT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];

						$voucher_dr_clo_amt = $this->db->query("
							SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
							FROM voucher_master vm 
							WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 1 ORDER BY grp_id) 
							AND vm.vm_type = 'RECEIPT' 
							AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];
					}

					if($acc_cr_op_amt > 0){
						$total_op_amt = (0 - $acc_cr_op_amt);

						$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

						$total_bal = $total_clo_amt;

						if($total_bal >= 0){
							$total_bal = abs($total_bal);//."  TO RECEIVE";
							$bal_mode = "RECEIVE";
						}else{
							$total_bal = abs($total_bal);//."  TO PAY";
							$bal_mode = "PAY";
						}
					}else if($acc_dr_op_amt > 0){
						$total_op_amt = ($acc_dr_op_amt);
						$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

						$total_bal = $total_clo_amt;

						if($total_bal >= 0){
							$total_bal = ($total_bal);
							$bal_mode = "RECEIVE";        				
						}else{
							$total_bal = ($total_bal);
							$bal_mode = "PAY";
						}
					}else{
							$total_op_amt = 0;

							$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

							$total_bal = $total_clo_amt;

							if($total_bal >= 0){
								$total_bal = ($total_bal);
								$bal_mode = "RECEIVE";        				
							}else{
								$total_bal = ($total_bal);
								$bal_mode = "PAY";
							}
					}
					$record['asset_details'][$key]['account_name'] 			= $value['account_name'];
					$record['asset_details'][$key]['bal_mode'] 				= $bal_mode;
					$record['asset_details'][$key]['total_bal_in_number'] 	= $total_clo_amt;
					$record['asset_details'][$key]['total_bal'] 			= $total_bal;
					$record['asset_details'][$key]['zero_check'] 			= $zero_check;

					$record['asset_details'][$key]['acc_cr_op_amt'] 		= $acc_cr_op_amt;
					$record['asset_details'][$key]['acc_dr_op_amt'] 		= $acc_dr_op_amt;
					$record['asset_details'][$key]['voucher_cr_op_amt'] 	= $voucher_cr_op_amt;
					$record['asset_details'][$key]['voucher_dr_op_amt'] 	= $voucher_dr_op_amt;
					$record['asset_details'][$key]['voucher_cr_clo_amt'] 	= $voucher_cr_clo_amt;
					$record['asset_details'][$key]['voucher_dr_clo_amt'] 	= $voucher_dr_clo_amt;
				}
				// echo "<pre>"; print_r($record);exit;
				return $record;
			}
			public function get_liabilities(){
				$record = [];
				$extra1 = '';
				$extra2 = '';
				$zero_check  = (isset($_GET['zero_check'])) ? $_GET['zero_check'] : "";
				$date_start = date('2017-04-01');
    			$date_end 	= date('Y-m-t');
				if((!empty($_GET['date_start'])) && (!empty($_GET['date_end']))){
					$date_start = date("Y-m-d", strtotime($_GET['date_start']));
					$date_end = date("Y-m-d", strtotime($_GET['date_end']));
					
					$extra1 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
				}else{
					$extra1 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
				}
				$query = "
					SELECT account_id, account_open_bal, account_group_id, account_name
					FROM account_master 
					WHERE account_group_id IN(SELECT grp_id FROM group_master WHERE grp_belong = 2 ORDER BY grp_id) 
					ORDER BY account_name";
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				foreach ($data as $key => $value) {
					$acc_cr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
						FROM account_master 
						WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 2 ORDER BY grp_id) 
						AND account_drcr = 'CR' 
						AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];

					// echo "<pre>"; print_r($acc_cr_op_amt);exit;

					$acc_dr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
						FROM account_master 
						WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 2 ORDER BY grp_id) 
						AND account_drcr = 'DR' 
						AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
					// echo "<pre>"; print_r($acc_dr_op_amt);exit;
					$voucher_cr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
						FROM voucher_master vm 
						WHERE vm.vm_type = 'PAYMENT' 
						AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];
					// echo "<pre>"; print_r($voucher_cr_op_amt);exit;

					$voucher_dr_op_amt = $this->db->query("
						SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
						FROM voucher_master vm 
						WHERE vm.vm_type = 'RECEIPT' 
						AND vm.vm_acc_id = '".$value['account_id']."' $extra2")->result_array()[0]['vm_final_total'];
					// echo "<pre>"; print_r($voucher_dr_op_amt);exit;

					$voucher_cr_clo_amt = $this->db->query("
						SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
						FROM voucher_master vm 
						WHERE vm.vm_type = 'PAYMENT' 
						AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];
					// echo "<pre>"; print_r($voucher_cr_clo_amt);exit;

					$voucher_dr_clo_amt = $this->db->query("
						SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
						FROM voucher_master vm 
						WHERE vm.vm_type = 'RECEIPT' 
						AND vm.vm_acc_id = '".$value['account_id']."' $extra1")->result_array()[0]['vm_final_total'];
					// echo "<pre>"; print_r($voucher_dr_clo_amt);exit;

					if($acc_cr_op_amt > 0){
						$total_op_amt = ($acc_cr_op_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = ($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				$total_bal = ($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
					}else if($acc_dr_op_amt > 0){
						$total_op_amt = (0 - $acc_dr_op_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = ($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				$total_bal = ($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
					}else{
						$total_op_amt = 0;

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = ($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				$total_bal = ($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
					}
					$record['liabilities_details'][$key]['account_name'] 		= $value['account_name'];
					$record['liabilities_details'][$key]['account_group_id'] 	= $value['account_group_id'];
					$record['liabilities_details'][$key]['bal_mode'] 			= $bal_mode;
					$record['liabilities_details'][$key]['total_bal_in_number'] = $total_clo_amt;
					$record['liabilities_details'][$key]['total_bal'] 			= $total_bal;
					$record['liabilities_details'][$key]['zero_check'] 			= $zero_check;
				}
				// echo "<pre>"; print_r($record);exit;
				return $record;
			}
			public function get_income(){
				$date_start = date('2017-04-01');
		    	$date_end 	= date('Y-m-t');
				

				$zero_check  = (isset($_GET['zero_check'])) ? $_GET['zero_check'] : "";
				$extra1 = "";
				$extra2 = "";
				$extra3 = "";
				$extra4 = "";
				$extra5 = "";
				$extra6 = "";

				if((!empty($_GET['date_start'])) && (!empty($_GET['date_end']))){
					$date_start = date("Y-m-d", strtotime($_GET['date_start']));
					$date_end = date("Y-m-d", strtotime($_GET['date_end']));

					$extra1 .= " AND (sm.sm_bill_date BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (srm.srm_entry_date BETWEEN '$date_start' AND '$date_end') ";
					$extra3 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";

					$extra4 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
					$extra5 .= " AND (DATE(sm.sm_bill_date) < '$date_start')";
					$extra6 .= " AND (DATE(srm.srm_entry_date) < '$date_start')";
				}else{
					$extra1 .= " AND (sm.sm_bill_date BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (srm.srm_entry_date BETWEEN '$date_start' AND '$date_end')";
					$extra3 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";

					$extra4 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";
					$extra5 .= " AND (DATE(sm.sm_bill_date) < '$date_start')";
					$extra6 .= " AND (DATE(srm.srm_entry_date) < '$date_start')";
				}

		    	$query = "
		    				SELECT account_id, account_name
		    				FROM account_master 
		    				WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    				ORDER BY account_name";
		    	

		    	$data = $this->db->query($query)->result_array();
		    	// echo "<pre>"; print_r($data);exit();
		    	foreach ($data as $key => $value){
		    		$acc_cr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
		    			FROM account_master 
		    			WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND account_drcr = 'CR' 
		    			AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
		    		// echo "<pre>"; print_r($acc_cr_op_amt);exit();

		    		$acc_dr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(account_open_bal),0) as account_open_bal 
		    			FROM account_master 
		    			WHERE account_group_id IN (SELECT grp_id FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND account_drcr = 'DR' 
		    			AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
		    		// echo "<pre>"; print_r($acc_dr_op_amt);exit();

		    		$sale_amt = $this->db->query("
		    			SELECT IFNULL(SUM(sm_final_amt),0) as sm_final_amt 
		    			FROM sales_master sm 
		    			WHERE sm.sm_acc_id='".$value['account_id']."' $extra1")->result_array()[0]['sm_final_amt'];
		    		// echo "<pre>"; print_r($sale_amt);exit();

		    		$sale_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(sm_final_amt),0) as sm_final_amt 
		    			FROM sales_master sm 
		    			WHERE sm.sm_acc_id='".$value['account_id']."' $extra5")->result_array()[0]['sm_final_amt'];
		    		// echo "<pre>"; print_r($sale_clo_amt);exit();

		    		$return_amt = $this->db->query("
		    			SELECT IFNULL(SUM(srm_final_amt),0) as srm_final_amt 
		    			FROM sales_return_master srm 
		    			WHERE srm.srm_acc_id='".$value['account_id']."' $extra2")->result_array()[0]['srm_final_amt'];
		    		// echo "<pre>"; print_r($return_amt);exit();

		    		$return_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(srm_final_amt),0) as srm_final_amt 
		    			FROM sales_return_master srm 
		    			WHERE srm.srm_acc_id='".$value['account_id']."' $extra6")->result_array()[0]['srm_final_amt'];
		    		// echo "<pre>"; print_r($return_clo_amt);exit();

		    		$voucher_cr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND vm.vm_type = 'PAYMENT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra4")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_cr_op_amt);exit();

		    		$voucher_dr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND vm.vm_type = 'RECEIPT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra4")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_dr_op_amt);exit();

		    		$voucher_cr_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_group IN (SELECT grp_name FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND vm.vm_type = 'PAYMENT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra3")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_cr_clo_amt);exit();

		    		$voucher_dr_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_group IN (SELECT grp_id FROM group_master WHERE grp_belong = 3 ORDER BY grp_id) 
		    			AND vm.vm_type = 'RECEIPT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra3")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_dr_clo_amt);exit();
		    	
		    		if($acc_cr_op_amt > 0){
		    			$total_op_amt = (0 - $acc_cr_op_amt) + ($sale_amt - $return_amt) + ($sale_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = abs($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}else{
		    				$total_bal = abs($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}
		    			
		    		}else if($acc_dr_op_amt > 0){
		    			$total_op_amt = ($acc_dr_op_amt) + ($sale_amt - $return_amt) + ($sale_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = ($total_bal);
		    				$bal_mode = "RECEIVE";        				
		    			}else{
		    				$total_bal = ($total_bal);
		    				$bal_mode = "PAY";
		    			}
		    		}else{
		    			
		    			$total_op_amt = ($sale_amt - $return_amt) + ($sale_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_dr_op_amt - $voucher_cr_op_amt) - ($voucher_dr_clo_amt - $voucher_cr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = ($total_bal);
		    				$bal_mode = "RECEIVE";        				
		    			}else{
		    				$total_bal = ($total_bal);
		    				$bal_mode = "PAY";
		    			}
		    		}

		    		$record['customer_details'][$key]['account_name'] 		= $value['account_name'];
		    		$record['customer_details'][$key]['bal_mode'] 			= $bal_mode;
		    		$record['customer_details'][$key]['total_bal_in_number']= $total_clo_amt;
		    		$record['customer_details'][$key]['total_bal'] 			= $total_bal;
		    		$record['customer_details'][$key]['zero_check'] 		= $zero_check;
		    		
		    		$record['customer_dates'] = array('date_start'=>$date_start,'date_end'=>$date_end);
		    	}
		       	// echo "<pre>"; print_r($record);exit;
				return $record;
			}
			public function get_expense(){
				$date_start = date('2017-04-01');
		    	$date_end = date('Y-m-t');

				$zero_check  = (isset($_GET['zero_check'])) ? $_GET['zero_check'] : "";;
				$extra1 = "";
				$extra2 = "";
				$extra3 = "";
				$extra4 = "";
				$extra5 = "";
				$extra6 = "";

				if((!empty($_GET['date_start'])) && (!empty($_GET['date_end'])))
				{
					$date_start = date("Y-m-d", strtotime($_GET['date_start']));
					$date_end = date("Y-m-d", strtotime($_GET['date_end']));

					$extra1 .= " AND (pm.pm_bill_date BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (prm.prm_entry_date BETWEEN '$date_start' AND '$date_end') ";
					$extra3 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";

					$extra4 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";

					$extra5 .= " AND (DATE(pm.pm_bill_date) < '$date_start')";
					$extra6 .= " AND (DATE(prm.prm_entry_date) < '$date_start')";
				}
				else
				{
					$extra1 .= " AND (pm.pm_bill_date BETWEEN '$date_start' AND '$date_end')";
					$extra2 .= " AND (prm.prm_entry_date BETWEEN '$date_start' AND '$date_end')";
					$extra3 .= " AND (DATE(vm.vm_entry_date) BETWEEN '$date_start' AND '$date_end')";

					$extra4 .= " AND (DATE(vm.vm_entry_date) < '$date_start')";

					$extra5 .= " AND (DATE(pm.pm_bill_date) < '$date_start')";
					$extra6 .= " AND (DATE(prm.prm_entry_date) < '$date_start')";
				}
				
				$query = "
							SELECT account_id, account_name, account_group_id
							FROM account_master 
							WHERE account_group_id IN(SELECT grp_id FROM group_master WHERE grp_belong = 4 ORDER BY grp_id) 
							ORDER BY account_name";

		    	$data = $this->db->query($query)->result_array();
		    	// echo "<pre>"; print_r($data); exit();
		    	foreach ($data as $key => $value) {
		    		$acc_cr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(account_open_bal),0) as  account_open_bal 
		    			FROM account_master 
		    			WHERE account_group_id IN(SELECT grp_id FROM group_master WHERE grp_belong = 4 ORDER BY grp_id) 
		    			AND account_drcr = 'CR' 
		    			AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
		    		// echo "<pre>"; print_r($acc_cr_op_amt); exit();

		    		$acc_dr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(account_open_bal),0) as account_open_bal 
		    			FROM account_master 
		    			WHERE account_group_id IN(SELECT grp_id FROM group_master WHERE grp_belong = 4 ORDER BY grp_id) 
		    			AND account_drcr = 'DR' 
		    			AND account_id = '".$value['account_id']."'")->result_array()[0]['account_open_bal'];
		    		// echo "<pre>"; print_r($acc_dr_op_amt); exit();

		    		$pur_amt = $this->db->query("
		    			SELECT IFNULL(SUM(pm_final_amt),0) as pm_final_amt 
		    			FROM purchase_master pm 
		    			WHERE pm.pm_acc_id='".$value['account_id']."' $extra1")->result_array()[0]['pm_final_amt'];
		    		// echo "<pre>"; print_r($pur_amt); exit();

		    		$pur_clo_amt = $this->db->query("
		    			SELECT SUM(pm_final_amt) as pm_final_amt 
		    			FROM purchase_master pm 
		    			WHERE pm.pm_acc_id='".$value['account_id']."' $extra5")->result_array()[0]['pm_final_amt'];
		    		// echo "<pre>"; print_r($pur_clo_amt); exit();

		    		$return_amt = $this->db->query("
		    			SELECT IFNULL(SUM(prm_final_amt),0) as prm_final_amt 
		    			FROM purchase_return_master prm 
		    			WHERE prm.prm_acc_id='".$value['account_id']."' $extra2")->result_array()[0]['prm_final_amt'];
		    		// echo "<pre>"; print_r($return_amt); exit();

		    		$return_clo_amt = $this->db->query("
		    			SELECT SUM(prm_final_amt) as prm_final_amt 
		    			FROM purchase_return_master prm 
		    			WHERE prm.prm_acc_id='".$value['account_id']."' $extra6")->result_array()[0]['prm_final_amt'];
		    		// echo "<pre>"; print_r($return_clo_amt); exit();

		    		$voucher_cr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_type = 'PAYMENT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra3")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_cr_op_amt); exit();

		    		$voucher_dr_op_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_type = 'RECEIPT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra3")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_dr_op_amt); exit();

		    		$voucher_cr_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_type = 'PAYMENT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra4")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_cr_clo_amt); exit();

		    		$voucher_dr_clo_amt = $this->db->query("
		    			SELECT IFNULL(SUM(vm.vm_total_amt+vm.vm_round_off),0) as vm_final_total 
		    			FROM voucher_master vm 
		    			WHERE vm.vm_type = 'RECEIPT' 
		    			AND vm.vm_acc_id = '".$value['account_id']."' $extra4")->result_array()[0]['vm_final_total'];
		    		// echo "<pre>"; print_r($voucher_dr_clo_amt); exit();
		    		if($acc_cr_op_amt > 0){
		    			$total_op_amt = ($acc_cr_op_amt) + ($pur_amt - $return_amt) + ($pur_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = abs($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				$total_bal = abs($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
		    		}else if($acc_dr_op_amt > 0){
		    			$total_op_amt = (0 - $acc_dr_op_amt) + ($pur_amt - $return_amt) + ($pur_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = abs($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				//$total_bal = abs($total_bal)."  TO RECEIVE";
		    				$total_bal = abs($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
		    		}else{
		    			$total_op_amt = ($pur_amt - $return_amt) + ($pur_clo_amt - $return_clo_amt);

		    			$total_clo_amt = ($total_op_amt) - ($voucher_cr_op_amt - $voucher_dr_op_amt) - ($voucher_cr_clo_amt - $voucher_dr_clo_amt);

		    			$total_bal = $total_clo_amt;

		    			if($total_bal >= 0){
		    				$total_bal = abs($total_bal);//."  TO PAY";
		    				$bal_mode = "PAY";
		    			}else{
		    				$total_bal = abs($total_bal);//."  TO RECEIVE";
		    				$bal_mode = "RECEIVE";
		    			}
		    		}
		    		

		    		$record['supplier_details'][$key]['account_name'] 		= $value['account_name'];
		    		$record['supplier_details'][$key]['account_group_id'] 	= $value['account_group_id'];
		    		$record['supplier_details'][$key]['bal_mode'] 			= $bal_mode;
		    		$record['supplier_details'][$key]['total_bal_in_number']= $total_clo_amt;
		    		$record['supplier_details'][$key]['total_bal'] 			= $total_bal;
		    		$record['supplier_details'][$key]['zero_check'] 		= $zero_check;
		    	}
		       	// echo "<pre>"; print_r($record);exit;
				return $record;
			}
			public function get_balance_sheet(){
				$record['asset'] 		= $this->get_assets();
	        	$record['liabilities'] 	= $this->get_liabilities();
	        	$record['customer'] 	= $this->get_income();
	        	$record['supplier'] 	= $this->get_expense();
				$total_pay_bal	 		= 0;
	            $total_receive_bal 		= 0;
	            $total_receive_bal1 	= 0;
	            foreach ($record['supplier']['supplier_details'] as $key => $value){
	                if($value['bal_mode'] == 'PAY'){
	                    $total_pay_bal = $total_pay_bal + $value['total_bal'];
	                }elseif($value['bal_mode'] == 'RECEIVE'){
	                    if($value['account_group_id'] == 6){
	                        $total_receive_bal1 = $total_receive_bal1 + $value['total_bal'];    
	                    }else{
	                        $total_receive_bal = $total_receive_bal + $value['total_bal'];    
	                    }
	                }

	                $total_bal_amt = abs($total_pay_bal) + abs($total_receive_bal) - abs($total_receive_bal1);
	            }
	            $total_pay_bal_cust = 0;
	            $total_receive_bal_cust = 0;
	            foreach ($record['customer']['customer_details'] as $key => $value) {
	                if($value['bal_mode'] == 'PAY'){
	                    $total_pay_bal_cust = $total_pay_bal_cust + $value['total_bal'];
	                }elseif($value['bal_mode'] == 'RECEIVE'){
	                    $total_receive_bal_cust = $total_receive_bal_cust + $value['total_bal'];
	                }
	                $total_bal_amt_cust = abs($total_receive_bal_cust) - abs($total_pay_bal_cust);
	            }
	            $record['profit_loss']['total_expense_head'] = $total_bal_amt;
	        	$record['profit_loss']['total_income_head'] = $total_bal_amt_cust;				
				// echo "<pre>"; print_r($record);exit;
				return $record;
			}
		/****************** BALANCE SHEET *********************/
		/****************** BALANCE STOCK *********************/
			public function get_balance_stock(){
				$record = [];
				$subsql = "";
				$having = "";
				if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
					$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
					$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
				}
				if(isset($_GET['bm_id']) && !empty($_GET['bm_id'])){
					$subsql .=" AND bm.bm_id = ".$_GET['bm_id'];
					$record['search']['bm_id'] = $this->Barcodemdl->get_search(['bm_id' => $_GET['bm_id']]);
				}
				if(isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id'])){
					$subsql .=" AND bm.bm_style_id = ".$_GET['bm_style_id'];
					$record['search']['bm_style_id'] = $this->Stylemdl->get_search(['style_id' => $_GET['bm_style_id']]);
				}
				if(isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id'])){
					$subsql .=" AND bm.bm_design_id = ".$_GET['bm_design_id'];
					$record['search']['bm_design_id'] = $this->designmdl->get_search(['design_id' => $_GET['bm_design_id']]);
				}
				if(isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id'])){
					$subsql .=" AND bm.bm_brand_id = ".$_GET['bm_brand_id'];
					$record['search']['bm_brand_id'] = $this->Brandmdl->get_search(['brand_id' => $_GET['bm_brand_id']]);
				}
				if(isset($_GET['bm_age_id']) && !empty($_GET['bm_age_id'])){
					$subsql .=" AND bm.bm_age_id = ".$_GET['bm_age_id'];
					$record['search']['bm_age_id'] = $this->Agemdl->get_search(['age_id' => $_GET['bm_age_id']]);
				}
				if(isset($_GET['pt_amt_frm'])){
					if($_GET['pt_amt_frm'] != ''){
						$having .=" AND pt_amt >= ".$_GET['pt_amt_frm'];
					}
				}
				if(isset($_GET['pt_amt_to'])){
					if($_GET['pt_amt_to'] != ''){
						$having .=" AND pt_amt <= ".$_GET['pt_amt_to'];
					}
				}
				if(isset($_GET['st_amt_frm'])){
					if($_GET['st_amt_frm'] != ''){
						$having .=" AND st_amt >= ".$_GET['st_amt_frm'];
					}
				}
				if(isset($_GET['st_amt_to'])){
					if($_GET['st_amt_to'] != ''){
						$having .=" AND st_amt <= ".$_GET['st_amt_to'];
					}
				}
				if(isset($_GET['sold_amt_frm'])){
					if($_GET['sold_amt_frm'] != ''){
						$having .=" AND sold_amt >= ".$_GET['sold_amt_frm'];
					}
				}
				if(isset($_GET['sold_amt_to'])){
					if($_GET['sold_amt_to'] != ''){
						$having .=" AND sold_amt <= ".$_GET['sold_amt_to'];
					}
				}
				if(isset($_GET['bal_qty_frm'])){
					if($_GET['bal_qty_frm'] != ''){
						$having .=" AND bal_qty >= ".$_GET['bal_qty_frm'];
					}
				}else{
					$having .=" AND bal_qty >= 1";
				}
				if(isset($_GET['bal_qty_to'])){
					if($_GET['bal_qty_to'] != ''){
						$having .=" AND bal_qty <= ".$_GET['bal_qty_to'];
					}
				}
				if(isset($_GET['bal_amt_frm'])){
					if($_GET['bal_amt_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_amt_frm'];
					}
				}
				if(isset($_GET['bal_amt_to'])){
					if($_GET['bal_amt_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_amt_to'];
					}
				}
				$query 	="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, 
							UPPER(style.style_name) as style_name, 
							UPPER(design.design_name) as design_name,
							UPPER(brand.brand_name) as brand_name,
							UPPER(age.age_name) as age_name,
							SUM(bm.bm_pt_qty) as pt_qty, (bm.bm_pt_rate - bm.bm_pt_disc) as pt_rate, 
							SUM(bm.bm_pt_qty * (bm.bm_pt_rate - bm.bm_pt_disc)) as pt_amt, 
							SUM(bm.bm_prt_qty) as prt_qty, 
							SUM(bm.bm_st_qty) as st_qty, (bm.bm_sp_amt - bm.bm_st_disc) as st_rate, 
							SUM(bm.bm_st_qty * bm.bm_sp_amt) as st_amt,  
							SUM(bm.bm_srt_qty) as srt_qty,
							((bm.bm_pt_rate - bm.bm_pt_disc) * SUM(bm.bm_st_qty)) as sold_amt, 
							((SUM(bm.bm_pt_qty) + SUM(bm.bm_srt_qty)) - (SUM(bm.bm_st_qty) + SUM(bm.bm_prt_qty))) as bal_qty,
							(((SUM(bm.bm_pt_qty) + SUM(bm.bm_srt_qty)) - (SUM(bm.bm_st_qty) + SUM(bm.bm_prt_qty))) * (bm.bm_pt_rate - bm.bm_pt_disc)) as bal_amt
							FROM barcode_master bm
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
							INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
							INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
							LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
							WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND bm.bm_fin_year = '".$_SESSION['fin_year']."' 
							AND bm.bm_delete_status = 0
							$subsql
							GROUP BY acc.account_id, style.style_id, design.design_id, brand.brand_id, age.age_id, bm.bm_pt_rate, bm.bm_pt_disc ASC
							HAVING 1
							$having
						 ";
				$record['data'] = $this->db->query($query)->result_array();
				$pt_qty  		= 0;
				$pt_amt  		= 0;
				$prt_qty  		= 0;
				$st_qty  		= 0;
				$st_amt  		= 0;
				$srt_qty   		= 0;
				$sold_amt  		= 0;
				$bal_qty  		= 0;
				$bal_amt  		= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$pt_qty 		= $pt_qty + $value['pt_qty'];
						$pt_amt 		= $pt_amt + $value['pt_amt'];
						$prt_qty 		= $prt_qty + $value['prt_qty'];
						$st_qty 		= $st_qty + $value['st_qty'];
						$st_amt 		= $st_amt + $value['st_amt'];
						$srt_qty 		= $srt_qty + $value['srt_qty'];
						$sold_amt 		= $sold_amt + $value['sold_amt'];
						$bal_qty 		= $bal_qty + $value['bal_qty'];
						$bal_amt 		= $bal_amt + $value['bal_amt'];
					}
				}
				$record['totals']['pt_qty'] 		= $pt_qty;
				$record['totals']['pt_amt'] 		= $pt_amt;
				$record['totals']['prt_qty'] 		= $prt_qty;
				$record['totals']['st_qty'] 		= $st_qty;
				$record['totals']['st_amt'] 		= $st_amt;
				$record['totals']['srt_qty'] 		= $srt_qty;
				$record['totals']['sold_amt'] 		= $sold_amt;
				$record['totals']['bal_qty'] 		= $bal_qty;
				$record['totals']['bal_amt'] 		= $bal_amt;

				if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'EXCEL'){
					return $this->get_balance_stock_excel($record);
				}
				return $record;
			}
			public function get_balance_stock_excel($record){
				// echo "<pre>"; print_r($record); exit();
				$excel_array[0] = array(
	                0 =>  '#',
	                1 =>  'SUPPLIER',
	                2 =>  'STYLE',
	                3 =>  'design',
	                4 =>  'PURCHASE QTY',
	                5 =>  'PURCHASE RATE',
	                6 =>  'PURCHASE AMT',
	                7 =>  'PURCHASE RETURN QTY',
	                8 =>  'SALE QTY',
	                9 =>  'SALE RATE',
	                10 => 'SALE RETURN QTY',
	                11 => 'SOLD QTY X PURCHASE RATE',
	                12 => 'BALANCE QTY',
	                13 => 'BALANCE STOCK',
	            );
	            $sr_no = 1;
	            foreach ($record['data'] as $key => $value){
	            	$excel_array[$sr_no][0] = $sr_no;
	                $excel_array[$sr_no][1] = $value['account_name'];
	                $excel_array[$sr_no][2] = $value['style_name'];
	                $excel_array[$sr_no][3] = $value['design_name'];
	                $excel_array[$sr_no][4] = $value['pt_qty'];
	                $excel_array[$sr_no][5] = $value['pt_rate'];
	                $excel_array[$sr_no][6] = $value['pt_amt'];
	                $excel_array[$sr_no][7] = $value['prt_qty'];
	                $excel_array[$sr_no][8] = $value['st_qty'];
	                $excel_array[$sr_no][9] = $value['st_rate'];
	                $excel_array[$sr_no][10]= $value['srt_qty'];
	                $excel_array[$sr_no][11]= $value['sold_amt'];
	                $excel_array[$sr_no][12]= $value['bal_qty'];
	                $excel_array[$sr_no][13]= $value['bal_amt'];
	                $sr_no++;                                  
	            }
	            return $excel_array;            
			}
		/****************** BALANCE STOCK *********************/
		/****************** BARCODE STOCK *********************/
			public function get_barcode_stock(){
				$record 	= [];
				$subsql 	= "";
				$having 	= "";
				$per_page 	= isset($_GET['per_page']) && !empty($_GET['per_page']) ? $_GET['per_page'] : PER_PAGE;
				$offset 	= isset($_GET['offset']) && !empty($_GET['offset']) ? $_GET['offset'] : OFFSET;
				$limit  	= " LIMIT $per_page";
				$ofset  	= " OFFSET $offset";
				if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
					$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
					$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
				}
				if(isset($_GET['bm_id']) && !empty($_GET['bm_id'])){
					$subsql .=" AND bm.bm_id = ".$_GET['bm_id'];
					$record['search']['bm_id'] = $this->Barcodemdl->get_search(['bm_id' => $_GET['bm_id']]);
				}
				if(isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id'])){
					$subsql .=" AND bm.bm_style_id = ".$_GET['bm_style_id'];
					$record['search']['bm_style_id'] = $this->Stylemdl->get_search(['style_id' => $_GET['bm_style_id']]);
				}
				if(isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id'])){
					$subsql .=" AND bm.bm_design_id = ".$_GET['bm_design_id'];
					$record['search']['bm_design_id'] = $this->designmdl->get_search(['design_id' => $_GET['bm_design_id']]);
				}
				if(isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id'])){
					$subsql .=" AND bm.bm_brand_id = ".$_GET['bm_brand_id'];
					$record['search']['bm_brand_id'] = $this->Brandmdl->get_search(['brand_id' => $_GET['bm_brand_id']]);
				}
				if(isset($_GET['bm_age_id']) && !empty($_GET['bm_age_id'])){
					$subsql .=" AND bm.bm_age_id = ".$_GET['bm_age_id'];
					$record['search']['bm_age_id'] = $this->Agemdl->get_search(['age_id' => $_GET['bm_age_id']]);
				}
				if(isset($_GET['pt_amt_frm'])){
					if($_GET['pt_amt_frm'] != ''){
						$having .=" AND pt_amt >= ".$_GET['pt_amt_frm'];
					}
				}
				if(isset($_GET['pt_amt_to'])){
					if($_GET['pt_amt_to'] != ''){
						$having .=" AND pt_amt <= ".$_GET['pt_amt_to'];
					}
				}
				if(isset($_GET['st_rate_frm'])){
					if($_GET['st_rate_frm'] != ''){
						$having .=" AND st_rate >= ".$_GET['st_rate_frm'];
					}
				}
				if(isset($_GET['st_rate_to'])){
					if($_GET['st_rate_to'] != ''){
						$having .=" AND st_rate <= ".$_GET['st_rate_to'];
					}
				}
				if(isset($_GET['st_amt_frm'])){
					if($_GET['st_amt_frm'] != ''){
						$having .=" AND st_amt >= ".$_GET['st_amt_frm'];
					}
				}
				if(isset($_GET['st_amt_to'])){
					if($_GET['st_amt_to'] != ''){
						$having .=" AND st_amt <= ".$_GET['st_amt_to'];
					}
				}
				if(isset($_GET['bal_qty_frm'])){
					if($_GET['bal_qty_frm'] != ''){
						$having .=" AND bal_qty >= ".$_GET['bal_qty_frm'];
					}
				}
				if(isset($_GET['bal_qty_to'])){
					if($_GET['bal_qty_to'] != ''){
						$having .=" AND bal_qty <= ".$_GET['bal_qty_to'];
					}
				}
				if(isset($_GET['bal_amt_frm'])){
					if($_GET['bal_amt_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_amt_frm'];
					}
				}
				if(isset($_GET['bal_amt_to'])){
					if($_GET['bal_amt_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_amt_to'];
					}
				}
				if(isset($_GET['profit_frm'])){
					if($_GET['profit_frm'] != ''){
						$having .=" AND profit_amt >= ".$_GET['profit_frm'];
					}
				}
				if(isset($_GET['profit_to'])){
					if($_GET['profit_to'] != ''){
						$having .=" AND profit_amt <= ".$_GET['profit_to'];
					}
				}
				$extra = empty($subsql) && empty($having) ? " AND bm.bm_id = 0" : "";
				$query 	="
							SELECT bm.bm_id, bm.bm_item_code, CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_code, 
							UPPER(style.style_name) as style_name, 
							UPPER(design.design_name) as design_name,
							UPPER(brand.brand_name) as brand_name,
							UPPER(age.age_name) as age_name,
							bm.bm_pt_qty as pt_qty, (bm.bm_pt_rate - bm.bm_pt_disc) as pt_rate, 
							(bm.bm_pt_qty * (bm.bm_pt_rate - bm.bm_pt_disc)) as pt_amt, 
							bm.bm_prt_qty as prt_qty, 
							bm.bm_st_qty as st_qty, (bm.bm_sp_amt - bm.bm_st_disc) as st_rate, 
							(bm.bm_st_qty * (bm.bm_st_rate - bm.bm_st_disc)) as st_amt,  
							bm.bm_srt_qty as srt_qty,
							((bm.bm_pt_qty + bm.bm_srt_qty) - (bm.bm_st_qty + bm.bm_prt_qty)) as bal_qty,
							(((bm.bm_pt_qty + bm.bm_srt_qty) - (bm.bm_st_qty + bm.bm_prt_qty)) * (bm.bm_pt_rate - bm.bm_pt_disc)) as bal_amt,
							((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_st_rate - bm.bm_st_disc)) - ((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_pt_rate - bm.bm_pt_disc)) as profit_amt
							FROM barcode_master bm
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
							INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
							INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
							LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
							WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND bm.bm_fin_year = '".$_SESSION['fin_year']."' 
							AND bm.bm_delete_status = 0
							$subsql
							$extra
							HAVING 1
							$having
							ORDER BY bm.bm_id DESC
						 ";
				// echo $query;exit;
				$record['data'] = $this->db->query($query)->result_array();
				$pt_qty  		= 0;
				$pt_amt  		= 0;
				$prt_qty  		= 0;
				$st_qty  		= 0;
				$st_amt  		= 0;
				$srt_qty   		= 0;
				$bal_qty  		= 0;
				$bal_amt  		= 0;
				$profit_amt  	= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$pt_qty 		= $pt_qty + $value['pt_qty'];
						$pt_amt 		= $pt_amt + $value['pt_amt'];
						$prt_qty 		= $prt_qty + $value['prt_qty'];
						$st_qty 		= $st_qty + $value['st_qty'];
						$st_amt 		= $st_amt + $value['st_amt'];
						$srt_qty 		= $srt_qty + $value['srt_qty'];
						$bal_qty 		= $bal_qty + $value['bal_qty'];
						$bal_amt 		= $bal_amt + $value['bal_amt'];
						$profit_amt		= $profit_amt + $value['profit_amt'];
					}
				}
				$record['totals']['pt_qty'] 		= $pt_qty;
				$record['totals']['pt_amt'] 		= $pt_amt;
				$record['totals']['prt_qty'] 		= $prt_qty;
				$record['totals']['st_qty'] 		= $st_qty;
				$record['totals']['st_amt'] 		= $st_amt;
				$record['totals']['srt_qty'] 		= $srt_qty;
				$record['totals']['bal_qty'] 		= $bal_qty;
				$record['totals']['bal_amt'] 		= $bal_amt;
				$record['totals']['profit_amt'] 	= $profit_amt;

				return $record;
			}
		/****************** BARCODE STOCK *********************/
		/****************** BEST PERSON ***********************/
			public function get_best_person(){
				$record 	= [];
				$subsql 	= "";
				$having 	= "";
				$per_page 	= isset($_GET['per_page']) && !empty($_GET['per_page']) ? $_GET['per_page'] : PER_PAGE;
				$offset 	= isset($_GET['offset']) && !empty($_GET['offset']) ? $_GET['offset'] : OFFSET;
				$limit  	= " LIMIT $per_page";
				$ofset  	= " OFFSET $offset";
				$from_date 	= $_SESSION['start_year'];
				$to_date 	= $_SESSION['end_year'];
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}else{
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}else{
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}
				if(isset($_GET['user_id']) && !empty($_GET['user_id'])){
					$subsql .=" AND sm.sm_user_id = ".$_GET['user_id'];
					$record['search']['user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['user_id']]);
				}
				if(isset($_GET['sm_qty_frm'])){
					if($_GET['sm_qty_frm'] != ''){
						$having .=" AND sm_qty >= ".$_GET['sm_qty_frm'];
					}
				}
				if(isset($_GET['sm_qty_to'])){
					if($_GET['sm_qty_to'] != ''){
						$having .=" AND sm_qty <= ".$_GET['sm_qty_to'];
					}
				}
				if(isset($_GET['srm_qty_frm'])){
					if($_GET['srm_qty_frm'] != ''){
						$having .=" AND srm_qty >= ".$_GET['srm_qty_frm'];
					}
				}
				if(isset($_GET['srm_qty_to'])){
					if($_GET['srm_qty_to'] != ''){
						$having .=" AND srm_qty <= ".$_GET['srm_qty_to'];
					}
				}
				if(isset($_GET['sale_qty_frm'])){
					if($_GET['sale_qty_frm'] != ''){
						$having .=" AND sale_qty >= ".$_GET['sale_qty_frm'];
					}
				}
				if(isset($_GET['sale_qty_to'])){
					if($_GET['sale_qty_to'] != ''){
						$having .=" AND sale_qty <= ".$_GET['sale_qty_to'];
					}
				}
				if(isset($_GET['sm_amt_frm'])){
					if($_GET['sm_amt_frm'] != ''){
						$having .=" AND sm_amt >= ".$_GET['sm_amt_frm'];
					}
				}
				if(isset($_GET['sm_amt_to'])){
					if($_GET['sm_amt_to'] != ''){
						$having .=" AND sm_amt <= ".$_GET['sm_amt_to'];
					}
				}
				if(isset($_GET['srm_amt_frm'])){
					if($_GET['srm_amt_frm'] != ''){
						$having .=" AND srm_amt >= ".$_GET['srm_amt_frm'];
					}
				}
				if(isset($_GET['srm_amt_to'])){
					if($_GET['srm_amt_to'] != ''){
						$having .=" AND srm_amt <= ".$_GET['srm_amt_to'];
					}
				}
				if(isset($_GET['sale_amt_frm'])){
					if($_GET['sale_amt_frm'] != ''){
						$having .=" AND sale_amt >= ".$_GET['sale_amt_frm'];
					}
				}
				if(isset($_GET['sale_amt_to'])){
					if($_GET['sale_amt_to'] != ''){
						$having .=" AND sale_amt <= ".$_GET['sale_amt_to'];
					}
				}
				$query 	="
							SELECT UPPER(user.user_fullname) as user_fullname, SUM(sm.sm_total_qty) as sm_qty, SUM(sm.sm_return_qty) as srm_qty,
							SUM(sm.sm_total_qty - sm.sm_return_qty) as sale_qty,
							SUM(sm.sm_final_amt) as sm_amt, SUM(sm.sm_return_amt) as srm_amt, 
							SUM(sm.sm_final_amt - sm.sm_return_amt) as sale_amt
							FROM sales_master sm
							INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
							$subsql
							GROUP BY user.user_id
							HAVING 1
							$having
							ORDER BY sale_qty DESC, sale_amt DESC
						 ";
				// echo $query;exit;
				$record['data'] = $this->db->query($query)->result_array();
				// echo "<pre>";print_r($record);exit;

				$sm_qty  		= 0;
				$srm_qty  		= 0;
				$sale_qty  		= 0;
				$sm_amt  		= 0;
				$srm_amt  		= 0;
				$sale_amt   	= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$sm_qty 		= $sm_qty + $value['sm_qty'];
						$srm_qty 		= $srm_qty + $value['srm_qty'];
						$sale_qty 		= $sale_qty + $value['sale_qty'];
						$sm_amt 		= $sm_amt + $value['sm_amt'];
						$srm_amt 		= $srm_amt + $value['srm_amt'];
						$sale_amt 		= $sale_amt + $value['sale_amt'];
					}
				}
				$record['totals']['sm_qty'] 		= $sm_qty;
				$record['totals']['srm_qty'] 		= $srm_qty;
				$record['totals']['sale_qty'] 		= $sale_qty;
				$record['totals']['sm_amt'] 		= $sm_amt;
				$record['totals']['srm_amt'] 		= $srm_amt;
				$record['totals']['sale_amt'] 		= $sale_amt;

				return $record;
			}
		/****************** BEST PERSON ***********************/
		/****************** CUSTOMER LEDGER *******************/
			public function get_customer_ledger(){
				$subsql1 	= '';
				$subsql2 	= '';
				$subsql3 	= '';
				$from_date 	= $_SESSION['start_year'];
				$to_date 	= $_SESSION['end_year'];
				$account_id = 0;
				$record 	= [];
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql1 .=" AND sm.sm_acc_id = ".$_GET['acc_id'];
					$subsql2 .=" AND srm.srm_acc_id = ".$_GET['acc_id'];
					$subsql3 .=" AND vm.vm_party_id = ".$_GET['acc_id'];
					$account_id = $_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
				}else{
					$subsql1 .=" AND sm.sm_acc_id = 0";
					$subsql2 .=" AND srm.srm_acc_id = 0";
					$subsql3 .=" AND vm.vm_party_id = 0";
				}
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}else{
					$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}else{
					$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}
				$open_amt 				= $this->Accountmdl->get_opening_balance('CUSTOMER', $account_id, $from_date); 
				$record['open_amt'] 	= $open_amt;
				$amt_to_debit 			= $this->Salesmdl->get_debit_balance($account_id, $from_date); 
				$record['amt_to_debit']	= $amt_to_debit;
				$amt_to_credit 			= $this->SalesReturnmdl->get_credit_balance($account_id, $from_date); 
				$record['amt_to_credit']= $amt_to_credit;
				$amt_debited 			= $this->Vouchermdl->get_debited_balance('CUSTOMER', $account_id, $from_date); 
				$record['amt_debited']	= $amt_debited;
				$amt_credited 			= $this->Vouchermdl->get_credited_balance('CUSTOMER', $account_id, $from_date); 
				$record['amt_credited']	= $amt_credited;

				$open_amt 				= ($open_amt + ($amt_to_debit - $amt_debited)) - ($amt_to_credit - $amt_credited);
				$close_amt				= 0;
				$open_label 			= $open_amt < 0 ? TO_PAY : TO_RECEIVE;
				$close_label 			= $close_amt < 0 ? TO_PAY : TO_RECEIVE;
				$record['open_bal'] 	= abs($open_amt)." ".$open_label;
				$sales_query ="
							SELECT sm.sm_bill_no as entry_no, DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') as entry_date, 
							sm.sm_final_amt as amt_to_debit, (sm.sm_collected_amt - sm.sm_to_pay) as amt_debited, sm.sm_created_at as created_at,
							CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
							FROM sales_master sm
							INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
							$subsql1
							ORDER BY sm.sm_created_at ASC
						";
				// echo "<pre>"; print_r($sales_query); exit;
				$sales_data = $this->db->query($sales_query)->result_array();
				if(!empty($sales_data)){
					foreach ($sales_data as $key => $value) {
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= 'SALES';
						$record['data'][strtotime($value['created_at'])]['amt_to_debit']	= $value['amt_to_debit'];
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= $value['amt_debited'];
						$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_credited'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}

				$return_query ="
							SELECT srm.srm_entry_no as entry_no, DATE_FORMAT(srm.srm_entry_date, '%d-%m-%Y') as entry_date, 
							srm.srm_final_amt as amt_to_credit, srm.srm_amt_paid as amt_credited, srm.srm_created_at as created_at,
							CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
							FROM sales_return_master srm
							INNER JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
							WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
							AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
							$subsql2
							ORDER BY srm.srm_created_at ASC
						";
				// echo "<pre>"; print_r($return_query); exit;
				$return_data = $this->db->query($return_query)->result_array();
				if(!empty($return_data)){
					foreach ($return_data as $key => $value) {
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= 'SALES RETURN';
						$record['data'][strtotime($value['created_at'])]['amt_to_credit'] 	= $value['amt_to_credit'];
						$record['data'][strtotime($value['created_at'])]['amt_credited']	= $value['amt_credited'];
						$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}
				$voucher_query ="
							SELECT vm.vm_acc_id, vm.vm_entry_no as entry_no, DATE_FORMAT(vm.vm_entry_date, '%d-%m-%Y') as entry_date, 
							vm.vm_type as action, IF(vm.vm_type = 'RECEIPT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_debited,  
							IF(vm.vm_type = 'PAYMENT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_credited, vm.vm_created_at as created_at, 
							CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm.vm_group = 'CUSTOMER'
							$subsql3
							ORDER BY vm.vm_created_at ASC
						";
				// echo "<pre>"; print_r($voucher_query); exit;
				$voucher_data = $this->db->query($voucher_query)->result_array();
				if(!empty($voucher_data)){
					foreach ($voucher_data as $key => $value) {
						$amt_credited = 0;
						$amt_debited  = 0;
						if($value['vm_acc_id'] == CREDIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else if($value['vm_acc_id'] == DEBIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else{
							$amt_debited 	= $value['amt_debited'];
							$amt_credited 	= $value['amt_credited'];
						}
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= $value['action'];
						$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= $amt_debited;
						$record['data'][strtotime($value['created_at'])]['amt_credited']	= $amt_credited;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}
				if(!empty($record['data'])){
					usort($record['data'], function($a, $b){
						if ($a == $b) return 0;
			        	return ($a['created_at'] < $b['created_at']) ? -1 : 1;
					});
					$close_amt = $open_amt;
					foreach ($record['data'] as $key => $value) {
						$amt_to_debit 	= $value['amt_to_debit'];
						$amt_debited 	= $value['amt_debited'];
						$amt_to_credit 	= $value['amt_to_credit'];
						$amt_credited 	= $value['amt_credited'];
						$bal_amt 		= $close_amt + ($amt_to_debit - $amt_debited) - ($amt_to_credit - $amt_credited);
						$label 			= $bal_amt < 0 ? TO_PAY : TO_RECEIVE;
						$close_amt 		= $bal_amt;
						$bal_amt 		= abs($bal_amt);

						$record['data'][$key]['amt_to_debit'] 	= round($amt_to_debit, 2);
						$record['data'][$key]['amt_debited'] 	= round($amt_debited, 2);
						$record['data'][$key]['amt_to_credit'] 	= round($amt_to_credit, 2);
						$record['data'][$key]['amt_credited'] 	= round($amt_credited, 2);
						$record['data'][$key]['bal_amt'] 		= round($bal_amt, 2)." ".$label;
					}	
				}
				$close_label 		= $close_amt < 0 ? TO_PAY : TO_RECEIVE;
				$record['close_bal']= abs($close_amt)." ".$close_label;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** CUSTOMER LEDGER *******************/
		/****************** CUSTOMER MIS **********************/
			public function get_customer_mis(){
				$subsql 	= '';
				$subsql1 	= '';
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND sm.sm_acc_id = ".$_GET['acc_id'];
					$subsql1 .=" AND vm.vm_party_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, SUM(sm.sm_final_amt) as bill_amt, 
							(SUM(sm.sm_collected_amt) - SUM(sm.sm_to_pay)) as received_amt, 
							(SUM(sm.sm_final_amt) - (SUM(sm.sm_collected_amt) - SUM(sm.sm_to_pay))) as bal_amt
							FROM sales_master sm
							INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							GROUP BY acc.account_id
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();

				$vquery ="
							SELECT CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, SUM(vm.vm_total_amt) as voucher_amt
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm.vm_type = 'RECEIPT'
							AND vm.vm_group = 'CUSTOMER'
							AND vm.vm_acc_id != 3 AND vm.vm_acc_id != 4
							$subsql1
							GROUP BY acc.account_id
						";
				// echo "<pre>"; print_r($query); exit;
				$record['vdata'] = $this->db->query($vquery)->result_array();

				// echo "<pre>"; print_r($record); exit;
				$bill_amt 		= 0;
				$received_amt 	= 0;
				$bal_amt 		= 0;
				$voucher_amt 	= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$bill_amt 		= $bill_amt + $value['bill_amt'];
						$received_amt 	= $received_amt + $value['received_amt'];
						$bal_amt 		= $bal_amt + $value['bal_amt'];
					}
				}
				if(!empty($record['vdata'])){
					foreach ($record['vdata'] as $key => $value) {
						$voucher_amt 	= $voucher_amt + $value['voucher_amt'];
					}
				}
				$record['totals']['bill_amt'] 		= $bill_amt;
				$record['totals']['received_amt'] 	= $received_amt;
				$record['totals']['bal_amt'] 		= $bal_amt;
				$record['totals']['voucher_amt'] 	= $voucher_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** CUSTOMER MIS **********************/
		/****************** CUSTOMER OUTSTANDING **************/
			public function get_customer_outstanding(){
				$subsql 	= '';
				$having 	= '';
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND acc.account_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
				}
				if(isset($_GET['debit_frm'])){
					if($_GET['debit_frm'] != ''){
						$having .=" AND debit_amt >= ".$_GET['debit_frm'];
					}
				}
				if(isset($_GET['debit_to'])){
					if($_GET['debit_to'] != ''){
						$having .=" AND debit_amt <= ".$_GET['debit_to'];
					}
				}
				if(isset($_GET['debited_frm'])){
					if($_GET['debited_frm'] != ''){
						$having .=" AND debited_amt >= ".$_GET['debited_frm'];
					}
				}
				if(isset($_GET['debited_to'])){
					if($_GET['debited_to'] != ''){
						$having .=" AND debited_amt <= ".$_GET['debited_to'];
					}
				}
				if(isset($_GET['bal_frm'])){
					if($_GET['bal_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_frm'];
					}
				}else{
					$having .=" AND bal_amt >= 1";
				}
				if(isset($_GET['bal_to'])){
					if($_GET['bal_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_to'];
					}
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, acc.account_open_bal as open_amt,
							acc.account_drcr, acc.account_amt_to_credit as credit_amt, acc.account_amt_credited as credited_amt, 
							acc.account_amt_to_debit as debit_amt, acc.account_amt_debited as debited_amt,
							IF(acc.account_drcr = 'DR', ((acc.account_open_bal + (acc.account_amt_to_debit - acc.account_amt_debited)) - (acc.account_amt_to_credit - acc.account_amt_credited)), ((acc.account_open_bal + (acc.account_amt_to_credit - acc.account_amt_credited)) - (acc.account_amt_to_debit - acc.account_amt_debited))) as bal_amt
							FROM account_master acc
							WHERE acc.account_type = 'CUSTOMER'
							$subsql
							GROUP BY acc.account_id DESC
							HAVING 1
							$having
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$open_amt  		= 0;
				$credit_amt  	= 0;
				$credited_amt  	= 0;
				$debit_amt  	= 0;
				$debited_amt  	= 0;
				$bal_amt   		= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$open_amt 		= $open_amt + $value['open_amt'];
						$credit_amt 	= $credit_amt + $value['credit_amt'];
						$credited_amt 	= $credited_amt + $value['credited_amt'];
						$debit_amt 		= $debit_amt + $value['debit_amt'];
						$debited_amt 	= $debited_amt + $value['debited_amt'];
						if($value['account_drcr'] == 'DR'){
							$bal_amt 		= $bal_amt + $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_PAY : TO_RECEIVE;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}else{
							$bal_amt 		= $bal_amt - $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_RECEIVE : TO_PAY;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}
					}
				}
				$record['totals']['open_amt'] 		= $open_amt;
				$record['totals']['credit_amt'] 	= $credit_amt;
				$record['totals']['credited_amt'] 	= $credited_amt;
				$record['totals']['debit_amt'] 		= $debit_amt;
				$record['totals']['debited_amt'] 	= $debited_amt;
				$record['totals']['bal_amt'] 		= abs($bal_amt);
				$record['totals']['label'] 			= $bal_amt < 0 ? TO_PAY : TO_RECEIVE;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** CUSTOMER OUTSTANDING **************/
		/****************** DAILY PROFIT **********************/
			public function get_daily_profit_for_sales($date){
				$query ="
							SELECT st.st_qty as st_qty, st.st_rate as st_rate, st.st_disc_amt as st_disc, st.st_sub_total_amt as st_amt,
							st.st_pt_rate as pt_amt, (st.st_sub_total_amt - st.st_pt_rate) as profit_loss 
							FROM sales_master sm
							INNER JOIN sales_trans st ON(st.st_sm_id = sm.sm_id)
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
							AND sm.sm_bill_date = '".$date."'
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				$st_qty 	= 0;
				$st_rate 	= 0;
				$st_disc 	= 0;
				$st_amt 	= 0;
				$pt_amt 	= 0;
				$profit_loss= 0;
				if(!empty($data)) {
					foreach ($data as $key => $value) {
						$st_qty  	= $st_qty + $value['st_qty'];
						$st_rate 	= $st_rate + $value['st_rate'];
						$st_amt 	= $st_amt + $value['st_amt'];
						$st_disc 	= $st_disc + $value['st_disc'];
						$pt_amt 	= $pt_amt + $value['pt_amt'];
						$profit_loss= $profit_loss + $value['profit_loss'];
					}
				}

				return [
						'st_qty' 		=> $st_qty,
						'st_rate' 		=> $st_rate,
						'st_disc' 		=> $st_disc,
						'st_amt' 		=> $st_amt,
						'pt_amt' 		=> $pt_amt,
						'profit_loss' 	=> $profit_loss,
					];
			}
			public function get_daily_profit_for_return($date){
				$query ="
							SELECT srt.srt_qty as srt_qty, srt.srt_rate, srt.srt_disc_amt as srt_disc, srt.srt_total_amt as srt_amt, 
							srt_pt_rate as pt_amt, (srt.srt_pt_rate - srt.srt_total_amt) as profit_loss
							FROM sales_return_master srm
							INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
							INNER JOIN sales_trans st ON(st.st_id = srt.srt_st_id)
							WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
							AND srm.srm_fin_year = '".$_SESSION['fin_year']."' 
							AND srm.srm_entry_date = '".$date."'
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				$srt_qty 	= 0;
				$srt_rate 	= 0;
				$srt_disc 	= 0;
				$srt_amt 	= 0;
				$pt_amt 	= 0;
				$profit_loss= 0;
				if(!empty($data)) {
					foreach ($data as $key => $value) {
						$srt_qty  	= $srt_qty + $value['srt_qty'];
						$srt_rate 	= $srt_rate + $value['srt_rate'];
						$srt_disc 	= $srt_disc + $value['srt_disc'];
						$srt_amt 	= $srt_amt + $value['srt_amt'];
						$pt_amt 	= $pt_amt + $value['pt_amt'];
						$profit_loss= $profit_loss + $value['profit_loss'];
					}
				}

				return [
						'srt_qty' 	=> $srt_qty,
						'srt_rate' 	=> $srt_rate,
						'srt_disc'	=> $srt_disc,
						'srt_amt' 	=> $srt_amt,
						'pt_amt' 	=> $pt_amt,
						'profit_loss'=> $profit_loss,
					];
			}
			public function get_daily_profit_for_voucher($date){
				$query ="
							SELECT SUM(vm.vm_total_amt) as amt
							FROM voucher_master vm
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND  vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm_type = 'PAYMENT'
							AND vm.vm_acc_id != 3 AND vm.vm_acc_id != 4
							AND vm.vm_entry_date = '".$date."'
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				if(!empty($data)) return $data[0]['amt'];
				return 0;
			}
			public function get_daily_profit($flag = false){
				$subsql 	= '';
				$subsql1 	= '';
				$having 	= '';
				$from_date 	= $flag ? $_SESSION['start_year'] : date('Y-m-d');
				$to_date 	= $flag ? $_SESSION['end_year'] : date('Y-m-d');
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND ms.entry_date >= '".$from_date."'";
					$subsql1 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}else{
					$subsql .= " AND ms.entry_date >= '".$from_date."'";
					$subsql1 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND ms.entry_date <= '".$to_date."'";
					$subsql1 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}else{
					$subsql .= " AND ms.entry_date <= '".$to_date."'";
					$subsql1 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}
				$query ="
							SELECT ms.entry_date
							FROM (
									SELECT sm.sm_bill_date as entry_date
									FROM sales_master sm
									WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
									AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
									GROUP BY sm.sm_bill_date
									UNION
									SELECT srm.srm_entry_date as entry_date
									FROM sales_return_master srm
									WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
									AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
									GROUP BY srm.srm_entry_date
							) as ms 
							WHERE 1
							$subsql
							ORDER BY ms.entry_date DESC
							
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				
				$st_qty 	= 0;
				$srt_qty 	= 0;
				$sale_qty 	= 0;

				$pt_amt 	= 0;
				$st_rate 	= 0;
				$st_disc 	= 0;
				$srt_amt 	= 0;
				$payment_amt= 0;
				$st_amt 	= 0;
				$profit_loss= 0;
				if(!empty($data)){
					foreach ($data as $key => $value) {
						$sale_qty_from 	= true; 
						$sale_qty_to 	= true; 
						$st_amt_from 	= true; 
						$st_amt_to 		= true; 
						$profit_amt_from= true; 
						$profit_amt_to 	= true; 
						$sales_data 	= $this->get_daily_profit_for_sales($value['entry_date']);
						$return_data 	= $this->get_daily_profit_for_return($value['entry_date']);
						$payment 		= $this->get_daily_profit_for_voucher($value['entry_date']);
						$pt_amt1 		= !empty($sales_data['pt_amt']) ? $sales_data['pt_amt'] : $return_data['pt_amt'];
						$st_qty1 		= $sales_data['st_qty'] - $return_data['srt_qty'];
						$st_amt1 		= $sales_data['st_amt'] -  ($return_data['srt_amt'] + $payment);
						$profit_loss1 	= $st_amt1 - $pt_amt1;
						// echo "<pre>"; print_r($sales_data); exit;
						// echo "<pre>"; print_r($return_data); exit;
						if(isset($_GET['sale_qty_from'])){
							if($_GET['sale_qty_from'] != ''){
			                	if($st_qty1 >= $_GET['sale_qty_from']){
			                		$sale_qty_from = true;
			                	}else{
			                		$sale_qty_from = false;
			                	}
							}
						}
						if(isset($_GET['sale_qty_to'])){
			            	if($_GET['sale_qty_to'] != ''){
			                	if($st_qty1 <= $_GET['sale_qty_to']){
			                		$sale_qty_to = true;
			                	}else{
			                		$sale_qty_to = false;
			                	}
			            	}
			            }
			            if(isset($_GET['st_amt_from'])){
				            if($_GET['st_amt_from'] != ''){
			                	if($st_amt1 >= $_GET['st_amt_from']){
			                		$st_amt_from = true;
			                	}else{
			                		$st_amt_from = false;
			                	}
							}
			            }
			            if(isset($_GET['st_amt_to'])){
			            	if($_GET['st_amt_to'] != ''){
			                	if($st_amt1 <= $_GET['st_amt_to']){
			                		$st_amt_to = true;
			                	}else{
			                		$st_amt_to = false;
			                	}
			            	}
			            }

			            if(isset($_GET['profit_amt_from'])){
				            if($_GET['profit_amt_from'] != ''){
			                	if($profit_loss1 >= $_GET['profit_amt_from']){
			                		$profit_amt_from = true;
			                	}else{
			                		$profit_amt_from = false;
			                	}
							}
			            }
			            if(isset($_GET['profit_amt_to'])){
			            	if($_GET['profit_amt_to'] != ''){
			                	if($profit_loss1 <= $_GET['profit_amt_to']){
			                		$profit_amt_to = true;
			                	}else{
			                		$profit_amt_to = false;
			                	}
			            	}
			            }

						if($sale_qty_from && $sale_qty_to && $st_amt_from && $st_amt_to && $profit_amt_from && $profit_amt_to){
							$record['data'][$key]['entry_date'] = date('d-m-Y', strtotime($value['entry_date']));
							$record['data'][$key]['day'] 		= date('D', strtotime($value['entry_date']));
							$record['data'][$key]['st_qty'] 	= $sales_data['st_qty'];
							$record['data'][$key]['srt_qty'] 	= $return_data['srt_qty'];
							$record['data'][$key]['sale_qty'] 	= $st_qty1;

							$record['data'][$key]['pt_amt'] 	= $pt_amt1;
							$record['data'][$key]['st_rate'] 	= $sales_data['st_rate'];
							$record['data'][$key]['st_disc'] 	= $sales_data['st_disc'];
							$record['data'][$key]['srt_amt'] 	= $return_data['srt_amt'];

							$record['data'][$key]['payment_amt']= $payment;
							$record['data'][$key]['st_amt'] 	= $st_amt1;
							$record['data'][$key]['profit_loss']= $profit_loss1;

							$st_qty 	= $st_qty + $sales_data['st_qty'];
							$srt_qty 	= $srt_qty + $return_data['srt_qty'];
							$sale_qty 	= $sale_qty + $st_qty1;

							$pt_amt 	= $pt_amt + $pt_amt1;
							$st_rate 	= $st_rate + $sales_data['st_rate'];
							$st_disc 	= $st_disc + $sales_data['st_disc'];
							$srt_amt 	= $srt_amt + $return_data['srt_amt'];

							$payment_amt= $payment_amt + $payment;
							$st_amt 	= $st_amt + $st_amt1;
							$profit_loss= $profit_loss + $profit_loss1;
			            }
					}
				}
				// echo "<pre>"; print_r($record); exit;
				$record['totals']['st_qty'] 	= $st_qty;
				$record['totals']['srt_qty'] 	= $srt_qty;
				$record['totals']['sale_qty'] 	= $sale_qty;

				$record['totals']['pt_amt'] 	= $pt_amt;
				$record['totals']['st_rate'] 	= $st_rate;
				$record['totals']['st_disc'] 	= $st_disc;
				$record['totals']['srt_amt'] 	= $srt_amt;
				$record['totals']['payment_amt']= $payment_amt;
				$record['totals']['st_amt'] 	= $st_amt;
				$record['totals']['profit_loss'] = $profit_loss;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** DAILY PROFIT **********************/
		/****************** DAILY TRANSACTION *****************/
			public function get_daily_transaction(){
				$subsql1 	= '';
				$subsql2 	= '';
				$subsql3 	= '';
				$from_date 	= date('Y-m-d');
				$to_date 	= date('Y-m-d');
				$account_id = DEF_ACC;
				$mode 		= 'CASH';
				$record 	= [];
				if(isset($_GET['account_id']) && !empty($_GET['account_id'])){
					$account_id = $_GET['account_id'];
					$mode 		= $_GET['account_id'] == 1 ? 'CASH' : 'BANK';
				}
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}else{
					$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}else{
					$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}
				$account_data 			= $this->db_operations->get_record('account_master', ['account_id' => $account_id]);
				$account_drcr 			= !empty($account_data) ? $account_data[0]['account_drcr'] : 'CR';

				$open_amt 				= $this->Accountmdl->get_opening_balance('GENERAL', $account_id, $from_date); 
				$record['open_amt']		= $open_amt;
				
				$cust_debited 			= $this->Vouchermdl->get_debited_bal('CUSTOMER', $mode, $account_id, 0, $from_date); 
				$record['cust_debited']	= $cust_debited;

				$supp_debited 			= $this->Vouchermdl->get_debited_bal('SUPPLIER', $mode, $account_id, 0, $from_date, false); 
				$record['supp_debited']	= $supp_debited;

				$gen_debited 			= $this->Vouchermdl->get_debited_bal('GENERAL', $mode, $account_id, 0, $from_date, false); 
				$record['gen_debited']	= $gen_debited;
				
				$cust_credited 			= $this->Vouchermdl->get_credited_bal('CUSTOMER', $mode, $account_id, 0, $from_date); 
				$record['cust_credited']= $cust_credited;

				$supp_credited 			= $this->Vouchermdl->get_credited_bal('SUPPLIER', $mode, $account_id, 0, $from_date, false); 
				$record['supp_credited']= $supp_credited;

				$gen_credited 			= $this->Vouchermdl->get_credited_bal('GENERAL', $mode, $account_id, 0, $from_date, false); 
				$record['gen_credited'] = $gen_credited;
				
				$open_amt 				= ($open_amt + $cust_debited + $supp_debited + $gen_debited) - ($cust_credited + $supp_credited + $gen_credited); 
				$open_label 			= $open_amt < 0 ? TO_RECEIVE : TO_PAY;
				if($account_drcr == 'DR'){
					$open_label 		= $open_amt < 0 ? TO_PAY : TO_RECEIVE;
				}
				$record['open_bal'] 	= $open_amt;

				$sales_amt				= $this->Salesmdl->get_debited_amount($mode, $from_date, $to_date); 
				$record['sales_amt']	= round($sales_amt, 2);

				$return_amt				= $this->SalesReturnmdl->get_credited_amt($account_id == 1 ? 0 : -1, $from_date, $to_date); 
				$record['return_amt']	= round($return_amt, 2);
				// echo "<pre>"; print_r($record); exit;
				$receipt_amt			= 0;
				$payment_amt			= 0;
				$voucher_query ="
							SELECT vm.vm_acc_id, vm.vm_entry_no as entry_no, DATE_FORMAT(vm.vm_entry_date, '%d-%m-%Y') as entry_date, 
							vm.vm_type as action, IF(vm.vm_type = 'RECEIPT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_debited,  
							IF(vm.vm_type = 'PAYMENT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_credited, vm.vm_created_at as created_at, 
							CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm.vm_acc_id = $account_id
							$subsql3
							ORDER BY vm.vm_created_at ASC
						";
				// echo "<pre>"; print_r($voucher_query); exit;
				$voucher_data = $this->db->query($voucher_query)->result_array();
				if(!empty($voucher_data)){
					foreach ($voucher_data as $key => $value) {
						$amt_credited = 0;
						$amt_debited  = 0;
						if($value['vm_acc_id'] == CREDIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else if($value['vm_acc_id'] == DEBIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else{
							$amt_debited 	= $value['amt_debited'];
							$amt_credited 	= $value['amt_credited'];
						}
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= $value['action'];
						$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= round($amt_debited, 2);
						$record['data'][strtotime($value['created_at'])]['amt_credited']	= round($amt_credited, 2);
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';

						$receipt_amt = $receipt_amt + $amt_debited;
						$payment_amt = $payment_amt + $amt_credited;
					}
				}
				$record['receipt_amt']	= $receipt_amt;
				$record['payment_amt']	= $payment_amt;
				
				$close_amt				= ($open_amt + $sales_amt + $receipt_amt) - ($return_amt + $payment_amt);
				$close_label 			= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
				if($account_drcr == 'DR'){
					$close_label 		= $close_amt < 0 ? TO_PAY : TO_RECEIVE;
				}
				$record['close_bal']	= $close_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** DAILY TRANSACTION *****************/
		/****************** GENERAL LEDGER ********************/
			public function get_general_ledger(){
				$subsql 	= '';
				$from_date 	= date('Y-m-d');
				$to_date 	= date('Y-m-d');
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND vm.vm_acc_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
				}
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND vm.vm_entry_date >= '".$from_date."'";
				}else{
					$subsql .= " AND vm.vm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND vm.vm_entry_date <= '".$to_date."'";
				}else{
					$subsql .= " AND vm.vm_entry_date <= '".$to_date."'";
				}
				$query ="
							SELECT vm.*, UPPER(acc.account_name) as account_name
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
							WHERE 1
							AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							ORDER BY acc.account_name ASC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['vou_data'] = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** GENERAL LEDGER ********************/
		/****************** GENERAL OUTSTANDING ***************/
			public function get_general_outstanding(){
				$subsql 	= '';
				$having 	= '';
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND acc.account_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
				}
				if(isset($_GET['bal_frm'])){
					if($_GET['bal_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_frm'];
					}
				}
				if(isset($_GET['bal_to'])){
					if($_GET['bal_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_to'];
					}
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, acc.account_open_bal as open_amt,
							acc.account_drcr, acc.account_amt_to_credit as credit_amt, acc.account_amt_credited as credited_amt, 
							acc.account_amt_to_debit as debit_amt, acc.account_amt_debited as debited_amt,
							IF(acc.account_drcr = 'DR', ((acc.account_open_bal + (acc.account_amt_to_debit - acc.account_amt_debited)) - (acc.account_amt_to_credit - acc.account_amt_credited)), ((acc.account_open_bal + (acc.account_amt_to_credit - acc.account_amt_credited)) - (acc.account_amt_to_debit - acc.account_amt_debited))) as bal_amt
							FROM account_master acc
							WHERE acc.account_type = 'GENERAL'
							$subsql
							GROUP BY acc.account_id DESC
							HAVING 1
							$having
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$open_amt  		= 0;
				$credit_amt  	= 0;
				$credited_amt  	= 0;
				$debit_amt  	= 0;
				$debited_amt  	= 0;
				$bal_amt   		= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$open_amt 		= $open_amt + $value['open_amt'];
						$credit_amt 	= $credit_amt + $value['credit_amt'];
						$credited_amt 	= $credited_amt + $value['credited_amt'];
						$debit_amt 		= $debit_amt + $value['debit_amt'];
						$debited_amt 	= $debited_amt + $value['debited_amt'];
						if($value['account_drcr'] == 'DR'){
							$bal_amt 		= $bal_amt + $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_PAY : TO_RECEIVE;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}else{
							$bal_amt 		= $bal_amt - $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_RECEIVE : TO_PAY;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}
					}
				}
				$record['totals']['open_amt'] 		= $open_amt;
				$record['totals']['credit_amt'] 	= $credit_amt;
				$record['totals']['credited_amt'] 	= $credited_amt;
				$record['totals']['debit_amt'] 		= $debit_amt;
				$record['totals']['debited_amt'] 	= $debited_amt;
				$record['totals']['label'] 			= $bal_amt < 0 ? TO_PAY : TO_RECEIVE;
				$record['totals']['bal_amt'] 		= abs($bal_amt);
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** GENERAL OUTSTANDING ***************/
		/****************** MAX SUPPLIER SALE******************/
			public function get_max_supplier_sale(){
				$record = [];
				$subsql = "";
				$having = "";
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}
				if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
					$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
					$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
				}
				if(isset($_GET['from_qty'])){
					if($_GET['from_qty'] != ''){
						$having .=" AND st_qty >= ".$_GET['from_qty'];
					}
				}
				if(isset($_GET['to_qty'])){
					if($_GET['to_qty'] != ''){
						$having .=" AND st_qty <= ".$_GET['to_qty'];
					}
				}
				if(isset($_GET['from_sale'])){
					if($_GET['from_sale'] != ''){
						$having .=" AND st_rate >= ".$_GET['from_sale'];
					}
				}
				if(isset($_GET['to_sale'])){
					if($_GET['to_sale'] != ''){
						$having .=" AND st_rate <= ".$_GET['to_sale'];
					}
				}
				if(isset($_GET['from_disc'])){
					if($_GET['from_disc'] != ''){
						$having .=" AND st_disc >= ".$_GET['from_disc'];
					}
				}
				if(isset($_GET['to_disc'])){
					if($_GET['to_disc'] != ''){
						$having .=" AND st_disc <= ".$_GET['to_disc'];
					}
				}
				if(isset($_GET['from_amt'])){
					if($_GET['from_amt'] != ''){
						$having .=" AND st_amt >= ".$_GET['from_amt'];
					}
				}
				if(isset($_GET['to_amt'])){
					if($_GET['to_amt'] != ''){
						$having .=" AND st_amt <= ".$_GET['to_amt'];
					}
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_name), ' - ', UPPER(acc.account_code)) as account_name, 
							SUM(bm.bm_st_qty) as st_qty, SUM(bm.bm_st_rate) as st_rate, SUM(bm.bm_st_disc) as st_disc,
							(SUM(bm.bm_st_rate) - SUM(bm.bm_st_disc)) as st_amt
							FROM barcode_master bm
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							INNER JOIN sales_master sm ON(sm.sm_id = bm.bm_sm_id)
							WHERE bm.bm_delete_status = 0
							AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND bm.bm_fin_year = '".$_SESSION['fin_year']."'
							AND bm.bm_pt_qty - bm.bm_prt_qty = 1
							AND bm.bm_st_qty - bm.bm_srt_qty = 1
							$subsql
							GROUP BY acc.account_id
							HAVING 1
							$having
							ORDER BY st_qty DESC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$st_qty  		= 0;
				$st_rate  		= 0;
				$st_disc  		= 0;
				$st_amt  		= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$st_qty 	= $st_qty + $value['st_qty'];
						$st_rate 	= $st_rate + $value['st_rate'];
						$st_disc 	= $st_disc + $value['st_disc'];
						$st_amt 	= $st_amt + $value['st_amt'];
					}
				}
				$record['totals']['st_qty'] 	= $st_qty;
				$record['totals']['st_rate'] 	= $st_rate;
				$record['totals']['st_disc'] 	= $st_disc;
				$record['totals']['st_amt'] 	= $st_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** MAX SUPPLIER SALE******************/
		/****************** MONTHLY PROFIT ********************/
			public function get_monthly_profit(){
				$record = [];
				$temp 	= [];
				$data 	= $this->get_daily_profit(true);
				// echo "<pre>"; print_r($data); exit();
				if(!empty($data['data'])){
					$month_year = '';
					foreach ($data['data'] as $key => $value) {
						if(empty($month_year)){
							$month_year 						= date('m-Y', strtotime($value['entry_date']));
							$temp[$month_year]['month_year'] 	= date('M-Y', strtotime($value['entry_date']));
							$temp[$month_year]['st_qty'] 		= $value['st_qty'];
							$temp[$month_year]['srt_qty'] 		= $value['srt_qty'];
							$temp[$month_year]['sale_qty'] 		= $value['sale_qty'];
							
							$temp[$month_year]['pt_amt'] 		= $value['pt_amt'];
							$temp[$month_year]['st_rate'] 		= $value['st_rate'];
							$temp[$month_year]['st_disc'] 		= $value['st_disc'];
							$temp[$month_year]['srt_amt'] 		= $value['srt_amt'];
							$temp[$month_year]['payment_amt'] 	= $value['payment_amt'];
							$temp[$month_year]['st_amt'] 		= $value['st_amt'];
							$temp[$month_year]['profit_loss'] 	= $value['profit_loss'];
						}else{
							if($month_year == date('m-Y', strtotime($value['entry_date']))){
								$temp[$month_year]['month_year'] 	= date('M-Y', strtotime($value['entry_date']));
								$temp[$month_year]['st_qty'] 		= $temp[$month_year]['st_qty'] + $value['st_qty'];
								$temp[$month_year]['srt_qty'] 		= $temp[$month_year]['srt_qty'] + $value['srt_qty'];
								$temp[$month_year]['sale_qty'] 		= $temp[$month_year]['sale_qty'] + $value['sale_qty'];

								$temp[$month_year]['pt_amt'] 		= $temp[$month_year]['pt_amt'] + $value['pt_amt'];
								$temp[$month_year]['st_rate'] 		= $temp[$month_year]['st_rate'] + $value['st_rate'];
								$temp[$month_year]['st_disc'] 		= $temp[$month_year]['st_disc'] + $value['st_disc'];
								$temp[$month_year]['srt_amt'] 		= $temp[$month_year]['srt_amt'] + $value['srt_amt'];
								$temp[$month_year]['payment_amt'] 	= $temp[$month_year]['payment_amt'] + $value['payment_amt'];
								$temp[$month_year]['st_amt'] 		= $temp[$month_year]['st_amt'] + $value['st_amt'];
								$temp[$month_year]['profit_loss'] 	= $temp[$month_year]['profit_loss'] + $value['profit_loss'];
							}else{
								$month_year = date('m-Y', strtotime($value['entry_date']));
								$temp[$month_year]['month_year'] 	= date('M-Y', strtotime($value['entry_date']));
								$temp[$month_year]['st_qty'] 		= $value['st_qty'];
								$temp[$month_year]['srt_qty'] 		= $value['srt_qty'];
								$temp[$month_year]['sale_qty'] 		= $value['sale_qty'];

								$temp[$month_year]['pt_amt'] 		= $value['pt_amt'];
								$temp[$month_year]['st_rate'] 		= $value['st_rate'];
								$temp[$month_year]['st_disc'] 		= $value['st_disc'];
								$temp[$month_year]['srt_amt'] 		= $value['srt_amt'];
								$temp[$month_year]['payment_amt'] 	= $value['payment_amt'];
								$temp[$month_year]['st_amt'] 		= $value['st_amt'];
								$temp[$month_year]['profit_loss'] 	= $value['profit_loss'];
							}
						}
					}
				}
				$st_qty 	= 0;
				$srt_qty 	= 0;
				$sale_qty 	= 0;

				$pt_amt 	= 0;
				$st_rate 	= 0;
				$st_disc 	= 0;
				$srt_amt 	= 0;
				$payment_amt= 0;
				$st_amt 	= 0;
				$profit_loss= 0;
				if(!empty($temp)){
					foreach ($temp as $key => $value) {
						$st_qty_from 	= true; 
						$st_qty_to 		= true; 
						$sale_amt_from 	= true; 
						$sale_amt_to 	= true; 
						$profit_from= true; 
						$profit_to 	= true; 
						if(isset($_GET['st_qty_from'])){
							if($_GET['st_qty_from'] != ''){
			                	if($value['sale_qty'] >= $_GET['st_qty_from']){
			                		$st_qty_from = true;
			                	}else{
			                		$st_qty_from = false;
			                	}
							}
						}
						if(isset($_GET['st_qty_to'])){
			            	if($_GET['st_qty_to'] != ''){
			                	if($value['sale_qty'] <= $_GET['st_qty_to']){
			                		$st_qty_to = true;
			                	}else{
			                		$st_qty_to = false;
			                	}
			            	}
			            }
			            if(isset($_GET['sale_amt_from'])){
				            if($_GET['sale_amt_from'] != ''){
			                	if($value['st_amt'] >= $_GET['sale_amt_from']){
			                		$sale_amt_from = true;
			                	}else{
			                		$sale_amt_from = false;
			                	}
							}
			            }
			            if(isset($_GET['sale_amt_to'])){
			            	if($_GET['sale_amt_to'] != ''){
			                	if($value['st_amt'] <= $_GET['sale_amt_to']){
			                		$sale_amt_to = true;
			                	}else{
			                		$sale_amt_to = false;
			                	}
			            	}
			            }

			            if(isset($_GET['profit_from'])){
				            if($_GET['profit_from'] != ''){
			                	if($value['profit_loss'] >= $_GET['profit_from']){
			                		$profit_from = true;
			                	}else{
			                		$profit_from = false;
			                	}
							}
			            }
			            if(isset($_GET['profit_to'])){
			            	if($_GET['profit_to'] != ''){
			                	if($value['profit_loss'] <= $_GET['profit_to']){
			                		$profit_to = true;
			                	}else{
			                		$profit_to = false;
			                	}
			            	}
			            }

						if($st_qty_from && $st_qty_to && $sale_amt_from && $sale_amt_to && $profit_from && $profit_to){
							$record['data'][$key] = $value;
							$st_qty 	= $st_qty + $value['st_qty'];
							$srt_qty 	= $srt_qty + $value['srt_qty'];
							$sale_qty 	= $sale_qty + ($value['st_qty'] - $value['srt_qty']);

							$pt_amt 	= $pt_amt + $value['pt_amt'];
							$st_rate 	= $st_rate + $value['st_rate'];
							$st_disc 	= $st_disc + $value['st_disc'];
							$srt_amt 	= $srt_amt + $value['srt_amt'];
							$payment_amt= $payment_amt + $value['payment_amt'];
							$st_amt 	= $st_amt + $value['st_amt'];
							$profit_loss = $profit_loss + $value['profit_loss'];
						}
					}
				}
				// echo "<pre>"; print_r($record); exit();
				$record['totals']['st_qty'] 	= $st_qty;
				$record['totals']['srt_qty'] 	= $srt_qty;
				$record['totals']['sale_qty'] 	= $sale_qty;

				$record['totals']['pt_amt'] 	= $pt_amt;
				$record['totals']['st_rate'] 	= $st_rate;
				$record['totals']['st_disc'] 	= $st_disc;
				$record['totals']['srt_amt'] 	= $srt_amt;
				$record['totals']['payment_amt']= $payment_amt;
				$record['totals']['st_amt'] 	= $st_amt;
				$record['totals']['profit_loss']= $profit_loss;
				return $record;
			}
		/****************** MONTHLY PROFIT ********************/
		/****************** MONTHLY SUMMARY *******************/
			public function get_monthly_summary_for_sales($date){
				$query ="
							SELECT SUM(sm.sm_total_qty) as qty, SUM(sm.sm_sub_total) as amt, 
							SUM(sm.sm_total_disc + sm.sm_promo_disc + sm.sm_point_used) as disc, SUM(sm.sm_final_amt) as final,
							COUNT(sm.sm_id) as bill
							FROM sales_master sm
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
							AND sm.sm_bill_date = '".$date."'
							GROUP BY sm.sm_bill_date
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				$qty  = 0;
				$amt  = 0;
				$disc = 0;
				$final= 0;
				$bill = 0;
				if(!empty($data)) {
					$qty  = $data[0]['qty'];
					$amt  = $data[0]['amt'];
					$disc = $data[0]['disc'];
					$final= $data[0]['final'];
					$bill = $data[0]['bill'];
				}
				return ['qty' => $qty, 'amt' => $amt, 'disc' => $disc, 'final' => $final, 'bill' => $bill];
			}
			public function get_monthly_summary_for_return($date){
				$query ="
							SELECT SUM(srm.srm_total_qty) as qty, SUM(srm.srm_sub_total) as amt, SUM(srm.srm_total_disc + srm.srm_bill_disc) as disc,
							SUM(srm.srm_final_amt) as final
							FROM sales_return_master srm
							WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
							AND srm.srm_fin_year = '".$_SESSION['fin_year']."' 
							AND srm.srm_entry_date = '".$date."'
							GROUP BY srm.srm_entry_date
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data); exit;
				$qty   = 0;
				$amt   = 0;
				$disc  = 0;
				$final = 0;
				if(!empty($data)) {
					$qty   = $data[0]['qty'];
					$amt   = $data[0]['amt'];
					$disc  = $data[0]['disc'];
					$final = $data[0]['final'];
				}
				return ['qty' => $qty, 'amt' => $amt, 'disc' => $disc, 'final' => $final];
			}
			public function get_monthly_summary(){
				$subsql 	= '';
				$having 	= '';
				$from_date 	= date('Y-m-01');
				$to_date 	= date('Y-m-t');
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND ms.entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND ms.entry_date <= '".$to_date."'";
				}
				$query ="
							SELECT ms.entry_date
							FROM (
									SELECT sm.sm_bill_date as entry_date
									FROM sales_master sm
									WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
									AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
									GROUP BY sm.sm_bill_date
									UNION
									SELECT srm.srm_entry_date as entry_date
									FROM sales_return_master srm
									WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
									AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
									GROUP BY srm.srm_entry_date
							) as ms 
							WHERE 1
							$subsql
							ORDER BY ms.entry_date DESC
							
						";
				// echo "<pre>"; print_r($query); exit;
				$data = $this->db->query($query)->result_array();
				$st_qty 	= 0;
				$srt_qty 	= 0;
				$sale_qty 	= 0;
				$st_amt 	= 0;
				$st_disc 	= 0;
				$srt_amt 	= 0;
				$sale_amt 	= 0;
				$bill 		= 0;
				if(!empty($data)){
					foreach ($data as $key => $value) {
						$from_qty 	= true; 
						$to_qty 	= true; 
						$from_amt 	= true; 
						$to_amt 	= true; 
						$sales_data = $this->get_monthly_summary_for_sales($value['entry_date']);
						$return_data= $this->get_monthly_summary_for_return($value['entry_date']);
						if(isset($_GET['from_qty'])){
							if($_GET['from_qty'] != ''){
			                	if(($sales_data['qty'] - $return_data['qty']) >= $_GET['from_qty']){
			                		$from_qty = true;
			                	}else{
			                		$from_qty = false;
			                	}
							}
			            }
			            if(isset($_GET['to_qty'])){
			            	if($_GET['to_qty'] != ''){
			                	if(($sales_data['qty'] - $return_data['qty']) <= $_GET['to_qty']){
			                		$to_qty = true;
			                	}else{
			                		$to_qty = false;
			                	}
			            	}
			            }
			            if(isset($_GET['from_amt'])){
				            if($_GET['from_amt'] != ''){
			                	if(($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt'])) >= $_GET['from_amt']){
			                		$from_amt = true;
			                	}else{
			                		$from_amt = false;
			                	}
							}
						}
						if(isset($_GET['to_amt'])){
			            	if($_GET['to_amt'] != ''){
			                	if(($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt'])) <= $_GET['to_amt']){
			                		$to_amt = true;
			                	}else{
			                		$to_amt = false;
			                	}
			            	}
			            }

			            if($from_qty && $to_qty && $from_amt && $to_amt){
							$record['data'][$key]['entry_date'] = date('d-m-Y', strtotime($value['entry_date']));
							$record['data'][$key]['day'] 		= date('D', strtotime($value['entry_date']));
							$record['data'][$key]['st_qty'] 	= $sales_data['qty'];
							$record['data'][$key]['srt_qty'] 	= $return_data['qty'];
							$record['data'][$key]['sale_qty'] 	= $sales_data['qty'] - $return_data['qty'];
							$record['data'][$key]['st_amt'] 	= $sales_data['amt'];
							$record['data'][$key]['st_disc'] 	= $sales_data['disc'];
							$record['data'][$key]['srt_amt'] 	= $return_data['amt'];
							$record['data'][$key]['sale_amt'] 	= $sales_data['amt'] - ($sales_data['disc'] + $return_data['amt']);
							$record['data'][$key]['bill'] 		= $sales_data['bill'];

							$st_qty 	= $st_qty + $sales_data['qty'];
							$srt_qty 	= $srt_qty + $return_data['qty'];
							$sale_qty 	= $sale_qty + ($sales_data['qty'] - $return_data['qty']);
							$st_amt 	= $st_amt + $sales_data['amt'];
							$st_disc 	= $st_disc + $sales_data['disc'];
							$srt_amt 	= $srt_amt + $return_data['amt'];
							$sale_amt 	= $sale_amt + ($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt']));
							$bill 		= $bill + $sales_data['bill'];
			            }
					}
				}
				// echo "<pre>"; print_r($record); exit;
				
				$record['totals']['st_qty'] 	= $st_qty;
				$record['totals']['srt_qty'] 	= $srt_qty;
				$record['totals']['sale_qty'] 	= $sale_qty;
				$record['totals']['st_amt'] 	= $st_amt;
				$record['totals']['st_disc'] 	= $st_disc;
				$record['totals']['srt_amt'] 	= $srt_amt;
				$record['totals']['sale_amt'] 	= $sale_amt;
				$record['totals']['bill'] 		= $bill;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** MONTHLY SUMMARY *******************/
		/****************** PROFIT & LOSS *********************/
			public function get_profit_loss(){
				$subsql1 	= '';
				$subsql2 	= '';
				$subsql3 	= '';
				if(isset($_GET['date_start']) && !empty($_GET['date_start'])){
					$date_start = date('Y-m-d', strtotime($_GET['date_start']));
					$date_end 	= date('Y-m-d', strtotime($_GET['date_start']));
					$subsql1 .= " AND ( sm.sm_bill_date BETWEEN '".$date_start."' AND '".$date_end."' )";
				}
				if(isset($_GET['id']) && !empty($_GET['id'])){
					$subsql1 .= " AND sm.sm_id = ". $_GET['id'];
				}
				$query ="
							SELECT (SUM(acc.account_open_bal) + SUM(acc.account_amt_credited)) as total_expense
							FROM account_master acc
							WHERE acc.account_group_id != 9
							AND acc.account_drcr = 'CR'
							$subsql1
							GROUP BY acc.account_type = 'SUPPLIER'
						";
				// echo "<pre>"; print_r($query); exit;
				$record['total_expense'] = $this->db->query($query)->result_array();
				$query ="
							SELECT acc.account_name, acc.account_open_bal + acc.account_amt_credited as expense
							FROM account_master acc
							WHERE acc.account_group_id != 9
							AND acc.account_drcr = 'CR'
							$subsql1
						";
				// echo "<pre>"; print_r($query); exit;
				$record['expense'] = $this->db->query($query)->result_array();
				$query ="
							SELECT (SUM(acc.account_open_bal) + SUM(acc.account_amt_debited)) as total_income
							FROM account_master acc
							WHERE acc.account_group_id != 9
							AND acc.account_drcr = 'DR'
							$subsql1
							GROUP BY acc.account_type = 'CUSTOMER'
						";
				// echo "<pre>"; print_r($query); exit;
				$record['total_income'] = $this->db->query($query)->result_array();
				$query ="
							SELECT acc.account_name, acc.account_open_bal + acc.account_amt_debited as income
							FROM account_master acc
							WHERE acc.account_group_id != 9
							AND acc.account_drcr = 'DR'
							$subsql1
						";
				// echo "<pre>"; print_r($query); exit;
				$record['income'] = $this->db->query($query)->result_array();
				return $record;
			}
		/****************** PROFIT & LOSS *********************/
		/****************** PURCHASE SUMMARY ******************/
			public function get_purchase_summary(){
				$subsql 	= '';
				$date_start = date('Y-m-01');
				$date_end 	= date('Y-m-t');
				if(isset($_GET['pm_entry_no']) && !empty($_GET['pm_entry_no'])){
					$subsql .=" AND pm.pm_id = ".$_GET['pm_entry_no'];
					$record['search']['pm_entry_no'] = $this->Purchasemdl->get_entry_no(['pm_id' => $_GET['pm_entry_no']]);
				}
				if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
					$subsql .= " AND pm.pm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
					$subsql .= " AND pm.pm_entry_date <= '".$to_date."'";
				}
				if(isset($_GET['pm_bill_no']) && !empty($_GET['pm_bill_no'])){
					$subsql .=" AND pm.pm_id = ".$_GET['pm_bill_no'];
					$record['search']['pm_bill_no'] = $this->Purchasemdl->get_bill_no(['pm_id' => $_GET['pm_bill_no']]);
				}
				if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
					$subsql .= " AND pm.pm_bill_date >= '".$from_date."'";
				}
				if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
					$subsql .= " AND pm.pm_bill_date <= '".$to_date."'";
				}
				if(isset($_GET['pm_acc_id']) && !empty($_GET['pm_acc_id'])){
					$subsql .=" AND pm.pm_acc_id = ".$_GET['pm_acc_id'];
					$record['search']['pm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['pm_acc_id']]);
				}
				if(isset($_GET['from_qty'])){
					if($_GET['from_qty'] != ''){
						$subsql .=" AND pm.pm_total_qty >= ".$_GET['from_qty'];
					}
				}
				if(isset($_GET['to_qty'])){
					if($_GET['to_qty'] != ''){
						$subsql .=" AND pm.pm_total_qty <= ".$_GET['to_qty'];
					}
				}
				if(isset($_GET['from_bill_amt'])){
					if($_GET['from_bill_amt'] != ''){
						$subsql .=" AND pm.pm_final_amt >= ".$_GET['from_bill_amt'];
					}
				}
				if(isset($_GET['to_bill_amt'])){
					if($_GET['to_bill_amt'] != ''){
						$subsql .=" AND pm.pm_final_amt <= ".$_GET['to_bill_amt'];
					}
				}
				$query ="
							SELECT pm.*, acc.account_name
							FROM purchase_master pm
							INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
							WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
							AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							ORDER BY pm.pm_id DESC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$total_qty 	= 0;
				$sub_amt 	= 0;
				$disc_amt 	= 0;
				$off_amt 	= 0;
				$bdisc_amt 	= 0;
				$gst_amt 	= 0;
				$total_amt 	= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$total_qty 	= $total_qty + $value['pm_total_qty'];
						$sub_amt 	= $sub_amt + $value['pm_sub_total'];
						$disc_amt 	= $disc_amt + $value['pm_total_disc'];
						$off_amt 	= $off_amt + $value['pm_round_off'];
						$bdisc_amt 	= $bdisc_amt + $value['pm_bill_disc'];
						$total_amt 	= $total_amt + $value['pm_final_amt'];
						$gst_amt 	= $gst_amt + $value['pm_gst_amt'];
					}
				}
				$record['totals']['total_qty'] 	= $total_qty;
				$record['totals']['sub_amt'] 	= $sub_amt;
				$record['totals']['disc_amt'] 	= $disc_amt;
				$record['totals']['off_amt'] 	= $off_amt;
				$record['totals']['bdisc_amt'] 	= $bdisc_amt;
				$record['totals']['gst_amt'] 	= $gst_amt;
				$record['totals']['total_amt'] 	= $total_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** PURCHASE SUMMARY ******************/
		/****************** PURCHASE RETURN SUMMARY ***********/
			public function get_purchase_return_summary(){
				$subsql 	= '';
				if(isset($_GET['prm_entry_no']) && !empty($_GET['prm_entry_no'])){
					$subsql .=" AND prm.prm_id = ".$_GET['prm_entry_no'];
					$record['search']['prm_entry_no'] = $this->PurchaseReturnmdl->get_entry_no(['prm_id' => $_GET['prm_entry_no']]);
				}
				if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
					$subsql .= " AND prm.prm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
					$subsql .= " AND prm.prm_entry_date <= '".$to_date."'";
				}
				if(isset($_GET['prm_acc_id']) && !empty($_GET['prm_acc_id'])){
					$subsql .=" AND prm.prm_acc_id = ".$_GET['prm_acc_id'];
					$record['search']['prm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['prm_acc_id']]);
				}
				if(isset($_GET['from_qty'])){
					if($_GET['from_qty'] != ''){
						$subsql .=" AND prm.prm_total_qty >= ".$_GET['from_qty'];
					}
				}
				if(isset($_GET['to_qty'])){
					if($_GET['to_qty'] != ''){
						$subsql .=" AND prm.prm_total_qty <= ".$_GET['to_qty'];
					}
				}
				if(isset($_GET['from_bill_amt'])){
					if($_GET['from_bill_amt'] != ''){
						$subsql .=" AND prm.prm_final_amt >= ".$_GET['from_bill_amt'];
					}
				}
				if(isset($_GET['to_bill_amt'])){
				 	if($_GET['to_bill_amt'] != ''){
						$subsql .=" AND prm.prm_final_amt <= ".$_GET['to_bill_amt'];
				 	}
				}
				$query ="
							SELECT prm.*, acc.account_name
							FROM purchase_return_master prm
							INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
							WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
							AND prm.prm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							ORDER BY prm.prm_id DESC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$total_qty 	= 0;
				$sub_amt 	= 0;
				$off_amt 	= 0;
				$bdisc_amt 	= 0;
				$gst_amt 	= 0;
				$total_amt 	= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$total_qty 	= $total_qty + $value['prm_total_qty'];
						$sub_amt 	= $sub_amt + $value['prm_sub_total'];
						$off_amt 	= $off_amt + $value['prm_round_off'];
						$bdisc_amt 	= $bdisc_amt + $value['prm_bill_disc'];
						$gst_amt 	= $gst_amt + $value['prm_gst_amt'];
						$total_amt 	= $total_amt + $value['prm_final_amt'];
					}
				}
				$record['totals']['total_qty'] 	= $total_qty;
				$record['totals']['sub_amt'] 	= $sub_amt;
				$record['totals']['off_amt'] 	= $off_amt;
				$record['totals']['bdisc_amt'] 	= $bdisc_amt;
				$record['totals']['gst_amt'] 	= $gst_amt;
				$record['totals']['total_amt'] 	= $total_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** PURCHASE RETURN SUMMARY ***********/
		/****************** SALES SUMMARY *********************/
			public function get_sales_summary(){
				$subsql 	= '';
				if(isset($_GET['sm_bill_no']) && !empty($_GET['sm_bill_no'])){
	                $subsql .=" AND sm.sm_id = ".$_GET['sm_bill_no'];
	                $record['search']['sm_bill_no'] = $this->Salesmdl->get_bill_no(['sm_id' => $_GET['sm_bill_no']]);
	            }
	            if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}
				if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}
	            if(isset($_GET['sm_acc_id']) && !empty($_GET['sm_acc_id'])){
	                $subsql .=" AND sm.sm_acc_id = ".$_GET['sm_acc_id'];
	                $record['search']['sm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['sm_acc_id']]);
	            }
	            if(isset($_GET['sm_user_id']) && !empty($_GET['sm_user_id'])){
	                $subsql .=" AND sm.sm_user_id = ".$_GET['sm_user_id'];
	                $record['search']['sm_user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['sm_user_id']]);
	            }
	            if(isset($_GET['from_qty'])){
	            	if($_GET['from_qty'] != ''){
	                	$subsql .=" AND sm.sm_total_qty >= ".$_GET['from_qty'];
	            	}
	            }
	            if(isset($_GET['to_qty'])){
	            	if($_GET['to_qty'] != ''){
	                	$subsql .=" AND sm.sm_total_qty <= ".$_GET['to_qty'];
	            	}
	            }
	            if(isset($_GET['from_bill_amt'])){
	            	if($_GET['from_bill_amt'] != ''){
	                	$subsql .=" AND sm.sm_final_amt >= ".$_GET['from_bill_amt'];
	            	}
	            }
	            if(isset($_GET['to_bill_amt'])){
	            	if($_GET['to_bill_amt'] != ''){
	                	$subsql .=" AND sm.sm_final_amt <= ".$_GET['to_bill_amt'];
	            	}
	            }
	            if(isset($_GET['sm_payment_mode']) && !empty($_GET['sm_payment_mode'])){
	                $subsql .=" AND sm.sm_payment_mode = '".$_GET['sm_payment_mode']."'";
	                $record['search']['sm_payment_mode'] = $this->Commonmdl->get_mode($_GET['sm_payment_mode']);
	            }
				$query ="
							SELECT sm.*, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, user.user_fullname
							FROM sales_master sm
							INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
							INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
							WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							ORDER BY sm.sm_id DESC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$total_qty 	= 0;
				$sub_amt 	= 0;
				$disc_amt 	= 0;
				$promo_amt 	= 0;
				$point_amt 	= 0;
				$off_amt 	= 0;
				$total_amt 	= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$total_qty 	= $total_qty + $value['sm_total_qty'];
						$sub_amt 	= $sub_amt + $value['sm_sub_total'];
						$disc_amt 	= $disc_amt + $value['sm_total_disc'];
						$promo_amt 	= $disc_amt + $value['sm_promo_disc'];
						$point_amt 	= $disc_amt + $value['sm_point_used'];
						$off_amt 	= $off_amt + $value['sm_round_off'];
						$total_amt 	= $total_amt + $value['sm_final_amt'];
					}
				}
				$record['totals']['total_qty'] 	= $total_qty;
				$record['totals']['sub_amt'] 	= $sub_amt;
				$record['totals']['disc_amt'] 	= $disc_amt;
				$record['totals']['promo_amt'] 	= $promo_amt;
				$record['totals']['point_amt'] 	= $point_amt;
				$record['totals']['off_amt'] 	= $off_amt;
				$record['totals']['total_amt'] 	= $total_amt;
				return $record;
			}
		/****************** SALES SUMMARY *********************/
		/****************** SALES RETURN SUMMARY **************/
			public function get_sales_return_summary(){
				$subsql 	= '';
				if(isset($_GET['srm_entry_no']) && !empty($_GET['srm_entry_no'])){
	                $subsql .=" AND srm.srm_id = ".$_GET['srm_entry_no'];
	                $record['search']['srm_entry_no'] = $this->SalesReturnmdl->get_entry_no(['srm_id' => $_GET['srm_entry_no']]);
	            }
	            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
					$subsql .= " AND srm.srm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
					$subsql .= " AND srm.srm_entry_date <= '".$to_date."'";
				}
	            if(isset($_GET['srm_acc_id']) && !empty($_GET['srm_acc_id'])){
	                $subsql .=" AND srm.srm_acc_id = ".$_GET['srm_acc_id'];
	                $record['search']['srm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['srm_acc_id']]);
	            }
	            if(isset($_GET['from_qty'])){
	            	if($_GET['from_qty'] != ''){
	                	$subsql .=" AND srm.srm_total_qty >= ".$_GET['from_qty'];
	            	}
	            }
	            if(isset($_GET['to_qty'])){
            	 	if($_GET['to_qty'] != ''){
            			$subsql .=" AND srm.srm_total_qty <= ".$_GET['to_qty'];

            	 	}
	            }
	            if(isset($_GET['from_bill_amt'])){
	            	if($_GET['from_bill_amt'] != ''){
	                	$subsql .=" AND srm.srm_final_amt >= ".$_GET['from_bill_amt'];
	            	}
	            }
	            if(isset($_GET['to_bill_amt'])){
	            	if($_GET['to_bill_amt'] != ''){
	                	$subsql .=" AND srm.srm_final_amt <= ".$_GET['to_bill_amt'];
	            	}
	            }
				$query ="
							SELECT srm.*, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
							FROM sales_return_master srm
							INNER JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
							WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
							AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							ORDER BY srm.srm_id DESC
						";
				$record['data'] = $this->db->query($query)->result_array();
				$total_qty 	= 0;
				$sub_amt 	= 0;
				$disc_amt 	= 0;
				$off_amt 	= 0;
				$total_amt 	= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$total_qty 	= $total_qty + $value['srm_total_qty'];
						$sub_amt 	= $sub_amt + $value['srm_sub_total'];
						$disc_amt 	= $disc_amt + $value['srm_total_disc'];
						$off_amt 	= $off_amt + $value['srm_round_off'];
						$total_amt 	= $total_amt + $value['srm_final_amt'];
					}
				}
				$record['totals']['total_qty'] 	= $total_qty;
				$record['totals']['sub_amt'] 	= $sub_amt;
				$record['totals']['disc_amt'] 	= $disc_amt;
				$record['totals']['off_amt'] 	= $off_amt;
				$record['totals']['total_amt'] 	= $total_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** SALES RETURN SUMMARY **************/
		/****************** SUPPLIER LEDGER *******************/
			public function get_supplier_ledger(){
				$subsql1 	= '';
				$subsql2 	= '';
				$subsql3 	= '';
				$from_date 	= $_SESSION['start_year'];
				$to_date 	= $_SESSION['end_year'];
				$account_id = 0;
				$record 	= [];
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql1 .=" AND pm.pm_acc_id = ".$_GET['acc_id'];
					$subsql2 .=" AND prm.prm_acc_id = ".$_GET['acc_id'];
					$subsql3 .=" AND vm.vm_party_id = ".$_GET['acc_id'];
					$account_id = $_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search_supplier(['account_id' => $_GET['acc_id']]);
				}else{
					$subsql1 .=" AND pm.pm_acc_id = 0";
					$subsql2 .=" AND prm.prm_acc_id = 0";
					$subsql3 .=" AND vm.vm_party_id = 0";
				}
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql1 .= " AND pm.pm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND prm.prm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}else{
					$subsql1 .= " AND pm.pm_bill_date >= '".$from_date."'";
					$subsql2 .= " AND prm.prm_entry_date >= '".$from_date."'";
					$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql1 .= " AND pm.pm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND prm.prm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}else{
					$subsql1 .= " AND pm.pm_bill_date <= '".$to_date."'";
					$subsql2 .= " AND prm.prm_entry_date <= '".$to_date."'";
					$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
				}
				$open_amt 				= $this->Accountmdl->get_opening_balance('SUPPLIER', $account_id, $from_date); 
				$record['open_amt'] 	= $open_amt;
				$amt_to_credit 			= $this->Purchasemdl->get_credit_balance($account_id, $from_date); 
				$record['amt_to_credit']= $amt_to_credit;
				$amt_to_debit 			= $this->PurchaseReturnmdl->get_debit_balance($account_id, $from_date); 
				$record['amt_to_debit'] = $amt_to_debit;
				$amt_credited 			= $this->Vouchermdl->get_credited_balance('SUPPLIER', $account_id, $from_date, false); 
				$record['amt_credited']	= $amt_credited;
				$amt_debited 			= $this->Vouchermdl->get_debited_balance('SUPPLIER', $account_id, $from_date, false); 
				$record['amt_debited']	= $amt_credited;

				$open_amt 				= ($open_amt + ($amt_to_credit - $amt_credited)) - ($amt_to_debit - $amt_debited);
				$close_amt				= 0;
				$open_label 			= $open_amt < 0 ? TO_RECEIVE : TO_PAY;
				$close_label 			= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
				$record['open_bal'] 	= abs($open_amt)." ".$open_label;
				$pur_query ="
							SELECT pm.pm_entry_no as entry_no, DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date, 
							pm.pm_final_amt as amt_to_credit, pm.pm_created_at as created_at,
							CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
							FROM purchase_master pm
							INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
							WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
							AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
							$subsql1
							ORDER BY pm.pm_created_at ASC
						";
				// echo "<pre>"; print_r($pur_query); exit;
				$pur_data = $this->db->query($pur_query)->result_array();
				if(!empty($pur_data)){
					foreach ($pur_data as $key => $value) {
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= 'PURCHASE';
						$record['data'][strtotime($value['created_at'])]['amt_to_debit']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= $value['amt_to_credit'];
						$record['data'][strtotime($value['created_at'])]['amt_credited'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}

				$return_query ="
							SELECT prm.prm_entry_no as entry_no, DATE_FORMAT(prm.prm_entry_date, '%d-%m-%Y') as entry_date, 
							prm.prm_final_amt as amt_to_debit, prm.prm_created_at as created_at,
							CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
							FROM purchase_return_master prm
							INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
							WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
							AND prm.prm_fin_year = '".$_SESSION['fin_year']."'
							$subsql2
							ORDER BY prm.prm_created_at ASC
						";
				// echo "<pre>"; print_r($return_query); exit;
				$return_data = $this->db->query($return_query)->result_array();
				if(!empty($return_data)){
					foreach ($return_data as $key => $value) {
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= 'PURCHASE RETURN';
						$record['data'][strtotime($value['created_at'])]['amt_to_credit'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_credited']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= $value['amt_to_debit'];
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}
				$voucher_query ="
							SELECT vm.vm_acc_id, vm.vm_entry_no as entry_no, DATE_FORMAT(vm.vm_entry_date, '%d-%m-%Y') as entry_date, 
							vm.vm_type as action, IF(vm.vm_type = 'RECEIPT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_debited,  
							IF(vm.vm_type = 'PAYMENT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_credited, vm.vm_created_at as created_at, 
							CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm.vm_group = 'SUPPLIER'
							$subsql3
							ORDER BY vm.vm_created_at ASC
						";
				// echo "<pre>"; print_r($voucher_query); exit;
				$voucher_data = $this->db->query($voucher_query)->result_array();
				if(!empty($voucher_data)){
					foreach ($voucher_data as $key => $value) {
						$amt_credited = 0;
						$amt_debited  = 0;
						if($value['vm_acc_id'] == CREDIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else if($value['vm_acc_id'] == DEBIT_NOTE){
							$amt_debited 	= $value['amt_credited'];
							$amt_credited 	= $value['amt_debited'];
						}else{
							$amt_debited 	= $value['amt_debited'];
							$amt_credited 	= $value['amt_credited'];
						}
						$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
						$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
						$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
						$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
						$record['data'][strtotime($value['created_at'])]['action'] 			= $value['action'];
						$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
						$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= $amt_debited;
						$record['data'][strtotime($value['created_at'])]['amt_credited']	= $amt_credited;
						$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
						$record['data'][strtotime($value['created_at'])]['label'] 			= '';
					}
				}
				if(!empty($record['data'])){
					usort($record['data'], function($a, $b){
						if ($a == $b) return 0;
			        	return ($a['created_at'] < $b['created_at']) ? -1 : 1;
					});
					$close_amt = $open_amt;
					foreach ($record['data'] as $key => $value) {
						$amt_to_debit 	= $value['amt_to_debit'];
						$amt_debited 	= $value['amt_debited'];
						$amt_to_credit 	= $value['amt_to_credit'];
						$amt_credited 	= $value['amt_credited'];
						$bal_amt 		= $close_amt + ($amt_to_credit - $amt_credited) - ($amt_to_debit - $amt_debited);
						$label 			= $bal_amt < 0 ? TO_RECEIVE : TO_PAY;
						$close_amt 		= $bal_amt;
						$bal_amt 		= abs($bal_amt);

						$record['data'][$key]['amt_to_debit'] 	= round($amt_to_debit, 2);
						$record['data'][$key]['amt_debited'] 	= round($amt_debited, 2);
						$record['data'][$key]['amt_to_credit'] 	= round($amt_to_credit, 2);
						$record['data'][$key]['amt_credited'] 	= round($amt_credited, 2);
						$record['data'][$key]['bal_amt'] 		= round($bal_amt, 2)." ".$label;
					}	
				}
				$close_label 		= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
				$record['close_bal']= abs($close_amt)." ".$close_label;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** SUPPLIER LEDGER *******************/
		/****************** SUPPLIER MIS **********************/
			public function get_supplier_mis(){
				$subsql 	= '';
				$subsql1 	= '';
				$subsql2 	= '';
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND pm.pm_acc_id = ".$_GET['acc_id'];
					$subsql1 .=" AND vm.vm_party_id = ".$_GET['acc_id'];
					$subsql2 .=" AND bm.bm_acc_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_search_supplier(['account_id' => $_GET['acc_id']]);
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, SUM(pm.pm_final_amt) as bill_amt
							FROM purchase_master pm
							INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
							WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
							AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							GROUP BY acc.account_id
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();

				$vquery ="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, SUM(vm.vm_total_amt) as voucher_amt
							FROM voucher_master vm
							INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
							AND vm.vm_type = 'PAYMENT'
							$subsql1
							GROUP BY acc.account_id
						";
				// echo "<pre>"; print_r($query); exit;
				$record['vdata'] = $this->db->query($vquery)->result_array();

				$squery ="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, 
							(SUM(bm.bm_st_rate) - SUM(bm.bm_st_disc)) as sale_amt
							FROM barcode_master bm
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND bm.bm_fin_year = '".$_SESSION['fin_year']."'
							AND bm.bm_delete_status = 0
							$subsql2
							GROUP BY acc.account_id
						";
				// echo "<pre>"; print_r($query); exit;
				$record['sdata'] = $this->db->query($squery)->result_array();

				// echo "<pre>"; print_r($record); exit;
				$bill_amt 		= 0;
				$voucher_amt 	= 0;
				$sale_amt 		= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$bill_amt 		= $bill_amt + $value['bill_amt'];
					}
				}
				if(!empty($record['vdata'])){
					foreach ($record['vdata'] as $key => $value) {
						$voucher_amt 	= $voucher_amt + $value['voucher_amt'];
					}
				}
				if(!empty($record['sdata'])){
					foreach ($record['sdata'] as $key => $value) {
						$sale_amt 	= $sale_amt + $value['sale_amt'];
					}
				}
				$record['totals']['bill_amt'] 		= $bill_amt;
				$record['totals']['voucher_amt'] 	= $voucher_amt;
				$record['totals']['sale_amt'] 		= $sale_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** SUPPLIER MIS **********************/
		/****************** SUPPLIER OUTSTANDING **************/
			public function get_supplier_outstanding(){
				$subsql 	= '';
				$having 	= '';
				if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
					$subsql .=" AND acc.account_id = ".$_GET['acc_id'];
					$record['search']['acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['acc_id']]);
				}
				if(isset($_GET['credit_frm'])){
					if($_GET['credit_frm'] != ''){
						$having .=" AND credit_amt >= ".$_GET['credit_frm'];
					}
				}
				if(isset($_GET['credit_to'])){
					if($_GET['credit_to'] != ''){
						$having .=" AND credit_amt <= ".$_GET['credit_to'];
					}
				}
				if(isset($_GET['credited_frm'])){
					if($_GET['credited_frm'] != ''){
						$having .=" AND credited_amt >= ".$_GET['credited_frm'];
					}
				}
				if(isset($_GET['credited_to'])){
					if($_GET['credited_to'] != ''){
						$having .=" AND credited_amt <= ".$_GET['credited_to'];
					}
				}
				if(isset($_GET['bal_frm'])){
					if($_GET['bal_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_frm'];
					}
				}else{
					$having .=" AND bal_amt >= 1";
				}
				if(isset($_GET['bal_to'])){
					if($_GET['bal_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_to'];
					}
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, acc.account_open_bal as open_amt,
							acc.account_drcr, acc.account_amt_to_credit as credit_amt, acc.account_amt_credited as credited_amt, 
							acc.account_amt_to_debit as debit_amt, acc.account_amt_debited as debited_amt,
							IF(acc.account_drcr = 'CR', ((acc.account_open_bal + (acc.account_amt_to_credit - acc.account_amt_credited)) - (acc.account_amt_to_debit - acc.account_amt_debited)), ((acc.account_open_bal + (acc.account_amt_to_debit - acc.account_amt_debited)) - (acc.account_amt_to_credit - acc.account_amt_credited))) as bal_amt
							FROM account_master acc
							WHERE acc.account_type = 'SUPPLIER'
							$subsql
							GROUP BY acc.account_id DESC
							HAVING 1
							$having
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$open_amt  		= 0;
				$credit_amt  	= 0;
				$credited_amt  	= 0;
				$debit_amt  	= 0;
				$debited_amt  	= 0;
				$bal_amt   		= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$open_amt 		= $open_amt + $value['open_amt'];
						$credit_amt 	= $credit_amt + $value['credit_amt'];
						$credited_amt 	= $credited_amt + $value['credited_amt'];
						$debit_amt 		= $debit_amt + $value['debit_amt'];
						$debited_amt 	= $debited_amt + $value['debited_amt'];
						if($value['account_drcr'] == 'DR'){
							$bal_amt 		= $bal_amt - $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_PAY : TO_RECEIVE;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}else{
							$bal_amt 		= $bal_amt + $value['bal_amt'];
							$label 			= $value['bal_amt'] < 0 ? TO_RECEIVE : TO_PAY;
							$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
						}
					}
				}
				$record['totals']['open_amt'] 		= $open_amt;
				$record['totals']['credit_amt'] 	= $credit_amt;
				$record['totals']['credited_amt'] 	= $credited_amt;
				$record['totals']['debit_amt'] 		= $debit_amt;
				$record['totals']['debited_amt'] 	= $debited_amt;
				$record['totals']['bal_amt'] 		= abs($bal_amt);
				$record['totals']['label'] 			= $bal_amt < 0 ? TO_RECEIVE : TO_PAY;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** SUPPLIER OUTSTANDING **************/
		/****************** TODAY SALE ************************/
			public function get_today_sale(){
				$record = [];
				$subsql 	= '';
				$having 	= '';
				$from_date 	= date('Y-m-d');
				$to_date 	= date('Y-m-d');
				if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
					$from_date = date('Y-m-d', strtotime($_GET['from_date']));
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}else{
					$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
				}
				if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
					$to_date = date('Y-m-d', strtotime($_GET['to_date']));
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}else{
					$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
				}
				if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
					$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
					$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
				}
				if(isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id'])){
					$subsql .=" AND bm.bm_style_id = ".$_GET['bm_style_id'];
					$record['search']['bm_style_id'] = $this->Stylemdl->get_search(['style_id' => $_GET['bm_style_id']]);
				}
				if(isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id'])){
					$subsql .=" AND bm.bm_design_id = ".$_GET['bm_design_id'];
					$record['search']['bm_design_id'] = $this->designmdl->get_search(['design_id' => $_GET['bm_design_id']]);
				}
				if(isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id'])){
					$subsql .=" AND bm.bm_brand_id = ".$_GET['bm_brand_id'];
					$record['search']['bm_brand_id'] = $this->Brandmdl->get_search(['brand_id' => $_GET['bm_brand_id']]);
				}
				if(isset($_GET['from_qty'])){
					if($_GET['from_qty'] != ''){
						$having .=" AND st_qty >= ".$_GET['from_qty'];
					}
				}
				if(isset($_GET['to_qty'])){
					if($_GET['to_qty'] != ''){
						$having .=" AND st_qty <= ".$_GET['to_qty'];
					}
				}
				if(isset($_GET['from_amt'])){
					if($_GET['from_amt'] != ''){
						$having .=" AND st_amt >= ".$_GET['from_amt'];
					}
				}
				if(isset($_GET['to_amt'])){
					if($_GET['to_amt'] != ''){
						$having .=" AND st_amt <= ".$_GET['to_amt'];
					}
				}
				$query ="
							SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name,
							UPPER(design.design_name) as design_name, UPPER(brand.brand_name) as brand_name, UPPER(style.style_name) as style_name,
							UPPER(age.age_name) as age_name, SUM(bm.bm_st_qty - bm.bm_srt_qty) as st_qty, 
							SUM((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_st_rate - bm.bm_st_disc)) as st_amt
							FROM barcode_master bm 
							INNER JOIN sales_master sm ON(sm.sm_id = bm.bm_sm_id)
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
							INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
							INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
							LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
							WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND bm.bm_fin_year = '".$_SESSION['fin_year']."'
							$subsql
							GROUP BY acc.account_id, design.design_id, brand.brand_id, style.style_id
							HAVING 1
							$having
							ORDER BY st_qty DESC, st_amt DESC
						";
				// echo "<pre>"; print_r($query); exit;
				$record['data'] = $this->db->query($query)->result_array();
				$st_qty  		= 0;
				$st_amt  		= 0;
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$st_qty 		= $st_qty + $value['st_qty'];
						$st_amt 		= $st_amt + $value['st_amt'];
					}
				}
				$record['totals']['st_qty'] 		= $st_qty;
				$record['totals']['st_amt'] 		= $st_amt;
				// echo "<pre>"; print_r($record); exit;
				return $record;
			}
		/****************** TODAY SALE ************************/
	}
?>