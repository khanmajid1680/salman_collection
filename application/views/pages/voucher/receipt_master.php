<?php 
	$this->load->view('templates/header'); 
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_entry_date= (isset($_GET['from_entry_date'])) ? $_GET['from_entry_date'] : "";
	$to_entry_date 	= (isset($_GET['to_entry_date'])) ? $_GET['to_entry_date'] : "";
	$from_amt 		= (isset($_GET['from_amt'])) ? $_GET['from_amt'] : "";
	$to_amt 		= (isset($_GET['to_amt'])) ? $_GET['to_amt'] : "";
?>
<script>
    let link = "voucher";
    let sub_link = "receipt";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('voucher/receipt?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
	    	    <li class="breadcrumb-item active" aria-current="page">
			    	RECEIPT(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="add-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('voucher/receipt?action=add')" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a >
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('voucher/receipt?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
				<div class="col-6 col-sm-3 col-md-2 col-lg-2 floating-label">
					<?php if(isset($data['search']['entry_no'])): ?><p>ENTRY NO</p><?php endif; ?>
					<select class="form-control floating-select" id="entry_no" name="entry_no">
                    	<?php if(isset($data['search']['entry_no']) && !empty($data['search']['entry_no'])): ?>
                        	<option value="<?php echo $data['search']['entry_no']['value']; ?>" selected>
                            	<?php echo $data['search']['entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_entry_date" name="from_entry_date" value="<?php echo $from_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ENTRY DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_entry_date" name="to_entry_date" value="<?php echo $to_entry_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ENTRY DATE</label>
					</div>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['account_id'])): ?><p>ACCOUNT NAME</p><?php endif; ?>
					<select class="form-control floating-select" id="account_id" name="account_id">
                    	<?php if(isset($data['search']['account_id']) && !empty($data['search']['account_id'])): ?>
                        	<option value="<?php echo $data['search']['account_id']['value']; ?>" selected>
                            	<?php echo $data['search']['account_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-4 col-md-3 col-lg-2 floating-label">
					<?php if(isset($data['search']['party_id'])): ?><p>PARTY NAME</p><?php endif; ?>
					<select class="form-control floating-select" id="party_id" name="party_id">
                    	<?php if(isset($data['search']['party_id']) && !empty($data['search']['party_id'])): ?>
                        	<option value="<?php echo $data['search']['party_id']['value']; ?>" selected>
                            	<?php echo $data['search']['party_id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_amt" name="from_amt" value="<?php echo $from_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM TOTAL AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_amt" name="to_amt" value="<?php echo $to_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO TOTAL AMT</label>
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
	                        <th width="15%">PARTY NAME</th>
	                        <th width="10%">VOUCHER AMT</th>
	                        <th width="10%">ROUND OFF</th>
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
									<td width="10%"><?php echo $value['vm_round_off']; ?></td>
									<td width="10%"><?php echo number_format($value['vm_total_amt'] + $value['vm_round_off'], 2); ?></td>
									<td width="13%"><?php echo $value['vm_notes']; ?></td>
									
									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('<?php echo 'voucher/receipt?action=edit&id='.$value['vm_id'] ?>')">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="4%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'voucher/receipt/remove/'.$value['vm_id']?>');">
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
<script src="<?php echo assets('dist/js/voucher/receipt.js?v=1')?>"></script>
	</body>
</html>