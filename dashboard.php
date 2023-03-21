<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head -->
   <?php require 'includes/head.php'; ?>
</head>
<body>
    <!-- Navbar -->
    <?php require 'includes/nav.php'; ?>

    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <div class="card m-4 p-4">
                    <?php
                        // Grab Values
                        $location = isset($_POST['location']) ? $_POST['location'] : null;

                        // Get user Coords
                        $coords = userCoords($location);
                        if ($coords != null) {
                            $lat = $coords[0];
                            $lon = $coords[1];
                        };
                    ?>
                    <?php
                        // Check for Valid Location
                        if ($coords != null) {
                            echo '<h1>Location Selected: ' . $location . '</h1>';
                        } else {
                            echo '<h1>Location Selected is Invalid</h1>';
                        }
                    ?>

                    <!-- Lets the user choose location -->
                    <form name="" action="" method="post">
                        <div class="input-group">
                            <input type="text" name="location" class="form-control" placeholder="Enter Location!">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card m-4 p-4">
                    <h1 style="text-align:center;">Date</h1>
                    <?php echo '<h1 style="text-align:center;">'. date('d/m/Y') .'</h1>' ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card m-4 p-4">
                    <h1 style="text-align: center;">Pollution Pie Chart</h1>
                    <?php
                        if (!isset($lat, $lon)) {
                            exit();
                        };

                        // Create API Requests
                        $apiKey = '9ec72fafc67f2ebbe14095e1c5426123';

                        // API request for polutants
                        $url = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
                        // Make Request and Decode into a JSON (True means assoc)
                        $polutionData = json_decode(file_get_contents($url),true);
                        
                        // Extact Values From JSON
                        $aqi = $polutionData['list'][0]['main']['aqi'];
                        $co = $polutionData['list'][0]['components']['co'];
                        $no = $polutionData['list'][0]['components']['no'];
                        $no2 = $polutionData['list'][0]['components']['no2'];
                        $o3 = $polutionData['list'][0]['components']['o3'];
                        $so2 = $polutionData['list'][0]['components']['so2'];
                        $pm2_5 = $polutionData['list'][0]['components']['pm2_5'];
                        $pm10 = $polutionData['list'][0]['components']['pm10'];
                    ?>

                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                    <script type="text/javascript">
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {
                            // Create the data table
                            var data = new google.visualization.DataTable();
                            data.addColumn('string', 'Pollutant');
                            data.addColumn('number', 'Value');
                            data.addRows([
                            ['CO', <?php echo $co ?>],
                            ['NO', <?php echo $no ?>],
                            ['NO2', <?php echo $no2 ?>],
                            ['O3', <?php echo $o3 ?>],
                            ['SO2', <?php echo $so2 ?>],
                            ['PM2.5', <?php echo $pm2_5 ?>],
                            ['PM10', <?php echo $pm10 ?>]
                            ]);

                            // Set chart options
                            var options = {
                            'title': 'Percentage of Pollutants in the Air',
                            backgroundColor : '#f8f9fa',
                            };

                            // Instantiate and draw the chart
                            var chart = new google.visualization.PieChart(document.getElementById('polution'));
                            chart.draw(data, options);
                        }
                    </script>

                    <!-- Display the chart -->
                    <div id="polution"></div>
                </div>
            </div>
            <div class="col">
                <div class="card m-4 p-4">
                    <h1 style="text-align: center;">Temperature Bar Chart</h1>

                    <?php 
                        if (!isset($lat, $lon)) {
                            exit();
                        };

                        // Create API Requests
                        $apiKey = '9ec72fafc67f2ebbe14095e1c5426123';
                        // Define unit of measeurments
                        $units = 'metric';
                        // API request for polutants
                        $url = "http://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&units={$units}&cnt=5&appid={$apiKey}";
                        // Make Request and Decode into a JSON (True means assoc)
                        $data = json_decode(file_get_contents($url),true);
                        // var_dump($data);
                        // Extact Values From JSON
                        $temperature = [];
                        $pressure = [];
                        foreach ($data['list'] as $item) {
                            $temperature[] = $item['main']['temp'];
                            $pressure[] = $item['main']['pressure'];
                        }

                        // Create data table for Google Charts
                        $dataTable = [['Time', 'Temperature']];
                        foreach ($data['list'] as $item) {
                            $dataTable[] = [$item['dt_txt'], $item['main']['temp']];
                        }
                    ?>
                    <!-- Iniitalise Chart -->
                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                    <script type="text/javascript">
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = google.visualization.arrayToDataTable(<?php echo json_encode($dataTable); ?>);

                        var options = {
                        title: 'Temperature and Pressure',
                        'height': 250,
                        backgroundColor : '#f8f9fa',
                        curveType: 'function',
                        legend: { position: 'bottom' }
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('temperature'));

                        chart.draw(data, options);
                    }
                    </script>
                    <div id="temperature"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="card m-4 p-4">
                    <h1 style="text-align:center;">Air Quality Index</h1>
                    <h1 style="text-align:center;">1</h1>
                </div>
            </div>
            <div class="col-4">
                <div class="card m-4 p-4">
                    <h1 style="text-align:center;">Feels Like</h1>
                    <h1 style="text-align:center;">11.5°C</h1>
                </div>
            </div>
            <div class="col-4">
                <div class="card m-4 p-4">
                    <h1 style="text-align:center;">Humidity</h1>
                    <h1 style="text-align:center;">98%</h1>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Footer -->
    <?php require 'includes/footer.php'; ?>
    <!-- JS Bootstrap -->
    <?php require 'includes/bootstrapjs.php'; ?>
</body>
</html>



