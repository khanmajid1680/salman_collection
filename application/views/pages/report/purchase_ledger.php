<table class="table table-sm table-hover">
	<tbody>
		<?php 
			if(!empty($data['pur_data'])):
				$sr_no 		= 1;
				$final_amt 	= 0;
				foreach ($data['pur_data'] as $key => $value):
					$final_amt = $final_amt + $value['pm_final_amt'];
		?>
					<tr>
						<td width="2%"><?php echo $sr_no; ?></td>
						<td width="15%"><?php echo strtoupper($value['account_name']); ?></td>
						<td width="5%"></td>
						<td width="8%"><?php echo $value['pm_bill_no']; ?></td>
						<td width="10%"><?php echo date('d-m-Y', strtotime($value['pm_bill_date'])); ?></td>
						<td width="10%"><?php echo $value['pm_total_qty']; ?></td>
						<td width="10%"><?php echo round($value['pm_sub_total'], 2); ?></td>
						<td width="10%"><?php echo round($value['pm_total_disc'], 2); ?></td>
						<td width="10%"><?php echo round($value['pm_bill_disc'], 2); ?></td>
						<td width="10%"><?php echo round($value['pm_gst_amt'], 2); ?></td>
						<td width="10%"><?php echo round($value['pm_final_amt'], 2); ?></td>
					</tr>
		<?php 
					$sr_no++;
				endforeach; 
		?>
				<tr style="font-size: 15px; font-weight: bold;">
					<td colspan="10" align="right">TOTAL PURCHASE AMT</td>
					<td><?php echo round($final_amt, 2); ?></td>
				</tr>
		<?php else: ?>
			<tr>
				<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>