<?php $this->load->view('templates/header'); ?>
<script>
    let link = "purchase";
    let sub_link = "purchase";
</script>
<section class="d-flex justify-content-between sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('purchase?action=view'); ?>">PURCHASE</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_purchase(<?php echo empty($master_data) ? 0 : $master_data[0]['pm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?> tabindex="20"><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary" onclick="redirectPage('purchase?action=view')" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="21"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav> 
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="purchase_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">BILL DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="pm_entry_no" name="pm_entry_no" value="<?php echo empty($master_data) ? $pm_entry_no : $master_data[0]['pm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY NO</label>
                                <small class="form-text text-muted helper-text" id="pm_entry_no_msg"></small>
                                <input 
                                    type="hidden"
                                    id="pm_gst_type"
                                    name="pm_gst_type"
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_gst_type'] ?>"/>

							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="text" class="form-control floating-input" id="pm_entry_date" name="pm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['pm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                                <small class="form-text text-muted helper-text" id="pm_entry_date_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="text" class="form-control floating-input" id="pm_bill_no" name="pm_bill_no" value="<?php echo empty($master_data) ? '' : $master_data[0]['pm_bill_no'] ?>" placeholder=" " autocomplete="off" onkeyup="validate_textfield(this)" tabindex="1" />   
                                <label for="inputEmail3">BILL NO <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="pm_bill_no_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="text" class="form-control floating-input datepicker" id="pm_bill_date" name="pm_bill_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['pm_bill_date'])) ?>" placeholder=" " onkeyup="validate_textfield(this)" tabindex="2"/>   
                                <label for="inputEmail3">BILL DATE <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="pm_bill_date_msg"></small>
							</div>
							<div class="col-sm-12 col-md-8 col-lg-6 floating-label">
								<p for="inputEmail3">
                                    SUPPLIER&nbsp;<span class="text-danger">*</span>&nbsp;&nbsp;
                                    <?php if(empty($master_data)): ?>
                                        <span><a style="cursor: pointer;" onclick="account_popup(0, 'SUPPLIER', 'pm_acc_id')" data-toggle="tooltip" data-placement="top" title="ADD SUPPLIER"><i class="text-success fa fa-plus"></i></a></span>
                                    <?php endif; ?>
                                </p>
                                <select class="form-control floating-select" id="pm_acc_id" name="pm_acc_id" placeholder=" " tabindex="3" onchange="validate_dropdown(this)" <?php echo empty($master_data) ? '' : 'disabled' ?>>
                                    <?php if(!empty($master_data)): ?>
                                        <option value="<?php echo $master_data[0]['pm_acc_id'] ?>" selected>
                                            <?php echo $master_data[0]['account_name'].' - '.$master_data[0]['account_code']; ?> 
                                        </option>
                                        <input type="hidden" name="pm_acc_id" value="<?php echo $master_data[0]['pm_acc_id'] ?>">
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted helper-text" id="pm_acc_id_msg"></small>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-6 floating-label">
                                <textarea class="form-control floating-textarea" id="pm_notes" name="pm_notes" placeholder=" " tabindex="4" autocomplete="off"><?php echo empty($master_data) ? '' : $master_data[0]['pm_notes']; ?></textarea>
                                <label for="inputEmail3">NOTES</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">AMOUNT DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="pm_total_qty" name="pm_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_total_qty'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" />   
                                <label for="inputEmail3">TOTAL QTY</label>
                                <small class="form-text text-muted helper-text" id="pm_total_qty_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="pm_sub_total" name="pm_sub_total" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_sub_total'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" />   
                                <label for="inputEmail3">SUB TOTAL AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_sub_total_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="pm_total_disc" name="pm_total_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_total_disc'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_total_disc_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="pm_taxable_amt" name="pm_taxable_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_taxable_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TAXABLE AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="pm_sgst_amt" name="pm_sgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_sgst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="pm_cgst_amt" name="pm_cgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_cgst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">CGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="pm_igst_amt" name="pm_igst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_igst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">IGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_igst_amt_msg"></small>
                            </div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="pm_bill_disc" name="pm_bill_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_bill_disc'] ?>" placeholder=" " autocomplete="off" tabindex="18" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_total()" <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'readonly' : ''; ?> />   
                                <label for="inputEmail3">BILL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_bill_disc_msg"></small>
                            </div>
                            
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="pm_round_off" name="pm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_round_off'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ROUND OFF AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_round_off_msg"></small>
							</div>
							
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label d-none">
								<input type="number" class="form-control floating-input" id="pm_gst_amt" name="pm_gst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_gst_amt'] ?>" placeholder=" " autocomplete="off" tabindex="19" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_total()"/>   
                                <label for="inputEmail3">GST AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_gst_amt_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 floating-label">
								<input type="number" class="form-control floating-input" id="pm_final_amt" name="pm_final_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['pm_final_amt'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" style="font-size: 20px; font-weight: bold;" />   
                                <label for="inputEmail3">BILL AMT.</label>
                                <small class="form-text text-muted helper-text" id="pm_final_amt_msg"></small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="card mb-3">
					<div class="card-header">PRODUCT DETAIL</div>
					<div class="card-body mt-2">
						<table class="table table-sm">
							<tr class="floating-form">
                                <td class="floating-label" width="11%">
									<p for="inputEmail3">
                                        DESIGN&nbsp;<span class="text-danger">*</span>
                                        <span>
                                            <a onclick="design_popup(0, 'design_id');"
                                                data-toggle="tooltip"  title="ADD design" 
                                                data-placement="top" >
                                                <i class="text-success fa fa-plus"></i>
                                            </a>
                                        </span>
                                    </p>                                                
                                    <select class="form-control floating-select" id="design_id" name="design_id" placeholder=" " tabindex="5" onchange="validate_dropdown(this, false)"></select>
                                	<small class="form-text text-muted helper-text" id="design_id_msg"></small>
								</td>
								<td class="floating-label" width="10%">
									<p for="inputEmail3">
                                        STYLE&nbsp;<span class="text-danger">*</span>
                                        <span>
                                            <a onclick="style_popup(0, 'style_id');"
                                                data-toggle="tooltip"  title="ADD STYLE" 
                                                data-placement="top" >
                                                <i class="text-success fa fa-plus"></i>
                                            </a>
                                        </span>
                                    </p>                                                
                                    <select class="form-control floating-select" id="style_id" name="style_id" placeholder=" " tabindex="6" onchange="validate_dropdown(this, false)"></select>
                                	<small class="form-text text-muted helper-text" id="style_id_msg"></small>
								</td>
                                <td class="floating-label" width="5%">
                                	<input type="number" class="form-control floating-input" id="qty" value="0" placeholder="" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_amt();" tabindex="7" />
                                    <label for="inputEmail3">QTY</label>       
                                </td>
                                <td class="floating-label" width="5%">
                                	<input type="number" class="form-control floating-input" id="rate" value="0" placeholder="" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)"  onkeyup="calculate_single_amt();" tabindex="8"/>
                                    <label for="inputEmail3">RATE</label>       
                                </td>
                                <td class="floating-label" width="5%">
                                    <input type="number" class="form-control floating-input mb-2" id="sp_amt" value="0" placeholder="AMT" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_amt();" tabindex="9"/>
                                     <input type="number" class="form-control floating-input" id="sp_per" value="0" placeholder="SP(%)" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_amt_by_sp_per();"  />
                                    <label for="inputEmail3">SP(AMT.)</label>
                                </td>
                                <td class="floating-label" width="5%">
                                    <input type="number" class="form-control floating-input" id="sub_total" value="0" placeholder="" readonly />
                                    <label for="inputEmail3">SUB TOTAL</label>       
                                </td>
                                <td class="floating-label" width="6%">
                                    <input type="number" class="form-control floating-input mb-2" id="disc_amt" value="0" placeholder="" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_amt();" tabindex="10" />
                                    <input type="number" class="form-control floating-input" id="disc_per" value="0" placeholder="Disc(%)" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_amt_by_disc_per()" tabindex="11" />
                                    <label for="inputEmail3">DISC(AMT.)</label>       
                                </td>
                                <td class="floating-label" width="9%">
                                    <input type="number" class="form-control floating-input" id="taxable_amt" value="0" placeholder="" readonly />
                                    <label for="inputEmail3">TAXABLE AMT</label>       
                                </td>          
							</tr>
                            <tr class="floating-form">
                                <td class="floating-label" width="10%">
                                    <p for="inputEmail3">
                                        HSN&nbsp;
                                        <span>
                                            <a onclick="hsn_popup(0, 'hsn_id');"
                                                data-toggle="tooltip"  title="ADD HSN" 
                                                data-placement="top" >
                                                <i class="text-success fa fa-plus"></i>
                                            </a>
                                        </span>
                                    </p>                                                
                                    <select class="form-control floating-select" id="hsn_id" name="hsn_id" placeholder=" " tabindex="12" onchange="validate_dropdown(this, false)"></select>
                                    <small class="form-text text-muted helper-text" id="hsn_id_msg"></small>
                                </td>
                                <td class="floating-label" width="10%">
                                    <p for="inputEmail3">
                                        BRAND&nbsp;<span>
                                            <a onclick="brand_popup(0, 'brand_id');"
                                                data-toggle="tooltip"  title="ADD BRAND" 
                                                data-placement="top" >
                                                <i class="text-success fa fa-plus"></i>
                                            </a>
                                        </span>
                                    </p>                                                
                                    <select class="form-control floating-select" id="brand_id" name="brand_id" placeholder=" " tabindex="13" onchange="validate_dropdown(this, false)"></select>
                                    <small class="form-text text-muted helper-text" id="brand_id_msg"></small>
                                </td> 
                                <td class="floating-label" width="6%">
                                    <input type="number" class="form-control floating-input mb-2" id="sgst_per" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <input type="number" class="form-control floating-input mb-2" id="cgst_per" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <input type="number" class="form-control floating-input mb-2" id="igst_per" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <label for="inputEmail3">GST(%)</label>       
                                </td>
                                <td class="floating-label" width="6%">
                                    <input type="number" class="form-control floating-input mb-2" id="sgst_amt" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <input type="number" class="form-control floating-input mb-2" id="cgst_amt" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <input type="number" class="form-control floating-input mb-2" id="isgst_amt" value="0" placeholder="" autocomplete="off" min="0" readonly="" />
                                    <label for="inputEmail3">GST(AMT)</label>       
                                </td>
                                <td class="floating-label" width="8%">
                                    <input type="number" class="form-control floating-input" id="sub_total_amt" placeholder="" readonly />
                                    <label for="inputEmail3">TOTAL</label>       
                                </td>                              
                                <td class="floating-label" width="5%">
                                    <input type="number" class="form-control floating-input" id="mrp" value="" placeholder="" autocomplete="off" tabindex="16" />
                                    <label for="inputEmail3">MRP</label>       
                                </td>
                              <!--   <td class="floating-label" width="5%">
                                    <input type="number" class="form-control floating-input" id="token_amt" value="" placeholder="" autocomplete="off"tabindex="16" />
                                    <label for="inputEmail3">TOKEN AMT</label>       
                                </td> -->
                                <td class="floating-label" width="6%">
                                    <textarea class="form-control floating-input" id="desc" placeholder="" autocomplete="off" ></textarea>
                                    <label for="inputEmail3">DESCRIPTION</label> 
                                </td> 
                                <td width="1%">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="<?php echo empty($master_data) ? 'add_purchase_row(0);' : "add_purchase_row(".$master_data[0]['pm_id'].");"  ?>"   disabled="disabled" id="trans_block_btn" tabindex="14"><i class="text-success fa fa-plus"></i></button>
                                </td>        
                                               
                            </tr>
						</table> 
						<div class="card-body p-0 table table-responsive" style="max-width:100vw; max-height:50vh; overflow:auto;">
                            <table class="table table-sm text-uppercase">
                                <thead>
        							<tr style="font-size: 12px;">
                                        <th>&nbsp;&nbsp;#</th>
                                        <th>DESIGN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>STYLE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >BRAND&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >HSN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >DESCRIPTION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >QTY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >RATE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >SP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >SUB&nbsp;TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >DISC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>TAXABLE&nbsp;AMT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>sgst&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>sgst&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>cgst&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>cgst&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>igst&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>igst&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >CP&nbsp;CODE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th >MRP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      <!--   <th >TOKEN&nbsp;AMT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th> -->
                                        <th >Action&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        	                        </tr>
                                </thead>
                                <tbody id="purchase_material_wrapper">
        	                        <?php
                                        $purchase_cnt = 1;
                                        if(!empty($trans_data)):
            	                            foreach ($trans_data as $key => $value):
                                                $editable = ($value['isExist']) ? false : true ;
                                    ?>
                                                <tr id="rowid_<?php echo $purchase_cnt; ?>" class="floating-form">
                                                    <!-- Serial No -->
        	                                            <td class="floating-label d-flex flex-column">
                                                            <?php if($value['isExist']): ?>
        	                                                	<input type="number" class="form-control floating-input" name="pt_serial_no[]" id="pt_serial_no_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_serial_no'];?>" readonly>
        	                                                <?php else: ?>
        	                                                    <input type="number" class="form-control floating-input" name="pt_serial_no[]" id="pt_serial_no_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_serial_no'];?>" onkeyup="check_for_duplicate_serial_no(<?php echo $purchase_cnt ?>)">
        	                                                <?php endif; ?>
                                                            <a type="button" class="btn btn-sm btn-primary mt-2" target="_blank" href="<?php echo base_url('purchase?action=trans_print&id='.$value['pt_id']) ?>">
                                                                <i class="text-info fa fa-print"></i>
                                                            </a>
        	                                            </td>
                                                    <!-- Serial No -->

                                                    <!-- design -->
                                                    <td class="floating-label">
                                                        <input type="text" class="form-control floating-input" value="<?php echo strtoupper($value['design_name']); ?>" title="<?php echo strtoupper($value['design_name']); ?>" readonly="">
                                                        <input type="hidden" name="pt_design_id[]" id="pt_design_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_design_id']; ?>">
                                                        <input type="hidden" name="pt_id[]" id="pt_id_<?php echo $purchase_cnt ?>" class="form-control" value="<?php echo $value['pt_id'];  ?>">
                                                    </td>
                                                    <!-- design -->

                                                    <!-- Style -->
                                                    <td class="floating-label"> 
                                                        <?php if($value['isExist']): ?>
                                                            <input type="text" class="form-control floating-input" id="style_name_<?php echo $purchase_cnt ?>" value="<?php echo strtoupper($value['style_name']); ?>" title="<?php echo strtoupper($value['style_name']); ?>" readonly="">
                                                            <input type="hidden" name="pt_style_id[]" id="pt_style_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_style_id']; ?>">

                                                        <?php else: ?>
                                                            <select class="form-control floating-select style_selec2" name="pt_style_id[]" id="pt_style_id_<?php echo $purchase_cnt ?>" >
                                                                   <?php if(!empty($value['pt_style_id'])): ?>
                                                                        <option value="<?php echo $value['pt_style_id'] ?>" selected>
                                                                            <?php echo $value['style_name']; ?> 
                                                                        </option>
                                                                        
                                                                    <?php endif; ?>
                                                            </select>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- Style -->

                                                    <!-- Brand -->
                                                    <td class="floating-label">
                                                        <input type="text" class="form-control floating-input" id="brand_name_<?php echo $purchase_cnt ?>" value="<?php echo strtoupper($value['brand_name']); ?>" readonly="">
                                                        <input type="hidden" name="pt_brand_id[]" id="pt_brand_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_brand_id']; ?>">
                                                    </td>
                                                    <!-- Brand -->
                                                     <!-- hsn -->
                                                    <td class="floating-label">
                                                        <input type="text" class="form-control floating-input" id="hsn_name_<?php echo $purchase_cnt ?>" value="<?php echo strtoupper($value['hsn_name']); ?>" readonly="">
                                                        <input type="hidden" name="pt_hsn_id[]" id="pt_hsn_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_hsn_id']; ?>">
                                                    </td>
                                                    <!-- hsn -->

                                                    <!-- Description -->
                                                    <td class="floating-label">
                                                        <input type="text" class="form-control floating-input" name="pt_desc[]" id="pt_desc_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_desc']; ?>" title="<?php echo $value['pt_desc']; ?>" readonly="">
                                                    </td>
                                                    <!-- Description -->

        	                                        <!-- Quantity -->
        	                                        <td class="floating-label">
        	                                            <input type="number" class="form-control floating-input" name="pt_qty[]" id="pt_qty_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_qty']; ?>" title="<?php echo $value['pt_qty']; ?>" readonly="">
        	                                        </td>
        	                                        <!-- Quantity -->

                                                    <!-- Rate -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_rate[]" id="pt_rate_<?php echo $purchase_cnt ?>" value="<?php echo round($value['pt_rate'], 2); ?>" title="<?php echo $value['pt_rate']; ?>" <?php echo $editable ? "" : "readonly" ?> <?php echo $editable ? "onfocusout='set_cp_code($purchase_cnt)'" : "readonly" ?>  onkeyup="calculate_master_total()">
                                                    </td>
                                                    <!-- Rate -->

        	                                        <!-- SP -->
        	                                        <td class="floating-label">
        	                                            <input type="number" class="form-control floating-input mb-2" name="pt_sp_amt[]" id="pt_sp_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sp_amt']; ?>" title="<?php echo $value['pt_sp_amt']; ?>" <?php echo $editable ? "" : "readonly" ?>  onkeyup="calculate_master_total1()">
        	                                            <input type="number" class="form-control floating-input" name="pt_sp_per[]" id="pt_sp_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sp_per']; ?>" title="<?php echo $value['pt_sp_per']; ?>"  <?php echo $editable ? "onkeyup='calculate_master_total()'" : "readonly" ?>>
        	                                        </td>
        	                                        <!-- SP -->

                                                    <!-- Sub Total -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_sub_total[]" id="pt_sub_total_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sub_total']; ?>" title="<?php echo $value['pt_sub_total']; ?>" readonly="">
                                                    </td>
                                                    <!-- Sub Total -->

        	                                        <!-- Disc -->
        	                                        <td class="floating-label">
        	                                            <input type="number" class="form-control floating-input mb-2" name="pt_disc_amt[]" id="pt_disc_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_disc_amt']; ?>" title="<?php echo $value['pt_disc_amt']; ?>" <?php echo $editable ? "onkeyup='calculate_master_total1()'" : "readonly" ?>>
        	                                            <input type="number" class="form-control floating-input" name="pt_disc_per[]" id="pt_disc_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_disc_per']; ?>" title="<?php echo $value['pt_disc_per']; ?>" <?php echo $editable ? "onkeyup='calculate_master_total()'" : "readonly" ?>>
        	                                        </td>
        	                                        <!-- Disc -->
                                                    <!-- TAXABLE AMT -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_taxable_amt[]" id="pt_taxable_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_taxable_amt']; ?>" title="<?php echo $value['pt_taxable_amt']; ?>" readonly="">
                                                    </td>
                                                    <!-- TAXABLE AMT -->

                                                    <!-- SGST -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_sgst_per[]" id="pt_sgst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sgst_per']; ?>" title="<?php echo $value['pt_sgst_per']; ?>" readonly>
                                                    </td>
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_sgst_amt[]" id="pt_sgst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sgst_amt']; ?>" title="<?php echo $value['pt_sgst_amt']; ?>" readonly>
                                                    </td>
                                                    <!-- SGST -->

                                                    <!-- CGST -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_cgst_per[]" id="pt_cgst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_cgst_per']; ?>" title="<?php echo $value['pt_cgst_per']; ?>" readonly>
                                                    </td>
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_cgst_amt[]" id="pt_cgst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_cgst_amt']; ?>" title="<?php echo $value['pt_cgst_amt']; ?>" readonly>
                                                    </td>
                                                    <!-- CGST -->

                                                    <!-- IGST -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_igst_per[]" id="pt_igst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_igst_per']; ?>" title="<?php echo $value['pt_igst_per']; ?>" 
                                                        <?php echo $editable ? "onkeyup='change_gst_per($purchase_cnt)'" : "readonly" ?>>
                                                    </td>
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_igst_amt[]" id="pt_igst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_igst_amt']; ?>" title="<?php echo $value['pt_igst_amt']; ?>" readonly>
                                                    </td>
                                                    <!-- IGST -->

                                                    <!-- Total -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_sub_total_amt[]" id="pt_sub_total_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_sub_total_amt']; ?>" title="<?php echo $value['pt_sub_total_amt']; ?>" readonly="">
                                                    </td>
                                                    <!-- Total -->

                                                    <!-- CP-Code -->
                                                    <td class="floating-label">
                                                        <input type="text" class="form-control floating-input" name="pt_cp_code[]" id="pt_cp_code_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_cp_code']; ?>" title="<?php echo $value['pt_cp_code']; ?>" readonly="">
                                                    </td>
                                                    <!-- CP-Code -->
                                                    <!-- MRP -->
                                                    <td class="floating-label">
                                                        <input type="number" class="form-control floating-input" name="pt_mrp[]" id="pt_mrp_<?php echo $purchase_cnt ?>" value="<?php echo $value['pt_mrp']; ?>" title="<?php echo $value['pt_mrp']; ?>">
                                                    </td>
                                                    <!-- MRP --> 
                                                  
                                                    <!-- Remove -->
                                                    <td class="text-center floating-label">
                                                        <?php if($value['isExist']): ?>
                                              		      <button type="button" class="btn btn-sm btn-primary" ><i class="text-danger fa fa-ban"></i></button>
                                                        <?php else: ?>
                                                          <button type="button" class="btn btn-sm btn-primary" onclick="remove_purchase_row(<?php echo $purchase_cnt ?>)"><i class="text-danger fa fa-trash"></i></button>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- Remove -->
                                                </tr>
                                    <?php 
        		                                $purchase_cnt++;
                                            endforeach; 
                                        endif;
                                    ?>
                                </tbody>
    						</table>
                        </div>    
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/purchase/purchase.js?v=6')?>"></script>
<script src="<?php echo assets('dist/js/master/account.js?v=1')?>"></script>
<script src="<?php echo assets('dist/js/master/design.js')?>"></script> 
<script src="<?php echo assets('dist/js/master/style.js')?>"></script>
<script src="<?php echo assets('dist/js/master/hsn.js')?>"></script>
<script src="<?php echo assets('dist/js/master/brand.js')?>"></script>
<?php 
    if(!empty($master_data))
    {
        echo "<script>";
        echo "purchase_cnt = $purchase_cnt;";
        echo "</script>";
    }
?>
</body>
</html>