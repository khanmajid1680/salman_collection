<?php 
    $this->load->view('templates/header');
    $with_gst  = (!empty($master_data) && ($master_data[0]['sm_with_gst'] == 1))  ? 'checked' : 0; 
    $bill_type = (!empty($master_data) && ($master_data[0]['sm_bill_type'] == 0))  ? '' : 'checked';
    $gst_type  = (!empty($master_data) && ($master_data[0]['sm_gst_type'] == 1))  ? 1 : 0;
    $id        = (!empty($master_data)) ? $master_data[0]['sm_id'] : 0;


?>
<script>
    let link = "sales";
    let sub_link = "sales";
</script>
<section class="d-flex justify-content-between sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('sales?action=view'); ?>">SALES</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <!-- <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_sales(<?php echo empty($master_data) ? 0 : $master_data[0]['sm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="10" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li> -->
        <!-- <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo base_url('sales?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="11"><i class="text-danger fa fa-close"></i></a>
        </li> -->
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="sales_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header text-uppercase d-flex justify-content-between">
                        <div>BILL DETAIL</div>
                        <div>
                            <input 
                                type="checkbox" 
                                id="sm_bill_type" 
                                name="sm_bill_type" 
                                data-toggle="toggle" 
                                data-on="INCLUSIVE" 
                                data-off="EXCLUSIVE" 
                                data-onstyle="secondary" 
                                data-offstyle="secondary" 
                                data-width="130" 
                                data-size="small" 
                                <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled' : 'onchange="set_bill_type()"' ?>
                                <?php echo $bill_type; ?>
                            />
                        </div>
                        <input type="hidden" name="sm_id" id="sm_id" value="<?php echo $id?>">
                    </div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
							  <input type="hidden"
                                    id="sm_gst_type"
                                    name="sm_gst_type"
                                    value="<?php echo $gst_type; ?>"/>
                            <div class="col-sm-12 col-md-4 col-lg-2 floating-label">
								<input type="number" class="form-control floating-input" id="sm_bill_no" name="sm_bill_no" value="<?php echo empty($master_data) ? $sm_bill_no : $master_data[0]['sm_bill_no'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">BILL NO&nbsp;<span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="sm_bill_no_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="date" class="form-control floating-input" id="sm_bill_date" name="sm_bill_date" value="<?php echo empty($master_data) ? date('Y-m-d') : date('Y-m-d', strtotime($master_data[0]['sm_bill_date'])) ?>" placeholder=" "  />   
                                <label for="inputEmail3">BILL DATE&nbsp;<span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="sm_bill_date_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-5 floating-label">
                                <p for="inputEmail3">
                                    EXISTING CUSTOMER&nbsp;<span class="text-danger">*</span>&nbsp;&nbsp;
                                </p>
                                <select class="form-control floating-select" id="sm_acc_id" name="sm_acc_id" placeholder=" " tabindex="1" onchange="validate_dropdown(this)" <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled' : '' ?>>
                                    <?php if(!empty($master_data)): ?>
                                            <option value="<?php echo $master_data[0]['sm_acc_id'] ?>" selected>
                                                <?php echo $master_data[0]['account_name'].' - '.$master_data[0]['account_mobile']; ?> 
                                            </option>
                                           <?php if(!empty($master_data) && $master_data[0]['isExist']): ?> 
                                                <input type="hidden" name="sm_acc_id" value="<?php echo $master_data[0]['sm_acc_id'] ?>">
                                                </option>
                                            <?php endif; ?>
                                        <?php else: ?>    
                                         <option value="<?php echo $walkin[0]['account_id'] ?>" selected>
                                                <?php echo strtoupper($walkin[0]['account_name']); ?>    
                                        <?php endif; ?>
                                </select>
                                <small class="form-text text-muted helper-text" id="sm_acc_id_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <?php if(empty($master_data)): ?>
                                    <input type="number" class="form-control floating-input" id="account_mobile" name="account_mobile" value="" placeholder=" " tabindex="2" onkeyup="set_mobile_no(this, true)" onfocusout="validate_mobile_no(this, true)" />   
                                <?php else: ?>
                                    <input type="number" class="form-control floating-input" id="account_mobile" name="account_mobile" value="" placeholder=" " tabindex="2" readonly="readonly" />   
                                <?php endif; ?>
                                <label for="inputEmail3">NEW CUST. MOBILE NO<span id="account_mobile_length">(10)</span></label>
                                <small class="form-text text-muted helper-text" id="account_mobile_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input type="text" class="form-control floating-input" id="account_name" name="account_name" value="" placeholder=" " tabindex="3" autocomplete="off" <?php echo empty($master_data) ? '' : 'readonly'; ?>/>   
                                <label for="inputEmail3">NEW CUST. NAME</label>
                                <small class="form-text text-muted helper-text" id="account_name_msg"></small>
                            </div>
							<div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <p for="inputEmail3">SALES PERSON&nbsp;<span class="text-danger">*</span></p>
                                <select class="form-control floating-select" id="sm_user_id" name="sm_user_id" placeholder=" " tabindex="4" onchange="validate_dropdown(this)" <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled' : '' ?>>
                                    <?php if(!empty($master_data)): ?>
                                        <option value="<?php echo $master_data[0]['sm_user_id'] ?>" selected>
                                            <?php echo $master_data[0]['user_fullname']; ?> 
                                        </option>
                                        <input type="hidden" name="sm_user_id" value="<?php echo $master_data[0]['sm_user_id'] ?>">
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted helper-text" id="sm_user_id_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <p for="inputEmail3">BARCODE</p>
                                <select class="form-control floating-select select2" id="bm_id" placeholder="" tabindex="5">                                                
                                </select>
                                <small class="form-text text-muted helper-text" id="bm_id_msg"></small>
                            </div>
                            
                            <div class="col-sm-12 col-md-6 floating-label">
                                <textarea class="form-control floating-input" id="sm_notes" name="sm_notes" placeholder=" " autocomplete="off" tabindex="7" rows="3"><?php echo (empty($master_data)?'':$master_data[0]['sm_notes'])?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header text-uppercase d-flex justify-content-between">
                        <div>AMOUNT DETAIL</div>
                        <div>
                            <input 
                                type="checkbox" 
                                id="sm_with_gst" 
                                name="sm_with_gst" 
                                data-toggle="toggle" 
                                data-on="INVOICE" 
                                data-off="ESTIMATE" 
                                data-onstyle="secondary" 
                                data-offstyle="secondary" 
                                data-width="130" 
                                data-size="small" 
                                <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled' : 'onchange="set_bill_type()"' ?>
                                <?php echo $with_gst; ?>
                            />
                        </div>
                    </div>
                    
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="sm_total_qty" name="sm_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_total_qty'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" />   
                                <label for="inputEmail3">TOTAL QTY</label>
                                <small class="form-text text-muted helper-text" id="sm_total_qty_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="sm_sub_total" name="sm_sub_total" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_sub_total'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" />   
                                <label for="inputEmail3">SUB TOTAL AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_sub_total_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="sm_disc_per" name="sm_disc_per" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_disc_per'] ?>" placeholder=" " onkeyup="calculate_master_from_item_disc(true)" />   
                                <label for="inputEmail3">TOTAL DISC %.</label>
                                <small class="form-text text-muted helper-text" id="sm_disc_per_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_total_disc" name="sm_total_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_total_disc'] ?>" placeholder=" " onkeyup="calculate_master_from_item_disc()" />   
                                <label for="inputEmail3">TOTAL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_total_disc_msg"></small>

                                <input type="hidden" class="form-control floating-input" id="sm_hidden_disc_amt" name="sm_hidden_disc_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_hidden_disc_amt'] ?>" placeholder=" " readonly="readonly" />   

                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_taxable_amt" name="sm_taxable_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_taxable_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TAXABLE AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_sgst_amt" name="sm_sgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_sgst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_cgst_amt" name="sm_cgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_cgst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">CGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_igst_amt" name="sm_igst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_igst_amt'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">IGST AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_igst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="sm_round_off" name="sm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_round_off'] ?>" onkeyup="calculate_master_total()" min="0" placeholder=" "  readonly/>   
                                <label for="inputEmail3">ROUND OFF AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_round_off_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_final_amt" name="sm_final_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_final_amt'] ?>" placeholder=" " onchange="validate_number(this)" readonly="readonly" style="font-size: 22px; font-weight: bold;" />   
                                <label for="inputEmail3">BILL AMT.</label>
                                <small class="form-text text-muted helper-text" id="sm_final_amt_msg"></small>
                            </div>
                            <div class="col-6 col-sm-12 col-md-6 col-lg-3 d-flex flex pl-0">
                                <div class="floating-label" style="width: 100%;">
                                    <input 
                                        type="number" 
                                        class="form-control floating-input font-weight-bold" 
                                        id="sm_collected_amt" 
                                        name="sm_collected_amt" 
                                        value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_collected_amt'] ?>" 
                                        readonly/>   
                                    <label class="text-uppercase">adv amt</label>
                                    <small class="form-text text-muted helper-text" id="sm_collected_amt_msg"></small>
                                </div>
                                <div> 
                                    <button
                                        type="button"
                                        class="btn btn-md btn-primary"
                                        onclick="toggle_payment_mode_popup()"
                                        data-toggle="tooltip" 
                                        data-placement="bottom" 
                                        title="PAYMENT MODE" 
                                    ><i class="text-success fa fa-rupee"></i></button>
                                </div>
                            </div>           
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="sm_balance_amt" name="sm_balance_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sm_balance_amt'] ?>" placeholder=" " readonly="readonly" style="font-size: 22px; font-weight: bold;"/>
                                <label >BAL. AMT</label>
                                <small class="form-text text-muted helper-text" id="sm_balance_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 floating-label">
                                <button type="button" class="btn btn-md btn-primary master_block_btn mr-1" onclick="add_update_sales(<?php echo empty($master_data) ? 0 : $master_data[0]['sm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="10" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save font-weight-bold"> SAVE</i></button>
                                <a type="button" class="btn btn-md btn-primary" href="<?php echo base_url('sales?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="11"><i class="text-danger fa fa-close font-weight-bold"> CANCEL</i></a>
                            </div>                        
                           
						</div>
					</div>
				</div>
			</div>
            <div class="col-sm-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h6>VIP BARCODE</h6>
                            <button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#vipbarcode" aria-expanded="false" aria-controls="vipbarcode" tabindex="14">OPEN / CLOSE
                          </button>
                        </div>
                    </div>
                    <div class="collapse hide" id="vipbarcode">
                        <div class="card-body mt-2">
                            <table class="table table-sm">
                                <tr class="floating-form">
                                    <td class="floating-label" width="10%">
                                        <p for="inputEmail3">
                                            DESIGN&nbsp;<span class="text-danger">*</span>
                                        </p>                                                
                                        <select class="form-control floating-select" id="design_id" name="design_id" placeholder=" " tabindex="15" onchange="validate_dropdown(this, false)"></select>
                                        <small class="form-text text-muted helper-text" id="design_id_msg"></small>
                                    </td>
                                    <td class="floating-label" width="10%">
                                        <p for="inputEmail3">
                                            STYLE&nbsp;
                                        </p>
                                        <select class="form-control floating-select" id="style_id" name="style_id" placeholder=" " tabindex="16" onchange="validate_dropdown(this, false)"></select>
                                        <small class="form-text text-muted helper-text" id="style_id_msg"></small>
                                    </td>
                                    <td class="floating-label" width="10%">
                                        <p for="inputEmail3">
                                            BRAND&nbsp;</p>
                                        <select class="form-control floating-select" id="brand_id" name="brand_id" placeholder=" " tabindex="17" onchange="validate_dropdown(this, false)"></select>
                                        <small class="form-text text-muted helper-text" id="brand_id_msg"></small>
                                    </td>
                                   
                                    <td class="floating-label" width="5%">
                                        <input type="number" class="form-control floating-input" id="qty" value="1" placeholder="" autocomplete="off" readonly="readonly" />
                                        <label for="inputEmail3">QTY</label>       
                                    </td>
                                    <td class="floating-label" width="5%">
                                        <input type="number" class="form-control floating-input" id="rate" value="0" placeholder="" autocomplete="off" min="0" oninput="this.value = Math.abs(this.value)"  onkeyup="calculate_single_vip_amt();" tabindex="19"/>
                                        <label for="inputEmail3">RATE</label>       
                                    </td>
                                    <td class="floating-label" width="9%">
                                        <input type="number" class="form-control floating-input" id="sub_total_amt" value="0" placeholder="" readonly />
                                        <label for="inputEmail3">TOTAL</label>       
                                    </td>                              
                                    <td width="1%">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="<?php echo empty($master_data) ? 'add_sales_row(0);' : "add_sales_row(".$master_data[0]['sm_id'].");"  ?>"   disabled="disabled" id="trans_block_btn" tabindex="20"><i class="text-success fa fa-plus"></i></button>
                                    </td>                  
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-sm-12">
				<div class="card mb-3">
					<div class="card-header">PRODUCT DETAIL</div>
					<div class="card-body mt-2">
						<table class="table table-hover table-sm table-responsive">
                            <thead>
    							<tr style="font-size: 15px;">
                                    <th width="10%">BARCODE</th>
                                    <th width="10%">DESIGN</th>
                                    <th width="10%">STYLE</th>
                                    <th width="10%">BRAND</th>
                                    <th width="5%">QTY</th>
                                    <th width="5%">RATE/TOTAL</th>
                                    <th width="8%">D%/AMT </th>
                                    <th width="5%">TAXABLE&nbsp;AMT</th>
                                    <th width="5%">GST %</th>
                                    <th width="5%">GST AMT</th>
                                    <th width="8%">TOTAL</th>
                                    <th width="2%">EDIT</th>
                                    <th width="2%">REMOVE</th> 
    	                        </tr>
                            </thead>
                            <tbody id="sales_material_wrapper">
                                <?php
                                    $sales_cnt = 1;
                                    if(!empty($trans_data)):
                                        foreach ($trans_data as $key => $value):
                                        $trial_area = (strtolower($value['style_name']) == 'readymade') ? '' : 'd-none';
                                        $checked = ($value['st_trial']>0) ? 'checked' :'';
                                ?>
                                            <tr id="rowid_<?php echo $sales_cnt; ?>" class="floating-form">
                                                <td class="floating-label">
                                                    <input type="hidden" name="st_id[]" id="st_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_id']; ?>" />
                                                    <input type="hidden" name="st_bm_id[]" id="st_bm_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_bm_id']; ?>" />
                                                    <input type="number" class="form-control floating-input" id="st_bm_item_code_<?php echo $sales_cnt; ?>" value="<?php echo $value['bm_item_code']; ?>" readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <input type="text" class="form-control floating-input" id="design_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['design_name']; ?>" readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <input type="text" class="form-control floating-input" id="style_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['style_name']; ?>" readonly />
                                                    <input type="hidden" name="st_style_id[]" id="st_style_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_style_id']; ?>" />

                                                    <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;" class="<?php echo $trial_area?>">
                                                        <input type="checkbox" name="st_trial[]" id="st_trial_<?php echo $sales_cnt; ?>" value="1" <?php echo $checked?>/>
                                                        <label for="st_trial_<?php echo $sales_cnt; ?>" style="margin: 0;">Trial</label>
                                                    </div>  
                                                </td>
                                                <td class="floating-label">
                                                    <input type="text" class="form-control floating-input" id="brand_name_<?php echo $sales_cnt; ?>" value="<?php echo $value['brand_name']; ?>" readonly />
                                                    <input type="hidden" name="st_brand_id[]" id="st_brand_id_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_brand_id']; ?>" />
                                                    <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;" class="<?php echo $trial_area?>">
                                                        <label for="st_dispatch_date_<?php echo $sales_cnt; ?>" style="margin: 0;">D.Date</label>
                                                        <input type="date" name="st_dispatch_date[]" id="st_dispatch_date_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_dispatch_date']; ?>" class="form-control  floating-input" />
                                                    </div>
                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_qty[]" id="st_qty_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_qty']; ?>" readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_rate[]" id="st_rate_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_rate']; ?>" readonly />
                                                    <input type="number" class="form-control floating-input" name="st_sub_total[]" id="st_sub_total_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_sub_total']; ?>" readonly />
                                                </td>
                                                <td class="floating-label">
                                                     <input type="number" class="form-control floating-input" name="st_disc_per[]" id="st_disc_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_disc_per']; ?>" onkeyup="calculate_single_total(<?php echo $sales_cnt; ?>,true)"  readonly />
                                                      <input type="number" class="form-control floating-input" name="st_disc_amt[]" id="st_disc_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_disc_amt']; ?>" onkeyup="calculate_single_total(<?php echo $sales_cnt; ?>)" readonly/>
                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_taxable_amt[]" id="st_taxable_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_taxable_amt']; ?>"  readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_sgst_per[]" id="st_sgst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_sgst_per']; ?>"  readonly />

                                                    <input type="number" class="form-control floating-input" name="st_cgst_per[]" id="st_cgst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_cgst_per']; ?>"  readonly />

                                                    <input type="number" class="form-control floating-input" name="st_igst_per[]" id="st_igst_per_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_igst_per']; ?>" onkeyup="change_gst_per(<?php echo $sales_cnt; ?>)"  readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_sgst_amt[]" id="st_sgst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_sgst_amt']; ?>"  readonly />

                                                    <input type="number" class="form-control floating-input" name="st_cgst_amt[]" id="st_cgst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_cgst_amt']; ?>"  readonly />

                                                    <input type="number" class="form-control floating-input" name="st_igst_amt[]" id="st_igst_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_igst_amt']; ?>"  readonly />

                                                </td>
                                                <td class="floating-label">
                                                    <input type="number" class="form-control floating-input" name="st_sub_total_amt[]" id="st_sub_total_amt_<?php echo $sales_cnt; ?>" value="<?php echo $value['st_sub_total_amt']; ?>" readonly />
                                                </td>
                                                <td class="floating-label">
                                                    <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="edit_row(<?php echo $sales_cnt; ?>)"> 
                                                        <i class="text-success fa fa-edit"></i>
                                                    </button>
                                                </td>
                                                <td class="floating-label">
                                                    <?php if($value['isExist']): ?>
                                                        <button type="button" class="btn btn-sm btn-primary" ><i class="text-danger fa fa-ban"></i></button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_sales_row(<?php echo $sales_cnt; ?>)"> 
                                                            <i class="text-danger fa fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>    
                                            </tr>
                                <?php 
                                            $sales_cnt++;
                                        endforeach; 
                                    endif;
                                ?>
                            </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
          <div id="payment_mode_wrapper"><?php $this->load->view('templates/panel/right'); ?></div>
	</form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/sales/sales.js?v=8')?>"></script>
<script src="<?php echo assets('dist/js/master/account.js?v=1')?>"></script>
<script type="text/javascript"> get_payment_mode_data();</script>
<?php 
    if(!empty($master_data))
    {
        echo "<script>";
        echo "sales_cnt = $sales_cnt;";
        echo "</script>";
    }
?>
</body>
</html>