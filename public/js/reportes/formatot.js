google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['UNIDAD CENTRAL', 'OBJETIVO CURSOS ABIERTOS', 'REALIZADOS CURSOS ABIERTOS'],
          ['TUXTLA', 1412, 216],
          ['SAN CRISTOBAL', 781, 30],
          ['YAJALON', 468, 45],
          ['TONALA', 420, 30],
          ['TAPACHULA', 423, 42],
          ['REFORMA', 379, 40],
          ['CATAZAJA', 351, 15],
          ['JIQUIPILAS', 372, 57],
          ['COMITAN', 362, 52],
          ['VILLAFLORES', 339, 45],
          ['OCOSINGO', 197, 11],
        ]);

        var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          vAxis: {format: 'decimal'},
          colors: ['#1b9e77', '#d95f02']
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
