<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    require_once APPPATH . 'core/MY_Model.php';
    class transport_model extends my_model{
        public function __construct(){ parent::__construct(); }
        public function isExist($id){
            return false;
        }
        public function read($search, $args){   
            $where  = '';
            if(isset($search['transport_id']) && !empty($search['transport_id']))
                $where .= " AND transport.transport_id = '".$search['transport_id']."'";
            
            if(isset($search['transport_name']) && !empty($search['transport_name']))
                $where .= " AND transport.transport_name LIKE '%".$search['transport_name']."%'";
            
            $query="SELECT 
                    transport.transport_id as transport_id,
                    UPPER(transport.transport_name) as transport_name
                    FROM transport_master transport
                    WHERE transport.transport_status = 1
                    $where
                    ORDER BY transport.transport_name ASC";
            if (isset($args['wantCount']) && $args['wantCount'] == true) 
                return $this->db->query($query)->num_rows();
            if (isset($args['limit']) && !empty($args['limit']))
                $query .= " LIMIT ".(int) $args['limit'];

            if (isset($args['offset']) && !empty($args['offset']))
                $query .= " OFFSET ".(int) $args['offset'];
            return $this->db->query($query)->result_array();
        }

        public function check_duplicate($id, $name){   
             $query="SELECT transport_id
                    FROM transport_master
                    WHERE transport_name = '$name'
                    AND transport_id != $id
                    LIMIT 1";
            return $this->db->query($query)->result_array();        
        }       
    }
?>