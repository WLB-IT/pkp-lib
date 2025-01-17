{**
 * templates/admin/index.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Site administration index.
 *
 *}
{extends file="layouts/backend.tpl"}

{block name="page"}
	<h1 class="app__pageHeading">
		{translate key="navigation.admin"}
	</h1>

	{if $newVersionAvailable}
		<notification>
			{translate key="site.upgradeAvailable.admin" currentVersion=$currentVersion->getVersionString(false) latestVersion=$latestVersion}
		</notification>
	{/if}

	<div class="app__contentPanel">
		<h2>{translate key="admin.siteManagement"}</h2>
		<ul>
			<li><a href="{url op="contexts"}">{translate key="admin.hostedContexts"}</a></li>
			{call_hook name="Templates::Admin::Index::SiteManagement"}
			<li><a href="{url op="settings"}">{translate key="admin.siteSettings"}</a></li>
		</ul>
		<h2>{translate key="admin.adminFunctions"}</h2>
		<ul>
			<li><a href="{url op="systemInfo"}">{translate key="admin.systemInformation"}</a></li>
			<li>
				<form type="post" action="{url op="expireSessions"}">
					{csrf}
					<button class="-linkButton" onclick="return confirm({translate|json_encode|escape key="admin.confirmExpireSessions"})">{translate key="admin.expireSessions"}</button>
				</form>
			</li>
			<li>
				<form type="post" action="{url op="clearDataCache"}">
					{csrf}
					<button class="-linkButton">{translate key="admin.clearDataCache"}</button>
				</form>
			</li>
			<li>
				<form type="post" action="{url op="clearTemplateCache"}">
					{csrf}
					<button class="-linkButton" onclick="return confirm({translate|json_encode|escape key="admin.confirmClearTemplateCache"})">{translate key="admin.clearTemplateCache"}</button>
				</form>
			</li>
			<li>
				<form type="post" action="{url op="clearScheduledTaskLogFiles"}">
					{csrf}
					<button class="-linkButton" onclick="return confirm({translate|json_encode|escape key="admin.scheduledTask.confirmClearLogs"})">{translate key="admin.scheduledTask.clearLogs"}</button>
				</form>
			</li>
			<li>
				<a href="{url op="jobs"}">
					{translate key="navigation.tools.jobs"}
				</a>
			</li>
			{call_hook name="Templates::Admin::Index::AdminFunctions"}
		</ul>
	</div>
{/block}
