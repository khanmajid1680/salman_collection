<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "STOCK MOVEMENT";
$file_name = "STOCK MOVEMENT";
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
$gender_name 		= isset($_GET['bm_gender_id']) && !empty($_GET['bm_gender_id']) ? $data['data'][0]['gender_name'] : '';
$brand_name 		= isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id']) ? $data['data'][0]['brand_name'] : '';
$age_name 			= isset($_GET['bm_age_id']) && !empty($_GET['bm_age_id']) ? $data['data'][0]['age_name'] : '';
$date_frm 			= (isset($_GET['date_frm'])) ? $_GET['date_frm'] : "";
$date_to 			= (isset($_GET['date_to']) && $_GET['date_to'] != '') ? ' TO '.$_GET['date_to'] : "";
$diff_frm 			= (isset($_GET['diff_frm'])) ? $_GET['diff_frm'] : "";
$diff_to 			= (isset($_GET['diff_to']) && $_GET['diff_to'] != '') ? ' TO '.$_GET['diff_to'] : "";
$bal_qty_frm 		= (isset($_GET['bal_qty_frm'])) ? $_GET['bal_qty_frm'] : 1;
$bal_qty_to 		= (isset($_GET['bal_qty_to']) && $_GET['bal_qty_to'] != '') ? ' TO '.$_GET['bal_qty_to'] : "";
$bal_amt_frm 		= (isset($_GET['bal_amt_frm'])) ? $_GET['bal_amt_frm'] : "";
$bal_amt_to 		= (isset($_GET['bal_amt_to']) && $_GET['bal_amt_to'] != '') ? ' TO '.$_GET['bal_amt_to'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>STOCK MOVEMENT</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="40%">SUPPLIER : $account_name</td>
						<td width="30%">STYLE : $style_name</td>
						<td width="30%">GENDER : $gender_name</td>
					</tr>
					<tr>
						<td width="40%">PURCHASE DATE : $date_frm $date_to</td>
						<td width="30%">BRAND : $brand_name</td>
						<td width="30%">AGE GROUP : $age_name</td>
					</tr>
					<tr>
						<td width="20%">DATE DIFF : $diff_frm $diff_to</td>
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
	                    <th width="10%">SUPPLIER</th>
	                    <th width="8%">STYLE</th>
	                    <th width="7%">GENDER</th>
	                    <th width="7%">BRAND</th>
	                    <th width="7%">AGE</th>
	                    <th width="8%">PURCHASE DATE</th>
	                    <th width="7%">DATE DIFF</th>
	                    <th width="5%">PUR QTY</th>
	                    <th width="8%">PUR RET. QTY</th>
	                    <th width="5%">SALE QTY</th>
	                    <th width="8%">SALE RET QTY</th>
	                    <th width="5%">PUR RATE</th>
	                    <th width="6%">BAL. QTY</th>
	                    <th width="6%">STOCK AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$style_name 	= $value['style_name'];
							$gender_name	= $value['gender_name'];
							$age_name		= $value['age_name'];
							$pm_entry_date	= $value['pm_entry_date'];
							$date_diff		= $value['date_diff'];
							$pt_qty 		= $value['pt_qty'];
							$pt_rate 		= round($value['pt_rate'], 2);
							$prt_qty 		= $value['prt_qty'];
							$st_qty 		= $value['st_qty'];
							$srt_qty 		= $value['srt_qty'];
							$sold_amt 		= round($value['sold_amt'], 2);
							$bal_qty 		= $value['bal_qty'];
							$bal_amt 		= round($value['bal_amt'], 2);

							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$style_name</td>
								<td >$gender_name</td>
								<td >$brand_name</td>
								<td >$age_name</td>
								<td >$pm_entry_date</td>
								<td >$date_diff</td>
								<td >$pt_qty</td>
								<td >$prt_qty</td>
								<td >$st_qty</td>
								<td >$srt_qty</td>
								<td >$pt_rate</td>
								<td >$bal_qty</td>
								<td >$bal_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$pt_qty = $data['totals']['pt_qty'];
$prt_qty= $data['totals']['prt_qty'];
$st_qty = $data['totals']['st_qty'];
$srt_qty= $data['totals']['srt_qty'];
$bal_qty= $data['totals']['bal_qty'];
$bal_amt= round($data['totals']['bal_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>TOTALS</td>
								<td>$pt_qty</td>
								<td>$prt_qty</td>
								<td>$st_qty</td>
								<td>$srt_qty</td>
								<td></td>
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