<?php	
$this->mypdf_class->tcpdf();
$obj_pdf = new TCPDF('L', PDF_UNIT, array('190','250'), true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$title = "CA PEPORT";
$file_name = "CA PEPORT";
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
$bill_no 			= isset($_GET['sm_bill_no']) && !empty($_GET['sm_bill_no']) ? $data['data'][0]['sm_bill_no'] : '';
$from_bill_date  	= (isset($_GET['from_bill_date'])) ? $_GET['from_bill_date'] : "";
$to_bill_date    	= (isset($_GET['to_bill_date']) && $_GET['to_bill_date'] != '') ? " TO ".$_GET['to_bill_date'] : "";

$tbl .= '
	<br pagebreak="true">
	<table cellpadding="2">
		<tr>
			<td align="center" style="font-size:10px;"><b>CA PEPORT</b> </td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:10px;">
					<tr>
						<td width="50%">BILL NO : '.$bill_no.'</td>
						<td width="50%">BILL DATE : '.$from_bill_date.' '.$to_bill_date.'</td>
					</tr>
					
				</table>		
			</td>
		</tr>		
		<tr>
			<td>
				<table border="1" style="font-size:9px;">
					<tr style="font-weight:bold;">
		                <th width="3%">#</th>
                        <th width="7%">BILL DATE</th>
                        <th width="7%">BILL NO</th>
                        <th width="7%">M/S NET 5%</th>
                        <th width="7%">CGST 2.5%</th>
                        <th width="7%">SGST 2.5%</th>
                        <th width="7%">CASH</th>
                        <th width="7%">M/S NET 18%</th>
                        <th width="7%">CGST 9%</th>
                        <th width="7%">SGST 9%</th>
                        <th width="7%">OMS NET 5%</th>
                        <th width="7%">IGST 5%</th>
                        <th width="7%">OMS NET 18%</th>
                        <th width="7%">IGST 18%</th>
                        <th width="7%">TOTAL</th>
		            </tr>';

					if(!empty($data['data'])):
						$sr_no = 1;
						foreach ($data['data'] as $key => $value):
						$tbl .= '
							<tr>
								<td>'.$sr_no.'</td>
								<td>'. $value['entry_date'].'</td>
								<td>'. $value['sm_bill_no'].'</td>
								<td>'. $value['ms_net_5'].'</td>
		                        <td>'. $value['cgst_25'].'</td>
		                        <td>'. $value['sgst_25'].'</td>
		                        <td>'. $value['cash_amt'].'</td>
		                        <td>'. $value['ms_net_18'].'</td>
		                        <td>'. $value['cgst_9'].'</td>
		                        <td>'. $value['sgst_9'].'</td>
		                        <td>'. $value['oms_net_5'].'</td>
		                        <td>'. $value['igst_5'].'</td>
		                        <td>'. $value['oms_net_18'].'</td>
		                        <td>'. $value['igst_18'].'</td>
		                        <td>'. $value['total_amt'].'</td>
							</tr>';

						$sr_no++;
						endforeach;
					endif;

					$tbl .= '
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td>'. $data['totals']['ms_net_5'].'</td>
		                        <td>'. $data['totals']['cgst_25'].'</td>
		                        <td>'. $data['totals']['sgst_25'].'</td>
		                        <td>'. $data['totals']['cash_amt'].'</td>
		                        <td>'. $data['totals']['ms_net_18'].'</td>
		                        <td>'. $data['totals']['cgst_9'].'</td>
		                        <td>'. $data['totals']['sgst_9'].'</td>
		                        <td>'. $data['totals']['oms_net_5'].'</td>
		                        <td>'. $data['totals']['igst_5'].'</td>
		                        <td>'. $data['totals']['oms_net_18'].'</td>
		                        <td>'. $data['totals']['igst_18'].'</td>
		                        <td>'. $data['totals']['total_amt'].'</td>
							</tr>
				</table>		
			</td>
		</tr>		
	</table>';


$obj_pdf->writeHTML($tbl, true, false, false, false, '');
$height = $obj_pdf->getY();
$obj_pdf->deletePage(1);
$obj_pdf->setPage($obj_pdf->getPage()); 
$obj_pdf->Output($file_name, 'I');
?>