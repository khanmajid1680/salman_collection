<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Barcode_stock extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('report/Barcode_stockmdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	

		if(sessionExist()){
			$config 				= array();
            $config 				= $this->config->item('pagination');
            $config['total_rows'] 	= $this->model->get_data(true,false);
            $config['base_url'] 	= base_url('report/barcode_stock?search=true');
    
            // foreach ($_GET as $key => $value){
            //     if($key != 'search' && $key != 'offset'){
            //         $config['base_url'] .= "&" . $key . "=" .$value;
            //     }
            // }
    
            $offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
            $this->pagination->initialize($config);
            $record['count']		= $offset;
            $record['total_rows'] 	= $config['total_rows'];
            $record['data']			= $this->model->get_data(false,false, $config['per_page'], $offset);
			
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$record['data'] = $this->model->get_data(true,true);
				$this->load->view('pdfs/report/barcode_stock', $record);
			}else{
				// $record['total_rows']= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/barcode_stock', $record);
			}
		}else{
			redirect('login/logout');	
		}
	}

	public function get_scroll_data(){
		$record = $this->model->get_data(false,true);
		echo json_encode(['status'=>TRUE,'data'=>$record['data'],'msg'=>'data fetched successfully.']);
	}
	public function update_barcode($id){
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
		$prev_data = $this->db_operations->get_record('barcode_master', ['bm_id' => $id]);
		// echo "<pre>"; print_r($prev_data); exit;
		if(empty($prev_data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not found.']);
			return;	
		}
		$post_data['bm_token_check'] = (isset($post_data['bm_token_check'])) ? 1 : 0;
		if($this->db_operations->data_update('barcode_master', $post_data, 'bm_id', $id) < 1){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not updated.']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [],  'msg' => 'Barcode update successfully.']);
	}

	public function update_multiple_barcode(){  
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
		if(empty($post_data['barcode_id'])){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode Not checked']);
			return;
		}

		$this->db->trans_begin();
		$ids ='';
		foreach ($post_data['barcode_id'] as $key => $value) {
			$prev_data = $this->db_operations->get_record('barcode_master', ['bm_id' => $key]);
			// echo "<pre>"; print_r($prev_data); exit;
			if(empty($prev_data)){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not found.']);
				return;	
			} 
			$barcode_master['bm_token_check'] 	= (isset($post_data['bm_token_check'])) ? 1 : 0;
			$barcode_master['bm_token_amt'] 	= $post_data['bm_token_amt'];
			$barcode_master['bm_sp_amt'] 		= $post_data['bm_sp_amt'];
			if($this->db_operations->data_update('barcode_master', $barcode_master, 'bm_id', $key) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not updated.']);
				return;	
			}

			$ids .= $key . ',';
		}
		
		$ids = rtrim($ids, ',');
		if ($this->db->trans_status() === FALSE){ 
		    $this->db->trans_rollback();
		    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
			return;
	    }
	    $this->db->trans_commit();
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $ids,  'msg' => 'Barcode update successfully.']);
	}
	public function get_data(){ 
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_data();
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}

	public function excel(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record);die;
			$line_no= 1;

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle("BARCODE STOCK");
			$this->excel->getActiveSheet()->setCellValue('A'.$line_no, "BARCODE STOCK)");			 
			$this->excel->getActiveSheet()->mergeCells('A'.$line_no.":M".$line_no);			 
			$this->excel->getActiveSheet()->getStyle('A'.$line_no.":M".$line_no)->getFont()->setBold( true );	
		 	$this->excel->getActiveSheet()->getStyle('A'.$line_no.":M".$line_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		 	$this->excel->getActiveSheet()->setCellValue('N'.$line_no, date('d-m-Y H:i:s'));			 
			$this->excel->getActiveSheet()->mergeCells('N'.$line_no.":P".$line_no);			 
			$this->excel->getActiveSheet()->getStyle('N'.$line_no.":P".$line_no)->getFont()->setBold( true );	
		 	$this->excel->getActiveSheet()->getStyle('N'.$line_no.":P".$line_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			 $line_no++;
			 if(!empty($record['data']['data'])){ 
				 $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('A'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('A'.$line_no, 'BARCODE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('B'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('B'.$line_no, 'SUPPLIER');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('C'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, 'style_name');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('D'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, 'DESIGN');

				 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('E'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, 'BRAND');

				 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('F'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, 'PUR QTY');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('G'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, 'PUR RATE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('H'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, 'PUR AMT');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('I'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, 'PUR RET QTY');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('J'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, 'SALE QTY');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('K'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, 'SALE RATE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('L'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, 'SALE AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('M'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('M'.$line_no, 'SALE RET QTY');

				 $this->excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('N'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('N'.$line_no, 'BAL QTY');

				 $this->excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('O'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('O'.$line_no, 'BAL AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('P'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('P'.$line_no, 'PROFIT AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('Q'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('Q'.$line_no, 'TOKEN');

				 $line_no++;
				 foreach ($record['data']['data'] as $key => $value) {
				 	
					 $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('A'.$line_no, $value['bm_item_code']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('B'.$line_no,$value['account_code']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, strtoupper($value['style_name']));
	 
					 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, strtoupper($value['design_name']));

					 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, $value['brand_name']);

					 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, $value['pt_qty']);

					 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, $value['pt_rate']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, $value['pt_amt']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, $value['prt_qty']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, $value['st_qty']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, $value['st_rate']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, $value['st_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('M'.$line_no, $value['srt_qty']);

					 $this->excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('N'.$line_no, $value['bal_qty']);

					 $this->excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('O'.$line_no, $value['bal_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('P'.$line_no, $value['profit_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('Q'.$line_no, $value['token']);


					 $line_no++;
				 }
		
				 // $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				 // $this->excel->getActiveSheet()->getStyle('G'.$line_no)->getFont()->setBold(true);	
				 // $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, $record['data']['totals']['total_qty']['total_amt']);

				
				
			 }

			 $filename='Barcode_stock_'.time().'.xlsx';
			 header('Content-Type: application/vnd.ms-excel');
			 header('Content-Disposition: attachment;filename="'.$filename.'"');
			 header('Cache-Control: max-age=0');
			 $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
			 $objWriter->save('php://output');
		 	return;	
		}else{
			redirect('login/logout');	
		}
	}
}
?>
