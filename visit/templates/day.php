<?php $this->layout('template', ['title' => 'Besöksräknare - '.$this->e($date)])?>

<?php $this->start('script') ?>
  <script type="text/javascript">
  google.charts.load('current', {packages: ['corechart', 'bar']});
  google.charts.setOnLoadCallback(drawChart);


  var dataRows = [
    <?php foreach ($list as $row): ?>
      [ [<?= $row['h'] ?>,<?= $row['m'] ?>,0,0], <?= $row['doorDtot'] ?>, <?= $row['doorCtot'] ?>, <?= $row['doorBtot'] ?>, <?= $row['doorAtot'] ?> ],
    <?php endforeach ?>
      [ null, 0 ] ];
  dataRows.pop();
  
  function drawChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('timeofday', 'Tidpunkt');
      data.addColumn('number', 'Innergård');
      data.addColumn('number', 'Entre C');
      data.addColumn('number', 'Entre B');
      data.addColumn('number', 'Entre A');
      data.addRows(dataRows);

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
        							      [23,0,0,0],
        							      [23,59,59,0]] 
        }
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
    <div class="infotext">F&ouml;r att zooma i diagrammet, v&auml;nsterklicka och dra en rektangel runt det omr&aring;de du vill zooma in. H&ouml;gerklicka f&ouml;r att zooma ut igen.</div>
    <div class="space"></div>
    <div class="space"></div>
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
    <td><?php if ($row['h'] < 10) { echo "0"; } ?><?= $row['h'] ?>:<?php if ($row['m'] < 10) { echo "0"; } ?><?= $row['m'] ?></td>
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



