<!DOCTYPE html>
<html class="" lang="sv-SE">
  <head>
    <title><?=$this->e($title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style>
	h1 {
	    color: #000000;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 24px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 26.4px;
	    margin-left: 20px;
	}
	h2 {
	    color: #000000;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 18px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 26.4px;
	    margin-left: 40px;
	}
	</style>
	
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
	<?=$this->section('content')?>
</head>
<body>
  	<h1>Cupolen bes&ouml;ksr&auml;knare (in och utpassager)</h1>
    <div id="datadiv" style="width: datadivWidthpx; height: datadivHeight; "></div>
<hr>
copyright Marcus Lindén, 2016
</body>
</html>
