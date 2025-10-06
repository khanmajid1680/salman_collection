<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "MONTHLY SUMMARY";
$file_name = "MONTHLY SUMMARY";
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
$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('01-m-Y');
$to_date 		= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('t-m-Y');
$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 		= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_amt 		= (isset($_GET['from_amt'])) ? $_GET['from_amt'] : "";
$to_amt 		= (isset($_GET['to_amt']) && $_GET['to_amt'] != '') ? " TO ".$_GET['to_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>MONTHLY SUMMARY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="33%">DATE : $from_date $to_date</td>
						<td width="34%">ACT. SALE QTY : $from_qty $to_qty</td>
						<td width="33%">ACT. SALE AMT : $from_amt $to_amt</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
                        <th width="9%">DATE</th>
                        <th width="8%">DAY</th>
                        <th width="10%">SALE QTY</th>
                        <th width="10%">SALE RETURN QTY</th>
                        <th width="10%">ACTUAL SALE QTY</th>
                        <th width="10%">SALE AMT</th>
                        <th width="10%">DISC AMT</th>
                        <th width="10%">SALES RETURN AMT</th>
                        <th width="10%">ACTUAL SALE AMT</th>
                        <th width="10%">BILL GENERATED</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$sm_bill_date 	= $value['entry_date'];
							$day 			= $value['day'];
							$st_qty 		= $value['st_qty'];
							$srt_qty 		= $value['srt_qty'];
							$sale_qty 		= $value['sale_qty'];
							$st_amt 		= round($value['st_amt'], 2);
							$st_disc 		= round($value['st_disc'], 2);
							$srt_amt 		= round($value['srt_amt'], 2);
							$sale_amt 		= round($value['sale_amt'], 2);
							$bill 			= $value['bill'];
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$sm_bill_date</td>
								<td >$day</td>
								<td >$st_qty</td>
								<td >$srt_qty</td>
								<td >$sale_qty</td>
								<td >$st_amt</td>
								<td >$st_disc</td>
								<td >$srt_amt</td>
								<td >$sale_amt</td>
								<td >$bill</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$st_qty 	= $data['totals']['st_qty'];
$srt_qty 	= $data['totals']['srt_qty'];
$sale_qty 	= $data['totals']['sale_qty'];
$st_amt 	= round($data['totals']['st_amt'], 2);
$st_disc 	= round($data['totals']['st_disc'], 2);
$srt_amt 	= round($data['totals']['srt_amt'], 2);
$sale_amt 	= round($data['totals']['sale_amt'], 2);
$bill 		= $data['totals']['bill'];
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
								<td ></td>
								<td ></td>
	                            <td >$st_qty</td>
	                            <td >$srt_qty</td>
	                            <td >$sale_qty</td>
	                            <td >$st_amt</td>
	                            <td >$st_disc</td>
	                            <td >$srt_amt</td>
	                            <td >$sale_amt</td>
	                            <td >$bill</td>
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