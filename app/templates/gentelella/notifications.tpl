{include file='gentelella/header.tpl'}
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>{$TITLE} {$SHOW|ucfirst}</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content col-md-3 col-sm-6 col-xs-12">
				{if isset($ALLNOTIFICATIONS) && !empty($ALLNOTIFICATIONS)}
				<ul class="messages">
				{foreach from=$ALLNOTIFICATIONS item=notification}
					<li>
						<img src="https://secure.gravatar.com/avatar/{$notification['email']|md5}" alt="{$notification['name']}" class="avatar" alt="Avatar">
						<div class="message_date">
							<h3 class="date text-info">{$notification['time']|date_format:"%d"}</h3>
							<p class="month">{$notification['time']|date_format:"%b"}</p>
						</div>
						<div class="message_wrapper">
							<h4 class="heading">{$notification['name']}</h4>
							<blockquote class="message">{$notification['message']}</blockquote>
							<br />
							<p class="url">
								<span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
								<a href="#"> {$notification['time']} </a>
							</p>
						</div>
					</li>
				{/foreach}
				</ul>
				{else}
				<h3 align="center">
					You have no notifications waiting.
				</h3>
				{/if}
			</div>
		</div>
	</div>
</div>
{include file='gentelella/footer.tpl'}