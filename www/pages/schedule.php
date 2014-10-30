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
      var searchfieldenabled = false;
      var datetimefieldenabled = false;
      
      if (type == "lecturer") {
        $("#searcharea > div:first").html("Toon rooster van docent");
        $("#searcharea > .panel-body").html("<input type=\"hidden\" id=\"searchfield\" style=\"width:100%\"/>");
        $("#searcharea").addClass("panel-red");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niemand</strong>. Kies hierhoven een docent");
        searchfieldenabled = true;
        
        placeholdertext = "Klik om een docent te zoeken";
      } else if (type == "class") {
        $("#searcharea > div:first").html("Toon rooster van klas");
        $("#searcharea > .panel-body").html("<input type=\"hidden\" id=\"searchfield\" style=\"width:100%\"/>");
        $("#searcharea").addClass("panel-green");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>geen enkele klas</strong>. Kies hierhoven een klas");
        searchfieldenabled = true;
        
        placeholdertext = "Klik om een klas te zoeken";
      } else if (type == "room") {
        $("#searcharea > div:first").html("Toon rooster van lokaal");
        $("#searcharea > .panel-body").html("<input type=\"hidden\" id=\"searchfield\" style=\"width:100%\"/>");
        $("#searcharea").addClass("panel-primary");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>nergens</strong>. Kies hierhoven een lokaal");
        searchfieldenabled = true;
        
        placeholdertext = "Klik om een lokaal te zoeken";
      } else if (type == "activity") {
        $("#searcharea > div:first").html("Toon rooster van activiteit");
        $("#searcharea > .panel-body").html("<input type=\"hidden\" id=\"searchfield\" style=\"width:100%\"/>");
        $("#searcharea").addClass("panel-yellow");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niets</strong>. Kies hierhoven een activiteit");
        searchfieldenabled = true;
        
        placeholdertext = "Klik om een activiteit te zoeken";        
      } else if (type == "datetime") {
        $("#searcharea > div:first").html("Toon rooster van dag en lesuur");
        $("#searcharea > .panel-body").html("<div class=\"form-group\"><div id=\"datepicker\" class=\"input-append input-group\"><div class=\"add-on input-group-addon\"><i class=\"fa fa-calendar fa-fw\"></i></div><input data-format=\"yyyy-MM-dd\" type=\"text\" class=\"form-control\"></input></div> <div class=\"input-group\">Lesuur <input id=\"lecturehourpicker\" class=\"form-control\" type=\"number\" name=\"quantity\" min=\"1\" max=\"16\"></div><button id=\"submitdatetime\">Haal rooster op</button><button id=\"submitdatetimenow\">Haal rooster van NU op</button></div>");
        $("#searcharea").addClass("panel-yellow");
        $("#schedulearea > div:first > span").html("Lesrooster van <strong>niets</strong>. Kies hierhoven een datum en lesuur");
        datetimefieldenabled = true;
        
        placeholdertext = "Klik om een activiteit te zoeken";        
      }
      
     if (searchfieldenabled) {
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
		  loadSchedule(type, e.choice.id);
		});   
     }
     
     if (datetimefieldenabled) {
	    $('#datepicker').datetimepicker({
		 	pickTime: false
    	});
    	
    	$("#submitdatetime").on("click", function(e) {
	    	var date = $('#datepicker > input').val();
	    	var lecturehour = $("#lecturehourpicker").val();
	    	
	    	loadSchedule(type, date + "/" + lecturehour);
	    });
	    
	    $("#submitdatetimenow").on("click", function(e) {
	    	var date = $('#datepicker > input').val();
	    	var lecturehour = $("#lecturehourpicker").val();
	    	
	    	loadSchedule(type, "now");
	    });
     }
    
    //Check if system is updating
    $.getJSON( "api/stats/is_updating.json" , function( data ) {
      if (data.response == "true") {
        $("#update_warning").show();
      }
    });
  });
</script>
