{extends file='main.tpl'}
{block name=body}
{$nav}
<div class = "col-md-10 col-md-offset-1 hidden-print">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Entry Analisis Sampel
				<span class="right">
			    	<span class="glyphicon glyphicon-remove"></span>
			    </span>
			</h3>
		</div>
		<div class="panel-body">
			<div id="header_input col-md-12">
				<form class="form-horizontal" >
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="inputNoSampel" class="col-sm-3 control-label">No. Sampel</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="inputNoSampel" placeholder="No. Sampel" name="inputNoSampel" value="{$no_sampel}" readonly=true>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="inputBahanBaku" class="col-sm-4 control-label">Bahan Baku</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="inputBahanBaku" placeholder="Bahan Baku" name="inputBahanBaku" value="{$detail_sample[0]['rm_label']}" readonly=true>
							</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li class="active">
					<a href="#sp" role="tab" data-toggle="tab" id="for_sp">SP</a>
				</li>
				<li>
					<a href="#non_sp" role="tab" data-toggle="tab" id="for_non_sp">Non-SP</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane fade in active" id="sp">
					<center>
						<div class="row">
							<div class="col-md-12">
								<form role="form" class="form-horizontal" action="analysis_sample/save" method="post" enctype="multipart/form-data" id="form-sp-entry">
								<div class="row">
									<table class="table table-bordered" id="list-sp">
										<thead>
											<tr>
												<th class="col-number">#</th>
												<th class="col-analisa">Jenis Analisis</th>
												<th class="col-parameter">Parameter Analisis</th>
												<th class="col-hasil">Hasil</th>
												<th class="col-keterangan">Keterangan</th>
												<!--th class="col-analisis">Analis</th-->
											</tr>
										</thead>
										<tbody>
											{$i=1}
											{foreach $detail_sample as $key=>$value}
											{if $value.important == 1}
											<tr class="{$i}">
												<td class="col-number"><label class="vertical-align">{$i++}</label><input type="hidden" class="form-control" name="id[]" placeholder="Id" value="{$value.id_rasi}"><input type="hidden" class="form-control id_sar" name="id_sar[]" placeholder="Id" value="{$value.id_sar}">
												</td>
												<td class="col-analisa"><label class="vertical-align">{$value.jenis}</label></td>
												<td class="col-parameter"><label class="vertical-align">{$value.parameter}</label></td>
												<td class="col-hasil">
													{if empty($value.verbatim)}
													<input type="text" class="form-control {if empty($detail_sample[0]['sp_review'])}{else}disabled{/if}" name="hasil[]" placeholder="Hasil" onkeyup="decimalOnly(this)" value="{if $value.value != ''}{floatval($value.value)}{/if}" {if $status_sp_entry == "LENGKAP"}disabled{/if}>
													{else}
													<input type="hidden" class="form-control {if empty($detail_sample[0]['sp_review'])}{else}disabled{/if}" name="hasil[]" placeholder="Hasil" onkeyup="decimalOnly(this)" value=''>
													N/A
													{/if}
												</td>
												<td class="col-keterangan">
													{if empty($value.verbatim)}
													<select class="form-control hidden {if empty($detail_sample[0]['sp_review'])}{else}disabled{/if}" name="keterangan[]" placeholder="Keterangan" onchange="kontrol_save(this)">
														<option value="" selected="true">Pilih Keterangan</option>
													</select>
													N/A
													{else}
													<select class="form-control {if empty($detail_sample[0]['sp_review'])}{else}disabled{/if}" name="keterangan[]" placeholder="Keterangan" onchange="kontrol_save(this)" {if $status_sp_entry == "LENGKAP"}disabled{/if}>
														<option value="">Pilih Keterangan</option>
														{foreach $value.list_keterangan as $key1=>$value1}
														{if $value1.id_keterangan == $value.verbatim_sar}
															<option value="{$value1.id_keterangan}" selected>{$value1.keterangan}</option>
														{else}
															<option value="{$value1.id_keterangan}">{$value1.keterangan}</option>
														{/if}
														{/foreach}
													</select>
													{/if}
												</td>
												<!--td class="col-analisis">
												<select class="form-control" name="analis[]" placeholder="Analis" disabled="true">
													<option value="" selected="true">Pilih Analis</option>
												</select></td-->
											</tr>
											{/if}
											{/foreach}
										</tbody>
									</table>
								</div>
								<div class="row">
									<input type="hidden" name="important_sp" id="important_sp" value = '1'>
									<button type="submit" class="btn btn-primary" id="save-sp" disabled="true">
										<span class="glyphicon glyphicon-save"></span>
										Simpan
									</button>
								</div>
								</form>
							</div>
						</div>
					</center>
				</div>
				<div class="tab-pane fade" id="non_sp">
					<center>
						<div class="row">
							<div class="col-md-12">
								<form role="form" class="form-horizontal" action="analysis_sample/save" method="post" enctype="multipart/form-data" id='form-nonsp-entry'>
								<div class="row">
									<table class="table table-bordered" id="list-nonsp">
										<thead>
											<tr>
												<th class="col-number">#</th>
												<th class="col-analisa">Jenis Analisis</th>
												<th class="col-parameter">Parameter Analisis</th>
												<th class="col-hasil">Hasil</th>
												<th class="col-keterangan">Keterangan</th>
												<!--th class="col-analisis">Analis</th-->
											</tr>
										</thead>
										<tbody>
											{$i=1}
											{foreach $detail_sample as $key=>$value}
											{if $value.important == 0}
											<tr class="{$i}">
												<td class="col-number"><label class="vertical-align">{$i++}</label><input type="hidden" class="form-control" name="id[]" placeholder="Id" value="{$value.id_rasi}"><input type="hidden" class="form-control id_sar" name="id_sar[]" placeholder="Id" value="{$value.id_sar}">
												</td>
												<td class="col-analisa"><label class="vertical-align">{$value.jenis}</label></td>
												<td class="col-parameter"><label class="vertical-align">{$value.parameter}</label></td>
												<td class="col-hasil">
													{if empty($value.verbatim)}
													<input type="text" class="form-control" name="hasil[]" placeholder="Hasil" onkeyup="decimalOnly(this)" value="{if $value.value != ''}{floatval($value.value)}{/if}" {if $status_nonsp_entry == "LENGKAP"}disabled{/if}>
													{else}
													<input type="hidden" class="form-control" name="hasil[]" placeholder="Hasil" onkeyup="decimalOnly(this)" value='' {if empty($detail_sample[0]['nonsp_review'])}{else}disabled{/if}>
													N/A
													{/if}
												</td>
												<td class="col-keterangan">
													{if empty($value.verbatim)}
													<select class="form-control hidden" name="keterangan[]" placeholder="Keterangan" onchange="kontrol_save(this)">
														<option value="" selected="true">Pilih Keterangan</option>
													</select>
													N/A
													{else}
													<select class="form-control" name="keterangan[]" placeholder="Keterangan" onchange="kontrol_save(this)" {if $status_nonsp_entry == "LENGKAP"}disabled{/if}>
														<option value="">Pilih Keterangan</option>
														{foreach $value.list_keterangan as $key1=>$value1}
														{if $value1.id_keterangan == $value.verbatim_sar}
															<option value="{$value1.id_keterangan}" selected>{$value1.keterangan}</option>
														{else}
															<option value="{$value1.id_keterangan}">{$value1.keterangan}</option>
														{/if}
														{/foreach}
													</select>
													{/if}
												</td>
												<!--td class="col-analisis">
												<select class="form-control" name="analis[]" placeholder="Analis" disabled="true">
													<option value="" selected="true">Pilih Analis</option>
												</select></td-->
											</tr>
											{else}
											{/if}
											{/foreach}
										</tbody>
									</table>
								</div>
								<div class="row">
									<input type="hidden" name="important_nonsp" id="important_nonsp" value = '0'>
									<button type="submit" class="btn btn-primary" id="save-nonsp" disabled="true">
										<span class="glyphicon glyphicon-save"></span>
										Simpan
									</button>
								</div>
								</form>
							</div>
						</div>
					</center>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<h3></h3>
		</div>
	</div>
</div>
{/block}
{block name=jsAdditional}
{include file='analysisSampleJs.tpl'}
{/block}