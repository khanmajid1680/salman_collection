<?php 
	$this->load->view('templates/header'); 
	$action 	= (isset($_GET['action'])) ? $_GET['action'] : "";
	$term   	= 'age';
	$search_status 	= !isset($_GET['search_status']);
?>
<script>
    let link = "item_description";
    let sub_link = "<?php echo $term; ?>";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url('master/age?action=view')?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="<?php echo base_url('master/'.$term.'?action=view'); ?>">MASTER</a></li>
			    <li class="breadcrumb-item active" aria-current="page">
			    	<?php echo strtoupper($term); ?> GROUP(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="add-page">
			    	<a type="button" class="btn btn-sm btn-primary" onclick="age_popup(0)" data-toggle="tooltip" data-placement="bottom" title="ADD NEW"><i class="text-success fa fa-plus"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button type="submit" class="btn btn-sm btn-primary mr-2" id="btn_search" data-toggle="tooltip" data-placement="bottom" title="SEARCH">
			    		<i class="text-warning fa fa-search"></i>
			    	</button>
					<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<button type="button" class="btn btn-sm btn-primary" onclick="redirectPage('master/age?action=view')" data-toggle="tooltip" data-placement="bottom" title="REFRESH"><i class="text-info fa fa-undo"></i></button>
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
				<div class="col-6 col-sm-6 col-md-4 col-lg-4 floating-label">
					<?php if(isset($data['search']['id'])): ?><p><?php echo strtoupper($term); ?> GROUP</p><?php endif; ?>
					<select class="form-control floating-select" id="id" name="id">
                    	<?php if(isset($data['search']['id']) && !empty($data['search']['id'])): ?>
                        	<option value="<?php echo $data['search']['id']['value']; ?>" selected>
                            	<?php echo $data['search']['id']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-6 col-md-4 col-lg-4 floating-label">
					<?php if(isset($data['search']['status'])): ?><p>STATUS</p><?php endif; ?>
					<select class="form-control floating-select" id="status" name="status">
                    	<?php if(isset($data['search']['status']) && !empty($data['search']['status'])): ?>
                        	<option value="<?php echo $data['search']['status']['value']; ?>" selected>
                            	<?php echo $data['search']['status']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
	                </select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark">
					<thead>
						<tr>
			                <th width="10%">#</th>
			                <th width="35%"><?php echo strtoupper($term); ?></th>
			                <th width="35%">STATUS</th>
			                <th width="10%">EDIT</th> 
			                <th width="10%">DELETE</th>
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
			<table class="table table-sm table-hover" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value['id'], SECRET_KEY);
					?>

								<tr>
									<td width="10%"><?php echo $key+1; ?></td>
									<td width="35%"><?php echo $value['name']; ?></td>
									<td width="35%"><?php echo $value['status'] == 1 ? 'ACTIVE' : 'INACTIVE'; ?></td>
									<td width="10%">
										<a type="button" class="btn btn-sm btn-primary" onclick="age_popup(<?php echo $value['id']; ?>)">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="10%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'master/'.$term.'/remove/'.$value['id']?>');">
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
<?= $this->pagination->create_links(); ?>

<?php $this->load->view('templates/footer'); ?>
		<script src="<?php echo assets('dist/js/master/age.js')?>"></script>
	</body>
</html>