{extends file="gentelella/layout.tpl"}
{block name=right_col}
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>{$TITLE} {$SHOW|ucfirst}</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content col-md-3 col-sm-6 col-xs-12">
				<form method="post" data-parsley-validate class="form-vertical form-label-left">
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
						<label class="control-label" for="password">Password <span class="required">*</span></label>
						<div class="">
							<input type="password" name="password" class="form-control" placeholder="Password" value="{if isset($smarty.post.password)}{$smarty.post.password}{/if}">
						</div>
					</div>
					<div class="clearfix"></div>


					<div class="ln_solid clearfix"></div>
					<div class="form-group col-md-3 col-sm-6 col-xs-12">
						<input type="hidden" name="csrf" value="{$CSRF}">
						<button type="submit" class="btn btn-success">Save &amp; Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
 {/block}
