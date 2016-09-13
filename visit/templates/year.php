<?php $this->layout('template', ['title' => 'Cupolen Besöksräknare'])?>

  google.charts.load("current", {packages:["calendar"]});
  google.charts.setOnLoadCallback(drawChart);

  var dataRows = [
    <?php foreach ($list as $row): ?>
      [ new Date(<?= $row['y'] ?>, <?= $row['m'] ?>, <?= $row['d'] ?>), <?= $row['visits'] ?> ],
    <?php endforeach ?>
      [ null, 0 ] ];
  dataRows.pop(); // Remove the last row (dummy value)
	alert("dataRows.length " + dataRows.length);
  if (dataRows.length > 0) {
    var nrYearsToShow = <?= $nrYears ?>;
    var pixelsPerDay = 15;
    var pixelsPerWeek = pixelsPerDay * 9;  // 9 due to 7 days + 2 spacing
    var datadivWidth = 1000;
    var datadivHeight = (30 + (pixelsPerWeek * nrYearsToShow));

    function drawChart() {
      var dataTable = new google.visualization.DataTable();
      dataTable.addColumn({ type: 'date', id: 'Datum' });
      dataTable.addColumn({ type: 'number', id: 'In/ut-passager' });
      dataTable.addRows([
        [ new Date(2012, 3, 13), 37032 ],
        [ new Date(2012, 3, 14), 38024 ],
        [ new Date(2012, 3, 15), 38024 ],
        [ new Date(2012, 3, 16), 38108 ],
        [ new Date(2012, 3, 17), 38229 ],
        // Many rows omitted for brevity.
        [ new Date(2013, 9, 4), 38177 ],
        [ new Date(2013, 9, 5), 38705 ],
        [ new Date(2013, 9, 12), 38210 ],
        [ new Date(2013, 9, 13), 38029 ],
        [ new Date(2013, 9, 19), 38823 ],
        [ new Date(2013, 9, 23), 38345 ],
        [ new Date(2013, 9, 24), 38 ],
              [ new Date(2026, 9, 24), 38436 ],
        [ new Date(2013, 9, 30), 38447 ]
      ]);
      dataTable.addRows(dataRows);

	  var theDiv = document.getElementById('datadiv');
	  theDiv.style.width  = 1000;
	  theDiv.style.height = (30+(15*9*<?= $nrYears ?>));

      var chart = new google.visualization.Calendar(theDiv);

      var options = {
        height: (30+(15*9*<?= $nrYears ?>)), // (30 + (pixelsPerWeek * nrYearsToShow));
        colorAxis:  {minValue: 0,  colors: ['#CCDDFF', '#0055AA']},
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
          var newUrl = "Day.html?d=" +
              selectedDate.getFullYear() + "-" +
              (selectedDate.getMonth() + 1 ) + "-" +
              selectedDate.getDate();
	          console.log(newUrl);
          //console.log(selectedDate.toISOString());
          window.location = newUrl;
        }
      }

      google.visualization.events.addListener(chart, 'select', selectHandler);

      chart.draw(dataTable, options);
    }
  }

</script>

<?=$this->e($name)?>
