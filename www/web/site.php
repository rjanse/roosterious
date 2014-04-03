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
		} else if ($sType == "activity") {
		  $sTitle = "Rooster voor activiteit " . $sVal;
		}
		$sUrl = "schedule/" . $sType . "/" . $sVal;
		renderPage($sType, $sTitle, $sUrl);
	});
	
	Flight::route('GET /schedule/now', function() {
		renderPage("now", "Lessen op dit moment", "schedule/now");
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
    
  <script src="js/jquery-1.11.0.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  <script>
    function openPage(name) {
      window.location = name;
    }
  </script>
</head>
<body>
  <div class="container">
    <div id="content">
    		<h1 id="title"></h1>
    		<!-- Schedule header -->
			<div class="row scheduleheaderrow">
				<div class="col-md-2 scheduleheaderfield">Tijd</div>
				<div class="col-md-2 scheduleheaderfield">activiteit</div>
				<div class="col-md-2 scheduleheaderfield">activiteittype</div>
				<div class="col-md-2 scheduleheaderfield">klassen</div>
				<div class="col-md-2 scheduleheaderfield">docenten</div>
				<div class="col-md-2 scheduleheaderfield">lokalen</div>
			</div>
    		
    	<div id="schedule">

    	</div>
    	
    	<div class="row footerrow">
    	  <p>
    	    <span style="background-color: #eee; padding: 5px;">Voorbeeld</span> = Les uit normale lesrooster.<br/>
    	    &nbsp;<br/>
    	    <span style="background-color: #FBFBC8; padding: 5px;">Voorbeeld</span> = Les uit het beta lesrooster.<br/>
    	    &nbsp;<br/>
    	    Alle gekleurde blokken (activiteiten, klassen, docenten en lokalen) zijn klikbaar.</br>
    	  </p>
    	  &nbsp;<br/>
    	  <p>Download dit rooster in 
    	    <button type="button" class="btn btn-default btn-xs" onClick="openPage('api/<?php echo $sApiUrl ?>.json');">JSON</button> 
    	    of 
    	    <button type="button" class="btn btn-default btn-xs" onClick="openPage('api/<?php echo $sApiUrl ?>.ical');">ICAL</button> 
    	    (not supported yet).</p>
    	</div>
    </div>
    
  </div>
  
  <script>
  	var sType = "<?php echo $sType ?>";
  	var sTitle = "<?php echo $sTitle ?>";
  	var sApiurl = "api/<?php echo $sApiUrl ?>.json";
  	$("#title").append(sTitle);
  	 
  	$.getJSON( sApiurl , function( data ) {
  		var row = "";
  		var currentdate = "";
  		var currentweek = "";
  		
  		$.each(data.response, function(index, lesson){
  		  var styles = "row schedulerow";
  		  if (lesson.is_beta == "1") {
  		    styles = styles + " schedulerow_beta";
  		  }
  		  
  		  //Check day of lesson date
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
  				
  				if (currentweek != lesson.weeknr) {
  		      row += "</div><div class=\"row scheduledayrow_separator\">Week " + lesson.weeknr + "</div><div class=\"row scheduledayrow scheduledayrow_nextweek\">" + dayofweek + " " + lesson.date + "</div><div class=\"" + styles + "\">";
  		    } else {
  		      row += "</div><div class=\"row scheduledayrow\">" + dayofweek + " " + lesson.date + "</div><div class=\"" + styles + "\">";
  		    }
  				
  			} else {
  				row += "</div><div class=\"" + styles + "\">";
  			}
  			currentdate = lesson.date;
  			currentweek = lesson.weeknr;
  			
        row += "<div class=\"col-md-2 schedulefield\">" + lesson.starttime + " - " + lesson.endtime + "</div>";
        row += "<div class=\"col-md-2 schedulefield\"><button type=\"button\" class=\"btn btn-warning btn-xs tag\" onClick=\"openPage('web/schedule/activity/" + lesson.activity + "');\">" + lesson.activity + "</button></div>";
        row += "<div class=\"col-md-2 schedulefield\">" + lesson.activitytype + "</div>";
      
        row += "<div class=\"col-md-2 schedulefield\">"
        if (lesson.classes) {
          $.each(lesson.classes.split(","), function(index, tclass) {
            tclass = tclass.trim();
            var classstring;
            if (tclass != "NULL") {
              classstring = "<button type=\"button\" class=\"btn btn-success btn-xs tag\" onClick=\"openPage('web/schedule/class/" + tclass + "');\">" + tclass + "</button>";
            } else {
              classstring = "<span class=\"tag taglecturer\">(Onbekende klas)</span>";
            }
            row+= classstring;
          });
        } else {
          row += "Geen klassen";
        }	
        row += "</div>";
      
        row += "<div class=\"col-md-2 schedulefield\">"
        if (lesson.lecturers) {
          $.each(lesson.lecturers.split(","), function(index, lecturer) {
            lecturer = lecturer.trim();
            var lecturerstring;
            if (lecturer != "NULL") {
              lecturerstring = "<button type=\"button\" class=\"btn btn-danger btn-xs tag\" onClick=\"openPage('web/schedule/lecturer/" + lecturer + "');\">" + lecturer + "</button>";
            } else {
              lecturerstring = "<span class=\"tag taglecturer\">(Onbekende docent)</span>";
            }
            row+= lecturerstring;
          });
        } else {
          row += "Geen docenten";
        }
        row += "</div>";
      
        row += "<div class=\"col-md-2 schedulefield\">"
        if (lesson.rooms) {
          $.each(lesson.rooms.split(","), function(index, room) {
            room = room.trim();
            var roomstring;
            if (room != "NULL") {
              roomstring = "<button type=\"button\" class=\"btn btn-primary btn-xs tag\" onClick=\"openPage('web/schedule/room/" + room + "');\">" + room + "</button>";
            } else {
              roomstring = "<span class=\"tag taglecturer\">(Onbekend lokaal)</span>";
            }
            row+= roomstring;
          });
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
