<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "SUPPLIER LEDGER";
$file_name = "SUPPLIER LEDGER";
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
$obj_pdf->SetFont('Helvetica', 'B', 14);
$tbl = "";
$branch 		= strtoupper($_SESSION['user_branch']);
$account_name 	= isset($_GET['acc_id']) && !empty($_GET['acc_id']) ? $data['data'][0]['account_name'] : 'ALL';
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y', strtotime($_SESSION['start_year']));
$to_date 		= (isset($_GET['to_date'])) ? $_GET['to_date'] : date('d-m-Y', strtotime($_SESSION['end_year']));
$open_bal 		= $data['open_bal'];
$close_bal 		= $data['close_bal'];
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center">SUPPLIER LEDGER (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:12px;">
					<tr>
						<td width="50%">SUPPLIER : $account_name</td>
						<td width="50%" align="right">DATE : $from_date TO $to_date</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
		                <th width="3%">#</th>
	                    <th width="17%">SUPPLIER</th>
	                    <th width="12%">BILL / ENTRY NO</th>
	                    <th width="12%">BILL / ENTRY DATE</th>
	                    <th width="10%">BILL AMT</th>
	                    <th width="12%">ACTION</th>
	                    <th width="10%">RECEIVED AMT</th>
	                    <th width="10%">PAID AMT</th>
	                    <th width="14%">BALANCE AMT</th>
		            </tr>
					<tr>
						<td width="100%" align="right">OPENING AMT : $open_bal</td>
					</tr>
EOD;
					if($data['data']):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$entry_no 		= $value['entry_no'];
							$entry_date 	= $value['entry_date'];
							$bill_amt 		= !empty($value['amt_to_debit']) ? $value['amt_to_debit'] : $value['amt_to_credit'];
							$action 		= $value['action'];
							$amt_debited	= $value['amt_debited'];
							$amt_credited	= $value['amt_credited'];
							$bal_amt		= $value['bal_amt'];
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="17%">$account_name</td>
								<td width="12%">$entry_no</td>
								<td width="12%">$entry_date</td>
								<td width="10%">$bill_amt</td>
								<td width="12%">$action</td>
								<td width="10%">$amt_debited</td>
								<td width="10%">$amt_credited</td>
								<td width="14%">$bal_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$tbl .= <<<EOD
					<tr>
						<td width="100%" align="right">CLOSING AMT : $close_bal</td>
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