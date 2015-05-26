<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
	<a class="navbar-brand" href="/index.php">
	    <img style='height: 32px;' alt='Abydos s.r.o.' src='../images/logo.png'>
	</a>
    </div>
    {if $prihlasen}
	<ul class='nav navbar-nav navbar-right'>
{*	    <li><p class='navbar-text'>{$user}</p></li>*}
	    <li><a class='btn btn-default navbar-btn' href="./index.php?akce=logout">{$user} logoff</a></li>
	</ul>
    {else}
	<ul class='nav navbar-nav navbar-right'>
	    <li><p class='navbar-text navbar-right'>Benutzer nicht angemeldet/neprihlaseny uzivatel</p></li>
	</ul>
    {/if}
	
  </div>
</nav>

{*<div id="header">
<h3>APL</h3>
</div>
<div id="podheader">
{if $prihlasen}
	{$user}  ({$roles})<a href="./index.php?akce=logout">&nbsp;abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>*}
