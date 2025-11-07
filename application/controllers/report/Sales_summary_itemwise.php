<?php defined('BASEPATH') OR exit('No direct script access allowed');
class sales_summary_itemwise extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('report/sales_summary_itemwisemdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$this->load->view('pdfs/report/sales_summary_itemwise', $record);
			}else{
				$record['total_rows']	= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/sales_summary_itemwise', $record);
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
			$this->excel->getActiveSheet()->setTitle("SALES SUMMARY(ITEMWISE)");
			$this->excel->getActiveSheet()->setCellValue('A'.$line_no, "SALES SUMMARY(ITEMWISE)");			 
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
				 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, 'CUSTOMER');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('D'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, 'DESIGN');

				 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('E'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, 'STYLE');

				 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('F'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, 'BRAND');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('G'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, 'QTY');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('H'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, 'RATE');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('I'.$line_no)->getFont()->setBold( true );
				 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, 'SUB AMT');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('J'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, 'DISC AMT');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('K'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, 'TAXABLE AMT');
	 
				 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('L'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, 'SGST%');

				 $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('M'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('M'.$line_no, 'SGST AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('N'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('N'.$line_no, 'CGST%');

				 $this->excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('O'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('O'.$line_no, 'CGST AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('P'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('P'.$line_no, 'IGST%');

				 $this->excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('Q'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('Q'.$line_no, 'IGST AMT');

				 $this->excel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('R'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('R'.$line_no, 'TOTAL AMT');

				 $line_no++;
				 foreach ($record['data']['data'] as $key => $value) {
				 	
					 $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('A'.$line_no, $value['sm_bill_no']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('B'.$line_no, date('d-m-Y', strtotime($value['sm_bill_date'])));
	 
					 $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('C'.$line_no, strtoupper($value['account_name']));
	 
					 $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('D'.$line_no, strtoupper($value['design_name']));

					 $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('E'.$line_no, $value['style_name']);

					 $this->excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('F'.$line_no, $value['brand_name']);

					 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, $value['st_qty']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('H'.$line_no, $value['st_rate']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, $value['st_sub_total']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, $value['st_disc_amt']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
					 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, $value['st_taxable_amt']);
	 
					 $this->excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('L'.$line_no, $value['st_sgst_per']);

					 $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('M'.$line_no, $value['st_sgst_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('N'.$line_no, $value['st_cgst_per']);

					 $this->excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('O'.$line_no, $value['st_cgst_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('P'.$line_no, $value['st_igst_per']);

					 $this->excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('Q'.$line_no, $value['st_igst_amt']);

					 $this->excel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true); 
					 $this->excel->getActiveSheet()->SetCellValue('R'.$line_no, $value['st_sub_total_amt']);

					 $line_no++;
				 }
		
				 $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('G'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('G'.$line_no, $record['data']['totals']['total_qty']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('I'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('I'.$line_no, $record['data']['totals']['sub_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('J'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('J'.$line_no, $record['data']['totals']['disc_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('K'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('K'.$line_no, $record['data']['totals']['taxable_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('M'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('M'.$line_no, $record['data']['totals']['sgst_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('O'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('O'.$line_no, $record['data']['totals']['cgst_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('Q'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('Q'.$line_no, $record['data']['totals']['igst_amt']['total_amt']);

				 $this->excel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
				 $this->excel->getActiveSheet()->getStyle('R'.$line_no)->getFont()->setBold(true);	
				 $this->excel->getActiveSheet()->SetCellValue('R'.$line_no, $record['data']['totals']['total_amt']['total_amt']);
			 }

			 $filename='sales_summary_itemwise_'.time().'.xlsx';
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
