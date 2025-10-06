<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Loyaltymdl extends CI_model{
		protected $table;
		public function __construct(){
			parent::__construct();

			$this->table = 'loyalty_point_master';
		}
		public function used_loyalty_data($acc_id){
            $loyalty_point_query ="
                                    SELECT lpm.*
                                    FROM loyalty_point_master lpm 
                                    WHERE lpm.lpm_acc_id = $acc_id 
                                    AND (lpm.lpm_point - lpm.lpm_point_used) > 0 
                                    AND lpm.lpm_exp_date >= '".date('Y-m-d')."' 
                                    ORDER BY lpm.lpm_id ASC
                                ";
            return $this->db->query($loyalty_point_query)->result_array();
        }
	}
?>