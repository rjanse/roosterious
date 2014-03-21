<?php
	require_once(dirname(__FILE__) . "/../../config.inc.php");
	require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
	require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");

	Flight::route('GET /schedule/@sType/@sVal', function($sType, $sVal){
		if ($sType == "lecturer") {
			$sTitle = "Rooster voor docent " . $sVal;
		} else if ($sType == "class") {
			$sTitle = "Rooster voor klas " . $sVal;
		} else if ($sType == "room") {
			$sTitle = "Rooster voor lokaal " . $sVal;
		}
		$sUrl = "api/schedule/" . $sType . "/" . $sVal . ".json";
		renderPage($sType, $sTitle, $sUrl);
	});
	
	Flight::route('GET /schedule/now', function() {
		renderPage("now", "Lessen op dit moment", "api/schedule/now.json");
	});
	
	Flight::start();
	
	
	function renderPage($sType, $sTitle, $sApiUrl) {
	?>
	<html>
<head>
  <title><?php echo $sTitle ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <base href="<?php echo BASE_URL ?>">
  
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/roosterious.css"/>
    
  <script src="js/jquery-1.11.0.min.js">
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </script>
</head>
<body>
  <div class="container">
  	<div class="page-header">
     	<img src="img/logo.png"/>
    </div>
    
    <div id="content">
    		<h1 id="title"></h1>
    		<!-- Schedule header -->
			<div class="row">
				<div class="col-md-2 headerfield">Tijd</div>
				<div class="col-md-2 headerfield">activiteit</div>
				<div class="col-md-2 headerfield">activiteittype</div>
				<div class="col-md-2 headerfield">klassen</div>
				<div class="col-md-2 headerfield">docenten</div>
				<div class="col-md-2 headerfield">lokalen</div>
			</div>
    		
    		<div id="schedule">

    		</div>
      	<table>
    </div>
    
  </div>
  
  <script>
  	var sType = "<?php echo $sType ?>";
  	var sTitle = "<?php echo $sTitle ?>";
  	var sApiurl = "<?php echo $sApiUrl ?>";
  	$("#title").append(sTitle);
  	 
  	$.getJSON( sApiurl , function( data ) {
  		var row = "";
  		var currentdate = "";
  		$.each(data.response, function(index, lesson){
  			if (currentdate != lesson.date) {
  				var dayofweek = "";
  				if (lesson.day_of_week == 0) {
  					dayofweek = "zondag";
  				} else if (lesson.day_of_week == 1) {
  					dayofweek = "maandag";
  				} else if (lesson.day_of_week == 2) {
  					dayofweek = "dinsdag";
  				} else if (lesson.day_of_week == 3) {
  					dayofweek = "woensdag";
  				} else if (lesson.day_of_week == 4) {
  					dayofweek = "donderdag";
  				} else if (lesson.day_of_week == 5) {
  					dayofweek = "vrijdag";
  				} else if (lesson.day_of_week == 6) {
  					dayofweek = "zaterdag";
  				}
  				
  				row += "</div><h4>" + dayofweek + " " + lesson.date + "</h4><div class=\"row\">";
  			} else {
  				row += "</div><div class=\"row\">";
  			}
  			currentdate = lesson.date;
			row += "<div class=\"col-md-2 schedulefield\">" + lesson.starttime + " - " + lesson.endtime + "</div>";
			row += "<div class=\"col-md-2 schedulefield\">" + lesson.activity + "</div>";
			row += "<div class=\"col-md-2 schedulefield\">" + lesson.activitytype + "</div>";
			
			row += "<div class=\"col-md-2 schedulefield\">"
			if (lesson.classes) {
				row += lesson.classes.replace(/\,/g,", ");
			} else {
				row += "Geen klassen";
			}	
			row += "</div>";
			
			row += "<div class=\"col-md-2 schedulefield\">"
			if (lesson.lecturers) {
				row += lesson.lecturers.replace(/\,/g,", ");
			} else {
				row += "Geen docenten";
			}
			row += "</div>";
			
			row += "<div class=\"col-md-2 schedulefield\">"
			if (lesson.rooms) {
				row += lesson.rooms.replace(/\,/g,", ");
			} else {
				row += "Geen lokalen";
			}
			row += "</div>";
		});
  		$("#schedule").append(row);
  	}).fail(function() {
    	console.log( "error" );
  	})
  </script>
</body>
</html>
	
<?php
}
?>
