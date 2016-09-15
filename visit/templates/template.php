<!DOCTYPE html>
<html class="" lang="sv-SE">
  <head>
    <title><?=$this->e($title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style>
	h1 {

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
	
	#footer {
	    color: #000000;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 8px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
	    text-align: right;
	}

	#space {
		height:10px;
		font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
	    font-size: 10px;
		font-style: normal;
		font-variant: normal;
	    padding: 5px;
	}
	
	#logo {
		margin: auto;
		padding: 5px;
	}
	
	#titel {
	    padding: 5px;
	  	color: #FFFFFF;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 28px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 40px;
		border-color: red;
		border-width: 2px;
	}

	#titel2 {
	    padding: 5px;
	  	color: #FFFFFF;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 10px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 10px;
		border-color: red;
		border-width: 2px;
	}
	
	#grad1 {
	    height: 60px;
	    background: red; /* For browsers that do not support gradients */
	    background: -webkit-linear-gradient(left, #44276B, #78649E); /* For Safari 5.1 to 6.0 */
	    background: -o-linear-gradient(right, #44276B, #78649E); /* For Opera 11.1 to 12.0 */
	    background: -moz-linear-gradient(right, #44276B, #78649E); /* For Firefox 3.6 to 15 */
	    background: linear-gradient(to right, #44276B, #78649E); /* Standard syntax (must be last) */
	    margin: auto;
		vertical-align: middle;
    }
    </style>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?=$this->section('content')?>
</head>
<body>
    <div id="grad1">
    <div id="logo"><img alt="Cupolen" src="Logo.png" style="width:44px; height: 50px;"></div>
    <div id="titel">&nbsp;Bes&ouml;ksr&auml;knare</div>
    <div id="titel2">In- och ut-passager genom entre-d&ouml;rrarna och d&ouml;rren från innerg&aring;rden.<BR>Notera att ökalendern </div>
    </div>
    <div id="space">In- och ut-passager genom entre-d&ouml;rrarna och d&ouml;rren från innerg&aring;rden.</div>
    <div id="datadiv" style="border: 0px; height: 100px; width: 100px;"></div>
    <div id="space"></div>
    
<hr>
<div id="footer">Copyright Marcus Lind&eacute;n, Teknikgruppen 2016</div>
</body>
</html>
