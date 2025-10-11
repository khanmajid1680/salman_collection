<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Ca_reportmdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Usermdl');
			$this->load->model('sales/Salesmdl');
		}
		public function get_data(){ 
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
           
            
			$query = "
			    SELECT 
			        DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') AS entry_date,
			        sm.sm_bill_no as bill_no,

			       -- Local Sales 5%
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_cgst_per = 2.5 AND st.st_sgst_per = 2.5 
				                THEN st.st_taxable_amt 
				                ELSE 0 
				            END
				        ) AS ms_net_5,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_cgst_per = 2.5 
				                THEN ROUND(st.st_taxable_amt * st.st_cgst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS cgst_25,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_sgst_per = 2.5 
				                THEN ROUND(st.st_taxable_amt * st.st_sgst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS sgst_25,

				        sm.sm_bill_no,
				        0 AS cash_amt,

				        -- Local Sales 18%
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_cgst_per = 9 AND st.st_sgst_per = 9 
				                THEN st.st_taxable_amt 
				                ELSE 0 
				            END
				        ) AS ms_net_18,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_cgst_per = 9 
				                THEN ROUND(st.st_taxable_amt * st.st_cgst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS cgst_9,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 0 AND st.st_sgst_per = 9 
				                THEN ROUND(st.st_taxable_amt * st.st_sgst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS sgst_9,

				        -- OMS (Out of State) 5%
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 1 AND st.st_igst_per = 5 
				                THEN st.st_taxable_amt 
				                ELSE 0 
				            END
				        ) AS oms_net_5,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 1 AND st.st_igst_per = 5 
				                THEN ROUND(st.st_taxable_amt * st.st_igst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS igst_5,

				        -- OMS (Out of State) 18%
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 1 AND st.st_igst_per = 18 
				                THEN st.st_taxable_amt 
				                ELSE 0 
				            END
				        ) AS oms_net_18,
				        SUM(
				            CASE 
				                WHEN sm.sm_gst_type = 1 AND st.st_igst_per = 18 
				                THEN ROUND(st.st_taxable_amt * st.st_igst_per / 100, 2)
				                ELSE 0 
				            END
				        ) AS igst_18,

			        -- Total
			        SUM(st.st_sub_total_amt) AS total_amt

			    FROM sales_trans st 
			    INNER JOIN sales_master sm ON sm.sm_id = st.st_sm_id
			    INNER JOIN account_master acc ON acc.account_id = sm.sm_acc_id
			    WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
			      AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
			      AND sm.sm_with_gst=1
			      $subsql
			    GROUP BY sm.sm_id
			    ORDER BY sm.sm_id DESC
			";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$ms_net_5 	= 0;
			$cgst_25 	= 0;
			$sgst_25 	= 0;
			$cash_amt = 0;
			$ms_net_18 	= 0;
			$cgst_9 	= 0;
			$sgst_9 	= 0;
			$oms_net_5 	= 0;
			$igst_5 	= 0;

			$oms_net_18 = 0;
			$igst_18 	= 0;
			$total_amt 	= 0;



			if(!empty($record['data'])){ 
				foreach ($record['data'] as $key => $value) {
					$ms_net_5 	= $ms_net_5 + $value['ms_net_5'];
					$cgst_25 	= $cgst_25 + $value['cgst_25'];
					$sgst_25 	= $sgst_25 + $value['sgst_25'];
					$cash_amt = $cash_amt + $value['cash_amt'];
					$ms_net_18 	= $ms_net_18 	+ $value['ms_net_18'];
					$cgst_9 	= $cgst_9 + $value['cgst_9'];
					$sgst_9 	= $sgst_9 + $value['sgst_9'];
					$oms_net_5 	= $oms_net_5 + $value['oms_net_5'];
					$igst_5 	= $igst_5 + $value['igst_5'];

					$oms_net_18 = $oms_net_18 + $value['oms_net_18'];
					$igst_18 	= $igst_18 + $value['igst_18'];
					$total_amt 	= $total_amt + $value['total_amt'];

				}
			}
			$record['totals']['ms_net_5'] 	= $ms_net_5;
			$record['totals']['cgst_25'] 	= $cgst_25;
			$record['totals']['sgst_25'] 	= $sgst_25;
			$record['totals']['cash_amt'] 	= $cash_amt;
			$record['totals']['ms_net_18'] 	= $ms_net_18;

			$record['totals']['cgst_9'] 	= $cgst_9;
			$record['totals']['sgst_9'] 	= $sgst_9;
			$record['totals']['oms_net_5'] 	= $oms_net_5;
			$record['totals']['igst_5'] 	= $igst_5;

			$record['totals']['oms_net_18'] = $oms_net_18;
			$record['totals']['igst_18'] 	= $igst_18;
			$record['totals']['total_amt'] 	= $total_amt;

			return $record;
		}
	}
?>