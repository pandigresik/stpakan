{extends file='main.tpl'}
{block name=body}
{$nav}
<div class = "col-md-10 col-md-offset-1 hidden-print">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Detail Analisis Sampel
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
				<div class="row">
					<div class="col-md-6">
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="inputStatusKelengkapan" class="col-sm-4 control-label">Status Kelengkapan</label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="inputStatusKelengkapan" placeholder="Status Kelengkapan" name="inputStatusKelengkapan" value="{$lengkap}" readonly=true>
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
						<div class="row form-horizontal" style="margin-top:1%;">
							<div class="col-md-5">
								<div class="form-group">
									<label for="inputStatus" class="col-sm-3 control-label">Status</label>
									<div class="col-md-7">
										<input type="text" class="form-control" id="inputStatus" placeholder="Status" name="inputStatus" value="{$status_sp}" readonly=true>
									</div>
								</div>
							</div>
							<div class="col-md-7 right">
								<div class="form-group">
									<label for="statusApprove" class="col-sm-8 control-label">
									{if !empty({$detail_sample[0]['director_decision']})}
										Telah {$detail_sample[0]['director_decision_cast']} Oleh {strtoupper($detail_sample[0]['approver'])} Pada {date('d M Y H:i:s',strtotime($detail_sample[0]['stamp']))}
									{/if}
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<form role="form" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
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
								</form>
							</div>
						</div>
					</center>
				</div>
				<div class="tab-pane fade" id="non_sp">
					<center>
						<div class="row form-horizontal" style="margin-top:1%;">
							<div class="col-md-5">
								<div class="form-group">
									<label for="inputStatus" class="col-sm-3 control-label">Status</label>
									<div class="col-md-7">
										<input type="text" class="form-control" id="inputStatus" placeholder="Status" name="inputStatus" value="{$status_nonsp}" readonly=true>
									</div>
								</div>
							</div>
							<div class="col-md-7 right">
								<div class="form-group">
									<label for="statusApprove" class="col-sm-8 control-label">
									{if !empty({$detail_sample[0]['director_decision']})}
										Telah {$detail_sample[0]['director_decision_cast']} Oleh {strtoupper($detail_sample[0]['approver'])} Pada {date('d M Y H:i:s',strtotime($detail_sample[0]['stamp']))}
									{/if}
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<form role="form" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
								<div class="row">
									<table class="table table-bordered" id="list-nonsp">
										<thead>
											<tr>
												<th class="col-number">#</th>
												<th class="col-analisa">Jenis Analisis</th>
												<th class="col-parameter">Parameter Analisis</th>
												<th class="col-hasil">Hasil</th>
												<th class="col-keterangan">Keterangan</th>
												<!--th class="col-analisis">analisis</th-->
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