<table class="table table-sm table-hover">
	<tbody>
		<?php 
			if(!empty($data['sales_data'])):
				$sr_no 		= 1;
				$final_amt 	= 0;
				foreach ($data['sales_data'] as $key => $value):
					$final_amt = $final_amt + $value['sm_final_amt'];
		?>
					<tr>
						<td width="2%"><?php echo $sr_no; ?></td>
						<td width="15%"><?php echo strtoupper($value['account_name']); ?></td>
						<td width="8%"><?php echo $value['sm_bill_no']; ?></td>
						<td width="10%"><?php echo date('d-m-Y', strtotime($value['sm_bill_date'])); ?></td>
						<td width="10%"><?php echo $value['sm_total_qty']; ?></td>
						<td width="10%"><?php echo round($value['sm_sub_total'], 2); ?></td>
						<td width="10%"><?php echo round($value['sm_total_disc'], 2); ?></td>
						<td width="10%"><?php echo round($value['sm_point_used'], 2); ?></td>
						<td width="10%"><?php echo round($value['sm_final_amt'], 2); ?></td>
					</tr>
		<?php 
					$sr_no++;
				endforeach; 
		?>
				<tr style="font-size: 15px; font-weight: bold;">
					<td colspan="8" align="right">TOTAL SALES AMT</td>
					<td><?php echo round($final_amt, 2); ?></td>
				</tr>
		<?php else: ?>
			<tr>
				<td class="text-danger font-weight-bold text-center" colspan="20">NO RECORD FOUND!!!</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>