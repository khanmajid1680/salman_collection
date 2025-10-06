<?php 
    $this->mypdf_class->tcpdf();
	global $master_pdf;
	global $trans_pdf;
	global $yy;

	$master_pdf = $master_data;
	$trans_pdf  = $trans_data;

	class MYPDF extends TCPDF {
		public function Header(){
			global $master_pdf;
            $branch_name        = strtoupper($_SESSION['user_branch']);
            $date_time 		    = date('d-m-Y h:i:s a');
            $title 	            = 'SALMAN COLLECTION';
			$print_type 	    = 'PURCHASE';

			$entry_no 			= $master_pdf[0]['pm_entry_no'];
            $entry_date			= $master_pdf[0]['pm_entry_date'];
			$bill_no 			= $master_pdf[0]['pm_bill_no'];
			$bill_date 			= $master_pdf[0]['pm_bill_date'];
			$supplier_code		= $master_pdf[0]['supplier_code'];
			$this->SetFont('Times', '', 9);
			$tbl_header='<table border="0" cellpadding="3">
                            <tr>
                                <td width="25%" style="font-size:12px;" ><b>'.$branch_name.'</b></td>
                                <td width="50%" style="font-size:14px;" align="center"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$print_type.'</b></td>
                                <td width="25%" style="font-size:12px;" ><b>'.$date_time.'</b></td>
                            </tr>
                        </table>				
                        <table border="0" cellpadding="3">
                            <tr>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:1px solid #000;">
                                    <b>ENTRY NO</b>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$entry_no.'
                                </td>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000;">
                                    <b>ENTRY DATE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$entry_date.'
                                </td>
                            </tr>
							<tr>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:1px solid #000;">
                                    <b>BILL NO</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bill_no.'
                                </td>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000;">
                                    <b>P.O NO</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bill_date.'
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:1px solid #000;">
                                    <b>SUPPLIER</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$supplier_code.'
                                </td>
                                <td width="50%" style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                            </tr>
                        </table>
                        <table cellpadding="5" border="0" style="font-weight: bold;">
                            <tr>
                                <th width="3%" 	style="border-bottom: 1px dashed #000;">#</th>
                                <th width="17%"  style="border-bottom: 1px dashed #000;">DESIGN</th>
                                <th width="15%" style="border-bottom: 1px dashed #000;">STYLE</th>
                                <th width="9%"  style="border-bottom: 1px dashed #000;">BRAND</th>
                                <th width="10%" style="border-bottom: 1px dashed #000;">DESCRIPTION</th>
                                <th width="6%"  style="border-bottom: 1px dashed #000; text-align:center;">QTY</th>
                                <th width="7%"  style="border-bottom: 1px dashed #000; text-align:right;">RATE</th>
                                <th width="7%"  style="border-bottom: 1px dashed #000; text-align:right;">SP RATE</th>
                                <th width="7%"  style="border-bottom: 1px dashed #000; text-align:right;">SUB AMT</th>
                                <th width="10%" style="border-bottom: 1px dashed #000; text-align:right;">DISC AMT (%)</th>
                                <th width="9%"  style="border-bottom: 1px dashed #000; text-align:right;">TOTAL AMT</th>
                            </tr>
                        </table>';
			$this->writeHTMLCell(285, 280, 6, 6, $tbl_header, 0, 0, 0, true, 'L', true);
			// $this->SetTopMargin(3);	
			$yy = $this->GetY();
			$yy = $yy + 0;
			// $this->line(16,$yy,16,280);
			// $this->line(96,$yy,96,280);
			// $this->line(176,$yy,176,280);
			// $this->line(166,$yy,166,280);
			$this->SetTopMargin($yy + 30.5);
		}

		public function Footer(){
    		global $master_pdf;
    		$qty 			= round($master_pdf[0]['pm_total_qty'], 0); 
    		$sub_total 		= $master_pdf[0]['pm_sub_total']; 
    		$round_off 		= $master_pdf[0]['pm_round_off']; 
    		$bill_disc 		= number_format($master_pdf[0]['pm_total_disc'] + $master_pdf[0]['pm_bill_disc'], 2, '.', ''); 
    		$gst_amt 		= $master_pdf[0]['pm_gst_amt']; 
    		$final_amt 		= $master_pdf[0]['pm_final_amt']; 
    		$words			= number_to_word($final_amt);
    		$tbl_footer='<table  border="1"  cellpadding="3">
                            <tr>
                                <td width="64%" align="right"><b>TOTAL</b></td>												
                                <td width="10%">'.$qty.'</td>											
                                <td width="10%" align="right">SUB TOTAL</td>												
                                <td width="16%" align="right">'.$sub_total.'</td>												
                            </tr>
                            <tr>
                                <td width="84%" align="right">ROUND OFF</td>
                                <td width="16%" align="right">'.$round_off.'</td>
                            </tr>
                            <tr>
                                <td width="84%" align="right">DISC AMT</td>
                                <td width="16%" align="right">'.$bill_disc.'</td>
                            </tr>
                            <tr>
                                <td width="84%" align="right">GST AMT</td>
                                <td width="16%" align="right">'.$gst_amt.'</td>
                            </tr>
                            <tr>
                                <td width="84%" align="right" style="font-weight:bold;">FINAL AMT</td>
                                <td width="16%" align="right" style="font-weight:bold;">'.$final_amt.'</td>
                            </tr>
                        </table>';
		  	$this->writeHTMLCell(285, 150, 6, 175, $tbl_footer, 0, 0, 0, true, 'L', true);
        	// Set font
        	$this->SetFont('helvetica', 'I', 8);
        	// Page number
        	$this->Cell(0, 60, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    	}
	}

	// create new PDF document
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Imran Khan');
	$pdf->SetTitle('PURCHASE');
	$pdf->SetSubject('PURCHASE');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(6, 5, 4);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(74);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 45);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
	{
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}


	$pdf->SetFont('Times', '', 7);
	$pdf->AddPage('L');
	$tbl = '<table border="0" cellpadding="5" style="font-size:12px;">';
                foreach ($trans_pdf as $key => $value){
                    $sr_no		= $key+1;
                    $design_name= $value['design_name'];
                    $style_name	= $value['style_name'];
                    $brand_name	= $value['brand_name'];
                    $desc	    = $value['pt_desc'];
                    $qty		= $value['pt_qty'];
                    $rate		= $value['pt_rate'];
                    $sp_rate	= $value['pt_sp_amt'];
                    $sub_total	= $value['pt_sub_total'];
                    $disc_amt	= $value['pt_disc_amt'];
                    $disc_per	= $value['pt_disc_per'];
                    $total_amt	= $value['pt_sub_total_amt'];

                    $tbl.=' <tr >
                                <td width="3%"      style="border-bottom: 1px dashed #000;">'.$sr_no.'</td>
                                <td width="16.9%"    style="border-bottom: 1px dashed #000;">'.$design_name.'</td>
                                <td width="15%"     style="border-bottom: 1px dashed #000;">'.$style_name.'</td>
                                <td width="8.9%"    style="border-bottom: 1px dashed #000;">'.$brand_name.'</td>
                                <td width="9.9%"    style="border-bottom: 1px dashed #000;">'.$desc.'</td>
                                <td width="6%"      style="border-bottom: 1px dashed #000; text-align:center;">'.$qty.'</td>
                                <td width="6.9%"    style="border-bottom: 1px dashed #000; text-align:right;">'.$rate.'</td>
                                <td width="7%"      style="border-bottom: 1px dashed #000; text-align:right;">'.$sp_rate.' </td>
                                <td width="7%"      style="border-bottom: 1px dashed #000; text-align:right;">'.$sub_total.' </td>
                                <td width="9.9%"    style="border-bottom: 1px dashed #000; text-align:right;">'.$disc_amt.' ('.$disc_per.'%)</td>
                                <td width="9%"      style="border-bottom: 1px dashed #000; text-align:right;">'.$total_amt.'</td>
                            </tr>';
                }
    $tbl.=' </table>';
	$pdf->writeHTML($tbl, true, false, false, false, '');
	// $pdf->writeHTMLCell(188, 150, 6, 134, $tbl, 0, 0, 0, true, 'L', true);
	$pdf->IncludeJS("print();");
	// ---------------------------------------------------------

	//Close and output PDF document
	ob_end_clean();
	$pdf->Output('PURCHASE', 'I');
	//============================================================+
	// END OF FILE
	//============================================================+
