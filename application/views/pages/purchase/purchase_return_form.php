<?php $this->load->view('templates/header'); ?>
<script>
    let link = "purchase_return";
    let sub_link = "purchase_return";
</script>
<section class="d-flex justify-content-between sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('purchase_return?action=view'); ?>">PURCHASE RETURN</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_purchase_return(<?php echo empty($master_data) ? 0 : $master_data[0]['prm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="5" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary master_block_btn" href="<?php echo base_url('purchase_return?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="6"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
	<form class="form-horizontal" id="purchase_return_form">
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<div class="card mb-3">
					<div class="card-header">BILL DETAIL</div>
					<div class="card-body">
						<div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <p for="inputEmail3">BARCODE</p>
                                <select class="form-control floating-select" id="bm_id" placeholder="" tabindex="1">                                                
                                </select>
                                <input type="hidden" id="prm_id" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_id'] ?>">
                                <small class="form-text text-muted helper-text" id="bm_id_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <textarea class="form-control floating-textarea" id="prm_notes" name="prm_notes" placeholder=" " tabindex="2" autocomplete="off"><?php echo empty($master_data) ? '' : $master_data[0]['prm_notes']; ?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="prm_entry_no" name="prm_entry_no" value="<?php echo empty($master_data) ? $prm_entry_no : $master_data[0]['prm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY NO</label>
                                <small class="form-text text-muted helper-text" id="prm_entry_no_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="text" class="form-control floating-input" id="prm_entry_date" name="prm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['prm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                                <small class="form-text text-muted helper-text" id="prm_entry_date_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6 floating-label">
								<input type="text" class="form-control floating-input" id="account_name" value="<?php echo empty($master_data) ? '' : $master_data[0]['account_name'] ?>" placeholder=" " readonly/>   
                                <input type="hidden" id="prm_acc_id" name="prm_acc_id" value="<?php echo empty($master_data) ? '' : $master_data[0]['prm_acc_id'] ?>"/>   
                                <label for="inputEmail3">SUPPLIER <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="prm_acc_id_msg"></small>
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
								<input type="number" class="form-control floating-input" id="prm_total_qty" name="prm_total_qty" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_total_qty'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">TOTAL QTY</label>
                                <small class="form-text text-muted helper-text" id="prm_total_qty_msg"></small>
							</div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="prm_sub_total" name="prm_sub_total" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_sub_total'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">SUB TOTAL AMT.</label>
                                <small class="form-text text-muted helper-text" id="prm_sub_total_msg"></small>
							</div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="prm_taxable_amt" name="prm_taxable_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_taxable_amt'] ?>" placeholder=" " autocomplete="off" readonly/>   
                                <label for="inputEmail3">TAXABLE AMT</label>
                                <small class="form-text text-muted helper-text" id="prm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="prm_sgst_amt" name="prm_sgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_sgst_amt'] ?>" placeholder=" " autocomplete="off" readonly/>   
                                <label for="inputEmail3">SGST AMT</label>
                                <small class="form-text text-muted helper-text" id="prm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="prm_cgst_amt" name="prm_cgst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_cgst_amt'] ?>" placeholder=" " autocomplete="off" readonly/>   
                                <label for="inputEmail3">CGST AMT</label>
                                <small class="form-text text-muted helper-text" id="prm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="prm_igst_amt" name="prm_igst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_igst_amt'] ?>" placeholder=" " autocomplete="off" readonly/>   
                                <label for="inputEmail3">IGST AMT</label>
                                <small class="form-text text-muted helper-text" id="prm_igst_amt_msg"></small>
                            </div>
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="number" class="form-control floating-input" id="prm_bill_disc" name="prm_bill_disc" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_bill_disc'] ?>" placeholder=" " autocomplete="off" tabindex="3" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_total()"
                                <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'readonly' : ''; ?>>   
                                <label for="inputEmail3">BILL DISC AMT.</label>
                                <small class="form-text text-muted helper-text" id="prm_bill_disc_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
								<input type="number" class="form-control floating-input" id="prm_round_off" name="prm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_round_off'] ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ROUND OFF AMT.</label>
                                <small class="form-text text-muted helper-text" id="prm_round_off_msg"></small>
							</div>
							
							<div class="col-sm-12 col-md-4 col-lg-3 floating-label d-none">
								<input type="number" class="form-control floating-input" id="prm_gst_amt" name="prm_gst_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_gst_amt'] ?>" placeholder=" " autocomplete="off" tabindex="4" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_master_total()"/>   
                                <label for="inputEmail3">GST AMT.</label>
                                <small class="form-text text-muted helper-text" id="prm_gst_amt_msg"></small>
							</div>
							<div class="col-sm-12 col-md-6 floating-label">
								<input type="number" class="form-control floating-input" id="prm_final_amt" name="prm_final_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_final_amt'] ?>" placeholder=" " readonly="readonly" style="font-size: 20px; font-weight: bold;" />   
                                <label for="inputEmail3">BILL AMT.</label>
                                <small class="form-text text-muted helper-text" id="prm_final_amt_msg"></small>
							</div>
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
                                    <th width="8%">BILL NO</th>
                                    <th width="8%">BILL DATE</th>
                                    <th width="10%">DESIGN</th>
                                    <th width="10%">STYLE</th>
                                    <th width="10%">BRAND</th>
                                    <th width="6%">QUANTITY</th>
                                    <th width="6%">RATE</th>
                                    <th width="6%">DISC</th>
                                    <th width="6%">TAXABLE</th>
                                    <th width="6%">SGST%/AMT</th>
                                    <th width="6%">CGST%/AMT</th>
                                    <th width="6%">IGST%AMT</th>
                                    <th width="8%">TOTAL</th>
                                    <th width="2%">ACTION</th>
    	                        </tr>
                            </thead>
                            <tbody id="purchase_material_wrapper">
    	                        <?php
                                    $purchase_cnt = 1;
                                    if(!empty($trans_data)):
                                        foreach ($trans_data as $key => $value):
                                            
                                ?>
                                        <tr id="rowid_<?php echo $purchase_cnt; ?>" class="floating-form">
                                            <td>
                                                <input type="number" class="form-control floating-input" id="prt_bm_item_code_<?php echo $purchase_cnt ?>" value="<?php echo $value['bm_item_code'] ?>" readonly />
                                                <input type="hidden" name="prt_bm_id[]" id="prt_bm_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_bm_id']; ?>" />
                                                <input type="hidden" name="prt_id[]" id="prt_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_id'] ?>" />
                                            </td>          

                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_bill_no[]" id="prt_bill_no_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_bill_no'] ?>" readonly />
                                                <input type="hidden" name="prt_pm_id[]" id="prt_pm_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_pm_id'] ?>" />
                                                <input type="hidden" name="prt_pt_id[]" id="prt_pt_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_pt_id'] ?>" />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control floating-input" name="prt_bill_date[]" id="prt_bill_date_<?php echo $purchase_cnt ?>" value="<?php echo date('d-m-Y', strtotime($value['prt_bill_date'])) ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control floating-input" id="design_name_<?php echo $purchase_cnt ?>" value="<?php echo $value['design_name'] ?>" readonly />
                                                <input type="hidden" name="prt_design_id[]" id="prt_design_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_design_id'] ?>" />
                                            </td>

                                            <td>
                                                <input type="text" class="form-control floating-input" id="style_name_<?php echo $purchase_cnt ?>" value="<?php echo $value['style_name'] ?>" readonly />
                                                <input type="hidden" name="prt_style_id[]" id="prt_style_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_style_id'] ?>" />
                                            </td>

                                            <td>
                                                <input type="text" class="form-control floating-input" id="brand_name_<?php echo $purchase_cnt ?>" value="<?php echo $value['brand_name'] ?>" readonly />
                                                <input type="hidden" name="prt_brand_id[]" id="prt_brand_id_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_brand_id'] ?>" />
                                            </td>

                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_qty[]" id="prt_qty_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_qty'] ?>" readonly />
                                            </td>

                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_rate[]" id="prt_rate_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_rate'] ?>" readonly />
                                            </td>

                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_disc[]" id="prt_disc_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_disc'] ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_taxable_amt[]" id="prt_taxable_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_taxable_amt'] ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_sgst_per[]" id="prt_sgst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_sgst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="prt_sgst_amt[]" id="prt_sgst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_sgst_amt'] ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_cgst_per[]" id="prt_cgst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_cgst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="prt_cgst_amt[]" id="prt_cgst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_cgst_amt'] ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_igst_per[]" id="prt_igst_per_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_igst_per'] ?>" readonly />
                                                <input type="number" class="form-control floating-input" name="prt_igst_amt[]" id="prt_igst_amt_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_igst_amt'] ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control floating-input" name="prt_sub_total[]" id="prt_sub_total_<?php echo $purchase_cnt ?>" value="<?php echo $value['prt_sub_total'] ?>" readonly />
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_row(<?php echo $purchase_cnt ?>)">
                                                    <i class="text-danger fa fa-trash"></i>
                                                </a>
                                            </td>
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
	</form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/purchase/purchase_return.js')?>"></script>
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