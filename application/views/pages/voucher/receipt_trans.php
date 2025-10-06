<?php if(!empty($trans_data)): ?>
    <thead>
        <tr style="font-size: 15px;">
            <th width="5%">BILL NO</th>
            <th width="7%">BILL DATE</th>
            <th width="6%">TOTAL QTY</th>
            <th width="6%">BILL AMT</th>
            <th width="15%" colspan="2">ALLOCATED AMT & ROUND OFF</th>
            <th width="8%">ADJUST AMT</th>
            <th width="10%">ROUND OFF AMT</th>
        </tr>
    </thead>
    <tbody id="sales_material_wrapper">
        <?php foreach ($trans_data as $key => $value): ?>
            <tr id="rowid_<?php echo $value['vt_sm_id'] ?>" class="floating-form">
                <td class="floating-label">
                    <input type="hidden" id="vt_id_<?php echo $value['vt_sm_id'] ?>" name="vt_id[]" value="<?php echo $value['vt_id']; ?>" />
                    <input type="hidden" id="vt_bill_clear_<?php echo $value['vt_sm_id'] ?>" name="vt_bill_clear[]" value="YES" />
                    <input type="hidden" id="vt_sm_id_<?php echo $value['vt_sm_id'] ?>" name="vt_sm_id[]" value="<?php echo $value['vt_sm_id'] ?>" />
                    <input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_bill_no']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_bill_date']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_total_qty']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_total_amt']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_allocated_amt[]" id="vt_allocated_amt_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_allocated_amt']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_allocated_round_off[]" id="vt_allocated_round_off_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_allocated_round_off']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_adjust_amt[]" id="vt_adjust_amt_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_adjust_amt']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_adjust_round_off[]" id="vt_adjust_round_off_<?php echo $value['vt_sm_id'] ?>" value="<?php echo $value['vt_adjust_round_off']; ?>" readonly />
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
<?php endif; ?>