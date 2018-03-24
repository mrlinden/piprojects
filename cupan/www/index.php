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

<button onclick="setScene('scen_1')">Scen_1</button>
<button onclick="setScene('scen_2')">Scen_2</button>
<button onclick="setScene('scen_3')">Scen_3</button>
<button onclick="setScene('scen_4')">Scen_4</button>

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
	if (name == "dmx") {
		dmxValues = [];
		for (ch = 1; ch <= 512; ch++) {
			var valueField = document.getElementById("val" + ch);
				dmxValues.push(valueField.innerHTML);
		}
		params = params + dmxValues;
	}
    
    http.send(params);
    http.onload = function() {
        //alert(http.responseText);
    }
}

function periodicSendDmxValues() {
	if (doSendDmxValues) {
		doSendDmxValues = false;
		setScene("dmx");
	}
}

setInterval(periodicSendDmxValues, 2000);

</script>

</body>
</html>
