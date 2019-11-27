<?php $metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
					<?php if($len == 0){ ?>
					<tr class="tr-detail" data-ke="<?php echo $data_ke; ?>">
						<td colspan="7">
						<div class="div-detail-pakan">
							<table class="table table-bordered tbl-detail-pakan">
								<thead>
									<tr>
										<th class="arrow"></th>
										<th class="no-pallet">No</th>
										<th class="no-kavling">Kavling-Pallet</th>
										<th class="berat-pallet">Berat Pallet (Kg)</th>
										<th class="berat-timbang">Berat Timbang (kg)</th>
										<th class="berat-bersih">Berat Bersih (kg)</th>
										<th class="timbangan-sak">Timbangan (Sak)</th>
										<th class="selesai">Scan Barcode Pallet</th>
									</tr>
								</thead>
								<tbody>
						<?php } ?>		
                  					<tr class="tr-detail-pakan" data-ke="<?php echo $data_ke ?>">
										<td class="arrow">
											<span class="glyphicon glyphicon glyphicon-play"></span>
										</td>
										<td class="no-pallet" data-no-pallet="<?php echo $detail_penimbangan_pakan['no_pallet']; ?>"><?php echo $baris; ?></td>
										<td class="no-kavling" data-sak-tersimpan="0" data-no-kavling="<?php echo $detail_penimbangan_pakan['kode_pallet']; ?>" data-berat-hand-pallet="<?php echo ($detail_penimbangan_pakan['jml_pallet']==0) ? '' : $detail_penimbangan_pakan['berat_hand_pallet']; ?>" data-berat-pallet="<?php echo $detail_penimbangan_pakan['berat_pallet']; ?>"><span style="font-weight:bold;" onclick="ganti_kavling(this)"><?php echo $detail_penimbangan_pakan['kode_pallet']; ?></span>
											<?php if($detail_penimbangan_pakan['jml_pallet']>1){ ?><span data-status="1" onclick="ganti_hand_pallet(this)" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><?php } ?>
										</td>
										<td class="berat-pallet">
											<div class="input-group">
											<input type="text"
											placeholder="" name="berat-pallet"
											class="form-control berat-pallet text-center" onchange="get_detail_kandang(this)"
                                            onkeyup="replace_timbang(this)" onclick="selected(this)"
                                            value="<?php echo ($detail_penimbangan_pakan['jml_pallet']==0) ? '' : $detail_penimbangan_pakan['berat_pallet']+$detail_penimbangan_pakan['berat_hand_pallet']; ?>"
                                            readonly>
											<span class="input-group-addon" onclick="edit_berat_timbang(this)"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></span>
											</div>
										</td>
										<td class="berat-timbang">											
											<input type="text"
											placeholder="" name="berat-timbang"
											class="form-control berat-timbang text-center" onblur="get_detail_kandang(this)"
                                            <?php echo $metodeTimbangan ?>
                                            >
										</td>
										<td class="berat-bersih"></td>
										<td class="timbangan-sak"></td>
										<td class="selesai" data-selesai="0">
											<input type="text" class="form-control" readonly onchange="check_pallet(this)">
											<span class="help-block abang"></span>
											<!--
											<button class="btn btn-default" type="button" onclick="konfirmasi_selesai(this)" ondblclick="not_actived(this)" disabled>Selesai</button>
											-->
										</td>
									</tr>
					<?php if($len == 0){ ?>									
                                </tbody>
							</table>
						</div>
						</td>
					</tr>
					<?php } ?>
					