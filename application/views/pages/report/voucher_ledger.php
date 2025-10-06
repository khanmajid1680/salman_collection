<table class="table table-sm table-hover">
	<tbody>
		<?php 
			if(!empty($data['vou_data'])):
				$sr_no 		= 1;
				$final_amt 	= 0;
				foreach ($data['vou_data'] as $key => $value):
					$final_amt = $final_amt + ($value['vm_total_amt'] - $value['vm_round_off']);
		?>
					<tr>
						<td width="2%"><?php echo $sr_no; ?></td>
						<td width="15%"><?php echo strtoupper($value['account_name']); ?></td>
						<td width="8%"><?php echo $value['vm_entry_no']; ?></td>
						<td width="10%"><?php echo date('d-m-Y', strtotime($value['vm_entry_date'])); ?></td>
						<td width="10%"><?php echo $value['vm_type']; ?></td>
						<td width="25%"><?php echo strtoupper($value['vm_notes']); ?></td>
						<td width="10%"><?php echo round($value['vm_total_amt'], 2); ?></td>
						<td width="10%"><?php echo round($value['vm_round_off'], 2); ?></td>
						<td width="10%"><?php echo round($value['vm_total_amt'] - $value['vm_round_off'], 2); ?></td>
					</tr>
		<?php 
					$sr_no++;
				endforeach; 
		?>
				<tr style="font-size: 15px; font-weight: bold;">
					<td colspan="8" align="right">TOTAL VOUCHER AMT</td>
					<td><?php echo round($final_amt, 2); ?></td>
				</tr>
		<?php else: ?>
			<tr>
				<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>