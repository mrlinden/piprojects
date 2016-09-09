
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawStacked);

function drawStacked() {
      var data = new google.visualization.DataTable();
      data.addColumn('date', 'Tid pa dagen');
      data.addColumn('number', 'Passager');

      data.addRows([
      	[new Date(2016,9,1,7,30,0,0), 2],
      	[new Date(2016,9,1,8,30,0,0), 3],
      	[new Date(2016,9,1,8,31,0,0), 6],
      	[new Date(2016,9,1,8,33,0,0), 19],
      	[new Date(2016,9,2,0,0,0,0), 19]
      	
      ]);

      var options = {
        explorer: { actions: ['dragToZoom', 'rightClickToReset'] },
        legend: { position: 'none' },
        hAxis: { format: 'HH:mm', ticks: [new Date(2016,9,01,0,0,0,0), 
        							      new Date(2016,9,01,1,0,0,0), 
        							      new Date(2016,9,01,2,0,0,0), 
        							      new Date(2016,9,01,3,0,0,0), 
        							      new Date(2016,9,01,4,0,0,0), 
        							      new Date(2016,9,01,5,0,0,0), 
        							      new Date(2016,9,01,6,0,0,0), 
        							      new Date(2016,9,01,7,0,0,0), 
        							      new Date(2016,9,01,8,0,0,0), 
        							      new Date(2016,9,01,9,0,0,0), 
        							      new Date(2016,9,01,10,0,0,0), 
        							      new Date(2016,9,01,11,0,0,0), 
        							      new Date(2016,9,01,12,0,0,0), 
        							      new Date(2016,9,01,13,0,0,0), 
        							      new Date(2016,9,01,14,0,0,0), 
        							      new Date(2016,9,01,15,0,0,0), 
        							      new Date(2016,9,01,16,0,0,0), 
        							      new Date(2016,9,01,17,0,0,0), 
        							      new Date(2016,9,01,18,0,0,0), 
        							      new Date(2016,9,01,19,0,0,0), 
        							      new Date(2016,9,01,20,0,0,0), 
        							      new Date(2016,9,01,21,0,0,0), 
        							      new Date(2016,9,01,22,0,0,0), 
        							      new Date(2016,9,01,23,0,0,0), 
        							      new Date(2016,9,02,0,0,0,0)] }
      };

      var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    
    
    </script>
  </head>
  <body>
  	<h1>Cupolen bes&ouml;ksr&auml;knare (in och utpassager)</h1>
  	<h2>2016-09-01</h2>
    <div id="chart_div" style="width: 1000px; height: 350px;"></div>
  </body>
</html>