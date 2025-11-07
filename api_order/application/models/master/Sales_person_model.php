<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    require_once APPPATH . 'core/MY_Model.php';
    class sales_person_model extends my_model{
        public function __construct(){ parent::__construct(); }
        public function isExist($id){
            return false;
        }
        public function read($search, $args){   
            $where  = '';
            if(isset($search['sales_person_id']) && !empty($search['sales_person_id']))
                $where .= " AND user.user_id = '".$search['sales_person_id']."'";
            
            if(isset($search['sales_person_name']) && !empty($search['sales_person_name']))
                $where .= " AND user.user_fullname LIKE '%".$search['sales_person_name']."%'";
            
            $query="SELECT 
                    user.user_id as sales_person_id,
                    UPPER(user.user_fullname) as sales_person_name
                    FROM user_master user
                    WHERE user.user_status = 1
                    AND user.user_role='SALES'
                    $where
                    ORDER BY user.user_fullname ASC";
            if (isset($args['wantCount']) && $args['wantCount'] == true) 
                return $this->db->query($query)->num_rows();
            if (isset($args['limit']) && !empty($args['limit']))
                $query .= " LIMIT ".(int) $args['limit'];

            if (isset($args['offset']) && !empty($args['offset']))
                $query .= " OFFSET ".(int) $args['offset'];
            return $this->db->query($query)->result_array();
        }

        public function check_duplicate($id, $name){   
             $query="SELECT user_id
                    FROM user_master
                    WHERE user_fullname = '$name'
                    AND user_id != $id
                    LIMIT 1";
            return $this->db->query($query)->result_array();        
        }


}?>