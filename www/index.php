<?php
	require_once(dirname(__FILE__) . "/../config.inc.php");
?>
<html>
<head>
  <title>Roosterious rooster tool</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <base href="<?php echo BASE_URL ?>">
  
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/roosterious.css"/>
    
  <script src="js/jquery-1.11.0.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>
<body>
  <div class="container">
  	<div class="row headerrow">
     	<img src="img/logo.png"/>
    </div>
    
    <div id="content">
			<div class="row">
				<div class="col-md-12">
				  <h1 id="title">Welkom bij Roosterious</h1>
				
				  Op dit moment is er nog geen mooie zoekfunctie, maar de volgende URL's werken:
				
				  <h4>HTML weergave van roosters</h4>
			  </div>
			</div>
			
			<div class="row homepageheaderrow">
				<div class="col-md-6 homepageheaderfield">URL</div>
				<div class="col-md-6 homepageheaderfield">Functie</div>
			</div>
			
			<div class="row homepagerow">
				<div class="col-md-6 homepagefield"><a href="web/schedule/lecturer/rgr05">web/schedule/lecturer/rgr05</a></div>
				<div class="col-md-6 homepagefield">Toont docentrooster van docent rgr05</div>
			</div>
			
			<div class="row homepagerow">
				<div class="col-md-6 homepagefield"><a href="web/schedule/class/ein1vr">web/schedule/class/ein1vr</a></div>
				<div class="col-md-6 homepagefield">Toont klasserooster van klas ein1vr</div>
			</div>
			
			<div class="row homepagerow">
				<div class="col-md-6 homepagefield"><a href="web/schedule/room/g531">web/schedule/room/g531</a></div>
				<div class="col-md-6 homepagefield">Toont lokaalrooster voor lokaal g531</div>
			</div>
			
			<div class="row homepagerow">
				<div class="col-md-6 homepagefield"><a href="web/schedule/now">web/schedule/now</a></div>
				<div class="col-md-6 homepagefield">Toont alle lessen die op dit moment bezig zijn</div>
			</div>
			
			<div class="row">
			  <div class="col-md-12">
				  <h4>JSON weergave van roosters</h4>
				  <p>Pak bovenstaande URL's en vervang <i>web</i> door </i>api</i> en zet er .json achter. Zoals:
				  <a href="api/schedule/lecturer/rgr05.json">api/schedule/lecturer/rgr05.json</a>
				  of klik onderaan de links op de HTML versies</p>
			  </div>
    </div>
    
  </div>
</body>
</html>
