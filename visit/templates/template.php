<!DOCTYPE html>
<html class="" lang="sv-SE">
  <head>
    <title><?=$this->e($title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?=$this->section('script')?>
</head>
<body>
    <div class="grad">
	    <span class="titel"><img alt="Cupolen" src="Logo.png" class="logo"></span>
		<span class="titel"><a href="." class="titel"><?=$this->e($title)?></a></span>
    </div>
    <div class="space"></div>
    <?=$this->section('body')?>
    <div class="space"></div>    	
<hr>
<div class="footer">Copyright Marcus Lind&eacute;n, Teknikgruppen 2016</div>
</body>
</html>
