<?php 
	$this->load->view('templates/header'); 
	$term   		= 'design';
	$search_status 	= !isset($_GET['search_status']);
?>
<script>
    let link 	= "master";
    let sub_link= "<?php echo $term; ?>";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url("master/$term")?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			  	<li class="breadcrumb-item"><a href="<?php echo base_url("master/$term"); ?>">MASTER</a></li>
			    <li class="breadcrumb-item active text-uppercase" aria-current="page">
					<?php echo str_replace('_', ' ', $term); ?>(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
				<li class="breadcrumb-item" aria-current="add-page">
		    	    <a 
						type="button" 
						class="btn btn-sm btn-primary" 
						onclick="design_popup(0, 'add')" 
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="ADD NEW"
					><i class="text-success fa fa-plus"></i></a>
		        </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button 
						type="submit" 
						class="btn btn-sm btn-primary mr-2" 
						id="btn_search" 
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="SEARCH"
					><i class="text-warning fa fa-search"></i></button>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
					<a 
						type="button" 
						class="btn btn-sm btn-primary" 
						href="<?php echo base_url("master/$term"); ?>"
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="REFRESH"
					><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="search-box">
			    	<input 
			    		type="checkbox" 
			    		id="search_status" 
			    		name="search_status" 
			    		data-toggle="toggle" 
			    		data-on="FILTER <i class='fa fa-eye'></i>" 
			    		data-off="FILTER <i class='fa fa-eye-slash'></i>" 
			    		data-onstyle="primary" 
			    		data-offstyle="primary" 
			    		data-width="100" 
			    		data-size="mini" 
			    		data-style="show-hide" 
			    		onchange="set_search_box()" <?php echo empty($search_status) ? 'checked' : ''; ?>
		    		/>
			    </li>
			  </ol>
			</nav>
			<div class="d-none d-sm-block height_60_px">
				<?= $this->pagination->create_links(); ?>
			</div>
		</div>
		<div class="row collapse mt-2 <?php echo empty($search_status) ? '' : 'show'  ?>" id="search_box">
			<div class="d-flex flex-wrap justify-content-center floating-form">
				<div class="col-6 col-sm-6 col-md-3 col-lg-3 floating-label">
					<?php if(isset($data['search']['_name'])): ?><p class="text-uppercase"><?php echo str_replace('_', ' ', $term); ?></p><?php endif; ?>
					<select class="form-control floating-select" id="_name" name="_name">
                    	<?php if(isset($data['search']['_name']) && !empty($data['search']['_name'])): ?>
                        	<option value="<?php echo $data['search']['_name']['value']; ?>" selected>
                            	<?php echo $data['search']['_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-6 col-md-3 col-lg-3 floating-label">
					<?php if(isset($data['search']['_status'])): ?><p class="text-uppercase">status</p><?php endif; ?>
					<select class="form-control floating-select" id="_status" name="_status">
                    	<?php if(isset($data['search']['_status']) && !empty($data['search']['_status'])): ?>
                        	<option value="<?php echo $data['search']['_status']['value']; ?>" selected>
                            	<?php echo $data['search']['_status']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
	                </select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark text-uppercase">
					<thead>
						<tr>
			                <th width="3%">#</th>
			                <th width="10%"><?php echo strtoupper(str_replace('_', ' ', $term)); ?></th>
			                <th width="10%">sgst %</th>
			                <th width="10%">cgst %</th>
			                <th width="10%">igst %</th>
			                <th width="5%">status</th>
			                <th width="3%">edit</th> 
			                <th width="3%">delete</th>
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
			<table class="table table-sm table-hover text-uppercase" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
								// echo "<pre>"; print_r($value); exit;
                    			$id = encrypt_decrypt("encrypt", $value[$term.'_id'], SECRET_KEY);
					?>

								<tr class="<?php echo $value[$term.'_status'] == 0 ? 'text-danger' : '' ?>">
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value[$term.'_name']; ?></td>
									<td width="10%"><?php echo $value[$term.'_sgst_per']; ?></td>
									<td width="10%"><?php echo $value[$term.'_cgst_per']; ?></td>
									<td width="10%"><?php echo $value[$term.'_igst_per']; ?></td>
									<td width="5%"><?php echo $value[$term.'_status'] == 1 ? 'active' : 'inactive'; ?></td>
                                    <td width="3%">
										<a type="button" class="btn btn-sm btn-primary" onclick="design_popup(<?php echo $value[$term.'_id']; ?>)">
											<i class="text-success fa fa-edit"></i>
										</a>										
									</td>
									<td width="3%">
										<?php if($value['isExist']): ?>
											<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
										<?php else: ?>
											<a type="button" class="btn btn-sm btn-primary" onclick="remove_master('<?php echo 'master/'.$term.'/remove/'.$value[$term.'_id']?>');">
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
<script src="<?php echo assets('dist/js/master/design.js?v=1')?>"></script>
</body>
</html>