<!DOCTYPE html>
<html class="" lang="sv-SE">
  <head>
    <title><?=$this->e($title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style>

	.footer {
	    color: #000000;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 8px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
	    text-align: right;
	}
	
	.infotext {
		font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
	    font-size: 12px;
		font-style: normal;
		font-variant: normal;
	    padding: 5px;
	}
	
	.space {
		height:15px;
	}
	
	.titel {
	    padding: 5px;
	  	color: #FFFFFF;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 28px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 40px;
		vertical-align: middle;
        display: table-cell;
	}
	
	.logo {
	    vertical-align: middle;
	    display: table-cell;
	    margin-right: 1em;
	    width:44px; 
	    height: 50px;
    }
    
	.grad {
	    height: 60px;
	    background: #44276B; /* For browsers that do not support gradients */
	    background: -webkit-linear-gradient(left, #44276B, #78649E); /* For Safari 5.1 to 6.0 */
	    background: -o-linear-gradient(right, #44276B, #78649E); /* For Opera 11.1 to 12.0 */
	    background: -moz-linear-gradient(right, #44276B, #78649E); /* For Firefox 3.6 to 15 */
	    background: linear-gradient(to right, #44276B, #78649E); /* Standard syntax (must be last) */
	    display: table;
    }
    </style>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?=$this->section('content')?>
</head>
<body>
    <div class="grad">
	    <span class="titel"><img alt="Cupolen" src="Logo.png" class="logo"></span>
		<span class="titel">Bes&ouml;ksr&auml;knare</span>
    </div>
    <div class="space"></div>
    <div id="datadiv" style="border: 0px; height: 100px; width: 100px;"></div>
    <div class="space"></div>
    <div class="infotext">In- och utpassager genom entre-d&ouml;rrarna och d&ouml;rren från innerg&aring;rden.
    	<br>Notera att veckorna inleds med S&ouml;ndagen.</div>
    <div class="space"></div>    	
<hr>
<div class="footer">Copyright Marcus Lind&eacute;n, Teknikgruppen 2016</div>
</body>
</html>
