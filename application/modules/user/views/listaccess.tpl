{extends file='base.tpl'}
{block name=body}
	
{/block}
{block name=cssAdditional}
					<div class="row col-md-10">
						<table class="table table-bordered" id="listaccess">
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
								{foreach $access as $ac}
								<tr>
									<td>{$i++}</td>	
									{foreach $ac as $k => $item}
										<td>{$item}</td>
									{/foreach}
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
					
{/block}
{block name=jsAdditional}

{/block}