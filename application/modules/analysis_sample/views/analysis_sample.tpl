{extends file='main.tpl'}
{block name=body}
{$nav}
<div class = "col-md-10 col-md-offset-1 hidden-print">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"> Analisis Sampel </h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" class="col-sm-1 control-label" name="checkbox_belum_lengkap" id="checkbox_belum_lengkap" checked="true" value=1 onclick="filter_checkbox(this)" data='1'>
								Filter antrian pekerjaan yang belum lengkap</label>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" class="col-sm-1 control-label" name="checkbox_belum_checkout" id="checkbox_belum_checkout" checked="true" value=1 onclick="filter_checkbox(this)" data='2'>
								Filter sampel berasal dari kendaraan yang belum checkout</label>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" class="col-sm-1 control-label" name="checkbox_belum_checkout" id="checkbox_pending" value=0 onclick="filter_checkbox(this)" data='3'>
								Filter sampel pending</label>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<table class="table table-bordered" id="table-sample">
							<thead>
								<tr class="no-border">
									<th class="col-sampel">
										<input type="text" class="form-control col-sampel" placeholder="No. Sampel" onkeyup="filter(this)" id='inputSample'>
									</th>
									<th class="col-bahan-baku">
										<select class="form-control col-bahan-baku" id="inputBahanBaku" placeholder="Bahan Baku" name="inputBahanBaku" onchange="filter(this)">
											<option value="">Semua Bahan Baku</option>
											{foreach $data_bahan_baku as $key=>$value}
											<option value="{$value.label}">{$value.label}</option>
											{/foreach}
										</select>
									</th>
								</tr>
								<tr>
									<th class="col-sampel">No. Sampel</th>
									<th class="col-bahan-baku">Bahan Baku</th>
									<th class="col-cetak-label">Cetak Label</th>
									<th class="col-sp">SP</th>
									<th class="col-non-sp">Non-SP</th>
									<th class="col-lengkap">Lengkap</th>
								</tr>
							</thead>
							<tbody>
								{$i=1}
								{$tmp_sample = ''}
								{foreach $data_sampel as $key => $value}
								{if $value.no_sampel != $tmp_sample}
								<tr class="{$i++} {if ($value.to_be_composed == 1 && empty($value.item_placement))}composed{else if ($value.to_be_composed == '0' && !empty($value.item_placement))}{else}not_composed{/if}" onclick="tandai_sampel(this)" ondblclick="detail_hasil(this)" data-vdni="{$value.id_vdni}" data-nopanggil="{$value.no_panggil}" data-nomerop="{$value.nomerop}">
									<td class="col-sampel"><label class="vertical-align">{$value.no_sampel} {if $value.printed == 0}<span class="glyphicon glyphicon-star" style="color:blue;"></span>{/if}</label></td>
									<td class="col-bahan-baku"><label class="vertical-align">{$value.item_label}</label></td>
									<td class="col-cetak-label" data="{$value.printed}" stamp="{$value.stamp}"><label class="vertical-align">{$value.printed_label}</label></td>
									{if $value.to_be_composed == 0}
									<td class="col-sp" data="{$value.sp_nol}"><label class="vertical-align">{$value.sp_label_nol}</label></td>
									<td class="col-non-sp" data="{$value.non_sp_nol}"><label class="vertical-align">{$value.non_sp_label_nol}</label></td>
									<td class="col-lengkap"><label class="vertical-align">{$value.lengkap_nol}</label></td>
									{else}

									<td class="col-sp" data="{$value.sp}"><label class="vertical-align">{$value.sp_label}</label></td>
									<td class="col-non-sp" data="{$value.non_sp}"><label class="vertical-align">{$value.non_sp_label}</label></td>
									<td class="col-lengkap"><label class="vertical-align">{$value.lengkap}</label></td>
									{/if}
								</tr>
								{$tmp_sample = $value.no_sampel}
								{/if}
								{/foreach}
							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="form-group">
							<span class="glyphicon glyphicon-star" style="color:blue;margin-left:1%;"></span>
							Belum dicetak.
						</div>
					</div>
					<div class="row">
						<table border='0' align='center'>
							<tr>
								<td class='{$as_print}'>
									<div class="col-md-2 center-block">
										<form action="{base_url()}analysis_sample/print_sample" method="post" target='_blank' onsubmit='return check_printed_composit()'>
											<input type="hidden" name="sample" id="sample_cetak">
											<input type="hidden" name="panggil" id="nopanggil">
											<input type="hidden" name="nomerop" id="nomerop">
											<input type="hidden" name="rm" id="rm_cetak">
											<input type="hidden" name="datetime" id="waktu_cetak">
											<input type="hidden" name="composit" id="composit_cetak">
											<button type="button" class="btn btn-primary center-block btn-menu" id="btn_cetak" data-toggle="" data-target=".modal_question" onclick="cetak_label()">
												<span class="glyphicon glyphicon-print"></span>
												Cetak Label
											</button>
										</form>
									</div>
								</td>
								<td class='{$as_entry}'>
									<div class="col-md-2 center-block">
										<form target="_blank" action="{base_url()}analysis_sample/entry" method="post">
											<input type="hidden" name="sample_entry" id="sample_entry">
											<input type="hidden" name="composit_entry" id="composit_entry">
											<input type="hidden" name="status_sp_entry" id="status_sp_entry">
											<input type="hidden" name="status_nonsp_entry" id="status_nonsp_entry">
											<button type="submit" class="btn btn-primary center-block btn-menu" id="btn_entry" disabled='true'>
												<span class="glyphicon glyphicon-pencil"></span>
												Entry Hasil Analysis
											</button>
										</form>
									</div>
								</td>
								<td class='{$as_review}'>
									<div class="col-md-2 center-block">
										<form target="_blank" action="{base_url()}analysis_sample/review" method="post">
											<input type="hidden" name="sample_review" id="sample_review">
											<input type="hidden" name="vdni_review" id="vdni_review">
											<input type="hidden" name="composit_review" id="composit_review">
											<button type="submit" class="btn btn-primary center-block btn-menu" id="btn_review" disabled='true'>
												<span class="glyphicon glyphicon-list-alt"></span>
												Review
											</button>
										</form>
									</div>
								</td>
								<td>
									<div class="col-md-2 center-block">
										<form id="form_detail" target="_blank" action="{base_url()}analysis_sample/detail" method="post">
											<input type="hidden" name="sample_detail" id="sample_detail">
											<input type="hidden" name="composit_detail" id="composit_detail">
											<input type="hidden" name="lengkap" id="lengkap">
											<input type="hidden" name="status_sp" id="status_sp">
											<input type="hidden" name="status_nonsp" id="status_nonsp">
											<button type="submit" class="btn btn-primary center-block btn-menu" id="btn_detail" disabled='true'>
												<span class="glyphicon glyphicon-tasks"></span>
												Detail
											</button>
										</form>
									</div>
								</td>
								<td>
									<div class="col-md-2 center-block">
										<form id="form_detail_composit" action="" method="post">
											<input type="hidden" name="sample_detail_composit" id="sample_detail_composit">
											<button type="button" class="btn btn-primary center-block btn-menu" id="btn_detail_composit" data-toggle="modal" data-target=".modal_tree" onclick='detail_komposit()' disabled='true'>
												<span class="glyphicon glyphicon-tree-conifer"></span>
												Detail Komposit
											</button>
										</form>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<h3></h3>
		</div>
	</div>
</div>
<div id="barcode_sampel">
<center>
<div id="label-bahan-baku-sampel"></div>
<div id="label-waktu-sampel"></div>
<div id="label-barcode-sampel"></div>
</center>
</div>
<div class="modal fade modal_question hidden-print" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Keluar</span>
				</button>
				<h4 class="modal-title">Cetak Label Sampel</h4>
			</div>
			<div class="modal-body">
				<p>
					Apakah anda yakin ingin mencetak ulang label sampel <label id="id_sampel"></label> ?
				</p>
			</div>
			<form action="{base_url()}analysis_sample/print_sample" method="post" target='_blank'>
				<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel">
					Tidak
				</button>
				<!--button type="button" class="btn btn-primary" id="btn-again" onclick='print_again()'-->
					<input type="hidden" name="sample" id="sample_cetak2">
					<input type="hidden" name="panggil" id="nopanggil2">
					<input type="hidden" name="nomerop" id="nomerop2">
					<input type="hidden" name="rm" id="rm_cetak2">
					<input type="hidden" name="datetime" id="waktu_cetak2">
					<input type="hidden" name="composit" id="composit_cetak2">
					<button type="submit" class="btn btn-primary" id="btn-again">
						<span class="glyphicon glyphicon-print"></span> Ya
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade modal_tree hidden-print" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Keluar</span>
				</button>
				<h4 class="modal-title">Detail Sampel Komposit</h4>
			</div>
			<div class="modal-body">
				<div id = 'list-sample'>
					Loading ...
				</div>
			</div>
		</div>
	</div>
</div>
<div class="sound-logo" style="position:fixed;right:0px;bottom:0px">
	<!--
	<button class="btn btn-primary" onclick="sudahPenuh()"><span class="glyphicon glyphicon-tint"></span> Penuh</button>
	-->
	<button class="btn btn-default" onclick="matikanSuara()"><span class="glyphicon glyphicon-volume-up"></span></button>
</div>
{/block}
{block name=jsAdditional}
	<script type="text/javascript" src="assets/js/ws_events_dispatcher.js"></script>
	<script type="text/javascript" src="assets/js/bootbox.js"></script>
{include file='analysisSampleJs.tpl'}
{/block}