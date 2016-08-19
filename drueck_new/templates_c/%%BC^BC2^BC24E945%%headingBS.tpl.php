<?php /* Smarty version 2.6.14, created on 2016-06-16 09:22:30
         compiled from ../../templates/headingBS.tpl */ ?>
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

    <?php if ($this->_tpl_vars['prihlasen']): ?>
      <div class="collapse navbar-collapse" id="myNavbar">
	<ul class='nav navbar-nav navbar-right'>
        <li><p style="margin-left: 16px;" class='navbar-text'><?php echo $this->_tpl_vars['user']; ?>
</p></li>
	    <li><a href="./indexBS.php?akce=logout"><span class="glyphicon glyphicon-log-out"></span> logoff</a></li>
	</ul>
      </div>
    <?php else: ?>
	<div class="collapse navbar-collapse" id="myNavbar">
	<ul class='nav navbar-right'>
	    <li><p class='navbar-text'>Benutzer nicht angemeldet/neprihlaseny uzivatel</p></li>
	</ul>
      </div>
    <?php endif; ?>
  </nav>
