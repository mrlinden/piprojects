<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
h1 {
	font-family: Arial, Helvetica, sans-serif;
}

p {
 	font-family: Arial, Helvetica, sans-serif;
}

.dmxVal {
	position: absolute;
    left: 80px;
}

.slidecontainer {
	font-family: Arial, Helvetica, sans-serif;
    width: 100%;
}

.slider {
    -webkit-appearance: none;
    width: 50%;
    height: 15px;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
    position:absolute;
    left: 120px;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}
</style>
</head>
<body>

<h1>Cupan DXM channel test</h1>

<button onclick="setScene('Scen1')">Scen1</button>
<button onclick="setScene('Scen2')">Scen2</button>
<button onclick="setScene('Scen3')">Scen3</button>
<button onclick="setScene('Scen4')">Scen4</button>
<button onclick="setScene('ScenAv')">ScenAv</button>
<button onclick="setScene('Dekor1')">Dekor1</button>
<button onclick="setScene('Dekor2')">Dekor2</button>
<button onclick="setScene('Dekor3')">Dekor3</button>
<button onclick="setScene('Dekor4')">Dekor4</button>
<button onclick="setScene('DekorAv')">DekorAv</button>

<div class="""slidecontainer">
<?php 
for ($ch = 1; $ch <= 512; $ch++) {
    echo "<p>Ch ".$ch.": <span class=\"dmxVal\" id=\"val".$ch."\">0</span> <input type=\"range\" min=\"0\" max=\"255\" value=\"0\" class=\"slider\" oninput=\"updateCh(".$ch.", this.value)\"></p>";
} 
?>
</div>

<script>

var doSendDmxValues = false;

function updateCh(ch, val) {
	var valueField = document.getElementById("val" + ch);
	valueField.innerHTML = val;
	doSendDmxValues = true;
}

function setScene(name) {
    var http = new XMLHttpRequest();
    http.open("POST", "setScene.php/", true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    var params = "scene=" + name
    console.log("params: " + params);
    http.send(params);
    http.onload = function() {
        //alert(http.responseText);
    }
}

function sendDmxValues(start, stop) {
	var http = new XMLHttpRequest();
    http.open("POST", "setScene.php/", true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    var params = "scene=dmx"
	var dmxValues = [start];
	for (ch = start; ch <= stop; ch++) {
		var valueField = document.getElementById("val" + ch);
			dmxValues.push(valueField.innerHTML);
	}
	params = params + dmxValues;
	
    console.log("params: " + params + " from " + start + " to " + stop);
    http.send(params);
    http.onload = function() {
        //alert(http.responseText);
    }
}

function periodicSendDmxValues() {
	if (doSendDmxValues) {
		doSendDmxValues = false;
		sendDmxValues(1, 199);
		sendDmxValues(200, 399);
		sendDmxValues(400, 512);
	}
}

setInterval(periodicSendDmxValues, 2000);

</script>

</body>
</html>
