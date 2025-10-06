<?php $this->mypdf_class->tcpdf();
	global $master_pdf;
	global $trans_pdf;
	global $yy;

	$master_pdf = $master_data;
	$trans_pdf = $trans_data;

	class MYPDF extends TCPDF 
	{
		public function Header()
		{
			global $master_pdf;
			$tbl_header = "";
			$name 		= '';
			$grp_name 	= '';
			$notes 		= '';

			$entry_no 	= $master_pdf[0]['prm_entry_no'];
			$entry_date	= $master_pdf[0]['prm_entry_date'];
			$acc_name 	= $master_pdf[0]['account_name'];
			$mode 		= '';
			$chq_no 	= '';
			$chq_date 	= '';
			$cheque 	= '';
			$title 		= 'SALMAN COLLECTION';
			$address 	= "";
			$print_type = 'PURCHASE RETURN';

			$this->SetFont('Helvetica', 'B', 8);
			$tbl_header .= <<<EOD
				<table border="0" cellpadding="3">
					<tr>
						<td align="center" style="font-size:12px;border:1px solid black;">
							<b>$title<br/></b>
							<b style="font-size:10px;border:1px solid black;">$address</b>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:11px;border:1px solid black;"><b>$print_type</b></td>
					</tr>
				</table>				
EOD;
			$tbl_header .= <<<EOD
				<table border="1" cellpadding="3" style="border:1px solid #000;">
					<tr>
						<td width="20%">
							<b>ENTRY NO</b> : $entry_no
						</td>			
						<td width="30%">
							<b>ENTRY DATE</b> : $entry_date
						</td>
						<td width="50%">
							<b>SUPPLIER</b> : $acc_name
						</td>
					</tr>
				</table>
				<table cellpadding="3" border="1" style="border:1px solid #000;">
					<tr>
						<th width="8%">BILL NO.</th>
						<th width="12%">BILL DATE</th>
						<th width="18%">DESIGN</th>
						<th width="15%">STYLE</th>
						<th width="11%">BRAND</th>
						<th width="8%">QTY</th>
						<th width="10%">RATE</th>
						<th width="8%">DISC</th>
						<th width="10%">SUB AMT</th>
					</tr>
				</table>			
EOD;
			$this->writeHTMLCell(148, 145, 6, 5, $tbl_header, 1, 0, 0, true, 'P', true);
			// $this->SetTopMargin(3);	
			$yy = $this->GetY();
			$yy = $yy + 24;
			// $this->line(21,$yy,21,160);
			// $this->line(35.6,$yy,35.6,160);
			// $this->line(49,$yy,49,160);
			// $this->line(103.7,$yy,103.7,160);
			// $this->line(130.3,$yy,130.3,160);
			$this->SetTopMargin($yy + 6);
		}

		public function Footer() 
    	{
    		global $master_pdf;
    		$tbl_footer 	= "";
    		$qty 			= round($master_pdf[0]['prm_total_qty'], 0); 
    		$sub_total 		= round($master_pdf[0]['prm_sub_total'], 2); 
    		$round_off 		= round($master_pdf[0]['prm_round_off'], 2); 
    		$bill_disc 		= round($master_pdf[0]['prm_bill_disc'], 2); 
    		$gst_amt 		= round($master_pdf[0]['prm_gst_amt'], 2); 
    		$final_amt 		= round($master_pdf[0]['prm_final_amt'], 2); 
    		$words			= number_to_word($final_amt);
    		$tbl_footer .= <<<EOD
    			<table  border="1"  cellpadding="3">
		  			<tr>
		    			<td width="64%" align="right"><b>TOTAL</b></td>												
						<td width="20%">$qty</td>											
						<td width="16%" align="right">$sub_total</td>												
		  			</tr>
		  			<tr>
		    			<td width="84%" align="right">ROUND OFF</td>
		    			<td width="16%" align="right">$round_off</td>
		  			</tr>
		  			<tr>
		    			<td width="84%" align="right">DISC AMT</td>
		    			<td width="16%" align="right">$bill_disc</td>
		  			</tr>
		  			<tr>
		    			<td width="84%" align="right">GST AMT</td>
		    			<td width="16%" align="right">$gst_amt</td>
		  			</tr>
		  			<tr>
		    			<td width="84%" align="right" style="font-weight:bold;">FINAL AMT</td>
		    			<td width="16%" align="right" style="font-weight:bold;">$final_amt</td>
		  			</tr>
		  			<tr>
		    			<td width="100%">Words : $words</td>
		  			</tr>
		  			<tr>
		    			<td width="100%" align="center"><b></b><br><br>SALMAN COLLECTION<br>Authorised Signatory</td>
		  			</tr>
		  			
		  		</table>
EOD;
		  	$this->writeHTMLCell(148, 150, 6, 150, $tbl_footer, 0, 0, 0, true, 'P', true);
        	// Set font
        	$this->SetFont('helvetica', 'I', 8);
        	// Page number
        	$this->Cell(0, 90, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    	}
	}

	// create new PDF document
	$pdf = new MYPDF('P', PDF_UNIT, array(160, 200), true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Imran Khan');
	$pdf->SetTitle('Purchase Return Pdf');
	$pdf->SetSubject('Purchase Return Pdf');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(6, 38, 6);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(74);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 55);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
	{
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}


	$pdf->SetFont('Helvetica', '', 8);
	$tbl = "";
	$pdf->AddPage('P');

	$tbl .= <<<EOD
			<table border="1" cellpadding="5">
EOD;
			foreach ($trans_pdf as $key => $value) 
			{
				$entry_no 		= $value['prt_bill_no'];
				$entry_date 	= $value['prt_bill_date'];
				$design_name 	= $value['design_name'];
				$style_name 	= $value['style_name'];
				$brand_name 	= $value['brand_name'];
				$qty 			= round($value['prt_qty'], 0);
				$rate 			= round($value['prt_rate'], 2);
				$disc 			= round($value['prt_disc'], 2);
				$sub_total 		= round($value['prt_sub_total'], 2);
				$tbl .= <<<EOD
					<tr >
						<td width="8%">$entry_no</td>
						<td width="12%">$entry_date</td>
						<td width="18%">$design_name</td>
						<td width="15%">$style_name</td>
						<td width="11%">$brand_name</td>
						<td width="8%">$qty</td>
						<td width="10%">$rate</td>
						<td width="8%">$disc</td>
						<td width="10%" align="right">$sub_total</td>
					</tr>

EOD;
			}

	$tbl .= <<<EOD
			</table>			
EOD;
	$pdf->writeHTML($tbl, true, false, false, false, '');
	// $pdf->writeHTMLCell(188, 150, 6, 134, $tbl, 0, 0, 0, true, 'P', true);
	$pdf->IncludeJS("print();");
	// ---------------------------------------------------------

	//Close and output PDF document
	ob_end_clean();
	$pdf->Output('Purchase Return.pdf', 'I');
	//============================================================+
	// END OF FILE
	//============================================================+
