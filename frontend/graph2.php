<?php
require 'dbCreds.php';
$id = $_GET['id'];
if (isset($_GET['op'])) { //used for plotting sale prices, do we need this?
    $opt = $_GET['op'];
}
if (isset($_GET['a'])) {
    $a = array_unique($_GET['a']);
} else {
    echo "Error ID not set".PHP_EOL;
}
if (is_numeric($a[0])) {

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //forst we need to make the columns
    if(count($a)>1)
    {
        $legend_or_not="";
        $chart_width="'width': '80%',";
    }
    else{
        $legend_or_not=",legend: {position: 'none'}";
        $chart_width="right:0,'width': '90%',";
    }
    
    foreach ($a as $id){
        $sql = "SELECT * FROM Items WHERE id='$id' LIMIT 1";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
                $name = $row["name"];
        }
        //echo $name;
        $echo_str = $echo_str."data.addColumn('number', '".$name."');".PHP_EOL;                
    }
    $echo_str = $echo_str."data.addColumn('number', 'Max');".PHP_EOL
                        ."data.addColumn({type:'string', role:'annotation'});".PHP_EOL
                        ."data.addColumn('number', 'Min');".PHP_EOL
                        ."data.addColumn({type:'string', role:'annotation'});".PHP_EOL
                        ."data.addColumn('number', 'AVG');".PHP_EOL
                        ."data.addColumn({type:'string', role:'annotation'});".PHP_EOL;// annotation role col.

    
    $oldest= new DateTime("2020-01-01");
    $newest=new DateTime("1980-01-01");
    $maxval=0;
    $minval=INF;
    for ($i = 0; $i < count($a); $i++) {
        $id=$a[$i];
        
        $sql="SELECT *, CASE WHEN  `sale_price` <  `price` 
                    THEN  `sale_price` 
                    ELSE  `price` 
                    END AS TheMin
                    FROM Prices
                    WHERE id ='$id'";

        $avg_query = "SELECT avg(CASE WHEN  `sale_price` <  `price` 
                    THEN  `sale_price` 
                    ELSE  `price` 
                    END) as avg
                    FROM Prices
                    WHERE id ='$id'";
        $result = $conn->query($sql);
        $result4 = $conn->query($avg_query);
        if ($result->num_rows > 0) {

            while ($row = $result4->fetch_assoc()) {
                $avg = $row["avg"];
            }
            
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $currdate=new DateTime($row["scrape_date"]);
                if($oldest>$currdate){
                    $oldest=$currdate;
                }
                if($currdate>$newest){
                    $newest=$currdate;
                }
                if($row["TheMin"]>$maxval){
                    $maxval=$row["TheMin"];
                }
                if($row["TheMin"]<$minval){
                    $minval=$row["TheMin"];
                }
                $echo_str = $echo_str."data.addRow(";
                $echo_str = $echo_str
                        . ("[new Date("
                        . date("Y", strtotime($row["scrape_date"]))
                        . ","
                        . date("m", strtotime($row["scrape_date"]))
                        . "),");
                        //. $row["Sale Price"]
                       // . ", "
                        for ($b=0;$b < $i;$b++ ) {
                            $echo_str = $echo_str. ("null,");
                        }
                        $echo_str = $echo_str
                        . $row["TheMin"].",";
                        //. ", ";
                        for ($b=$i;$b < count($a)-1;$b++ ) {
                            $echo_str = $echo_str. ("null");
                        }
//                        $echo_str = $echo_str.
//                        $maxval
//                        . ",null, "
//                        . $minval. ",null, "
//                        . $avg. ", "
//                        . "null]);" .PHP_EOL;                        
$echo_str = $echo_str . "null,null,null,null,null,null]);\n".PHP_EOL;
            }
            //$echo_str = rtrim($echo_str, ",");
            
        } else {
            echo "0 results";
        }
        //$echo_str = $echo_str.");".PHP_EOL;
    }
    $echo_str=$echo_str."data.addRows(["
                        . "[new Date("
                        . $oldest->format('Y')
                        . ", "
                        . $oldest->format('m')
                        . "), ";
                        for ($b=0;$b < count($a);$b++ ) {
                            $echo_str = $echo_str. (" null,");
                        }
                        $echo_str = $echo_str.
                        $maxval
                        . ",null, "
                        . $minval. ",null, "
                        . $avg. ", "
                        . "null],".PHP_EOL;
    $echo_str=$echo_str
                        . "[new Date("
                        . $newest->format('Y')
                        . ", "
                        . $newest->format('m')
                        . "), ";
                        for ($b=0;$b < count($a);$b++ ) {
                            $echo_str = $echo_str. (" null,");
                        }
                        $echo_str = $echo_str.
                        $maxval
                        . ", ". $maxval.".toString(),"
                        . $minval. ", ".$minval.".toString(),"
                        . $avg. ", "
                        .$avg.".toString()]]);"; 
       
    //echo "Oldest ".$oldest->format('Y-m-d')."\r\nNewest ",$newest->format('Y-m-d')."\r\nMinVal: ".$minval."\r\nMaxVal: ".$maxval;
    $conn->close();
} else {
    echo "Illegal Value<br>";
}
?>

<html>
    <head>
    </head>
    <body>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {
                packages: ['annotatedtimeline']
            });
            google.charts.setOnLoadCallback(drawCrosshairs);


            function drawCrosshairs() {
                var data = new google.visualization.DataTable();
                data.addColumn('date', 'X');
                
                
                <?php echo $echo_str; ?>
                
                
                var options = {
                  //  chartArea: {<?php #echo $chart_width; ?> 'height': '80%',top:10},
                    hAxis: {
                        title: 'Time'                       
                    },
                    vAxis: {
                        title: 'Price',
                        format: 'currency',
                        /*viewWindow:{
                            min: <?php echo $minval; ?>,
                            max: <?php echo $maxval; ?>
                        }*/
                    },
                    
                    series: {
                        0: {},
                        1: {},
                        <?php echo count($a); ?>: {
                            lineDashStyle: [4, 4],
                            lineWidth: 1,
                            color: 'red',
                            tooltip: false,
                            crosshair: false,
                            pointSize: 0.01, //just tryna make it as small as possible
                            pointShape: {type: 'star', sides: 5, dent: 0.005}
                        },
                        <?php echo count($a)+1; ?>: {
                            lineDashStyle: [4, 4],
                            lineWidth: 1,
                            color: 'green',
                            tooltip: false,
                            crosshair: false,
                            pointSize: 0.01, //just tryna make it as small as possible
                            pointShape: {type: 'star', sides: 5, dent: 0.005}
                        },
                        <?php echo count($a)+2; ?>: {
                            lineDashStyle: [4, 4],
                            lineWidth: 1,
                            color: 'orange',
                            tooltip: false,
                            crosshair: false,
                            pointSize: 0.01, //just tryna make it as small as possible
                            pointShape: {type: 'star', sides: 5, dent: 0.005}
                        }

                    },
                    displayAnnotations: false,
                    annotations: {
                        textStyle: {
                        align: 'left',
                        fontSize: 18
                        
                      }
                      },
                    colors: ['blue', 'fuchsia', 'orange', 'brown', 'cyan'],
                    crosshair: {
                        color: '#000',
                        trigger: 'none'
                    }
                    <?php echo $legend_or_not; ?>
                };

                var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));

                chart.draw(data, options);
                chart.setSelection([{
                        row: 38,
                        column: 1
                    }]);
            }

        </script>
        <div id="chart_div" style="width:100%; height:80%"></div>
        <?php echo "Average Price: $" . floor($avg * 1000) / 1000 ?>
    </body>
</html>