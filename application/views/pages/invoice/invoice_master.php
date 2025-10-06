<?php 
	$this->load->view('templates/header'); 
	$role 			= $_SESSION['user_role'];
	$date 			= $role == SALES ? date('d-m-Y') : '';
	$action 		= (isset($_GET['action'])) ? $_GET['action'] : "";
	$search_status 	= !isset($_GET['search_status']);
	$from_date 		= (isset($_GET['from_date'])) ? $_GET['from_date'] : $date;
	$to_date 		= (isset($_GET['to_date'])) ? $_GET['to_date'] : $date;
	$from_qty 		= (isset($_GET['from_qty'])) ? $_GET['from_qty'] : "";
	$to_qty 		= (isset($_GET['to_qty'])) ? $_GET['to_qty'] : "";
	$from_bill_amt 	= (isset($_GET['from_bill_amt'])) ? $_GET['from_bill_amt'] : "";
	$to_bill_amt 	= (isset($_GET['to_bill_amt'])) ? $_GET['to_bill_amt'] : "";
?>
<script>
    let link = "invoice";
    let sub_link = "invoice";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('invoice?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item active" aria-current="page">
			    	CA REPORT(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="add-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('invoice?action=add')" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a >
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="reload-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('invoice?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></a>
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
					<?php if(isset($data['search']['im_entry_no'])): ?><p>ENTRY NO</p><?php endif; ?>
					<select class="form-control floating-select" id="im_entry_no" name="im_entry_no">
                    	<?php if(isset($data['search']['im_entry_no']) && !empty($data['search']['im_entry_no'])): ?>
                        	<option value="<?php echo $data['search']['im_entry_no']['value']; ?>" selected>
                            	<?php echo $data['search']['im_entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="from_date" name="from_date" value="<?php echo $from_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM ENTRY DATE</label>
					</div>
					<div class="floating-label">
						<input type="text" class="form-control floating-input datepicker" id="to_date" name="to_date" value="<?php echo $to_date ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO ENTRY DATE</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_qty" name="from_qty" value="<?php echo $from_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM TOTAL INVOICE</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_qty" name="to_qty" value="<?php echo $to_qty ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO TOTAL INVOICE</label>
					</div>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="from_bill_amt" name="from_bill_amt" value="<?php echo $from_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">FROM INVOICE AMT</label>
					</div>
					<div class="floating-label">
						<input type="number" class="form-control floating-input" id="to_bill_amt" name="to_bill_amt" value="<?php echo $to_bill_amt ?>" placeholder=" " autocomplete="off"/>   
	                    <label for="inputEmail3">TO INVOICE AMT</label>
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
	                        <th width="10%">ENTRY NO</th>
	                        <th width="10%">ENTRY DATE</th>
	                        <th width="10%">SALES BILL DATE FROM</th>
	                        <th width="10%">SALES BILL DATE TO</th>
	                        <th width="10%">TOTAL INVOICE GENERATED</th>
	                        <th width="10%">TOTAL INVOICE AMT</th>
	                        <th width="3%" align="center">PRINT</th> 
	                        <?php if($role == ADMIN): ?>
	                        <th width="3%" align="center">EDIT</th> 
	                        <th width="4%" align="center">DELETE</th>
	                    	<?php endif; ?>
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
                    			$id = encrypt_decrypt("encrypt", $value['im_id'], SECRET_KEY);
					?>

								<tr>
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value['im_entry_no']; ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['im_entry_date'])); ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['im_from_bill_date'])); ?></td>
									<td width="10%"><?php echo date('d-m-Y', strtotime($value['im_to_bill_date'])); ?></td>
									<td width="10%"><?php echo $value['im_total_qty']; ?></td>
									<td width="10%"><?php echo round($value['im_final_amt'], 2); ?></td>

									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" target="_blank" href="<?php echo base_url('invoice?action=print&id='.$value['im_id']) ?>">
											<i class="text-info fa fa-print"></i>
										</a>										
									</td>
									<?php if($role == ADMIN): ?>
									<td width="3%">
										<a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('<?php echo 'invoice?action=edit&id='.$value['im_id'] ?>')">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="4%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'invoice/remove/'.$value['im_id']?>');">
												<i class="text-danger fa fa-trash"></i>
											</a>
										<?php endif; ?>                         
									</td>
									<?php endif; ?>
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
<?= $this->pagination->create_links(); ?>
<?php $this->load->view('templates/footer'); ?>
		<script src="<?php echo assets('dist/js/invoice/invoice.js')?>"></script>
	</body>
</html>