<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "PURCHASE RETURN SUMMARY";
$file_name = "PURCHASE RETURN SUMMARY";
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
$entry_no 			= isset($_GET['prm_entry_no']) && !empty($_GET['prm_entry_no']) ? $data['data'][0]['prm_entry_no'] : '';
$from_entry_date 	= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
$to_entry_date 	 	= (isset($_GET['to_entry_date']) && $_GET['to_entry_date'] != '') ? " TO ".$_GET['to_entry_date'] : "";
$account_name 		= isset($_GET['prm_acc_id']) && !empty($_GET['prm_acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$from_qty 		 	= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 		 	= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_bill_amt 	 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
$to_bill_amt 	 	= (isset($_GET['to_bill_amt']) && $_GET['to_bill_amt'] != '') ? " TO ".$_GET['to_bill_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>PURCHASE RETURN SUMMARY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="15%">ENTRY NO : $entry_no</td>
						<td width="30%">ENTRY DATE : $from_entry_date $to_entry_date</td>
						<td width="25%">TOTAL QTY : $from_qty $to_qty</td>
						<td width="30%">BILL AMT : $from_bill_amt $to_bill_amt</td>
					</tr>
					<tr>
						<td width="100%">SUPPLIER : $account_name</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
                        <th width="8%">ENTRY NO</th>
                        <th width="9%">ENTRY DATE</th>
                        <th width="32%">SUPPLIER</th>
                        <th width="8%">TOTAL QTY</th>
                        <th width="8%">SUB AMT</th>
                        <th width="8%">ROUND OFF</th>
                        <th width="8%">BILL DISC</th>
                        <th width="8%">GST AMT</th>
                        <th width="8%">BILL AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$prm_entry_no 	= $value['prm_entry_no'];
							$prm_entry_date = date('d-m-Y', strtotime($value['prm_entry_date']));
							$account_name 	= strtoupper($value['account_name']);
							$prm_total_qty 	= $value['prm_total_qty'];
							$prm_sub_total 	= round($value['prm_sub_total'], 2);
							$prm_round_off 	= round($value['prm_round_off'], 2);
							$prm_bill_disc 	= round($value['prm_bill_disc'], 2);
							$prm_gst_amt 	= round($value['prm_gst_amt'], 2);
							$prm_final_amt 	= round($value['prm_final_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="8%">$prm_entry_no</td>
								<td width="9%">$prm_entry_date</td>
								<td width="32%">$account_name</td>
								<td width="8%">$prm_total_qty</td>
								<td width="8%">$prm_sub_total</td>
								<td width="8%">$prm_round_off</td>
								<td width="8%">$prm_bill_disc</td>
								<td width="8%">$prm_gst_amt</td>
								<td width="8%">$prm_final_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$total_qty 	= $data['totals']['total_qty'];
$sub_amt 	= round($data['totals']['sub_amt'], 2);
$off_amt 	= round($data['totals']['off_amt'], 2);
$bdisc_amt 	= round($data['totals']['bdisc_amt'], 2);
$gst_amt 	= round($data['totals']['gst_amt'], 2);
$total_amt 	= round($data['totals']['total_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td>TOTAL</td>
								<td>$total_qty</td>
		                        <td>$sub_amt</td>
		                        <td>$off_amt</td>
		                        <td>$bdisc_amt</td>
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