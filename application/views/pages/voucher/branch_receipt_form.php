<?php 
    $this->load->view('templates/header'); 
    $disabled       = empty($master_data) ? '' : "disabled";
    $wrapper_area   = empty($trans_data) ? "d-none" : "";
    $party_id       = isset($_GET['party_id']) && !empty($_GET['party_id']) ? $_GET['party_id'] : 0;
    $redirected     = isset($_GET['party_id']) && !empty($_GET['party_id']);
?>
<script>
    let link = "voucher";
    let sub_link = "branch_receipt";
</script>
<style type="text/css">
    .report_table > tbody {
        display:block;
        max-height:30vh;
        overflow:auto;
    }
    .report_table > thead, .report_table > tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;
    }
</style>
<section class="d-flex justify-content-between sticky_top neu_flat_primary breadcrumb_pagination">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('voucher/branch_receipt?action=view'); ?>">BRANCH RECEIPT</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo empty($master_data) ? 'ADD' : 'EDIT'; ?></li>
        <li class="breadcrumb-item" aria-current="save-page">
            <button type="button" class="btn btn-sm btn-primary master_block_btn" onclick="add_update_receipt(<?php echo empty($master_data) ? 0 : $master_data[0]['vm_id']; ?>)" data-toggle="tooltip" data-placement="bottom" title="SAVE" tabindex="8" <?php echo empty($master_data) ? 'disabled="disabled"' : '' ?>><i class="text-success fa fa-save"></i></button>
        </li>
        <li class="breadcrumb-item" aria-current="cancel-page">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo base_url('voucher/branch_receipt?action=view')?>" data-toggle="tooltip" data-placement="bottom" title="CANCEL" tabindex="9"><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
    <form class="form-horizontal" id="receipt_form">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-header">RECEIPT DETAIL</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-4 col-lg-2 floating-label">
                                <input type="number" class="form-control floating-input" id="vm_entry_no" name="vm_entry_no" value="<?php echo empty($master_data) ? $vm_entry_no : $master_data[0]['vm_entry_no'] ?>" placeholder=" " readonly="readonly" />   
                                <input type="hidden" name="vm_type" value="RECEIPT">
                                <input type="hidden" id="vm_redirected" value="<?php echo $redirected; ?>" />
                                <input type="hidden" id="_party_id" value="<?php echo $party_id; ?>" />
                                <input type="hidden" name="vm_group" value="BRANCH" />
                                <label for="inputEmail3">ENTRY NO</label>
                                <small class="form-text text-muted helper-text" id="vm_entry_no_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input type="text" class="form-control floating-input" id="vm_entry_date" name="vm_entry_date" value="<?php echo empty($master_data) ? date('d-m-Y') : date('d-m-Y', strtotime($master_data[0]['vm_entry_date'])) ?>" placeholder=" " readonly="readonly" />   
                                <label for="inputEmail3">ENTRY DATE</label>
                                <small class="form-text text-muted helper-text" id="vm_entry_date_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-3 floating-label">
                                <p for="inputEmail3">ACCOUNT&nbsp;<span class="text-danger">*</span></p>
                                <?php echo form_dropdown('', $accounts, empty($master_data) ? 0 : $master_data[0]['vm_acc_id'],'class="form-control floating-select" id="vm_acc_id" name="vm_acc_id" onchange="validate_dropdown(this)" tabindex="1"'.$disabled);?>
                                <?php if(!empty($master_data)): ?>
                                    <input type="hidden" name="vm_acc_id" value="<?php echo $master_data[0]['vm_acc_id']?>" />
                                <?php endif; ?>
                                <input type="hidden" id="vm_constant" name="vm_constant" value="<?php echo !empty($master_data) ? $master_data[0]['account_constant'] : '';?>" />
                                <input type="hidden" id="vm_party_id" name="vm_party_id" value="<?php echo !empty($master_data) ? $master_data[0]['vm_party_id'] : 0;?>" />
                                <small class="form-text text-muted helper-text" id="vm_acc_id_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <p for="inputEmail3">BRANCH&nbsp;<span class="text-danger">*</span></p>
                                <select class="form-control floating-select" id="vm_branch" name="vm_branch" placeholder="" tabindex="2" onchange="validate_dropdown(this)" <?php echo $disabled; ?>>
                                    <?php if(!empty($master_data)): ?>
                                        <option value="<?php echo $master_data[0]['vm_branch'] ?>">
                                            <?php echo $master_data[0]['party_name']; ?>
                                        </option>
                                        <input type="hidden" name="vm_branch" value="<?php echo $master_data[0]['vm_branch'] ?>" />
                                    <?php endif; ?>                                                
                                </select>
                                <small class="form-text text-muted helper-text" id="vm_branch_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>AMOUNT DETAIL</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap mt-2 form-group floating-form">
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input type="number" class="form-control floating-input" id="vm_bill_amt" name="vm_bill_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_bill_amt'] ?>" placeholder=" " readonly="readonly" />
                                <label for="inputEmail3">OUTWARD AMT</label>
                                <small class="form-text text-muted helper-text" id="vm_bill_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input type="number" class="form-control floating-input" id="vm_total_amt" name="vm_total_amt" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_total_amt'] ?>" placeholder=" " value="0" readonly/>
                                <input type="hidden" id="vm_round_off" name="vm_round_off" value="<?php echo empty($master_data) ? 0 : $master_data[0]['vm_round_off'] ?>"/>
                                <label for="inputEmail3">RECEIPT AMT</label>
                                <small class="form-text text-muted helper-text" id="vm_total_amt_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 floating-label">
                                <?php if(!empty($master_data)): ?>
                                <?php else: ?>
                                    <input type="text" class="form-control floating-input" id="vm_balance_show" value="" placeholder=" " readonly="readonly" />   
                                    <label for="inputEmail3">BALANCE</label>
                                <?php endif; ?>
                                <input type="hidden" id="vm_balance" value="0">
                                <input type="hidden" id="vm_balance_type" value="">
                                <small class="form-text text-muted helper-text" id="vm_balance_show_msg"></small>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 floating-label">
                                <textarea class="form-control floating-textarea" id="vm_notes" name="vm_notes" placeholder=" " tabindex="7" autocomplete="off"><?php echo empty($master_data) ? '' : $master_data[0]['vm_notes']; ?></textarea>
                                <label for="inputEmail3">NOTES</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card wrapper_area <?php echo $wrapper_area; ?>">
                    <div class="card-header">OUTWARD DETAIL</div>
                    <div class="card-body mt-2">
                        <table class="table table-hover table-sm report_table" id="voucher_material_wrapper">
                            <?php if(!empty($trans_data)): ?>
                                <thead>
                                    <tr style="font-size: 15px;">
                                        <th width="5%">ENTRY NO</th>
                                        <th width="7%">ENTRY DATE</th>
                                        <th width="6%">OUTWARD QTY</th>
                                        <th width="6%">GRN QTY</th>
                                        <th width="6%">OUTWARD AMT</th>
                                        <th width="6%">GRN AMT</th>
                                        <th width="8%">ADJUST AMT</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt_material_wrapper">
                                    <?php foreach ($trans_data as $key => $value): ?>
                                        <tr id="rowid_<?php echo $value['vt_om_id'] ?>" class="floating-form">
                                            <td width="5%" class="floating-label">
                                                <input type="hidden" id="vt_id_<?php echo $value['vt_om_id'] ?>" name="vt_id[]" value="<?php echo $value['vt_id']; ?>" />
                                                <input type="hidden" id="vt_bill_clear_<?php echo $value['vt_om_id'] ?>" name="vt_bill_clear[]" value="YES" />
                                                <input type="hidden" id="vt_om_id_<?php echo $value['vt_om_id'] ?>" name="vt_om_id[]" value="<?php echo $value['vt_om_id'] ?>" />
                                                <input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_bill_no']; ?>" readonly />
                                            </td>
                                            <td width="7%" class="floating-label">
                                                <input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_bill_date']; ?>" readonly />
                                            </td>
                                            <td width="6%" class="floating-label">
                                                <input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_total_qty']; ?>" readonly />
                                            </td>
                                            <td width="6%" class="floating-label">
                                                <input type="number" class="form-control floating-input" name="vt_return_qty[]" id="vt_return_qty_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_return_qty']; ?>" readonly />
                                            </td>
                                            <td width="6%" class="floating-label">
                                                <input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_total_amt']; ?>" readonly />
                                                <input type="hidden" class="form-control floating-input" name="vt_allocated_amt[]" id="vt_allocated_amt_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_allocated_amt']; ?>" readonly />
                                                <input type="hidden" name="vt_allocated_round_off[]" id="vt_allocated_round_off_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_allocated_round_off']; ?>" />
                                            </td>
                                            <td width="6%" class="floating-label">
                                                <input type="number" class="form-control floating-input" name="vt_return_amt[]" id="vt_return_amt_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_return_amt']; ?>" readonly />
                                            </td>
                                            <td width="8%" class="floating-label">
                                                <input type="number" class="form-control floating-input" name="vt_adjust_amt[]" id="vt_adjust_amt_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_adjust_amt']; ?>" readonly />
                                                <input type="hidden" name="vt_adjust_round_off[]" id="vt_adjust_round_off_<?php echo $value['vt_om_id'] ?>" value="<?php echo $value['vt_adjust_round_off']; ?>" />
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/voucher/branch_receipt.js')?>"></script>
</body>
</html>