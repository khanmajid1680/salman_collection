<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "PURCHASE SUMMARY";
$file_name = "PURCHASE SUMMARY";
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
$entry_no 			= isset($_GET['pm_entry_no']) && !empty($_GET['pm_entry_no']) ? $data['data'][0]['pm_entry_no'] : '';
$from_entry_date 	= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
$to_entry_date 	 	= (isset($_GET['to_entry_date']) && $_GET['to_entry_date'] != '') ? " TO ".$_GET['to_entry_date'] : "";
$bill_no 			= isset($_GET['pm_bill_no']) && !empty($_GET['pm_bill_no']) ? $data['data'][0]['pm_bill_no'] : '';
$from_bill_date  	= (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
$to_bill_date    	= (isset($_GET['to_bill_date']) && $_GET['to_bill_date'] != '') ? " TO ".$_GET['to_bill_date'] : "";
$account_name 		= isset($_GET['pm_acc_id']) && !empty($_GET['pm_acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$from_qty 		 	= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 		 	= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_bill_amt 	 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
$to_bill_amt 	 	= (isset($_GET['to_bill_amt']) && $_GET['to_bill_amt'] != '') ? " TO ".$_GET['to_bill_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>PURCHASE SUMMARY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="20%">ENTRY NO : $entry_no</td>
						<td width="30%">ENTRY DATE : $from_entry_date $to_entry_date</td>
						<td width="20%">BILL NO : $bill_no</td>
						<td width="30%">BILL DATE : $from_bill_date $to_bill_date</td>
					</tr>
					<tr>
						<td width="40%">SUPPLIER : $account_name</td>
						<td width="30%">TOTAL QTY : $from_qty $to_qty</td>
						<td width="30%">BILL AMT : $from_bill_amt $to_bill_amt</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
                        <th width="7%">ENTRY NO</th>
                        <th width="8%">ENTRY DATE</th>
                        <th width="7%">BILL NO</th>
                        <th width="8%">BILL DATE</th>
                        <th width="18%">SUPPLIER</th>
                        <th width="7%">TOTAL QTY</th>
                        <th width="7%">SUB AMT</th>
                        <th width="5%">D.AMT</th>
                        <th width="5%">R.OFF</th>
                        <th width="5%">B.DISC</th>
                        <th width="6%">T.AMT</th>
                        <th width="7%">GST AMT</th>
                        <th width="7%">BILL AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$pm_entry_no 	= $value['pm_entry_no'];
							$pm_entry_date 	= date('d-m-Y', strtotime($value['pm_entry_date']));
							$pm_bill_no 	= $value['pm_bill_no'];
							$pm_bill_date 	= date('d-m-Y', strtotime($value['pm_bill_date']));
							$account_name 	= strtoupper($value['account_name']);
							$pm_total_qty 	= $value['pm_total_qty'];
							$pm_sub_total 	= round($value['pm_sub_total'], 2);
							$pm_total_disc 	= round($value['pm_total_disc'], 2);
							$pm_round_off 	= round($value['pm_round_off'], 2);
							$pm_bill_disc 	= round($value['pm_bill_disc'], 2);
							$pm_taxable_amt 	= round($value['pm_taxable_amt'], 2);

							$gst_amt 	= round($value['gst_amt'], 2);
							$pm_final_amt 	= round($value['pm_final_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="7%">$pm_entry_no</td>
								<td width="8%">$pm_entry_date</td>
								<td width="7%">$pm_bill_no</td>
								<td width="8%">$pm_bill_date</td>
								<td width="18%">$account_name</td>
								<td width="7%">$pm_total_qty</td>
								<td width="7%">$pm_sub_total</td>
								<td width="5%">$pm_total_disc</td>
								<td width="5%">$pm_round_off</td>
								<td width="5%">$pm_bill_disc</td>
								<td width="6%">$pm_taxable_amt</td>
								<td width="7%">$gst_amt</td>
								<td width="7%">$pm_final_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$total_qty 	= $data['totals']['total_qty'];
$sub_amt 	= round($data['totals']['sub_amt'], 2);
$disc_amt 	= round($data['totals']['disc_amt'], 2);
$off_amt 	= round($data['totals']['off_amt'], 2);
$bdisc_amt 	= round($data['totals']['bdisc_amt'], 2);
$taxable_amt 	= round($data['totals']['taxable_amt'], 2);

$gst_amt 	= round($data['totals']['gst_amt'], 2);
$total_amt 	= round($data['totals']['total_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>TOTAL</td>
								<td>$total_qty</td>
		                        <td>$sub_amt</td>
		                        <td>$disc_amt</td>
		                        <td>$off_amt</td>
		                        <td>$bdisc_amt</td>
		                        <td>$taxable_amt</td>
		                        <td>$gst_amt</td>
		                        <td>$total_amt</td>
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