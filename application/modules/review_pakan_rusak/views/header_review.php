<?php foreach ($pakan_rusak as $key => $value) { ?>
					<tr onclick="ReviewPakanRusak.detail_pakan_rusak(this)"
						data-no-reg="<?php echo $value['no_reg']; ?>"
						data-no-urut="<?php echo $value['no_urut']; ?>">
						<td><?php echo $value['no_retur']; ?></td>
						<td><?php echo $value['kandang']; ?></td>
						<td><?php echo date('d M Y H:i',strtotime($value['tgl_retur'])); ?></td>
						<td><?php echo $value['diserahkan_oleh']; ?></td>
						<td><?php echo $value['penerima']; ?></td>
						<td class='wkt_review'><?php echo !empty($value['wkt_review']) ? date('d M Y H:i',strtotime($value['wkt_review'])) : ''; ?></td>
						<td><?php echo $value['keputusan']; ?></td>
						<td><a href="review_pakan_rusak/review/download?no_retur=<?php echo $value['no_reg']."-".$value['no_urut']; ?>"><?php echo $value['attachment_name']; ?></a></td>
					</tr>
				<?php } ?>