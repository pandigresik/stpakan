<?php foreach ($pakan_rusak as $key => $value) { ?>
					<tr>
						<td><?php echo $value['nama_pakan']; ?></td>
						<td><?php echo $value['jenis_kelamin']; ?></td>
						<td><?php echo $value['jml_retur']; ?></td>
						<td><?php echo $value['berat']; ?></td>
					</tr>
				<?php } ?>