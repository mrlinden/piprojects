<?php $this->layout('template', ['title' => 'Cupolen Besöksräknare',
		'infotext1' => 'In- och utpassager genom entre-dörrarna och dörren från innergården.',
		'infotext2' => 'Notera att det går att zooma i diagrammet. Högerklicka för att zooma ut.'])?>

<?php $this->start('script') ?>
  <script type="text/javascript">
  google.charts.load('current', {packages: ['corechart', 'bar']});
  google.charts.setOnLoadCallback(drawChart);


  var dataRows = [
    <?php foreach ($list as $row): ?>
      /* [ new Date("<?= $row['intervalStop'] ?>"), <?= $row['doorDtot'] ?>, <?= $row['doorCtot'] ?>, <?= $row['doorBtot'] ?>, <?= $row['doorAtot'] ?> ], */
      [ [<?= $row['h'] ?>,<?= $row['m'] ?>,<?= $row['s'] ?>,0], <?= $row['doorDtot'] ?>, <?= $row['doorCtot'] ?>, <?= $row['doorBtot'] ?>, <?= $row['doorAtot'] ?> ],
    <?php endforeach ?>
      [ null, 0 ] ];
  dataRows.pop(); // Remove the last row (dummy value)
  
  function drawChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Tidpunkt');
      data.addColumn('number', 'Innergård');
      data.addColumn('number', 'Entre C');
      data.addColumn('number', 'Entre B');
      data.addColumn('number', 'Entre A');
      data.addRows(dataRows);
      /*
      data.addRows([
      	[new Date(2016,9,1,7,30,0,0), 2,5,2,9],
      	[new Date(2016,9,1,8,30,0,0), 3,8,6,12],
      	[new Date(2016,9,1,8,31,0,0), 6,11,8,13],
      	[new Date(2016,9,1,8,33,0,0), 19,15,13,16],
      	[new Date(2016,9,2,0,0,0,0), 19,20,16,18]
      	
      ]);
      */
      var options = {
        explorer: { actions: ['dragToZoom', 'rightClickToReset'] },
        legend: { position: 'right' },
        isStacked: true,
        hAxis: { format: 'HH:mm', ticks: [[0,0,0,0], 
        							      [1,0,0,0], 
        							      [2,0,0,0], 
        							      [3,0,0,0], 
        							      [4,0,0,0], 
        							      [5,0,0,0], 
        							      [6,0,0,0], 
        							      [7,0,0,0], 
        							      [8,0,0,0], 
        							      [9,0,0,0], 
        							      [10,0,0,0], 
        							      [11,0,0,0], 
        							      [12,0,0,0], 
        							      [13,0,0,0], 
        							      [14,0,0,0], 
        							      [15,0,0,0], 
        							      [16,0,0,0], 
        							      [17,0,0,0], 
        							      [18,0,0,0], 
        							      [19,0,0,0], 
        							      [20,0,0,0], 
        							      [21,0,0,0], 
        							      [22,0,0,0], 
        							      [23,0,0,0]] 
        }
	      /*
        hAxis: { format: 'HH:mm', ticks: [[0,0,0,0), 
        							      [1,0,0,0), 
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
        							      new Date(2016,9,02,0,0,0,0)] } */

      };
	  var theDiv = document.getElementById('datadiv');
	  theDiv.style.width  = "1000px";
	  theDiv.style.height = "350px";

      var chart = new google.visualization.AreaChart(theDiv);

      chart.draw(data, options);
    }

</script>
<?php $this->stop() ?>


<?php $this->start('body') ?>
	<div id="datadiv" style="border: 0px; height: 100px; width: 100px;"></div>
    <div class="space"></div>
    <div class="infotext"><?=$this->e($infotext1)?></div>
    <div class="infotext"><?=$this->e($infotext2)?></div>
    <div class="space"></div>
    <div class="table">
  <table id="datatable">
  <tr>
    <th>Tidpunkt</th>
    <th>Entre A</th> 
    <th>Entre B</th>
    <th>Entre C</th> 
    <th>Innerg&aring;rd</th>
    <th>Totalt f&ouml;r tidpunkten</th>
    <th>Totalt hittils under dagen</th>
  </tr>
  <?php foreach ($list as $row): ?>
  <tr>
    <td><?= $row['intervalStop'] ?></td>
    <td><?= $row['doorA'] ?></td>
    <td><?= $row['doorB'] ?></td>
    <td><?= $row['doorC'] ?></td>
    <td><?= $row['doorD'] ?></td>
    <td><?= $row['visits'] ?></td>
    <td><?= $row['visitsSum'] ?></td>
  </tr>
  <?php endforeach ?>
  </table>

<?php $this->stop() ?>



