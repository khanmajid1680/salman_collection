<?php $this->mypdf_class->tcpdf();
	global $yy;

	class MYPDF extends TCPDF {
		public function Header(){
			$this->SetFont('Helvetica', 'B', 8); 
			$header = "";
			
			$header .= '<table border="1" cellpadding="3">
                            <tr>
                                <td>
                                    <table border="0" cellpadding="5">
                                        <tr>
                                            <td width="20%" >'.strtoupper($_SESSION['user_branch']).'</td>
                                            <td width="60%" align="center" style="font-size:12px;"><b>ALTER REPORT</b></td>
                                            <td width="20%" align="right">'.date('d-m-Y H:i:s a').'</td>
                                        </tr>
                                    </table>		
                                </td>
                            </tr>	
                        </table>
                        <br/><br/>
						<table border="1" cellpadding="3" style="font-size:9px;">
							<tr>
								<th width="3%">#</th>
		                        <th width="5%">BILL NO</th>
		                        <th width="7%">BILL DATE</th>
		                        <th width="10%">SALES PERSON</th>
		                        <th width="13%">CUSTOMER</th>
		                        <th width="8%">BARCODE</th>
		                        <th width="8%">DESIGN</th>
		                        <th width="8%">STYLE</th>
		                        <th width="7%">BRAND</th>
		                        <th width="7%">TRIAL</th>
		                        <th width="7%">DL DATE</th>
		                        <th width="10%">STATUS</th>
		                        <th width="7%">TOTAL AMT</th>
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
	$pdf->SetTitle('ALTER REPORT');
	$pdf->SetSubject('ALTER REPORT');

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
        	$dispatch_date = (empty($value['st_dispatch_date'])) ? '' : date('d-m-Y',strtotime($value['st_dispatch_date']));

        	$status = '';
				 	if($value['st_alter_status']==1){
				 		$status = 'READY FOR DELIVERY';
				 	}
				 	if($value['st_alter_status']==2){
				 		$status = 'DELIVERED';
				 	}

            $body .= ' <tr>
							<td width="3%">'.($key + 1).'</td>
							<td width="5%">'. $value['sm_bill_no'].'</td>
							<td width="7%">'. date('d-m-Y', strtotime($value['sm_bill_date'])).'</td>
							<td width="10%">'. strtoupper($value['user_fullname']).'</td>
	                        <td width="13%">'. strtoupper($value['account_name']).'</td>
	                        <td width="8%">'. $value['bm_item_code'].'</td>
	                        <td width="8%">'. $value['design_name'].'</td>
	                        <td width="8%">'. $value['style_name'].'</td>
	                        <td width="7%">'. $value['brand_name'].'</td>
	                        <td width="7%">'. $value['trial'].'</td>
	                        <td width="7%">'. $dispatch_date.'</td>
	                        <td width="10%">'. $status.'</td>
	                        <td width="7%">'. $value['sm_final_amt'].'</td>
                    </tr>';
        endforeach;
       
    endif;
	$body .= '</table>';
	$pdf->writeHTML($body, true, false, false, false, '');
	// $pdf->writeHTMLCell(188, 150, 6, 134, $body, 0, 0, 0, true, 'L', true);
	$pdf->IncludeJS("print();");
	// ---------------------------------------------------------

	//Close and output PDF document
	ob_end_clean();
	$pdf->Output('ALTER REPORT.pdf', 'I');
	//============================================================+
	// END OF FILE
	//============================================================+
