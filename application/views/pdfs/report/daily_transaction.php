<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "DAILY TRANSACTION";
$file_name = "DAILY TRANSACTION";
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
$branch 		= strtoupper($_SESSION['user_branch']);
$account_name 	= (isset($_GET['constant']) && $_GET['constant'] == 'BANK') ? 'BANK A/C' : 'CASH A/C';
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y');
$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y');
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>DAILY TRANSACTION</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="50%">ACCOUNT : $account_name</td>
						<td width="50%">BILL DATE : $from_date $to_date</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
	                    <th width="25%">PARTY NAME</th>
	                    <th width="12%">ENTRY NO</th>
	                    <th width="12%">ENTRY DATE</th>
	                    <th width="12%">ACTION</th>
	                    <th width="12%">RECEIVED AMT</th>
	                    <th width="12%">PAID AMT</th>
	                    <th width="12%"></th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$entry_no 		= $value['entry_no'];
							$entry_date 	= $value['entry_date'];
							$action 		= $value['action'];
							$amt_debited 	= $value['amt_debited'];
							$amt_credited 	= $value['amt_credited'];
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="25%">$account_name</td>
								<td width="12%">$entry_no</td>
								<td width="12%">$entry_date</td>
								<td width="12%">$action</td>
								<td width="12%">$amt_debited</td>
								<td width="12%">$amt_credited</td>
								<td width="12%"></td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$open_bal 	= $data['open_bal'];
$sales_amt 	= $data['sales_amt'];
$return_amt = $data['return_amt'];
$receipt_amt= $data['receipt_amt'];
$payment_amt= $data['payment_amt'];
$close_bal 	= $data['close_bal'];
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<th ></th>
			                    <th >OPENING BAL</th>
			                    <th >TOTAL SALES AMT</th>
			                    <th colspan="2">TOTAL SALES RETURN AMT</th>
			                    <th >TOTAL RECEIPT AMT</th>
			                    <th >TOTAL PAID AMT</th>
			                    <th >CLOSING BAL</th>
							</tr>
							<tr style="font-weight:bold;">
								<th ></th>
			                    <th >$open_bal</th>
			                    <th >$sales_amt</th>
			                    <th colspan="2">$return_amt</th>
			                    <th >$receipt_amt</th>
			                    <th >$payment_amt</th>
			                    <th >$close_bal</th>
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