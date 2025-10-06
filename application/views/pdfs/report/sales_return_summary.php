<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "SALES RETURN SUMMARY";
$file_name = "SALES RETURN SUMMARY";
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
$entry_no 			= isset($_GET['srm_entry_no']) && !empty($_GET['srm_entry_no']) ? $data['data'][0]['srm_entry_no'] : '';
$from_entry_date  	= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
$to_entry_date    	= (isset($_GET['to_entry_date']) && $_GET['to_entry_date'] != '') ? " TO ".$_GET['to_entry_date'] : "";
$account_name 		= isset($_GET['srm_acc_id']) && !empty($_GET['srm_acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$user_fullname 		= isset($_GET['srt_user_id']) && !empty($_GET['srt_user_id']) ? strtoupper($data['data'][0]['user_fullname']) : '';
$barcode 			= isset($_GET['bm_id']) && !empty($_GET['bm_id']) ? $data['data'][0]['bm_item_code'] : '';
$style_name 		= isset($_GET['style_id']) && !empty($_GET['style_id']) ? $data['data'][0]['style_name'] : '';
$design_name 		= isset($_GET['design_id']) && !empty($_GET['design_id']) ? $data['data'][0]['design_name'] : '';
$brand_name 		= isset($_GET['brand_id']) && !empty($_GET['brand_id']) ? $data['data'][0]['brand_name'] : '';
$age_name 			= isset($_GET['age_id']) && !empty($_GET['age_id']) ? $data['data'][0]['age_name'] : '';
$from_qty 			= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 			= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_bill_amt 		= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
$to_bill_amt 		= (isset($_GET['to_bill_amt']) && $_GET['to_bill_amt'] != '') ? " TO ".$_GET['to_bill_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>SALES RETURN SUMMARY</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="25%">ENTRY NO : $entry_no</td>
						<td width="30%">ENTRY DATE : $from_entry_date $to_entry_date</td>
						<td width="45%">CUSTOMER : $account_name</td>
					</tr>
					<tr>
						<td width="25%">BARCODE : $barcode</td>
						<td width="30%">TOTAL AMT : $from_bill_amt $to_bill_amt</td>
						<td width="45%">SALES PERSON : $user_fullname</td>
					</tr>
					<tr>
						<td width="25%">STYLE : $style_name</td>
						<td width="25%">BRAND : $brand_name</td>
						<td width="25%">design : $design_name</td>
						<td width="25%">AGE GROUP : $age_name</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="3%">#</th>
                        <th width="7%">ENTRY NO</th>
                        <th width="7%">ENTRY DATE</th>
                        <th width="15%">CUSTOMER</th>
                        <th width="10%">BARCODE</th>
                        <th width="8%">STYLE</th>
                        <th width="8%">BRAND</th>
                        <th width="11%">DESIGN</th>
                        <th width="6%">RATE</th>
                        <th width="6%">DISC AMT</th>
                        <th width="6%">T.AMT</th>
                        <th width="6%">GST</th>
                        <th width="7%">TOTAL AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$srm_entry_no 	= $value['srm_entry_no'];
							$srm_entry_date = date('d-m-Y', strtotime($value['srm_entry_date']));
							$account_name 	= strtoupper($value['account_name']);
							$user_fullname 	= strtoupper($value['user_fullname']);
							$bm_item_code 	= $value['bm_item_code'];
							$style_name 	= strtoupper($value['style_name']);
							$brand_name 	= strtoupper($value['brand_name']);
							$design_name 	= strtoupper($value['design_name']);
							$age_name 		= strtoupper($value['age_name']);

							$srt_rate 		= round($value['srt_rate'], 2);
							$srt_disc_amt 	= round($value['srt_disc_amt'], 2);
							$taxable_amt 	= round($value['srt_taxable_amt'], 2);
							$gst_amt 		= round($value['gst_amt'], 2);
							$srt_total_amt 	= round($value['srt_total_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$srm_entry_no</td>
								<td >$srm_entry_date</td>
								<td >$account_name</td>
								<td >$bm_item_code</td>
								<td >$style_name</td>
								<td >$brand_name</td>
								<td >$design_name</td>
								<td >$srt_rate</td>
								<td >$srt_disc_amt</td>
								<td >$taxable_amt</td>
								<td >$gst_amt</td>
								<td >$srt_total_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$total_qty 	= $data['totals']['total_qty'];
$disc_amt 	= round($data['totals']['disc_amt'], 2);
$taxable_amt 	= round($data['totals']['taxable_amt'], 2);
$gst_amt 	= round($data['totals']['gst_amt'], 2);
$total_amt 	= round($data['totals']['total_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            <td ></td>
	                            
	                            <td >TOTALS</td>
	                            <td >$disc_amt</td>
	                            <td >$taxable_amt</td>
	                            <td >$gst_amt</td>
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