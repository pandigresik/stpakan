{extends file='base.tpl'}
{block name=body}
	
{/block}
{block name=cssAdditional}
					<div class="row col-md-10">
						<table class="table table-bordered" id="listuser">
							<thead>
								<tr>
									<th>No</th>
									<th>Username</th>
									<th>Published</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								{$i=1}
								{foreach $users as $user}
								<tr>
									<td>{$i++}</td>	
									{foreach $user as $k => $item}
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
									<td>
										<span class="btn btn-primary" onclick="listAccess('{$user.id}')">Hak Akses</span>
										{if $user.published == 1}
											<span class="btn btn-danger" onclick="disableUser('{$user.id}')">Disable</span>	
										{else}	
											<span class="btn btn-success" onclick="enableUser('{$user.id}')">Enable</span>
										{/if}
										<span class="btn btn-warning" onclick="resetPassword('{$user.id}')">Reset Password</span>
										<span class="btn btn-danger" onclick="removeUser('{$user.id}')">Hapus</span>
									</td>
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
					
{/block}
{block name=jsAdditional}
	<script type="text/javascript" src="assets/js/user/user.js"></script>
{/block}