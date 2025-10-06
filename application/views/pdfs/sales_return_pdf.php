<?php
	$this->mypdf_class->tcpdf();
	$no_of_items = 0;
	foreach($trans_data as $key => $value) 
	{
		$no_of_items = $key+1;
	}
	$page_size = array('75',($no_of_items*3)+116);
	// $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$obj_pdf = new TCPDF('P',PDF_UNIT, $page_size, true, 'UTF-8', false);
	$obj_pdf->SetCreator(PDF_CREATOR);

	// set a barcode on the page footer
	$obj_pdf->setBarcode(date('Y-m-d H:i:s'));
	$title = 'Sales Return Bill';

	// $file_name = $sales_master['outward'][0]['om_billno'].'.pdf';
	// $file_path = 'public/extra/temp/'.$file_name;

	$file_name = "Sales_Return_Bill_" . $sales_data[0]['srm_entry_no'] . ".pdf";
	$file_path = 'public/extra/temp/'.$file_name;
	if($_SESSION['user_branch_id'] == 1){
		$add = "92/C, Mohd Ali Road, Opp. Ajmal perfumes, <br/>Mumbai - 400 003.";
	}else{
		$add = "Shri Chhatrapati Shivaji Shopping Center, <br/>M.G. Road, Shop No. 19, Opp. Swarna Plaza,<br/> Mulund (W), Mumbai - 400 080.";
	}


	$obj_pdf->SetTitle($title);
	$obj_pdf->SetDefaultMonospacedFont('helvetica');
	$obj_pdf->SetAutoPageBreak(TRUE, 3);
	$obj_pdf->SetFont('helvetica', '', 11);
	$obj_pdf->setFontSubsetting(true);
	
	$obj_pdf->SetPrintHeader(false);
	$obj_pdf->SetPrintFooter(false);

	$obj_pdf->SetTopMargin(1);
	$obj_pdf->SetLeftMargin(6);
	$obj_pdf->SetRightMargin(5);

		
	$path = base_url();
	$obj_pdf->AddPage();
		
	$tbl = "";


	$tbl .= <<<EOD

	<table border="0" cellpadding="1">
			<tr>
				<td  style="font-size:16px;text-align:center" >
					<b>COTTON HOUSE</b>
				</td>
			</tr>

			<tr>
				<td  style="font-size:7px;text-align:center" >
					$add
				</td>
			</tr>
			<tr>
				<td  style="font-size:12px;text-align:center" >
					<b>CASH PAID</b>
				</td>
			</tr>

	</table>
	
EOD;

  $cash_billno 	= $sales_data[0]['srm_entry_no'];
  $name 		= strtoupper($sales_data[0]['account_name']);
  $date 		= date('d-m-y',strtotime($sales_data[0]['srm_entry_date']));
  $time 		= date("h:i:s A");
  $mob 			= $sales_data[0]['account_mobile'];
  $oop 			= strtoupper($trans_data[0]['user_fullname']);
  
	$tbl .= <<<EOD
	<br/>
	<table border="0" cellpadding="1" >
		<tr>
			<td style="font-size:8px; margin-top:40px" >
				<b>Entry No.: </b>$cash_billno
			</td>
			<td style="font-size:8px; margin-top:40px" >
				<b>Date : </b>$date
			</td>
		</tr>	
		<tr>
			<td style="font-size:8px; margin-top:40px;">
				<b>Name : </b>$name			
			</td>
			<td style="font-size:8px; margin-top:40px;">
				<b>Time : </b>$time		
			</td>			
		</tr>
		<tr>
			<td style="font-size:8px; margin-top:40px;">
				<b>Mob : </b>$mob			
			</td>
			<td style="font-size:8px; margin-top:40px;">
				<b>Staff : </b>$oop			
			</td>				
		</tr>
		
	</table>
EOD;

$tbl .= <<<EOD
    <br/>
EOD;
$tbl .= <<<EOD

    <table border="0" cellpadding="1">
		<tr>
            <td style="font-size:8px;width:36%;border-bottom:1px dashed #000;border-top:1px dashed #000; ">
            ITEM NAME
            </td>
            <td  style="font-size:8px;width:13%;border-bottom:1px dashed #000;border-top:1px dashed #000;" >
               QTY
            </td>
            <td style="font-size:8px;width:18%;border-bottom:1px dashed #000;border-top:1px dashed #000;text-align:right" >
            RATE
            </td>
            <td style="font-size:8px;width:15%;border-bottom:1px dashed #000;border-top:1px dashed #000;text-align:right" >
            DISC 
            </td>
            <td style="font-size:8px;width:18%;text-align:right;border-bottom:1px dashed #000;border-top:1px dashed #000;" >
            T.V.L
            </td>
		</tr>
            		
            		
EOD;
$qty = 0;
foreach($trans_data as $key => $value) 
{
	$cnt = $key+1;
    $tbl .= '<tr>
        <td style="font-size:8px;">' . $value['style_name'] . '</td>
        <td style="font-size:8px;text-align:center">' . $value['srt_qty'] . '</td>
        <td style="text-align:right;font-size:8px;">' . $value['srt_rate'] . '</td>
        <td style="text-align:right;font-size:8px;">' . $value['srt_disc_amt'] . '</td>
        <td style="text-align:right;font-size:8px;">' . $value['srt_total_amt'] . '</td>
    </tr>';    
}


 $total_qty 	= round($sales_data[0]['srm_total_qty'],0);
 $disc_amt 		= $sales_data[0]['srm_total_disc'];
 $total_disc 	= $sales_data[0]['srm_total_disc'];
 $total_amt 	= $sales_data[0]['srm_sub_total'];
 $final_amt 	= $sales_data[0]['srm_final_amt'];
 $amt_collected = $sales_data[0]['srm_amt_paid'];
 $bal_to_pay 	= $sales_data[0]['srm_final_amt'];
 


$tbl .= <<<EOD
		<tr>
	    	<td style="text-align:left;font-size:8px;border-bottom:1px dashed #000;border-top:1px dashed #000;"><b>Total</b> </td>
	    	<td  style="text-align:center;font-size:8px;border-bottom:1px dashed #000;border-top:1px dashed #000;"> <b>$total_qty</b> </td>
	    	<td style="text-align:right;font-size:8px;border-bottom:1px dashed #000;border-top:1px dashed #000;"><b></b></td>
	    	<td style="text-align:right;font-size:8px;border-bottom:1px dashed #000;border-top:1px dashed #000;"><b>$disc_amt</b></td>
	    	<td style="text-align:right;font-size:8px;border-bottom:1px dashed #000;border-top:1px dashed #000;"><b>$final_amt</b></td>
	    </tr>	
    </table>
   
    <table border="0" cellpadding="1" >
	   

EOD;
$tbl .= <<<EOD
		<tr>
	    	<td colspan="2" style="text-align:right;font-size:12px;border-bottom:1px dashed #000;">
	    		<b>Disc Total:   </b>
    		</td>
	    	<td style="text-align:right;font-size:12px;border-bottom:1px dashed #000;">
	    		<b>$total_disc</b>
    		</td>
	    </tr>
EOD;
		
	$tbl .= <<<EOD
		<tr>
	    	<td colspan="2" style="text-align:right;font-size:12px;border-bottom:1px dashed #000;"><b>Bill Total:  </b></td>
	    	<td style="text-align:right;font-size:12px;border-bottom:1px dashed #000;"><b>$final_amt</b></td>
	    </tr>
		<tr>
	    	<td colspan="2" style="text-align:right;font-size:10px;"><b>Amt Paid</b></td>
	    	<td style="text-align:right;font-size:10px;"><b>$amt_collected</b></td>
	    </tr>
    </table>
    
    <br/>
    
     
    <table style="padding-top:2px;">
    	<tr>
    	<td style="font-size:7px;text-align:center;">Exchange within 3 days from the date of purchase</td>
    	</tr>
    	<tr>
    	<td style="font-size:7px;text-align:center;">Exchange from 11AM to 3PM</td>
    	</tr>
    	<tr>
    	<td style="font-size:7px;text-align:center;">No Exchange without Price Tag,Barcode and Bill</td>
    	</tr>
    	<tr>
    	<td style="font-size:7px;text-align:center;">Discounted Items will not be Exchange or Refund</td>
    	</tr>
    	<tr>
    	<td style="font-size:7px;text-align:center;">No gurantee for any Product</td>
    	</tr>
    	
    	<tr>
    	<td style="font-size:8px;text-align:center;">GST NO-27ABDPL8998E1Z4</td>
    	</tr>

    </table>
EOD;


	


$obj_pdf->writeHTML($tbl, true, false, false, false, '');
$obj_pdf->IncludeJS("print();");
$obj_pdf->Output($file_name, 'I');
	
	
?>