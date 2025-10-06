<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$_date_from 	= (isset($_GET['_date_from'])) ? $_GET['_date_from'] : "";
	$_date_to 		= (isset($_GET['_date_to'])) ? $_GET['_date_to'] : "";
	$_amt_from 		= (isset($_GET['_amt_from'])) ? $_GET['_amt_from'] : "";
	$_amt_to 		= (isset($_GET['_amt_to'])) ? $_GET['_amt_to'] : "";
?>
<script>
    let link = "voucher";
    let sub_link = "branch_payment";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('voucher/branch_payment?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
	    	    <li class="breadcrumb-item active" aria-current="page">
			    	BRANCH PAYMENT(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="add-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('voucher/branch_payment?action=add')" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a >
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('voucher/branch_payment?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="search-box">
			    	<input type="checkbox" id="search_status" name="search_status" data-toggle="toggle" data-on="FILTER <i class='fa fa-eye'></i>" data-off="FILTER <i class='fa fa-eye-slash'></i>" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="mini" data-style="show-hide" onchange="set_search_box()" <?php echo empty($search_status) ? 'checked' : ''; ?>>
			    </li>
			  </ol>
			</nav>
			<div class="d-none d-sm-block height_60_px">
				<?= $this->pagination->create_links(); ?>
			</div>
		</div>
		<div class="row collapse mt-2 <?php echo empty($search_status) ? '' : 'show'  ?>" id="search_box">
			<div class="d-flex flex-wrap justify-content-center floating-form">
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['_entry_no'])): ?><p>ENTRY NO</p><?php endif; ?>
					<select class="form-control floating-select" id="_entry_no" name="_entry_no">
                    	<?php if(isset($data['search']['_entry_no']) && !empty($data['search']['_entry_no'])): ?>
                        	<option value="<?php echo $data['search']['_entry_no']['value']; ?>" selected>
                            	<?php echo $data['search']['_entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="_date_from" name="_date_from" value="<?php echo $_date_from ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
	                    <label for="inputEmail3">ENTRY DATE <small class="font-weight-bold">FROM</small></label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="_date_to" name="_date_to" value="<?php echo $_date_to ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
	                    <label for="inputEmail3">ENTRY DATE <small class="font-weight-bold">TO</small></label>
					</div>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['_account_name'])): ?><p>ACCOUNT</p><?php endif; ?>
					<select class="form-control floating-select" id="_account_name" name="_account_name">
                    	<?php if(isset($data['search']['_account_name']) && !empty($data['search']['_account_name'])): ?>
                        	<option value="<?php echo $data['search']['_account_name']['value']; ?>" selected>
                            	<?php echo $data['search']['_account_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['_party_name'])): ?><p>BRANCH</p><?php endif; ?>
					<select class="form-control floating-select" id="_party_name" name="_party_name">
                    	<?php if(isset($data['search']['_party_name']) && !empty($data['search']['_party_name'])): ?>
                        	<option value="<?php echo $data['search']['_party_name']['value']; ?>" selected>
                            	<?php echo $data['search']['_party_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="_amt_from" name="_amt_from" value="<?php echo $_amt_from ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
	                    <label for="inputEmail3">TOTAL AMT <small class="font-weight-bold">FROM</small></label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="_amt_to" name="_amt_to" value="<?php echo $_amt_to ?>" placeholder=" " autocomplete="off" onchange="trigger_search()"/>   
	                    <label for="inputEmail3">TOTAL AMT <small class="font-weight-bold">TO</small></label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="3%">#</th>
	                        <th width="7%">ENTRY NO</th>
	                        <th width="10%">ENTRY DATE</th>
	                        <th width="15%">ACCOUNT NAME</th>
	                        <th width="15%">BRANCH</th>
	                        <th width="10%">VOUCHER AMT</th>
	                        <th width="10%">TOTAL AMT</th>
	                        <th width="13%">NOTES</th>
	                        <th width="3%" align="center">EDIT</th> 
	                        <th width="4%" align="center">DELETE</th>
			            </tr>
					</thead>
				</table>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-reponsive table-hover" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value['vm_id'], SECRET_KEY);
					?>

								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="7%"><?php echo $value['vm_entry_no']; ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['vm_entry_date'])); ?></td>
									<td width="15%"><?php echo strtoupper($value['account_name']); ?></td>
									<td width="15%"><?php echo strtoupper($value['party_name']); ?></td>
									<td width="10%"><?php echo $value['vm_total_amt']; ?></td>
									<td width="10%"><?php echo number_format($value['vm_total_amt'] + $value['vm_round_off'], 2); ?></td>
									<td width="13%"><?php echo $value['vm_notes']; ?></td>
									
									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('<?php echo 'voucher/branch_payment?action=edit&id='.$value['vm_id'] ?>')">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="4%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'voucher/branch_payment/remove/'.$value['vm_id']?>');">
												<i class="text-danger fa fa-trash"></i>
											</a>
										<?php endif; ?>                         
									</td>
								</tr>
					<?php 
							endforeach;
						else: 
					?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>

<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/voucher/branch_payment.js')?>"></script>
</body>
</html>