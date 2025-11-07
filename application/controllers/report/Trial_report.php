<?php defined('BASEPATH') OR exit('No direct script access allowed');
class trial_report extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		$this->load->model('report/trial_report_model', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$this->load->view('pdfs/report/trial_report', $record);
			}else{
				$record['total_rows']	= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/trial_report', $record);
			}
		}else{
			redirect('login/logout');	
		}
	}

	public function excel(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record);die;
			$line_no= 1;

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle("Alter Report");
			$this->excel->getActiveSheet()->setCellValue('A'.$line_no, "Alter Report");			 
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
				 $this->excel->getActiveSheet()->SetCellValue('A'.$line_no, 'BILL NO');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('B'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('B'.$line_no, 'BILL DATE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('C'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, 'SALES PERSON');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('D'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, 'CUSTOMER');

				 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('E'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, 'BARCODE');

				 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('F'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, 'DESIGN');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('G'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, 'STYLE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('H'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, 'BRAND');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('I'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, 'TRIAL');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('J'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, 'DELIVERY DATE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('K'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, 'STATUS');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('L'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, 'TOTAL AMT');
	 
				 $line_no++;
				 foreach ($record['data']['data'] as $key => $value) {
				 	$dispatch_date = (empty($value['st_dispatch_date'])) ? '' : date('d-m-Y',strtotime($value['st_dispatch_date']));
				 	$status = '';
				 	if($value['st_alter_status']==1){
				 		$status = 'READY FOR DELIVERY';
				 	}
				 	if($value['st_alter_status']==2){
				 		$status = 'DELIVERED';
				 	}
					 $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('A'.$line_no, $value['sm_bill_no']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('B'.$line_no, date('d-m-Y', strtotime($value['sm_bill_date'])));
	 
					 $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, strtoupper($value['user_fullname']));
	 
					 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, strtoupper($value['account_name']));

					 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, $value['bm_item_code']);

					 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, $value['design_name']);

					 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, $value['style_name']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, $value['brand_name']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, $value['trial']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, $dispatch_date);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, $status);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, $value['sm_final_amt']);

					 $line_no++;
				 }
		
				 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('L'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, $record['data']['totals']['total_amt']);
				
			 }

			 $filename='Alter_report_'.time().'.xlsx';
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

	public function set_order_status($id,$status){
		
		if(empty($id) || $id<0){
			return ['msg' => '1. Order not found.'];
		}
		if($this->db_operations->data_update('sales_trans',['st_alter_status'=>$status], 'st_id', $id) < 1) return ['msg' => 'Order not updated.'];
		echo json_encode(['status' => TRUE, 'data' => $id,  'msg' => 'Order status updated successfully.']);
	}

}
?>
