<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "CA REPORT";
$file_name = "CA REPORT";
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
$from_bill_date  	= !empty($master_data[0]['im_from_bill_date']) ? date('d-m-Y', strtotime($master_data[0]['im_from_bill_date'])) : '';
$to_bill_date    	= !empty($master_data[0]['im_to_bill_date']) ? date('d-m-Y', strtotime($master_data[0]['im_to_bill_date'])) : '';
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>CA REPORT</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="100%">SALES BILL FROM $from_bill_date TO $to_bill_date</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="10%">#</th>
                        <th width="10%">BILL NO</th>
                        <th width="20%">BILL DATE</th>
                        <th width="20%">PAYMENT MODE</th>
                        <th width="20%">TOTAL QTY</th>
                        <th width="20%">BILL AMT</th>
		            </tr>

EOD;
					if(!empty($trans_data)):
						$sr_no = 1;
						foreach ($trans_data as $key => $value):
							$sm_bill_no 	= $value['it_invoice_no'];
							$sm_bill_date 	= date('d-m-Y', strtotime($value['it_bill_date']));
							$user_fullname 	= strtoupper($value['user_fullname']);
							$account_name 	= strtoupper($value['account_name']);
							$sm_payment_mode= $value['it_payment_mode'];
							$sm_total_qty 	= $value['it_total_qty'];
							$sm_sub_total 	= round($value['it_sub_amt'], 2);
							$sm_total_disc 	= round($value['it_disc_amt'], 2);
							$sm_promo_disc 	= round($value['it_promo_amt'], 2);
							$sm_point_used 	= round($value['it_point_amt'], 2);
							$sm_round_off 	= round($value['it_round_off'], 2);
							$sm_final_amt 	= round($value['it_final_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td>$sr_no</td>
								<td>$sm_bill_no</td>
								<td>$sm_bill_date</td>
								<td>$sm_payment_mode</td>
								<td>$sm_total_qty</td>
								<td>$sm_final_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$tbl .= <<<EOD
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