<?php $this->mypdf_class->tcpdf();
	global $yy;

	class MYPDF extends TCPDF {
		public function Header(){
			$this->SetFont('Helvetica', 'B', 8); 
			$header = "";
			$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y');
			$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y');
			$_bill_no 	= (isset($_GET['_bill_no']) && $_GET['_bill_no'] != '') ? $_GET['_bill_no'] : "";

			$header .= '<table border="1" cellpadding="3">
                            <tr>
                                <td>
                                    <table border="0" cellpadding="5">
                                        <tr>
                                            <td width="20%" >BILL DATE : '.$from_date.' '.$to_date.'</td>
                                            <td width="15%" align="left"><b>BILL NO</b> '.$_bill_no.'</td>
                                            <td width="50%" align="left" style="font-size:12px;"><b>BILLWISE PROFIT</b></td>
                                            <td width="15%" align="right">'.date('d-m-Y H:i:s a').'</td>
                                        </tr>
                                    </table>		
                                </td>
                            </tr>	
                        </table>
                        <br/><br/>
						<table border="1" cellpadding="3" style="font-size:9px;">
							<tr>
								<th width="3%">#</th>
		                        <th width="8%">BILL DATE</th>
		                        <th width="6%">BILL NO</th>
		                        <th width="11%">SP NAME</th>
		                        <th width="8%">SOLD QTY</th>
		                        <th width="10%">SALE AMT</th>
		                        <th width="10%">DISC AMT</th>
		                        <th width="12%">ACT SALE AMT</th>
		                        <th width="10%">PUR AMT</th>
		                        <th width="12%">PROFIT</th>
		                        <th width="10%"></th>
							</tr>
						</table>';
			
			$this->writeHTMLCell(287, 195, 5, 5, $header, 0, 0, 0, true, 'L', true);
			// $this->SetTopMargin(3);	
			$yy = $this->GetY();
			$yy = $yy + 18.5;
			$this->SetTopMargin($yy + 0);
		}

		public function Footer(){
    		$footer = "";
    		$this->writeHTMLCell(200, 195, 5, 150, $footer, 0, 0, 0, true, 'L', true);
        	// Set font
        	$this->SetFont('helvetica', 'I', 8);
        	// Page number
        	$this->Cell(175, 110, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    	}
	}

	// create new PDF document
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Imran Khan');
	$pdf->SetTitle('BILL WISE PROFIT REPORT');
	$pdf->SetSubject('BILL WISE PROFIT REPORT');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(5, 0, 5);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(74);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 16);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
	{
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}

	$pdf->SetFont('Helvetica', '', 8);
	$body = "";
	$pdf->AddPage('L');
	$body .= '<table border="1" cellpadding="5">';
    if(!empty($data['data'])):
        foreach ($data['data'] as $key => $value):
            $body .= ' <tr>
							<td width="3%">'.($key + 1).'</td>
							<td width="8%">'.$value['entry_date'].'</td>
							<td width="6%">'.$value['entry_no'].'</td>
							<td width="11%">'.$value['user_fullname'].'</td>
							<td width="8%">'.$value['st_qty'].'</td>
							<td width="10%">'.$value['st_rate'].'</td>
							<td width="10%">'.$value['st_disc'].'</td>
							<td width="12%">'.$value['st_amt'].'</td>
							<td width="10%">'.$value['pt_amt'].'</td>
							<td width="12%">'.$value['profit'].'</td>
							<td width="10%"></td>
                    </tr>';
        endforeach;
        $body .= ' <tr style="font-weight:bold;">
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td>'. $data['totals']['st_qty'].'</td>
	                        <td>'. $data['totals']['st_rate'].'</td>
	                        <td>'. $data['totals']['st_disc'].'</td>
	                        <td>'. $data['totals']['st_amt'].'</td>
	                        <td>'. $data['totals']['pt_amt'].'</td>
	                        <td>'. $data['totals']['profit'].'</td>
	                        <td>TOTALS</td>
                    </tr>';
        if(!empty($data['totals']['expense'])):            
        	$body .= ' <tr style="font-weight:bold;">
							<td colspan="9"></td>
                            <td >'.$data['totals']['expense'].'</td>
                            <td >EXPENSE</td>
                    </tr>';
     	endif; 

     	if(!empty($data['totals']['srt_amt'])):            
        	$body .= ' <tr style="font-weight:bold;">
							<td colspan="9"></td>
                            <td >'.$data['totals']['srt_amt'].'</td>
                            <td >SALES RET</td>
                    </tr>';
     	endif;

     	$body .= ' <tr style="font-weight:bold;">
					<td colspan="9"></td>
                    <td >'.$data['totals']['profit_loss'].'</td>
                    <td >ACT. PROFIT</td>
				</tr>';               
                                
    endif;
	$body .= '</table>';
	$pdf->writeHTML($body, true, false, false, false, '');
	// $pdf->writeHTMLCell(188, 150, 6, 134, $body, 0, 0, 0, true, 'L', true);
	$pdf->IncludeJS("print();");
	// ---------------------------------------------------------

	//Close and output PDF document
	ob_end_clean();
	$pdf->Output('BILL WISE PROFIT REPORT.pdf', 'I');
	//============================================================+
	// END OF FILE
	//============================================================+
