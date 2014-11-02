<div class="row">
    <div class="col-lg-12">&nbsp;</div>
</div>

<div class="row">
    <div class="col-lg-12">
      <div id="searcharea" class="panel">
        <div class="panel-heading">
          &nbsp;
        </div>
        <div class="panel-body">
	        <div id="unitselectorview" style="display: none;">
		        <input type="hidden" id="searchfield" style="width:100%"/>
	        </div>
	        
        	<div id="datetimeselectorview" role="form" style="display: none;">
	        	<div class="form-group">
		        	<label for="datepicker">Datum</label>
		        	<div id="datepicker" class="input-append input-group">
			        	<div class="add-on input-group-addon">
				        	<i class="fa fa-calendar fa-fw"></i>
				        </div>
				        <input data-format="yyyy-MM-dd" type="text" class="form-control"></input>
				    </div>
				</div>
				<div class="form-group"> 
					<label for="lecturehourpicket">Lesuur</label>
					<select id="lecturehourpicker" class="form-control">
					  <option value="1">&nbsp;1 - &nbsp;8:30 - 9:15</option>
					  <option value="2">&nbsp;2 - &nbsp;9:15 - 10:00</option>
					  <option value="3">&nbsp;3 - 10:15 - 11:00</option>
					  <option value="4">&nbsp;4 - 11:00 - 11:45</option>
					  <option value="5">&nbsp;5 - 11:45 - 12:30</option>
					  <option value="6">&nbsp;6 - 12:30 - 13:15</option>
					  <option value="7">&nbsp;7 - 13:15 - 14:00</option>
					  <option value="8">&nbsp;8 - 14:00 - 14:45</option>
					  <option value="9">&nbsp;9 - 15:00 - 15:45</option>
					  <option value="10">10 - 15:45 - 16:30</option>
					  <option value="11">11 - 16:30 - 17:15</option>
					  <option value="12">12 - 17:15 - 18:00</option>
					  <option value="13">13 - 18:15 - 19:00</option>
					  <option value="14">14 - 19:00 - 19:45</option>
					  <option value="15">15 - 20:00 - 20:45</option>
					  <option value="16">16 - 20:45 - 21:30</option>
					</select>
				</div>
				<button id="submitdatetime">Haal rooster op</button>
        	</div>
        	
        </div>
      </div>
    </div>
</div><!-- /.row -->

<div class="row" id="update_warning" style="display: none;">
    <div class="col-lg-12">
      <div class="alert alert-danger">
        <strong>Let op: </strong>Op dit moment is Roosterious zijn informatie aan het updaten. Hierdoor kan de informatie onvolledig zijn!
      </div>    
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
      <div id="schedulearea" class="panel panel-default">
        <div class="panel-heading">
          <span>&nbsp;</span>
          <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    Download rooster
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="#" onClick="loadExt('iCal');"><i class="fa fa-calendar fa-fw"></i> iCal</a>
                    </li>
                    <li><a href="#" onClick="loadExt('json');"><i class="fa fa-file-code-o fa-fw"></i> json</a>
                    </li>
                    <li><a href="#" onClick="loadExt('csv');"><i class="fa fa-file-excel-o fa-fw"></i> CSV</a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
        <div class="panel-body">
          <div class="table-responsive" id="scheduleplaceholder">
          
          </div>
        </div>
      </div>
    </div>
</div><!-- /.row -->
     
<script type="text/javascript">
  $(function() {
      <?php
        echo "var type = '" . $_GET['type'] . "';";
      ?>
      
      var placeholdertext = "";
      var apiUrl = "api/" + type + ".json";
      
      if (type == "lecturer") {
        $("#searcharea > div:first").html("Toon rooster van docent");
        $("#unitselectorview").show();
        $("#searcharea").addClass("panel-red");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niemand</strong>. Kies hierhoven een docent");
        
        placeholdertext = "Klik om een docent te zoeken";
      } else if (type == "class") {
        $("#searcharea > div:first").html("Toon rooster van klas");
        $("#unitselectorview").show();
        $("#searcharea").addClass("panel-green");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>geen enkele klas</strong>. Kies hierhoven een klas");
        
        placeholdertext = "Klik om een klas te zoeken";
      } else if (type == "room") {
        $("#searcharea > div:first").html("Toon rooster van lokaal");
        $("#unitselectorview").show();
        $("#searcharea").addClass("panel-primary");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>nergens</strong>. Kies hierhoven een lokaal");
        
        placeholdertext = "Klik om een lokaal te zoeken";
      } else if (type == "activity") {
        $("#searcharea > div:first").html("Toon rooster van activiteit");
        $("#unitselectorview").show();
        $("#searcharea").addClass("panel-yellow");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niets</strong>. Kies hierhoven een activiteit");
        
        placeholdertext = "Klik om een activiteit te zoeken";        
      } else if (type == "datetime") {
        $("#searcharea > div:first").html("Toon rooster van dag en lesuur");
        $("#datetimeselectorview").show();
        $("#searcharea").addClass("panel-yellow");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niets</strong>. Kies hierhoven een datum en lesuur");
        
        placeholdertext = "Klik om een activiteit te zoeken";        
      }
      
     if($('#unitselectorview').is(':visible')) {
	    //UNITSELECTOR VIEW VISIBLE
	     
		$("#searchfield").select2({
		   placeholder: placeholdertext,
		   minimumInputLength: 1,
		   ajax: {
		      url: apiUrl,
		      dataType: 'json',
		      quietMillis: 100,
		      data: function (term, page) { // page is the one-based page number tracked by Select2
		          return {
		              q: term, //search term
		              page_limit: 10, // page size
		          };
		      },
		      results: function (data, page) {
		        var newData = [];
		        $.each(data.response, function (index, item) {
		            if (type == "lecturer") {
		              newData.push({
		                id: item.lecturer_id
		              , text: item.lecturer_name + " (" + item.lecturer_id + ")"
		              });
		            } else if (type == "class") {
		              newData.push({
		                id: item.class_id
		              , text: item.class_id
		              });                  
		            } else if (type == "room") {
		              newData.push({
		                id: item.room_id
		              , text: item.room_id
		              });                  
		            } else if (type == "activity") {
		              newData.push({
		                id: item.activity_id
		              , text: item.activity_id
		              });                   
		            }
		        });
		        return { results: newData };
		      },
		    }
		});
		
		$("#searchfield").on("select2-selecting", function(e) {
		  currentScheduleType = type;
		  currentScheduleId = e.choice.id;
		  
		  var title = "";
		  if (type == "lecturer") {
			  title = "Rooster van docent <strong>" + e.choice.id + "</strong>";
		  } else if (type == "class") {
			  title = "Rooster van klas <strong>" + e.choice.id + "</strong>";
		  } else if (type == "room") {
			  title = "Rooster van lokaal <strong>" + e.choice.id + "</strong>";
		  } else if (type == "activity") {
			  title = "Rooster van activiteit <strong>" + e.choice.id + "</strong>";
		  }
		  loadSchedule(type, e.choice.id, title);
		});   
     } else if($('#datetimeselectorview').is(':visible')) {
	    //DATETIMESELECTOR VIEW VISIBLE
	     
	    $('#datepicker').datetimepicker({
		 	pickTime: false,
    	}).data('datetimepicker').setLocalDate(new Date());
    	
    	$("#submitdatetime").on("click", function(e) {
	    	var date = $('#datepicker > input').val();
	    	var lecturehour = $("#lecturehourpicker").val();
	    	
			currentScheduleType = type;
			currentScheduleId = date + "/" + lecturehour;
	    	
	    	loadSchedule(type, date + "/" + lecturehour, "Rooster van het <strong>" + lecturehour + "e</strong> lesuur op <strong>" + date + "</strong>");
	    });
	    
	    //Get default schedule for now
	    loadSchedule(type, "now", "Rooster van dit moment");
     }
    
    //Check if system is updating
    $.getJSON( "api/stats/is_updating.json" , function( data ) {
      if (data.response == "true") {
        $("#update_warning").show();
      }
    });
  });
</script>
