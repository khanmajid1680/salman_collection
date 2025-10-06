<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "PAYMENT REMINDER";
$file_name = "PAYMENT REMINDER";
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
$as_on_date  		= (isset($_GET['as_on_date'])) ? $_GET['as_on_date'] : date('d-m-Y');
$account_name 		= isset($_GET['_acc_id']) && !empty($_GET['_acc_id']) ? strtoupper($data['data'][0]['account_name']) : '';
$bill_no 			= isset($_GET['pm_bill_no']) && !empty($_GET['pm_bill_no']) ? $data['data'][0]['pm_bill_no'] : '';
$from_bill_date  	= (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
$to_bill_date    	= (isset($_GET['to_bill_date']) && $_GET['to_bill_date'] != '') ? " TO ".$_GET['to_bill_date'] : "";
$from_credit_day  	= (isset($_GET['from_credit_day'])) ? $_GET['from_credit_day'] : "";
$to_credit_day    	= (isset($_GET['to_credit_day']) && $_GET['to_credit_day'] != '') ? " TO ".$_GET['to_credit_day'] : "";
$from_rem_day  		= (isset($_GET['from_rem_day'])) ? $_GET['from_rem_day'] : "";
$to_rem_day    		= (isset($_GET['to_rem_day']) && $_GET['to_rem_day'] != '') ? " TO ".$_GET['to_rem_day'] : "";
$from_qty 		 	= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
$to_qty 		 	= (isset($_GET['to_qty']) && $_GET['to_qty'] != '') ? " TO ".$_GET['to_qty'] : "";
$from_bill_amt 	 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
$to_bill_amt 	 	= (isset($_GET['to_bill_amt']) && $_GET['to_bill_amt'] != '') ? " TO ".$_GET['to_bill_amt'] : "";
$tbl .= <<<EOD
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>PAYMENT REMINDER</b> (<span style="font-size:10px;">$branch</span>)</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="30%">AS ON DATE : $as_on_date</td>
						<td width="50%">SUPPLIER : $account_name</td>
						<td width="20%">BILL NO : $bill_no</td>
					</tr>
					<tr>
						<td width="30%">BILL DATE : $from_bill_date $to_bill_date</td>
						<td width="25%">CREDIT DAYS : $from_credit_day $to_credit_day</td>
						<td width="25%">OVERDUE DAYS : $from_rem_day $to_rem_day</td>
						<td width="20%">BAL AMT : $from_bill_amt $to_bill_amt</td>
					</tr>
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr>
		                <th width="5%">#</th>
                        <th width="30%">SUPPLIER</th>
                        <th width="12%">BILL NO</th>
                        <th width="10%">BILL DATE</th>
                        <th width="10%">CREDIT DAYS</th>
                        <th width="10%">DUE DATE</th>
                        <th width="13%">REMAINING DAYS</th>
                        <th width="10%">BAL AMT</th>
		            </tr>

EOD;
					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
							$account_name 	= $value['account_name'];
							$pm_bill_no 	= $value['pm_bill_no'];
							$pm_bill_date 	= date('d-m-Y', strtotime($value['pm_bill_date']));
							$credit_days 	= $value['account_credit_days'];
							$due_date 		= $value['due_date'];
							$diff 			= $value['diff'];
							$bal_amt 		= round($value['bal_amt'], 2);
							$tbl .= <<<EOD
							<tr>
								<td >$sr_no</td>
								<td >$account_name</td>
								<td >$pm_bill_no</td>
								<td >$pm_bill_date</td>
								<td >$credit_days</td>
								<td >$due_date</td>
								<td >$diff</td>
								<td >$bal_amt</td>
							</tr>
EOD;
						$sr_no++;
						endforeach;
					endif;
$bal_amt 	= round($data['totals']['bal_amt'], 2);
$tbl .= <<<EOD
							<tr style="font-weight:bold;">
								<td colspan="6"></td>
								<td>TOTAL</td>
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