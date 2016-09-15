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
	h3 {
	    color: #000000;
	    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
		font-size: 8px;
		font-style: normal;
		font-variant: normal;
		font-weight: 500;
		line-height: 26.4px;
	    margin-left: 40px;
	    text-align: right;
	}
	#grad1 {
    height: 80px;
    background: red; /* For browsers that do not support gradients */
    background: -webkit-linear-gradient(left, #44276B, #78649E); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(right, #44276B, #78649E); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(right, #44276B, #78649E); /* For Firefox 3.6 to 15 */
    background: linear-gradient(to right, #44276B, #78649E); /* Standard syntax (must be last) */
    margin: auto;
    display: block;
    padding: 5px;
  	color: #FFFFFF;
    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
	font-size: 28px;
	font-style: normal;
	font-variant: normal;
	font-weight: 500;
	line-height: 26.4px;
    }
    </style>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?=$this->section('content')?>
</head>
<body>
    <div id="grad1"><img alt="Cupolen" src="Logo.png" height="50" width="44"/>&nbsp;&nbsp;&nbsp;Bes&ouml;ksr&auml;knare (in- och utpassager)</div>
    <div id="datadiv" style="border: 0px; height: 100px; width: 100px;"></div>
<hr>
<h3>Copyright Marcus Lind&eacute;n, Teknikgruppen 2016</h3>
</body>
</html>
