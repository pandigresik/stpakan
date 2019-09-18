{extends file='main.tpl'}
{block name=body}
{$nav}
<div class = "col-md-10 col-md-offset-1 hidden-print">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Review Hasil Analisis Sampel
				<span class="right">
			    	<span class="glyphicon glyphicon-remove"></span>
			    </span>
			</h3>
		</div>
		<div class="panel-body">
			<div id="header_input col-md-12">
				<div class="row form-horizontal">
					<div class="col-md-6">
						<div class="form-group">
							<label for="inputNoSampel" class="col-sm-3 control-label">No. Sampel</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="inputNoSampel" placeholder="No. Sampel" name="inputNoSampel" value="{$no_sampel}" readonly=true>
								<input type="hidden" class="form-control" id="inputVendor" placeholder="Vendor" name="inputVendor" value="{$detail_sample[0]['vendor']}" readonly=true>
								<input type="hidden" class="form-control" id="inputVdni" placeholder="No. Sampel" name="inputVdni" value="{$id_vdni}" readonly=true>
								<input type="hidden" class="form-control" id="inputTipe" placeholder="Tipe" name="inputTipe" value="{$detail_sample[0]['tipe']}" readonly=true>
								<input type="hidden" class="form-control" id="inputVehicleSegment" placeholder="No. Sampel" name="inputVehicleSegment" value="{$detail_sample[0]['vehicle_segment']}" readonly=true>
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
						<!--div>
						{if count($rm_oracle)>0}
						<div class="form-group">
							<label for="inputBahanBaku" class="col-sm-4 control-label">Klasifikasi Bahan Baku</label>
							<div class="col-md-6">
								<select class="form-control" id="inputKlasisfikasiBahanBaku" name="inputKlasisfikasiBahanBaku" placeholder="Klasifikasi Bahan Baku">
									<option value="" selected="true">Pilih Bahan Baku</option>
									{foreach $rm_oracle as $key => $value}
										<option value="{$value.KODE_BARANG}">{$value.NAMA}</option>
									{/foreach}
								</select>
							</div>
						</div>
						{else}
						<input type='hidden' class="form-control" id="inputKlasisfikasiBahanBaku" name="inputKlasisfikasiBahanBaku" placeholder="Klasifikasi Bahan Baku" value='0'>
						{/if}
						</div-->
					</div>
				</div>
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
								<form role="form" class="form-horizontal" action="analysis_sample/approve_sp" method="post" enctype="multipart/form-data" id="form-sp-review">
								<input type="hidden" class="form-control" id="composit" placeholder="Composit" name="composit" value="{$composit}">
								<div class="hide" id="sp_review">{$detail_sample[0]['sp_review']}</div>
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
												<td class="col-number"><label class="vertical-align">{$i++}</label></td>
												<td class="col-analisa"><label class="vertical-align">{$value.jenis}</label></td>
												<td class="col-parameter"><label class="vertical-align">{$value.parameter}</label></td>
												<td class="col-hasil"><label class="vertical-align">{if empty($value.verbatim)}{if $value.value != ''}{floatval($value.value)}{/if}{else}N/A{/if}</label></td>
												<td class="col-keterangan"><label class="vertical-align">{if empty($value.verbatim)}N/A{else}{$value.verbatim_label}{/if}</label></td>
												<!--td class="col-analisis"><label class="vertical-align">N/A</label></td-->
											</tr>
											{/if}
											{/foreach}
										</tbody>
									</table>
								</div>
								<div class="row">
									<button type="submit" class="btn btn-primary" id="approve-sp">
										<span class="glyphicon glyphicon-save"></span>
										Approve
									</button>
									<button type="button" class="btn btn-danger hide" id="re-approve-sp" onclick="re_approve_sp()">
										<span class="glyphicon glyphicon-fire"></span>
										re-Approve
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
								<form role="form" class="form-horizontal" action="analysis_sample/approve_nonsp" method="post" enctype="multipart/form-data" id="form-nonsp-review">
								<input type="hidden" class="form-control" id="composit" placeholder="Composit" name="composit" value="{$composit}">
								<div class="hide" id="nonsp_review">{$detail_sample[0]['nonsp_review']}</div>
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
												<td class="col-number"><label class="vertical-align">{$i++}</label></td>
												<td class="col-analisa"><label class="vertical-align">{$value.jenis}</label></td>
												<td class="col-parameter"><label class="vertical-align">{$value.parameter}</label></td>
												<td class="col-hasil"><label class="vertical-align">{if empty($value.verbatim)}{if $value.value != ''}{floatval($value.value)}{/if}{else}N/A{/if}</label></td>
												<td class="col-keterangan"><label class="vertical-align">{if empty($value.verbatim)}N/A{else}{$value.verbatim_label}{/if}</label></td>
												<!--td class="col-analisis"><label class="vertical-align">N/A</label></td-->
											</tr>
											{/if}
											{/foreach}
										</tbody>
									</table>
								</div>
								<div class="row">
									<button type="submit" class="btn btn-primary" id="approve-nonsp" {if $composit == 1}disabled{/if}>
										<span class="glyphicon glyphicon-save"></span>
										Approve
									</button>
								</div>
								</form>
							</div>
						</div>
					</center>
				</div>
			</div>
			</form>
		</div>
		<div class="panel-footer">
			<h3></h3>
		</div>
	</div>
</div>
{/block}
{block name=jsAdditional}
{include file='analysisSampleJs.tpl'}
<script type="text/javascript">
	cek_isi_hasil('sp');
	cek_isi_hasil('nonsp');
	function cek_isi_hasil(type){
		var count = 0;
		$.each($('#list-'+type+' tbody').find('tr'), function() {
			var kls = $(this).attr('class');
			var hasil = $('#list-'+type+' .'+kls+' .col-hasil label').html();
			(hasil) ? count	= count : count = count + 1;
		});
		
		var html_review = $('#'+type+'_review').html();
		
		if(count >= 1){
			//$('#approve-'+type).addClass('disabled');
			$('#approve-'+type).attr('disabled',true);
		}
		else{
			//(html_review) ? $('#approve-'+type).addClass('disabled') : $('#approve-'+type).removeClass('disabled');
			(html_review) ? $('#approve-'+type).attr('disabled',true) : $('#approve-'+type).attr('disabled',false);
		}

	}
</script>
{/block}