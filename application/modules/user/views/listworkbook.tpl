{extends file='base.tpl'}
{block name=body}
	
{/block}
{block name=cssAdditional}
					<div class="row col-md-10">
						<table class="table table-bordered" id="listuser">
							<thead>
								<tr>
									<th>No</th>
									<th>Id</th>
									<th>Token</th>
									<th>Label</th>
									<th>Published</th>
								</tr>
							</thead>
							<tbody>
								{$i=1}
								{foreach $workbooks as $workbook}
								<tr>
									<td>{$i++}</td>	
									{foreach $workbook as $k => $item}
										{if $k == 'published'}
											{if $item == 1}
												<td>Aktif</td>	
											{else}
												<td>Non Aktif</td>
											{/if}
										{else}
											<td>{$item}</td>
										{/if}
										
									{/foreach}
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
					
{/block}
{block name=jsAdditional}

{/block}