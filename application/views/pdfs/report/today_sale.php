<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "WHAT SOLD TODAY";
$file_name = "WHAT SOLD TODAY";
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
$account_name 	= isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id']) ? $data['data'][0]['account_name'] : '';
$brand_name	 	= isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id']) ? $data['data'][0]['bm_brand_id'] : '';
$design_name 	= isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id']) ? $data['data'][0]['bm_design_id'] : '';
$style_name 	= isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id']) ? $data['data'][0]['bm_style_id'] : '';
$from_date 			= (isset($_GET['from_date'])) ? $_GET['from_date'] : date('d-m-Y');
$to_date 			= (isset($_GET['to_date']) && $_GET['to_date'] != '') ? " TO ".$_GET['to_date'] : " TO ".date('d-m-Y');
$from_qty 			= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 			= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_amt 			= (isset($_GET['from_amt'])) ? $_GET['from_amt'] : "";
$to_amt 			= (isset($_GET['to_amt']) && $_GET['to_amt'] != '') ? " TO ".$_GET['to_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>WHAT SOLD TODAY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="50%">DATE : $from_date $to_date</td>
						<td width="50%">SUPPLIER : $account_name</td>
					</tr>
					<tr>
						<td width="20%">DESIGN : $design_name</td>
						<td width="20%">BRAND : $brand_name</td>
						<td width="20%">STYLE : $style_name</td>
						<td width="20%">SOLD QTY : $from_qty $to_qty</td>
						<td width="20%">SALE AMT : $from_amt $to_amt</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
		                <th width="20%">SUPPLIER</th>
	                    <th width="20%">DESIGN</th>
	                    <th width="20%">BRAND</th>
	                    <th width="15%">STYLE</th>
	                    <th width="11%">SOLD QTY</th>
	                    <th width="11%">SALE AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$design_name 	= $value['design_name'];
							$brand_name 	= $value['brand_name'];
							$style_name 	= $value['style_name'];
							$age_name 		= $value['age_name'];
							$st_qty 		= $value['st_qty'];
							$st_amt 		= round($value['st_amt'], 2);

							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$design_name</td>
								<td >$brand_name</td>
								<td >$style_name</td>
								<td >$st_qty</td>
								<td >$st_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$st_qty = $data['totals']['st_qty'];
$st_amt = round($data['totals']['st_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td ></td>
			                    <td >TOTALS</td>
			                    <td >$st_qty</td>
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