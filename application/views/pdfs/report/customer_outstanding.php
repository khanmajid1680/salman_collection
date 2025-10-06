<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "CUSTOMER OUTSTANDING";
$file_name = "CUSTOMER OUTSTANDING";
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
$account_name 	= isset($_GET['acc_id']) && !empty($_GET['acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$debit_frm 		= (isset($_GET['debit_frm'])) ? $_GET['debit_frm'] : "";
$debit_to 		= (isset($_GET['debit_to']) && $_GET['debit_to'] != '') ? " TO ".$_GET['debit_to'] : "";
$debited_frm 	= (isset($_GET['debited_frm'])) ? $_GET['debited_frm'] : "";
$debited_to 	= (isset($_GET['debited_to']) && $_GET['debited_to'] != '') ? " TO ".$_GET['debited_to'] : "";
$bal_frm 		= (isset($_GET['bal_frm'])) ? $_GET['bal_frm'] : "";
$bal_to 		= (isset($_GET['bal_to']) && $_GET['bal_to'] != '') ? " TO ".$_GET['bal_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>CUSTOMER OUTSTANDING</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="25%">CUSTOMER : $account_name</td>
						<td width="25%">SALES AMT : $debit_frm $debit_to</td>
						<td width="25%">RECEIPT AMT : $debited_frm $debited_to</td>
						<td width="25%">BAL AMT : $bal_frm $bal_to</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
		                <th width="17%">CUSTOMER</th>
		                <th width="10%">TYPE</th>
	                    <th width="10%">OPENING AMT</th>
	                    <th width="10%">SALES AMT</th>
	                    <th width="10%">RECEIPT AMT</th>
	                    <th width="15%">SALES RETURN AMT</th>
	                    <th width="15%">CREDIT NOTE AMT</th>
	                    <th width="10%">BALANCE AMT</th>
		            </tr>
EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= strtoupper($value['account_name']);
							$account_drcr 	= $value['account_drcr'];
							$open_amt 		= round($value['open_amt'], 2);
							$debit_amt 		= round($value['debit_amt'], 2);
							$debited_amt 	= round($value['debited_amt'], 2);
							$credit_amt 	= round($value['credit_amt'], 2);
							$credited_amt 	= round($value['credited_amt'], 2);
							$bal_amt 		= round($value['bal_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="17%">$account_name</td>
								<td width="10%">$account_drcr</td>
								<td width="10%">$open_amt</td>
								<td width="10%">$debit_amt</td>
								<td width="10%">$debited_amt</td>
								<td width="15%">$credit_amt</td>
								<td width="15%">$credited_amt</td>
								<td width="10%">$bal_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$open_amt 		= round($data['totals']['open_amt'], 2);
$debit_amt 		= round($data['totals']['debit_amt'], 2);
$debited_amt 	= round($data['totals']['debited_amt'], 2);
$credit_amt 	= round($data['totals']['credit_amt'], 2);
$credited_amt 	= round($data['totals']['credited_amt'], 2);
$bal_amt 		= round($data['totals']['bal_amt'], 2)." ".$data['totals']['label'];
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
								<td ></td>
			                    <td >TOTALS</td>
			                    <td >$open_amt</td>
			                    <td >$debit_amt</td>
			                    <td >$debited_amt</td>
			                    <td >$credit_amt</td>
			                    <td >$credited_amt</td>
			                    <td >$bal_amt</td>
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