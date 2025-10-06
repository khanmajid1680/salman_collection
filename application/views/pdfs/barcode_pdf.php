<?php
// Include the main TCPDF library (search for installation path).
$this->mypdf_class->tcpdf();

global $print_array ; 
global $barcode;
global $params;
global $brand_name;
global $design_name;
global $cp_code;
global $sp_amt;
global $mrp;
global $acc_code;
global $entry_no;
global $serial_no;



$print_array = $barcode_data;
class MYPDF extends TCPDF 
{
    //Page header
    public function Header() 
    {
		
    }

    // Page footer
    public function Footer() 
    {	
    	$this->SetY(2);

    	global $print_array ;
    	global $barcode;
		global $params;
		global $design_name;
		global $brand_name;
		global $cp_code;
		global $sp_amt;
		global $mrp;

		global $acc_code;
		global $entry_no;
		global $serial_no;

		// echo $vend_code;
			if($mrp>$sp_amt){
				// $mrp = '<del>'.$mrp.'</del>';
				$label_width = '35%';
				$mrp_div = '<td width="30%" style="text-align:left;font-size:11px;"><b><del>'.$mrp.'</del></b></td>';
				$code_align ='center';
			}else{
				$label_width = '70%';
				$mrp_div = '';
				$code_align ='left';

			}


		$footer_tbl = "";
		
			$footer_tbl .= <<<EOD
				<table border="0">
					<tr>
						<td width="100%" style="text-align:center;font-size:10px;"><b>SALMAN COLLECTION</b></td>
					</tr>										
					<tr>
						<td width="$label_width" style="text-align:left;font-size:9px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$design_name</b></td>
						<td width="35%" style="font-size:9px;text-align:$code_align;"><b>$acc_code</b></td>
						$mrp_div
					</tr>					
					<tr>
						<td width="35%" style="text-align:left;font-size:9px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$cp_code</b></td>
						<td width="35%" style="text-align:center;font-size:9px;"><b>$entry_no - $serial_no</b></td>
						<td width="30%" style="text-align:left;font-size:11px;"><b>$sp_amt</b></td>
					</tr>					
					<tr >
						<td width="100%" style="font-size:5px;"><tcpdf method="write1DBarcode" params="$params" /><br/><br/><br/></td>
					</tr>
					<tr >
						<td width="100%" style="text-align:center;font-size:11px;"><b>$barcode</b></td>
					</tr>
				</table>
				
EOD;
		
		$this->writeHTML($footer_tbl, true, false, false, false, '');
    }
}

// $page_size = array('38','34');
$page_size = array('25','50');

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT,$page_size, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Imran Khan');
$pdf->SetTitle('Barcode Pdf');
$pdf->SetSubject('Barcode Pdf');
// $pdf->SetFont('helvetica', '', 10);
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(0, 0, 0);
$pdf->SetHeaderMargin(4);
$pdf->SetFooterMargin(12);

// $pdf->SetMargins(PDF_MARGIN_LEFT- 0, PDF_MARGIN_TOP-29, PDF_MARGIN_RIGHT-16);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}



$array_cnt = count($print_array);

// echo "<pre>"; print_r($print_array); exit();


for($i=0; $i<$array_cnt; $i++)
{
	$pdf->AddPage();
	$barcode 				= $print_array[$i]['bm_item_code'];
	$brand_name 			= strtoupper(substr($print_array[$i]['brand_name'], 0, 68));
	$design_name 			= strtoupper(substr($print_array[$i]['design_name'], 0, 36));
	$sp_amt 				= round($print_array[$i]['bm_sp_amt'], 2);
	$mrp 				= round($print_array[$i]['bm_mrp'], 2);

	$cp_code 				= strtoupper($print_array[$i]['bm_cp_code']);
	$acc_code 				= strtoupper($print_array[$i]['account_code']);
	$entry_no 				= $print_array[$i]['pm_entry_no'];
	$serial_no 				= $print_array[$i]['pt_serial_no'];
	
	$options['bgcolor'] 	= array(255,255,255);
	$options['border'] 		= false;
	$options['fgcolor'] 	= array(0,0,0);
	$options['font'] 		= 'helvetica';
	$options['fontsize'] 	= 6;
	$options['padding'] 	= 1;
	$options['position'] 	= 'C';
	$options['stretchtext'] = 2;
	$options['text'] 		= false;

	$params = $pdf->serializeTCPDFtagParameters(array($barcode, 'I25', '', '', 45, 8, 0.8, $options, 'S'));
	
}
// ---------------------------------------------------------


// note


// first declare all variable global becouse they can inherited by any class function

// then set page size 24,42 if landscape mode first para is height and second width

// if u increase page height then set value of setfootermargin 
// ---------------------------------------------------------
// $pdf->IncludeJS("print();");
//Close and output PDF document
$pdf->Output('Barcode.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+