<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    require_once APPPATH . 'core/MY_Model.php';
    class customer_model extends my_model{
        public function __construct(){ parent::__construct(); }
        public function isExist($id){
            return false;
        }
        public function read($search, $args){   
            $where  = '';
            if(isset($search['customer_id']) && !empty($search['customer_id']))
                $where .= " AND account.account_id = '".$search['customer_id']."'";
            
            if(isset($search['customer_name']) && !empty($search['customer_name']))
                $where .= " AND account.account_name LIKE '%".$search['customer_name']."%'";

            if(isset($search['customer_mobile']) && !empty($search['customer_mobile']))
                $where .= " AND account.account_mobile LIKE '%".$search['customer_mobile']."%'";
            
            $query="SELECT 
                    account.account_id as account_id,
                    UPPER(account.account_name) as account_name,
                    account.account_mobile as account_mobile
                    FROM account_master account
                    WHERE account.account_status = 1
                    AND account.account_type='CUSTOMER'
                    $where
                    ORDER BY account.account_name ASC";
            if (isset($args['wantCount']) && $args['wantCount'] == true) 
                return $this->db->query($query)->num_rows();
            if (isset($args['limit']) && !empty($args['limit'])) 
                $query .= " LIMIT ".(int) $args['limit'];

            if (isset($args['offset']) && !empty($args['offset']))
                $query .= " OFFSET ".(int) $args['offset'];
            return $this->db->query($query)->result_array();
        }
   
        public function check_duplicate($id, $name){   
             $query="SELECT customer_id
                    FROM customer_master
                    WHERE customer_mobile = '$name'
                    AND customer_id != $id
                    LIMIT 1";
            return $this->db->query($query)->result_array();        
        }       
    }
?>