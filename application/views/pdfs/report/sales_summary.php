<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "SALES SUMMARY";
$file_name = "SALES SUMMARY";
$obj_pdf->SetTitle($title);
$obj_pdf->SetDefaultMonospacedFont('helvetica');
$obj_pdf->SetAutoPageBreak(TRUE, 1);
$obj_pdf->setFontSubsetting(true);
	
$obj_pdf->SetPrintHeader(false);
$obj_pdf->SetPrintFooter(false);
$obj_pdf->SetTopMargin(5);
$obj_pdf->SetLeftMargin(5); //
$obj_pdf->SetRightMargin(5);

$obj_pdf->AddPage();
$obj_pdf->SetFont('Helvetica', 'S', 8);
$tbl = "";
$branch 			= strtoupper($_SESSION['user_branch']);
$bill_no 			= isset($_GET['sm_bill_no']) && !empty($_GET['sm_bill_no']) ? $data['data'][0]['sm_bill_no'] : '';
$from_bill_date  	= (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
$to_bill_date    	= (isset($_GET['to_bill_date']) && $_GET['to_bill_date'] != '') ? " TO ".$_GET['to_bill_date'] : "";
$user_fullname 		= isset($_GET['sm_user_id']) && !empty($_GET['sm_user_id']) ? strtoupper($data['data'][0]['user_fullname']) : '';
$account_name 		= isset($_GET['sm_acc_id']) && !empty($_GET['sm_acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$from_qty 			= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 			= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_bill_amt 		= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
$to_bill_amt 		= (isset($_GET['to_bill_amt']) && $_GET['to_bill_amt'] != '') ? " TO ".$_GET['to_bill_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>SALES SUMMARY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="20%">BILL NO : $bill_no</td>
						<td width="30%">BILL DATE : $from_bill_date $to_bill_date</td>
						<td width="25%">TOTAL QTY : $from_qty $to_qty</td>
						<td width="25%">BILL AMT : $from_bill_amt $to_bill_amt</td>
					</tr>
					<tr>
						<td width="50%">CUSTOMER : $account_name</td>
						<td width="50%">SALES PERSON : $user_fullname</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
                        <th width="5%">BILL NO</th>
                        <th width="5%">TYPE</th>
                        <th width="7%">BILL DATE</th>
                        <th width="11%">S-PERSON</th>
                        <th width="10%">CUSTOMER</th>
                        <th width="5%">QTY</th>
                        <th width="9%">SUB AMT</th>
                        <th width="7%">D.AMT</th>
                        <th width="12%">TAXABLE.AMT</th>
                        <th width="7%">GST</th>
                        <th width="7%">R.OFF</th>
                        <th width="12%">BILL AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$sm_bill_no 	= $value['sm_bill_no'];
							$sm_bill_date 	= date('d-m-Y', strtotime($value['sm_bill_date']));
							$user_fullname 	= strtoupper($value['user_fullname']);
							$account_name 	= strtoupper($value['account_name']);
							$sm_payment_mode= $value['sm_payment_mode'];
							$sm_total_qty 	= $value['sm_total_qty'];
							$sm_sub_total 	= round($value['sm_sub_total'], 2);
							$sm_total_disc 	= round($value['sm_total_disc'], 2);
							
							$sm_taxable_amt = round($value['sm_taxable_amt'], 2);
							$gst_amt 		= round($value['gst_amt'], 2);

							$sm_round_off 	= round($value['sm_round_off'], 2);
							$sm_final_amt 	= round($value['sm_final_amt'], 2);
							
							$sale_type = ($value['sm_sales_type']>0) ? 'APPR' : 'GEN';

							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="5%">$sm_bill_no</td>
								<td width="5%">$sale_type</td>
								<td width="7%">$sm_bill_date</td>
								<td width="11%">$user_fullname</td>
								<td width="10%">$account_name</td>
								<td width="5%">$sm_total_qty</td>
								<td width="9%">$sm_sub_total</td>
								<td width="7%">$sm_total_disc</td>
								<td width="12%">$sm_taxable_amt</td>
								<td width="7%">$gst_amt</td>
								<td width="7%">$sm_round_off</td>
								<td width="12%">$sm_final_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$total_qty 	= $data['totals']['total_qty'];
$sub_amt 	= round($data['totals']['sub_amt'], 2);
$disc_amt 	= round($data['totals']['disc_amt'], 2);
$taxable_amt= round($data['totals']['taxable_amt'], 2);
$gst_amt 	= round($data['totals']['gst_amt'], 2);

$promo_amt 	= round($data['totals']['promo_amt'], 2);
$point_amt 	= round($data['totals']['point_amt'], 2);
$off_amt 	= round($data['totals']['off_amt'], 2);
$total_amt 	= round($data['totals']['total_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>TOTAL</td>
								<td >$total_qty</td>
	                            <td >$sub_amt</td>
	                            <td >$disc_amt</td>
	                            <td >$taxable_amt</td>
	                            <td >$gst_amt</td>
	                            <td >$off_amt</td>
	                            <td >$total_amt</td>
							</tr>
				</table>		
			</td>
		</tr>		
	</table>
EOD;

$obj_pdf->writeHTML($tbl, true, false, false, false, '');
$height = $obj_pdf->getY();
$obj_pdf->deletePage(1);
$obj_pdf->setPage($obj_pdf->getPage()); 
$obj_pdf->Output($file_name, 'I');
?>