<?php 
	$this->load->view('templates/header'); 
	$action = (isset($_GET['action'])) ? $_GET['action'] : "";
?>
<script>
    let link 	= "report";
    let sub_link= "balance_sheet";
</script>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item active" aria-current="page">
	    	BALANCE SHEET
	    </li>
	    <li class="breadcrumb-item" aria-current="reload-page">
	    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('report/balance_sheet?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
	    </li>
	  </ol>
	</nav>
	<div class="d-none d-sm-block">
		<?= $this->pagination->create_links(); ?>
	</div>
</section>
<section class="container-fluid">
	<div class="d-flex">
		<div class="col-6">
			<div class="row sticky_table_header">
				<div class="col-12">
					<table class="table table-sm table-dark">
						<thead>
							<tr>
				                <th width="10%">#</th>
		                        <th width="20%">LIABILITIES HEAD</th>
		                        <th width="20%">AMT</th>
				            </tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="box">
						<table class="table table-sm table-hover">
							<tbody>
								<?php 
									if(!empty($liabilities['liabilities_details'])):
										$total_pay_bal 		= 0;
	                                    $total_receive_bal 	= 0;
	                                    $total_receive_bal1 = 0;
	                                    $total_bal_amt 		= 0;
	                                    $sr_no 				= 1;
	                                    foreach ($liabilities['liabilities_details'] as $key => $value):
	                                    	if($value['bal_mode'] == 'PAY'){
	                                            $total_pay_bal = $total_pay_bal + $value['total_bal'];
											}
	                                        elseif($value['bal_mode'] == 'RECEIVE'){
	                                        	if($value['acc_grp_id'] == 6){
	                                                $total_receive_bal = $total_receive_bal - $value['total_bal'];    
	                                            }else{
	                                                $total_receive_bal = $total_receive_bal + $value['total_bal'];    
	                                            }
	                                        }
	                                     	$total_bal_amt = abs($total_pay_bal) + abs($total_receive_bal);
	                                     	if($total_bal_amt >= 0){
	                                            $total_bal_amt = $total_bal_amt;//." TO PAY";
	                                     	}else{
	                                            $total_bal_amt = $total_bal_amt;//." TO RECEIVE";
	                                     	}
	                                     	if(($value['total_bal_in_number'] != "0") && ($zero_check == '')): // for check bal zero
	                            ?>
	                            				<tr>
													<td width="10%" <?php if($value['acc_grp_id'] == 6){?>style="color:red" <?php } ?>>>
														<?php echo $sr_no; ?>
													</td>
													<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
													<td width="20%"><?php echo number_format($value['total_bal'], 2); ?></td>
												</tr>
	                            <?php
	                                     	else:
	                            ?>
	                            				<tr>
													<td width="10%"><?php echo $sr_no; ?></td>
													<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
													<td width="20%"><?php echo number_format($value['total_bal'], 2); ?></td>
												</tr>
	                            <?php
	                                     	endif;
	                                     	$sr_no++;
	                                    endforeach;
	                            ?>
	                            		<tr>
											<td width="10%"></td>
											<td width="20%">TOTAL AMT</td>
											<td width="20%"><?php echo $total_bal_amt; ?></td>
										</tr>
	                            <?php
	                            		$income = abs($profit_loss['total_income_head']);
                                        $expense = abs($profit_loss['total_expense_head']);
                                        $prf_loss = $income - $expense;
                                        if($prf_loss >= 0){
                                            $prf = $prf_loss;
                                        }else{
                                            $loss = $prf_loss;   
                                        }
                                        if(!empty($loss)):
                                        	$total_liabilities = $total_bal_amt + $loss;
                                ?>
                                			<tr>
												<td width="10%"></td>
												<td width="20%"><b>Loss Amt</b></td>
												<td width="20%" style="color:red"><b><?php echo !empty($loss)? $loss:'' ?></b></td>
											</tr>
                                			<tr>
												<td width="10%"></td>
												<td width="20%">Total Liabilities</td>
												<td width="20%"><?php echo $total_liabilities; ?></td>
											</tr>		
                                <?php
                                        endif;
									endif;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		</div>
		<div class="col-6">
			<div class="row sticky_table_header">
				<div class="col-12">
					<table class="table table-sm table-dark">
						<thead>
							<tr>
				                <th width="10%">#</th>
		                        <th width="20%">ASSETS HEAD</th>
		                        <th width="20%">AMT</th>
				            </tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="row mt-5">
				<div class="col-12">
					<div class="box">
						<table class="table table-sm table-hover">
							<tbody>
								<?php 
									if(!empty($asset['asset_details'])): 
										$total_pay_bal = 0;
	                                    $total_receive_bal = 0;
	                                    $sr_no = 1;
	                                    foreach ($asset['asset_details'] as $key => $value):
	                                    	if($value['bal_mode'] == 'PAY'){
	                                            $total_pay_bal = $total_pay_bal + $value['total_bal'];
											}
	                                        elseif($value['bal_mode'] == 'RECEIVE'){
	                                            $total_receive_bal = $total_receive_bal + $value['total_bal'];    
	                                        }
	                                     	$total_bal_amt = abs($total_pay_bal) - abs($total_receive_bal);
	                                     	if($total_bal_amt >= 0){
	                                            $total_bal_amt = $total_bal_amt;//." TO PAY";
	                                     	}else{
	                                            $total_bal_amt = $total_bal_amt;//." TO RECEIVE";
	                                     	}
	                         	?>
	                         				<tr>
												<td width="10%"><?php echo $sr_no; ?></td>
												<td width="20%"><?php echo strtoupper($value['account_name']); ?></td>
												<td width="20%"><?php echo number_format($value['total_bal'], 2); ?></td>
											</tr>
	                            <?php         	
	                                     	$sr_no++;
	                                    endforeach;
	                            ?>
	                            		<tr>
											<td width="10%"></td>
											<td width="20%">TOTAL AMT</td>
											<td width="20%"><?php echo $total_bal_amt; ?></td>
										</tr>
	                            <?php
	                            		$income = abs($profit_loss['total_income_head']);
	                                    $expense = abs($profit_loss['total_expense_head']);
	                                    // echo $income;
	                                    // echo $expense;
	                                    $prf_loss = $income - $expense;
	                                    // echo $prf_loss;

	                                    if($prf_loss >= 0){
	                                        $prf = $prf_loss;
	                                    }else{
	                                        $loss = $prf_loss;   
	                                    }
	                                    if(!empty($prf)):
	                                    	$total_assets = $total_bal_amt + $prf;
	                            ?>
	                            			<tr>
												<td width="10%"></td>
												<td width="20%"><b>Profit Amt</b></td>
												<td width="20%" style="color:red"><b><?php echo !empty($prf)? $prf:'' ?></b></td>
											</tr>
	                            			<tr>
												<td width="10%"></td>
												<td width="20%">Total Assets</td>
												<td width="20%"><?php echo $total_assets; ?></td>
											</tr>		
	                            <?php
	                                    endif;
									endif;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		</div>
	</div>
	
</section>
<?= $this->pagination->create_links(); ?>
<?php $this->load->view('templates/footer'); ?>
	</body>
</html>