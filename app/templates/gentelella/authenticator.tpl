{include file='gentelella/header.tpl'}
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">

                <div class="x_title">
                  <h2>{$TITLE}</h2>
                  <div class="clearfix"></div>
                </div>
				<div class="x_content col-md-3 col-sm-6 col-xs-12">

				{if $FORM=='add'}
					<div>
						<p>
							Scan the following QR-Code using your authenticator application, and confirm it by entering your account password as well as a valid code from the app to confirm and bind the authenticator to your account.
						</p>
						<img src="{$QRCODE}" style="display: block;">
					</div>
					<form method="post" data-parsley-validate class="form-vertical form-label-left">
						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<label class="control-label" for="password">Password <span class="required">*</span></label>
							<div class="">
								<input type="password" name="password" class="form-control" placeholder="Password" required="">
							</div>
						</div>
						<div class="clearfix"></div>

						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<label class="control-label" for="code">Code <span class="required">*</span></label>
							<div class="">
								<input type="password" name="code" class="form-control" placeholder="Code" required="">
							</div>
						</div>
						<div class="clearfix"></div>

						<div class="ln_solid clearfix"></div>
						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<input type="hidden" name="csrf" value="{$CSRF}">
							<button type="submit" class="btn btn-success">Confirm &amp; Add</button>
						</div>
					</form>
					<div class="clearfix"></div>
				{elseif $FORM=='remove'}
					<p>
						You have an authenticator active on your account. To remove this authenticator, enter your account password and a valid authenticator code. This does not remove it from the app installed on your device such as the Google Authenticator app.
					</p>
					<form method="post" data-parsley-validate class="form-vertical form-label-left">
						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<label class="control-label" for="password">Password <span class="required">*</span></label>
							<div class="">
								<input type="password" name="password" class="form-control" placeholder="Password" required="">
							</div>
						</div>
						<div class="clearfix"></div>

						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<label class="control-label" for="code">Code <span class="required">*</span></label>
							<div class="">
								<input type="password" name="code" class="form-control" placeholder="Code" required="">
							</div>
						</div>
						<div class="clearfix"></div>

						<div class="ln_solid clearfix"></div>
						<div class="form-group col-md-3 col-sm-6 col-xs-12">
							<input type="hidden" name="csrf" value="{$CSRF}">
							<button type="submit" class="btn btn-success">Remove Authenticator</button>
						</div>
					</form>
					<div class="clearfix"></div>
				{elseif $FORM=='scratch'}
					<h1>Authenticator Added</h1>
					<p>
						The following codes can be used in the event you are unable to access your authnenticator. When you use one of these codes, the authenticator requirement will be removed from your account.
					</p>
					<br>
					<table>
						{foreach from=$CODES item=code}
						<tr>
							<td>{$code}</td>
						</tr>
						{/foreach}
					</table>
					<div class="clearfix"></div>
				{elseif $FORM=='removed'}
					<h1>Authenticator Removed</h1>
					<p>
						The requirement to use an authenticator has been removed from your account.
					</p>
					<div class="clearfix"></div>
				{/if}
			</div>
		</div>

	</div>
</div>
{include file='gentelella/footer.tpl'}