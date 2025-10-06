<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "MONTHLY PROFIT";
$file_name = "MONTHLY PROFIT";
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
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y', strtotime($_SESSION['start_year']));
$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y', strtotime($_SESSION['end_year']));
$sale_qty_from 	= (isset($_GET['sale_qty_from'])) ? $_GET['sale_qty_from'] : "";
$sale_qty_to 	= (isset($_GET['sale_qty_to']) && $_GET['sale_qty_to'] != '') ? " TO ".$_GET['sale_qty_to'] : "";
$st_amt_from 	= (isset($_GET['st_amt_from'])) ? $_GET['st_amt_from'] : "";
$st_amt_to 		= (isset($_GET['st_amt_to']) && $_GET['st_amt_to'] != '') ? " TO ".$_GET['st_amt_to'] : "";
$profit_amt_from= (isset($_GET['profit_amt_from'])) ? $_GET['profit_amt_from'] : "";
$profit_amt_to 	= (isset($_GET['profit_amt_to']) && $_GET['profit_amt_to'] != '') ? " TO ".$_GET['profit_amt_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>MONTHLY PROFIT</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="28%">BILL DATE : $from_date $to_date</td>
						<td width="25%">SOLD QTY : $sale_qty_from $sale_qty_to</td>
						<td width="25%">ACT. SALE AMT : $st_amt_from $st_amt_to</td>
						<td width="22%">PROFIT : $profit_amt_from $profit_amt_to</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="5%">#</th>
                        <th width="13%">MONTH - YEAR</th>
                        <th width="10%">SOLD QTY</th>
                        <th width="12%">PURCHASE AMT</th>
                        <th width="10%">SALE AMT</th>
                        <th width="10%">DISC AMT</th>
                        <th width="13%">ACTUAL SALE AMT</th>
                        <th width="12%">PROFIT</th>
                        <th width="15%"></th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$month_year 	= $value['entry_date'];
							$st_qty 		= $value['st_qty'];
							$pt_amt 		= round($value['pt_amt'], 2);
							$st_rate 		= round($value['st_rate'], 2);
							$st_disc 		= round($value['st_disc'], 2);
							$st_amt 		= round($value['st_amt'], 2);
							$profit	= round($value['profit'], 2);
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$month_year</td>
								<td >$st_qty</td>
								<td >$pt_amt</td>
								<td >$st_rate</td>
								<td >$st_disc</td>
								<td >$st_amt</td>
								<td >$profit</td>
								<td ></td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$st_qty 	= $data['totals']['st_qty'];
$pt_amt 	= round($data['totals']['pt_amt'], 2);
$st_rate 	= round($data['totals']['st_rate'], 2);
$st_disc 	= round($data['totals']['st_disc'], 2);
$srt_amt 	= round($data['totals']['srt_amt'], 2);
$profit 	= round($data['totals']['profit'], 2);
$points 	= round($data['totals']['points'], 2);
$expense 	= round($data['totals']['expense'], 2);
$st_amt 	= round($data['totals']['st_amt'], 2);
$profit_loss= round($data['totals']['profit_loss'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
								<td ></td>
	                            <td >$st_qty</td>
	                            <td >$pt_amt</td>
	                            <td >$st_rate</td>
	                            <td >$st_disc</td>
	                            <td >$st_amt</td>
	                            <td >$profit</td>
	                            <td >TOTALS</td>
							</tr>
EOD;
if(!empty($points)):
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="7"></td>
	                            <td >$points</td>
	                            <td >LOYALTY POINTS</td>
							</tr>
EOD;
endif;
if(!empty($expense)):
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="7"></td>
	                            <td >$expense</td>
	                            <td >EXPENSE</td>
							</tr>
EOD;
endif;
if(!empty($srt_amt)):
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="7"></td>
	                            <td >$srt_amt</td>
	                            <td >SALES RETURN</td>
							</tr>
EOD;
endif;
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="7"></td>
	                            <td >$profit_loss</td>
	                            <td >ACT. PROFIT</td>
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