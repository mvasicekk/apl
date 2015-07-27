<nav class="navbar navbar-default">
    <div class="container-fluid navbar-left">
        <div class="navbar-header" style="float:left;">
            <a class="navbar-brand" href="/index.php">
                <img style='height: 32px;' alt='Abydos s.r.o.' src='/images/logo.png'>
            </a>
        </div>

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>

        </button>

    </div>

    {if $prihlasen}
      <div class="collapse navbar-collapse" id="myNavbar">
	<ul class='nav navbar-nav navbar-right'>
{*	    <li><p class='navbar-text'>{$user}</p></li>*}
        <li><p style="margin-left: 16px;" class='navbar-text'>{$user}</p></li>
	    <li><a href="./indexBS.php?akce=logout"><span class="glyphicon glyphicon-log-out"></span> logoff</a></li>
	</ul>
      </div>
    {else}
	<div class="collapse navbar-collapse" id="myNavbar">
	<ul class='nav navbar-right'>
	    <li><p class='navbar-text'>Benutzer nicht angemeldet/neprihlaseny uzivatel</p></li>
	</ul>
      </div>
    {/if}
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
