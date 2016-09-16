<?php $this->layout('template', ['title' => 'Cupolen Besöksräknare',
								 'infotext' => 'In- och utpassager genom entre-dörrarna och dörren från innergården.<br>Notera att veckorna inleds med söndagen.'])?>

  <script type="text/javascript">
  google.charts.load('current', {packages: ['corechart', 'bar']});
  google.charts.setOnLoadCallback(drawChart);


  var dataRows = [
    <?php foreach ($list as $row): ?>
      [ new Date("<?= $row['intervalStart'] ?>"), <?= $row['doorA'] ?>, <?= $row['doorB'] ?>, <?= $row['doorC'] ?>, <?= $row['doorD'] ?> ],
    <?php endforeach ?>
      [ null, 0 ] ];
  dataRows.pop(); // Remove the last row (dummy value)
  

  function drawChart() {
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
	  var theDiv = document.getElementById('datadiv');
	  theDiv.style.width  = "1000px";
	  theDiv.style.height = "350px";

      var chart = new google.visualization.AreaChart(theDiv);

      chart.draw(data, options);
    }

</script>
