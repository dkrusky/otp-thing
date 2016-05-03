{include file='gentelella/header.tpl'}
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
			  <h2>{$TITLE} {$SHOW|ucfirst}</h2>
			  <div class="clearfix"></div>
			</div>
			<div class="x_content col-md-3 col-sm-6 col-xs-12">
{if $SHOW=='list'}

				{if !empty($USERS)}
				<table id="datatable-fixed-header" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th>Username</th>
							<th>OTP</th>
							<th>Active</th>
							<th>ACL</th>
							<th>Actions</th>
						</tr>
					</thead>

					<tbody>
						{foreach from=$USERS item=user}
						<tr>
							<td>{$user['name']}</td>
							<td>{$user['email']}</td>
							<td>{$user['username']}</td>
							<td>{if !empty($user['otp_key'])}T{else}F{/if}</td>
							<td>{if $user['enabled'] gt 0}T{else}F {$user['enabled']}{/if}</td>
							<td>{$user['acl']}</td>
							<td><a class="btn btn-primary btn-xs" href="{$ROOT}accounts?show=modify&id={$user['id']|md5}&csrf={$CSRF}">Modify</a><a class="btn btn-danger btn-xs" href="{$ROOT}accounts?show=remove&id={$user['id']|md5}&csrf={$CSRF}">Remove</a></td>
						</tr>
						{/foreach}
					</tbody>
				</table>
				{else}
				<h3 align="center">
					No users in the database <a class="btn btn-info" href="{$ROOT}accounts?show=add&csrf={$CSRF}">Add Account</a>
				</h3>
				{/if}

{elseif $SHOW=='add'}
				<form method="post" data-parsley-validate class="form-vertical form-label-left">
					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="username">Username <span class="required">*</span></label>
						<div class="">
							<input type="text" name="username" class="form-control" placeholder="Username" required="" value="{if isset($smarty.post.username)}{$smarty.post.username}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="password">Password <span class="required">*</span></label>
						<div class="">
							<input type="password" name="password" class="form-control" placeholder="Password" required="" value="{if isset($smarty.post.password)}{$smarty.post.password}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="name">Name</label>
						<div class="">
							<input type="text" name="name" class="form-control" placeholder="Name" value="{if isset($smarty.post.name)}{$smarty.post.name}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="email">Email</label>
						<div class="">
							<input type="email" name="email" class="form-control" placeholder="Email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="acl">Access Level <span class="required">*</span></label>
						<div class="">
							<input type="number" name="acl" class="form-control" placeholder="Access Level" required="" value="{if isset($smarty.post.acl)}{$smarty.post.acl}{else}0{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="ln_solid clearfix"></div>
					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<input type="hidden" name="csrf" value="{$CSRF}">
						<button type="submit" class="btn btn-success">Confirm &amp; Add</button>
					</div>
				</form>
{elseif $SHOW=='added'}
				<div class="alert alert-success fade in" role="alert">
					<strong>Success!</strong> The following user was added successfuly.
				</div>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Field</th>
							<th>Value</th>
						</tr>
					</thead>
					<tr>
						<td>Username</td>
						<td>{$RUSER['username']}</td>
					</tr>
					<tr>
						<td>Password</td>
						<td>*********</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>{$RUSER['name']}</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>{$RUSER['email']}</td>
					</tr>
					<tr>
						<td>Access Level</td>
						<td>{$RUSER['acl']}</td>
					</tr>
				</table>
				<a class="btn btn-info" href="{$ROOT}accounts">Return to Accounts List</a>


{elseif $SHOW=='remove'}
				<h3>
					Are you sure you wish to remove the following user ?
				</h3>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Field</th>
							<th>Value</th>
						</tr>
					</thead>
					<tr>
						<td>Username</td>
						<td>{$RUSER['username']}</td>
					</tr>
					<tr>
						<td>Password</td>
						<td>*********</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>{$RUSER['name']}</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>{$RUSER['email']}</td>
					</tr>
					<tr>
						<td>Access Level</td>
						<td>{$RUSER['acl']}</td>
					</tr>
				</table>
				<div class="ln_solid clearfix"></div>
				<a class="btn btn-info" href="{$ROOT}accounts">No. Do not remove.</a><a class="btn btn-danger" href="{$ROOT}accounts?show=remove&id={$smarty.get.id}&csrf={$CSRF}&confirm=1">Yes, Remove this User</a>

{elseif $SHOW=='modify'}
				<form method="post" data-parsley-validate class="form-vertical form-label-left">
					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="username">Username <span class="required">*</span></label>
						<div class="">
							<input type="text" name="username" class="form-control" placeholder="Username" required="" value="{if isset($smarty.post.username)}{$smarty.post.username}{else}{$RUSER['username']}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="password">Password <span class="required">*</span></label>
						<div class="">
							<input type="password" name="password" class="form-control" placeholder="Password" value="{if isset($smarty.post.password)}{$smarty.post.password}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="name">Name</label>
						<div class="">
							<input type="text" name="name" class="form-control" placeholder="Name" value="{if isset($smarty.post.name)}{$smarty.post.name}{else}{$RUSER['name']}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="email">Email</label>
						<div class="">
							<input type="email" name="email" class="form-control" placeholder="Email" value="{if isset($smarty.post.email)}{$smarty.post.email}{else}{$RUSER['email']}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<label class="control-label" for="acl">Access Level <span class="required">*</span></label>
						<div class="">
							<input type="number" name="acl" class="form-control" placeholder="Access Level" required="" value="{if isset($smarty.post.acl)}{$smarty.post.acl}{else}{$RUSER['acl']}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>

					<div class="ln_solid clearfix"></div>
					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<input type="hidden" name="csrf" value="{$CSRF}">
						<button type="submit" class="btn btn-success">Save &amp; Update</button>
					</div>
				</form>
{elseif $SHOW=='removed'}
				<div class="alert alert-success fade in" role="alert">
					<strong>Success!</strong> Account was removed. <a href="{$ROOT}accounts">Return to account list</a>
				</div>
				<a class="btn btn-info" href="{$ROOT}accounts">Return to Accounts List</a>
{/if}
			</div>
		</div>
	</div>
</div>

{include file='gentelella/footer.tpl'}