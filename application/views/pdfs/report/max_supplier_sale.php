<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "MAX. SUPPLIER SALE";
$file_name = "MAX. SUPPLIER SALE";
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
$account_name 		= isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id']) ? $data['data'][0]['account_name'] : '';
$from_date 			= (isset($_GET['from_date'])) ? $_GET['from_date'] : "";
$to_date 			= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : "";
$from_qty 			= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 			= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_sale 			= (isset($_GET['from_sale'])) ? $_GET['from_sale'] : "";
$to_sale 			= (isset($_GET['to_sale']) && $_GET['to_sale'] != '') ? " TO ".$_GET['to_sale'] : "";
$from_disc 			= (isset($_GET['from_disc'])) ? $_GET['from_disc'] : "";
$to_disc 			= (isset($_GET['to_disc']) && $_GET['to_disc'] != '') ? " TO ".$_GET['to_disc'] : "";
$from_amt 			= (isset($_GET['from_amt'])) ? $_GET['from_amt'] : "";
$to_amt 			= (isset($_GET['to_amt']) && $_GET['to_amt'] != '') ? " TO ".$_GET['to_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>MAX. SUPPLIER SALE</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="50%">SUPPLIER : $account_name</td>
						<td width="50%">DATE : $from_date $to_date</td>
					</tr>
					<tr>
						<td width="25%">SALE QTY : $from_qty $to_qty</td>
						<td width="25%">SALE AMT : $from_sale $to_sale</td>
						<td width="25%">DISC AMT : $from_disc $to_disc</td>
						<td width="25%">ACT. SALE AMT : $from_amt $to_amt</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
		                <th width="37%">SUPPLIER</th>
	                    <th width="15%">SALE QTY</th>
	                    <th width="15%">SALE AMT</th>
	                    <th width="15%">DISC AMT</th>
	                    <th width="15%">ACTUAL SALE AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$st_qty 		= $value['st_qty'];
							$st_rate 		= round($value['st_rate'], 2);
							$st_disc 		= round($value['st_disc'],2);
							$st_amt 		= round($value['st_amt'],2);
							$tbl .= <<<EOD
							<tr>
								<td width="3%">$sr_no</td>
								<td width="37%">$account_name</td>
								<td width="15%">$st_qty</td>
								<td width="15%">$st_rate</td>
								<td width="15%">$st_disc</td>
								<td width="15%">$st_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$st_qty 	= $data['totals']['st_qty'];
$st_rate 	= round($data['totals']['st_rate'], 2);					
$st_disc 	= round($data['totals']['st_disc'], 2);
$st_amt 	= round($data['totals']['st_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
			                    <td >TOTALS</td>
			                    <td >$st_qty</td>
			                    <td >$st_rate</td>
			                    <td >$st_disc</td>
			                    <td >$st_amt</td>
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