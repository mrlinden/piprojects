<?php $this->layout('template', ['title' => 'Besöksräknare'])?>
				
<?php $this->start('script') ?>
  <script type="text/javascript">
  google.charts.load("current", {packages:["calendar"]});
  google.charts.setOnLoadCallback(drawChart);

  var dataRows = [
    <?php foreach ($list as $row): ?>
      [ new Date(<?= $row['y'] ?>, <?= $row['m'] ?>-1, <?= $row['d'] ?>), <?= $row['count'] ?> ],
    <?php endforeach ?>
      [ null, 0 ] ];
  dataRows.pop();

  var nrYearsToShow = 0;
  if (dataRows.length > 0) {
    nrYearsToShow = <?= $nrYears ?>;
  }
  var pixelsPerDay = 15;
  var pixelsPerWeek = pixelsPerDay * 9;  // 9 due to 7 days + 2 spacing
  var datadivWidth = 1000;
  var datadivHeight = (30 + (pixelsPerWeek * nrYearsToShow));

  function twoDigits(d) {
      if(0 <= d && d < 10) return "0" + d.toString();
      if(-10 < d && d < 0) return "-0" + (-1*d).toString();
      return d.toString();
  }

  Date.prototype.toMysqlFormat = function() {
      return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate());
  };


    function drawChart() {
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn({ type: 'date', id: 'Datum' });
      dataTable.addColumn({ type: 'number', id: 'In/ut-passager' });
      dataTable.addRows(dataRows);

	  var theDiv = document.getElementById('datadiv');
	  theDiv.style.width  = "1000px";
	  theDiv.style.height = datadivHeight + "px";

      var chart = new google.visualization.Calendar(theDiv);

      var options = {
        height: datadivHeight,
        colorAxis:  {minValue: 0,  colors: ['#e4daf1', '#44276B']},
        calendar: {
          cellSize: 15, // pixelsPerDay
          underMonthSpace: 5,
          daysOfWeek:'SMTOTFL',
          cellColor: {
            stroke: '#aaaaaa',
            strokeOpacity: 1,
            strokeWidth: 1
          }
        }
      };

      function selectHandler() {
        var selectedItem = chart.getSelection()[0];
        if (selectedItem) {
          var selectedDate = new Date(selectedItem.date);
          window.location.search = '?d=' + selectedDate.toMysqlFormat();
        }
      }

      google.visualization.events.addListener(chart, 'select', selectHandler);

      chart.draw(dataTable, options);
    }
</script>
<?php $this->stop() ?>

<?php $this->start('body') ?>
	<div id="datadiv" style="border: 0px; height: 100px; width: 100px;"></div>
    <div class="space"></div>
    <div class="infotext">In- och utpassager genom entre-d&ouml;rrarna och d&ouml;rren fr&aring;n innerg&aring;rden.</div>
    <div class="infotext">Notera att &ouml;versta raden &auml;r s&ouml;ndagar.</div>
<?php $this->stop() ?>


