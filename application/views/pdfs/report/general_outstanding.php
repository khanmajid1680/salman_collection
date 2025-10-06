<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "GENERAL OUTSTANDING";
$file_name = "GENERAL OUTSTANDING";
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
$bal_frm 		= (isset($_GET['bal_frm'])) ? $_GET['bal_frm'] : "";
$bal_to 		= (isset($_GET['bal_to']) && $_GET['bal_to'] != '') ? " TO ".$_GET['bal_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>GENERAL OUTSTANDING</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="50%">GENERAL : $account_name</td>
						<td width="50%">BAL AMT : $bal_frm $bal_to</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="5%">#</th>
		                <th width="20%">GENERAL</th>
		                <th width="15%">TYPE</th>
	                    <th width="15%">OPENING AMT</th>
	                    <th width="15%">CREDITED AMT</th>
	                    <th width="15%">DEBITED AMT</th>
	                    <th width="15%">BALANCE AMT</th>
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
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$account_drcr</td>
								<td >$open_amt</td>
								<td >$credited_amt</td>
								<td >$debited_amt</td>
								<td >$bal_amt</td>
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
			                    <td >$credited_amt</td>
			                    <td >$debited_amt</td>
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