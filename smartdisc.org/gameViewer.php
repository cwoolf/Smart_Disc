<?php

// pathNode is an object class used for storing each GPS string, along with other calculated data specific to that exact moment in time
class pathNode
{
    private $_game;
    private $_player;
    private $_hole;
    private $_shot;
    private $_node;
    private $_lat;
    private $_lon;
    private $_alt;
    private $_speed;
    private $_time;
 
    public function __construct($game, $player, $hole, $shot, $node, $lat, $lon, $alt, $speed, $time)
    {
        $this->_game = $game;
        $this->_player = $player;
        $this->_hole = $hole;
        $this->_shot = $shot;
        $this->_node = $node;
        $this->_lat = $lat;
        $this->_lon = $lon;
        $this->_alt = $alt;
        $this->_speed = $speed;
		$this->_time = $time;
    }
    public function getGame(){
    	return $this->_game;
    }
    public function getPlayer(){
    	return $this->_player;
    }
    public function getHole(){
    	return $this->_hole;
    }
    public function getShot(){
    	return $this->_shot;
    }
    public function getNode(){
    	return $this->_node;
    }
    public function getLat(){
    	return $this->_lat;
    }
    public function getLon(){
    	return $this->_lon;
    }
    public function getAlt(){
    	return $this->_alt;
    }
    public function getSpeed(){
    	return $this->_speed;
    }
    public function getTime(){
    	return $this->_time;
    }
}

// holeDetails is an object class used for storing details related to each hole on the course, it is not time specific
class holeDetails
{
    private $_game;
    private $_player;
    private $_hole;
    private $_shots;
    private $_nodes;
    private $_score;
    private $_drive;
    private $_midrange;
    private $_putt;
    private $_time;
 
    public function __construct($game, $player, $hole, $shots, $nodes, $score, $drive, $midrange, $putt, $time)
    {
        $this->_game = $game;
        $this->_player = $player;
        $this->_hole = $hole;
        $this->_shots = $shots;
        $this->_nodes = $nodes;
        $this->_score = $score;
        $this->_drive = $drive;
        $this->_midrange = $midrange;
        $this->_putt = $putt;
		$this->_time = $time;
    }
    public function getGame(){
    	return $this->_game;
    }
    public function getPlayer(){
    	return $this->_player;
    }
    public function getHole(){
    	return $this->_hole;
    }
    public function getShots(){
    	return $this->_shots;
    }
    public function getNodes(){
    	return $this->_nodes;
    }
    public function getScore(){
    	return $this->_score;
    }
    public function getDrive(){
    	return $this->_drive;
    }
    public function getMidrange(){
    	return $this->_midrange;
    }
    public function getPutt(){
    	return $this->_putt;
    }
    public function getTime(){
    	return $this->_time;
    }
}

// File upload code
if(isset($_GET["upload"])){
	if(isset($_FILES['file']['name']) ){
		if($_FILES['file']['name'] != ""){
	    	$handle = fopen($_FILES['file']['name'], "r");
	    }else{
	    	$handle = fopen("data.txt", "r");
	    }      
	}
}else{
    $handle = fopen("data.txt", "r");  
}

// Process file data

$pathNode = array();
$countArray = 0; // pathNode array index
$holeTemp = 1;
$shotTemp = 0;
$nodeTemp = 1;
$distanceTemp = 1.1;
$blankLine = true; // flag for blank lines
$basketLat = 0.0;
$basketLon = 0.0;
$padLat = 0.0;
$padLon = 0.0;

$countArray2 = 0; // holeDetails array index
$scoreTemp = 0;
$driveTemp = 0.0;
$driveLat = 0.0;
$driveLon = 0.0;
$midrangeTemp = 0.0;
$midrangeTempMax = 0.0;
$puttTemp = 0.0;
$timeTemp = "";
$parTemp = 3;

if($handle){
	while(( $line = fgets($handle)) != false ){
			$value = explode(", ", $line); // split line into array of elements
			if(isset($value[1])){ // detect if blank line		
				if($countArray > 1){
					$distanceTemp = distance($value[0], $value[1], $pathNode[$countArray - 1]->getLat(), $pathNode[$countArray - 1]->getLon(), "K")*1000;
				}		
				if($distanceTemp > 1){			
					if($distanceTemp > 10 && $nodeTemp == 1){
						if($holeTemp == 1){
							$basketLat = 42.17784;
							$basketLon = -83.65209;
							$padLat = 42.17712;
							$padLon = -83.65224;
						}else if($holeTemp == 2){
							$basketLat = 42.1768;
							$basketLon = -83.65252;
							$padLat = 42.17783;
							$padLon = -83.65252;
							$parTemp = 4;
						}
						$scoreTemp = $shotTemp - $parTemp -1; // calculate score for hole
						$driveTemp = round(distance($driveLat, $driveLon, $padLat, $padLon, "K")*1000*3.28084, 2); // drive distance in feet of previous hole
						$puttTemp = round(distance($pathNode[$countArray - 1]->getLat(), $pathNode[$countArray - 1]->getLon(), $basketLat, $basketLon, "K")*1000*3.28084, 2); // putt distance in feet of previous hole
						$pathNode[$countArray] = new pathNode(0, "Sal", $holeTemp, $shotTemp, $nodeTemp, $basketLat , $basketLon , $value[2], $value[4], $value[5]);
						$countArray++; // increment pathNode index
						$holeDetails[$countArray2] = new holeDetails(0, "Sal", $holeTemp, $shotTemp, $nodeTemp, $scoreTemp , $driveTemp , $midrangeTempMax, $puttTemp, $timeTemp);
						$holeTemp++;
						$shotTemp = 1;
					 /* echo "Hole: " . $holeDetails[$countArray2]->getHole() . ", Shots: " . $holeDetails[$countArray2]->getShots() . " 
						, Nodes: " . $holeDetails[$countArray2]->getNodes() . "
						, Drive Distance: " . $holeDetails[$countArray2]->getDrive() . " Feet 
						, Midrange Distance: " . $holeDetails[$countArray2]->getMidrange() . " Feet 
						, Putt Distance: " . $holeDetails[$countArray2]->getPutt() . " Feet <br />";
					 */
						$countArray2++; // increment holeDetails index
						
					}
					if($nodeTemp == 1 && $shotTemp == 1){ // start at tee-pad
						if($holeTemp == 1){
							$basketLat = 42.17784;
							$basketLon = -83.65209;
							$padLat = 42.17712;
							$padLon = -83.65224;
						}else if($holeTemp == 2){
							$basketLat = 42.1768;
							$basketLon = -83.65252;
							$padLat = 42.17783;
							$padLon = -83.65252;
							$parTemp = 4;
						}
						$pathNode[$countArray] = new pathNode(0, "Sal", $holeTemp, $shotTemp, $nodeTemp, $padLat , $padLon , $value[2], $value[4], $value[5]);
					}else{
						$pathNode[$countArray] = new pathNode(0, "Sal", $holeTemp, $shotTemp, $nodeTemp, $value[0], $value[1], $value[2], $value[4], $value[5]);
						if($shotTemp == 1){
							$driveLat = $value[0];
							$driveLon = $value[1];
							$timeTemp = $value[5];
						}else{
							$midrangeTemp = round($distanceTemp*3.28084, 2);
							if($midrangeTemp > $midrangeTempMax){
								$midrangeTempMax = $midrangeTemp; // find max midrange distance for hole
							}
						}
					}
					//echo "Hole: " . $pathNode[$countArray]->getHole() . ", Shot: " . $pathNode[$countArray]->getShot() .", Distance: " . $distanceTemp * 3.28084 . " Feet <br />";
					$countArray++; // increment pathNode index
					
					$nodeTemp++; // add new node to shot
					$blankLine = true; // workaround for preserving correct shot count if multiple consecutive blank lines	
				}else{
					//echo "------ Garbage data ------<br />";
				}
				
			}else{ // blank line, button was pushed, so end of shot
				if($blankLine){
					$shotTemp++; // new shot or hole
					$nodeTemp = 1; // reset node count for next shot
					$blankLine = false;
				}
			}		
	}
}
if($holeTemp == 1){
	$basketLat = 42.17784;
	$basketLon = -83.65209;
}else if($holeTemp == 2){
	$basketLat = 42.1768;
	$basketLon = -83.65252;
}
$pathNode[$countArray-1] = new pathNode(0, "Sal", $holeTemp, $shotTemp+1, $nodeTemp+1, $basketLat , $basketLon , 0, 0, 0);

$scoreTemp = $shotTemp - $parTemp -1; // calculate score for hole
$driveTemp = round(distance($driveLat, $driveLon, $padLat, $padLon, "K")*1000*3.28084, 2); // drive distance in feet of previous hole
$puttTemp = round(distance($pathNode[$countArray - 1]->getLat(), $pathNode[$countArray - 1]->getLon(), $basketLat, $basketLon, "K")*1000*3.28084, 2); // putt distance in feet of previous hole
$holeDetails[$countArray2] = new holeDetails(0, "Sal", $holeTemp, $shotTemp, $nodeTemp, $scoreTemp , $driveTemp , $midrangeTempMax, $puttTemp, $timeTemp);

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                     													 :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles                                   :*/
/*::                  'K' is kilometers (default)                            :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at http://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: http://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2014		   		     :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Safari iOS - apps only -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Chrome for Android - NEW! -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="images/brand.png">
    <link rel="shortcut icon" href="images/favicon.ico">

    <title>Smart Disc</title>

    <!-- Bootstrap core CSS -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">

    <!-- Bootstrap-map-js -->
    <link rel="stylesheet" type="text/css" href="http://js.arcgis.com/3.8/js/esri/css/esri.css">   
    <link rel="stylesheet" type="text/css" href="../src/css/bootstrapmap.css"> 
    
    
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>
	
	<style type="text/css">

     #mapDiv {
        height: 500px;
        background-color:#777F28;
      }

      #myTabContent {
        margin-right: 15px;
        margin-left:  15px;
      }

      .row-map {
        background: #eee;
      }

      [class*="col-"] {
        background-color: #eee;
        border: 1px solid #E7E7E7;
        text-align: center;
      }

      .bg-none{
        background: none;
        border: none;
      }

      .no-col-padding {
        padding: 0;
      }

      .tab-content {
        margin-top: 10px;
      }

      .container.main {
        padding-bottom: 20px;
      }        

      @media (max-width:767px) {
        .container.main {
          margin-top: 10px;
          padding-bottom: 0;
        }  
        #myTabContent {
          margin-left: 0;
          margin-right: 0;
        }      
      }

    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../docs-assets/js/html5shiv.js"></script>
      <script src="../../docs-assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html"><img class="navbar-brand-image" src="images/Smart_Disc_Logo.png"></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.html">Get Started</a></li>
            <li class="active"><a href="gameViewer.html">Game Viewer</a></li> 
            <li><a href="upload.html">Import Game</a></li>  
          </ul>
          <ul class="nav navbar-nav pull-right">  
            <li><a class="navbar-brand" href="#"><img class="navbar-brand-image" src="images/profile_0.png" style="margin-top:-8px !important;"> Sal</a></li>    
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container main">
      <div class="hidden-xs">
      	<div style="float:left;">
			<h2>
				<div style="float:left;">Rolling Hills County Park (Ypsilanti, MI)</div>
			</h2> 
			<div class="lead" style="clear:both;float:left;padding-top:5px">
				<!-- Small button group -->
					<span class="glyphicon glyphicon-fire"></span> Personal Best for Course, 1 over Par
			</div>
        </div>
        <div style="float:right;padding-bottom:10px;">
			<h3>
				<div style="float:right;font-weight:lighter;">
					<?php echo date('g:i a', strtotime($holeDetails[0]->getTime()));?> 
					on <?php echo date("F j, Y", time() - 60 * 60 * 24);; ?> <!-- set to yesterday due to GPS not configured with date string by default -->
				</div>
			</h3>
			<div class="btn-group" style="float:right;padding-top:10px;">
			  <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
				Other Games <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
				<li>&nbsp;04/06/2014 @ 6:04 PM</li>
				<li>&nbsp;04/05/2014 @ 5:10 PM</li>
			  </ul>
			</div>
		</div>
      </div>

      <div class="row">
        <div class="col-xs-12 bg-none">
              <div id="mapRight"> 
               <div class="row">
                  <div class="col-xs-12">
                    <h5>
                    	<div style="float:left;padding-bottom:8px;text-align:left; width:33%;">
                    		<span class="glyphicon glyphicon-user"></span> Solo
                    	</div>
                    	<div style="float:left; text-align:center; width:33%;">
                    		Hole #<span id="hole">1</span>
                    	</div>
                    	<div style="float:right;text-align:right; width:33%;">
                    		<span class="glyphicon glyphicon-time"></span> 
                    		<?php echo date('i', strtotime($pathNode[$countArray-2]->getTime())) - date('i', strtotime($holeDetails[1]->getTime()));?> hour
                    	</div>
                    </h5>
                  </div>
                </div>
                <div class="row row-map">
                  <div class="col-xs-12 col-sm-4">
                    <div class="btn-group-vertical" style="width:100%;margin-top:16px;margin-bottom:16px;">
						<button type="button" class="btn btn-default" 
						onClick = "holeFocus(42.17712, -83.65224);
						document.getElementById('hole').innerHTML = '1';
						document.getElementById('drive').innerHTML = driveDist[1];
						document.getElementById('midrange').innerHTML = midrangeDist[1];
						document.getElementById('putt').innerHTML = puttDist[1];		
						">
							<div style="float:left;width:20%;">
								<span class="badge">
									1
								</span>
							</div>
							<div style="float:right;width:20%;">
								<?php 
								if($holeDetails[0]->getScore() > 0){
									echo "<span style='color:red;font-weight:bold;'>&nbsp;+";echo $holeDetails[0]->getScore(); echo "</span>";
								}else if($holeDetails[0]->getScore() < 0){
									echo "<span style='color:green;font-weight:bold;'>&nbsp;-";echo $holeDetails[0]->getScore(); echo "</span>";
								}else{
									echo "<span style='color:orange;font-weight:bold;'>&nbsp;0</span>";
								}
							?>	
							</div>
							<div style="float:right;width:60%;color:grey;font-weight:lighter;">
								Par 3, 252 ft
							</div>
						</button>
  						<button type="button" class="btn btn-default" 
  						onClick = "holeFocus(42.17783, -83.65252);
  						document.getElementById('hole').innerHTML = '2';
						document.getElementById('drive').innerHTML = driveDist[2];
						document.getElementById('midrange').innerHTML = midrangeDist[2];
						document.getElementById('putt').innerHTML = puttDist[2];		
						">
							<div style="float:left;width:20%;">
								<span class="badge">
									2
								</span>
							</div>
							<div style="float:right;width:20%;">
								<?php 
								if($holeDetails[1]->getScore() > 0){
									echo "<span style='color:red;font-weight:bold;'>&nbsp;+";echo $holeDetails[1]->getScore(); echo "</span>";
								}else if($holeDetails[1]->getScore() < 0){
									echo "<span style='color:green;font-weight:bold;'>&nbsp;-";echo $holeDetails[1]->getScore(); echo "</span>";
								}else{
									echo "<span style='color:orange;font-weight:bold;'>&nbsp;0</span>";
								}
							?>	
							</div>
							<div style="float:right;width:60%;color:grey;font-weight:lighter;">
								Par 4, 367 ft
							</div>
						</button>
					</div>
                  </div>
                  <div class="col-xs-12 col-sm-8 no-col-padding">
                    <!-- Bootstrap-map-js -->
                    <div id="mapDiv"></div>
                     <script type="text/javascript">
		//alert(<?php echo $holeTemp; ?>);
		 	// initialize the map on the "map" div with a given center and zoom
			var map = L.map('mapDiv', {
				center: [42.17729, -83.65235],
				zoom: 20
			});
		 
			//var imageUrl = '16131524510.tif',
    		//imageBounds = [[42.17007154207915, -83.659734970691233], [42.18367481072174, -83.64113271047205]];
    		
    		var imageUrl = 'vectorMap.png',
    		imageBounds = [[42.1779, -83.65293], [42.17673, -83.65197]];

			L.imageOverlay(imageUrl, imageBounds).addTo(map);
 //Debugging tool: show coordinates where clicked
 /*
			function onMapClick(e) {
    			alert("You clicked the map at " + e.latlng);
			}

			map.on('click', onMapClick);
*/			
			var pointList;
			var driveDist = new Array();
			var midrangeDist = new Array();
			var puttDist = new Array();
			
			// create a polyline from arrays of LatLng points
			<?php
		$holeTemp2 = 1;
		while($holeTemp2 <= $holeTemp){
			echo "driveDist[". $holeTemp2 ."] = ". $holeDetails[$holeTemp2-1]->getDrive() .";";
			echo "midrangeDist[". $holeTemp2 ."] = ". $holeDetails[$holeTemp2-1]->getMidrange() .";";
			echo "puttDist[". $holeTemp2 ."] = ". $holeDetails[$holeTemp2-1]->getPutt() .";";
			echo "pointList = [";			
			$j = 0;
				while($j < $countArray){
					if($pathNode[$j]->getHole() == $holeTemp2){
						echo 'new L.LatLng(' . $pathNode[$j]->getLat() . ', ' . $pathNode[$j]->getLon() . '),'; 
					}
					$j++;
				}
			?>
			];
			var firstpolyline = new L.polyline(pointList, {
			color: 'red',
			weight: 6,
			opacity: 0.3,
			smoothFactor: 10

			}).addTo(map);	
			L.control.scale({position:'bottomleft', imperial:true, metric:false}).addTo(map); // add scale bar to map in imperial units
			// create node markers along polyline from arrays of LatLng points
			<?php
			$j = 0;
				while($j < $countArray){
					if($pathNode[$j]->getNode() == 1 && $pathNode[$j]->getHole() == $holeTemp2){
						echo "
							L.circle([" . $pathNode[$j]->getLat() . ", " . $pathNode[$j]->getLon() . "], 0.4, {
   								color: 'red',
   								fillColor: 'red',
   								fillOpacity: 1
							}).addTo(map);
						"; 
					}	
					$j++;
				}
				/* // Mark end of hole, not working 100%
				echo "
							L.circle([" . $pathNode[$j-1]->getLat() . ", " . $pathNode[$j-1]->getLon() . "], 1.5, {
   								color: 'gold',
   								fillColor: 'red',
   								fillOpacity: 0.3
							}).addTo(map);
						"; 
				*/
			$holeTemp2++;
		}
			?>		
			
			function holeFocus(lat,lon){
				map.panTo(new L.LatLng(lat, lon));		
				map.setZoom(21);
			}
		</script>
                    
                    
                    
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-4"><h5>Drive: <span id="drive"><?php echo $holeDetails[0]->getDrive();?></span> ft</h5></div>
                  <div class="col-xs-4"><h5>Midrange (Max): <span id="midrange"><?php echo $holeDetails[0]->getMidrange();?></span> ft</h5></div>
                  <div class="col-xs-4"><h5>Putt: <span id="putt"><?php echo $holeDetails[0]->getPutt();?></span> ft</h5></div>
                </div>        
              </div> <!-- /.tab pane -->
        </div> <!-- col for tabs -->
      </div> <!-- row for tabs -->

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

  </body>
</html>
