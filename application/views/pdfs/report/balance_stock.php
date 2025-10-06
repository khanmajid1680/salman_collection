<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "BALANCE STOCK";
$file_name = "BALANCE STOCK";
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
$style_name 		= isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id']) ? $data['data'][0]['style_name'] : '';
$design_name 		= isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id']) ? $data['data'][0]['design_name'] : '';
$brand_name 		= isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id']) ? $data['data'][0]['brand_name'] : '';
$age_name 			= isset($_GET['bm_age_id']) && !empty($_GET['bm_age_id']) ? $data['data'][0]['age_name'] : '';
$pt_amt_frm 		= (isset($_GET['pt_amt_frm'])) ? $_GET['pt_amt_frm'] : "";
$pt_amt_to 			= (isset($_GET['pt_amt_to']) && $_GET['pt_amt_to'] != '') ? ' TO '.$_GET['pt_amt_to'] : "";
$st_amt_frm 		= (isset($_GET['st_amt_frm'])) ? $_GET['st_amt_frm'] : "";
$st_amt_to 			= (isset($_GET['st_amt_to']) && $_GET['st_amt_to'] != '') ? ' TO '.$_GET['st_amt_to'] : "";
$sold_amt_frm 		= (isset($_GET['sold_amt_frm'])) ? $_GET['sold_amt_frm'] : "";
$sold_amt_to 		= (isset($_GET['sold_amt_to']) && $_GET['sold_amt_to'] != '') ? ' TO '.$_GET['sold_amt_to'] : "";
$bal_qty_frm 		= (isset($_GET['bal_qty_frm'])) ? $_GET['bal_qty_frm'] : 1;
$bal_qty_to 		= (isset($_GET['bal_qty_to']) && $_GET['bal_qty_to'] != '') ? ' TO '.$_GET['bal_qty_to'] : "";
$bal_amt_frm 		= (isset($_GET['bal_amt_frm'])) ? $_GET['bal_amt_frm'] : "";
$bal_amt_to 		= (isset($_GET['bal_amt_to']) && $_GET['bal_amt_to'] != '') ? ' TO '.$_GET['bal_amt_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>BALANCE STOCK</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="40%">SUPPLIER : $account_name</td>
						<td width="30%">STYLE : $style_name</td>
						<td width="30%">design : $design_name</td>
					</tr>
					<tr>
						<td width="20%">PUR AMT : $pt_amt_frm $pt_amt_to</td>
						<td width="20%">SALE AMT : $st_amt_frm $st_amt_to</td>
						<td width="30%">BRAND : $brand_name</td>
						<td width="30%">AGE GROUP : $age_name</td>
					</tr>
					<tr>
						<td width="20%">SOLD AMT : $sold_amt_frm $sold_amt_to</td>
						<td width="20%">BAL QTY : $bal_qty_frm $bal_qty_to</td>
						<td width="60%">BAL AMT : $bal_amt_frm $bal_amt_to</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
	                    <th width="7%">SUPPLIER</th>
	                    <th width="9%">STYLE</th>
	                    <th width="12%">DESIGN</th>
	                    <th width="7%">BRAND</th>
	                    <th width="5%">PUR QTY</th>
	                    <th width="5%">PUR RATE</th>
	                    <th width="6%">PUR AMT</th>
	                    <th width="5%">PUR RET. QTY</th>
	                    <th width="5%">SALE QTY</th>
	                    <th width="6%">SALE RATE</th>
	                    <th width="6%">SALE AMT</th>
	                    <th width="6%">SALE RET QTY</th>
	                    <th width="6%">SOLD QTY X PUR RATE</th>
	                    <th width="6%">BAL. QTY</th>
	                    <th width="6%">STOCK AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$style_name 	= $value['style_name'];
							$design_name	= $value['design_name'];
							$pt_qty 		= $value['pt_qty'];
							$pt_rate 		= round($value['pt_rate'], 2);
							$pt_amt 		= round($value['pt_amt'],2);
							$prt_qty 		= $value['prt_qty'];
							$st_qty 		= $value['st_qty'];
							$st_rate 		= round($value['st_rate'], 2);
							$st_amt 		= round($value['st_amt'], 2);
							$srt_qty 		= $value['srt_qty'];
							$sold_amt 		= round($value['sold_amt'], 2);
							$bal_qty 		= $value['bal_qty'];
							$bal_amt 		= round($value['bal_amt'], 2);

							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$style_name</td>
								<td >$design_name</td>
								<td >$brand_name</td>
								<td >$pt_qty</td>
								<td >$pt_rate</td>
								<td >$pt_amt</td>
								<td >$prt_qty</td>
								<td >$st_qty</td>
								<td >$st_rate</td>
								<td >$st_amt</td>
								<td >$srt_qty</td>
								<td >$sold_amt</td>
								<td >$bal_qty</td>
								<td >$bal_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$pt_qty = $data['totals']['pt_qty'];
$pt_amt = round($data['totals']['pt_amt'], 2);
$prt_qty= $data['totals']['prt_qty'];
$st_qty = $data['totals']['st_qty'];
$st_amt = round($data['totals']['st_amt'], 2);
$srt_qty= $data['totals']['srt_qty'];
$sold_amt= round($data['totals']['sold_amt'], 2);
$bal_qty= $data['totals']['bal_qty'];
$bal_amt= round($data['totals']['bal_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>TOTALS</td>
								<td>$pt_qty</td>
								<td></td>
								<td>$pt_amt</td>
								<td>$prt_qty</td>
								<td>$st_qty</td>
								<td></td>
								<td>$st_amt</td>
								<td>$srt_qty</td>
								<td>$sold_amt</td>
								<td>$bal_qty</td>
								<td>$bal_amt</td>
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