<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "EXPENSE REPORT";
$file_name = "EXPENSE REPORT";
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
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y', strtotime($_SESSION['start_year']));
$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y', strtotime($_SESSION['end_year']));
$account_name 	= isset($_GET['_party_id']) && !empty($_GET['_party_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$amt_from 		= (isset($_GET['amt_from'])) ? $_GET['amt_from'] : "";
$amt_to 		= (isset($_GET['amt_to']) && $_GET['amt_to'] != '') ? " TO ".$_GET['amt_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>EXPENSE REPORT</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="33%">DATE : $from_date $to_date</td>
						<td width="34%">$account_name</td>
						<td width="33%">AMT : $amt_from $amt_to</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="33%">#</th>
                        <th width="34%">DESCRIPTION</th>
                        <th width="33%">AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$total_amt 		= round($value['total_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$total_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$total_amt 	= round($data['totals']['total_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
								<td >TOTALS</td>
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