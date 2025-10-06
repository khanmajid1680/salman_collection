<?php if(!empty($trans_data)): ?>
    <thead>
        <tr style="font-size: 15px;">
            <th width="10%">BILL NO</th>
            <th width="10%">BILL DATE</th>
            <th width="10%">TOTAL QTY</th>
            <th width="10%">BILL AMT</th>
            <th width="10%">ADJUSTED BILL AMT</th>
            <th width="10%">RETURN QTY</th>
            <th width="10%">DEBIT NOTE AMT</th>
            <th width="10%">ACTUAL BILL AMT</th>
        </tr>
    </thead>
    <tbody id="payment_material_wrapper">
        <?php foreach ($trans_data as $key => $value): ?>
            <tr id="rowid_<?php echo $value['vt_pm_id'] ?>" class="floating-form">
                <td class="floating-label">
                    <input type="hidden" id="vt_id_<?php echo $value['vt_pm_id'] ?>" name="vt_id[]" value="<?php echo $value['vt_id']; ?>" />
                    <input type="hidden" id="vt_bill_clear_<?php echo $value['vt_pm_id'] ?>" name="vt_bill_clear[]" value="YES" />
                    <input type="hidden" id="vt_pm_id_<?php echo $value['vt_pm_id'] ?>" name="vt_pm_id[]" value="<?php echo $value['vt_pm_id'] ?>" />
                    <input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_bill_no']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_bill_date']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_total_qty']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_total_amt']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="vt_adjusted_amt_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['pm_allocated_amt'] + $value['pm_allocated_round_off']; ?>" readonly />
                    <input type="hidden" name="vt_allocated_amt[]" id="vt_allocated_amt_<?php echo $value['vt_pm_id'] ?>" value="0" />
                    <input type="hidden" name="vt_allocated_round_off[]" id="vt_allocated_round_off_<?php echo $value['vt_pm_id'] ?>" value="0" />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_return_qty[]" id="vt_return_qty_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_return_qty']; ?>" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" name="vt_return_amt[]" id="vt_return_amt_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_return_amt']; ?>"  readonly/>
                    <input type="hidden" name="vt_adjust_amt[]" id="vt_adjust_amt_<?php echo $value['vt_pm_id'] ?>" value="0" />
                    <input type="hidden" name="vt_adjust_round_off[]" id="vt_adjust_round_off_<?php echo $value['vt_pm_id'] ?>" value="0" />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="vt_balance_amt_show_<?php echo $value['vt_pm_id'] ?>" value="<?php echo $value['vt_total_amt'] - $value['vt_return_amt']; ?>" readonly />
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
<?php endif; ?>