<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "BILL WISE DAILY PROFIT";
$file_name = "BILL WISE DAILY PROFIT";
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
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y');
$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y');
$sale_qty_from 	= (isset($_GET['sale_qty_from'])) ? $_GET['sale_qty_from'] : "";
$sale_qty_to 	= (isset($_GET['sale_qty_to']) && $_GET['sale_qty_to'] != '') ? " TO ".$_GET['sale_qty_to'] : "";
$st_amt_from 	= (isset($_GET['st_amt_from'])) ? $_GET['st_amt_from'] : "";
$st_amt_to 		= (isset($_GET['st_amt_to']) && $_GET['st_amt_to'] != '') ? " TO ".$_GET['st_amt_to'] : "";
$profit_amt_from= (isset($_GET['profit_amt_from'])) ? $_GET['profit_amt_from'] : "";
$_bill_no 	= (isset($_GET['_bill_no']) && $_GET['_bill_no'] != '') ? $_GET['_bill_no'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>BILL WISE DAILY PROFIT</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="60%">BILL DATE : $from_date $to_date</td>
						<td width="40%">BILL NO : $_bill_no</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="5%">#</th>
                        <th width="8%">BILL DATE</th>
                        <th width="8%">BILL NO</th>
                        <th width="12%">SP NAME</th>
                        <th width="8%">SOLD QTY</th>
                        <th width="8%">PUR AMT</th>
                        <th width="8%">SALE AMT</th>
                        <th width="10%">DISC AMT</th>
                        <th width="13%">ACTUAL SALE AMT</th>
                        <th width="10%">PROFIT</th>
                        <th width="10%"></th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$bill_date 		= $value['entry_date'];
							$bill_no 		= $value['entry_no'];
							$user_fullname 		= $value['user_fullname'];

							$st_qty 		= $value['st_qty'];
							$pt_amt 		= round($value['pt_amt'], 2);
							$st_rate 		= round($value['st_rate'], 2);
							$st_disc 		= round($value['st_disc'], 2);
							$st_amt 		= round($value['st_amt'], 2);
							$profit 		= round($value['profit'], 2);
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$bill_date</td>
								<td >$bill_no</td>
								<td >$user_fullname</td>
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
								<td colspan="9"></td>
	                            <td >$points</td>
	                            <td >LOYALTY POINTS</td>
							</tr>
EOD;
endif;
if(!empty($expense)):
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="9"></td>
	                            <td >$expense</td>
	                            <td >EXPENSE</td>
							</tr>
EOD;
endif;
if(!empty($srt_amt)):
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="9"></td>
	                            <td >$srt_amt</td>
	                            <td >SALES RETURN</td>
							</tr>
EOD;
endif;
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="9"></td>
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