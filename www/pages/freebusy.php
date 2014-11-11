<div class="row">
    <div class="col-lg-12">&nbsp;</div>
</div>

<div class="row">
    <div class="col-lg-4">
      <div class="panel panel-red">
        <div class="panel-heading">
          Docenten
        </div>
        <div class="panel-body">
			<input type="hidden" id="lecturerfield" style="width:100%"/>    	
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="panel panel-green">
        <div class="panel-heading">
          Klassen
        </div>
        <div class="panel-body">
			<input type="hidden" id="classfield" style="width:100%"/>    	
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="panel panel-primary">
        <div class="panel-heading">
          Lokalen
        </div>
        <div class="panel-body">
			<input type="hidden" id="roomfield" style="width:100%"/>    	
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
      <div id="freebusyarea">

      </div>
    </div>
</div><!-- /.row -->
     
<script type="text/javascript">
  $(function() {
	 
	//Load lessondays and generate panels
	$.getJSON( 'api/lessondays.json' , function( data ) {
		var prevdayofweek = -1;
		$.each(data.response, function(index, obj) {
			var id = obj.date.replace(/-/g,"");
			
			if (obj.day_of_week < prevdayofweek) {
				$("#freebusyarea").append("<div>&nbsp;</div>");	
			}
			
			$("#freebusyarea").append("<div class=\"panel panel-default\"><div class=\"panel-heading\">" + parseDate(obj.date, obj.day_of_week) + "</div>" +
					"<div class=\"panel-body freebusyday\" id=\"" + id + "\"></div></div></div>");
				

			prevdayofweek = obj.day_of_week;
		});
	});
	
	  
	  
     $("#lecturerfield").select2({
	   placeholder: "Kies een docent",
	   minimumInputLength: 1,
	   ajax: {
	      url: "api/lecturer.json",
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
	        	newData.push({
	                id: item.lecturer_id
	              , text: item.lecturer_name + " (" + item.lecturer_id + ")"
	            });
	        });
	        return { results: newData };
	      },
	    }
	});
	
	$("#classfield").select2({
	   placeholder: "Kies een klas",
	   minimumInputLength: 1,
	   ajax: {
	      url: "api/class.json",
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
	        	newData.push({
	                id: item.class_id
	              , text: item.class_id
	            });
	        });
	        return { results: newData };
	      },
	    }
	});
	
	$("#roomfield").select2({
	   placeholder: "Kies een lokaal",
	   minimumInputLength: 1,
	   ajax: {
	      url: "api/room.json",
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
	        	newData.push({
	                id: item.room_id
	              , text: item.room_id
	            });
	        });
	        return { results: newData };
	      },
	    }
	});
		
	$("#lecturerfield").on("select2-selecting", function(e) {
		loadFreebusyList("lecturer", e.choice.id, "api/freebusy/lecturer/" + e.choice.id + ".json");
	}); 
	
	$("#classfield").on("select2-selecting", function(e) {
		loadFreebusyList("class", e.choice.id, "api/freebusy/class/" + e.choice.id + ".json");
	});   

	$("#roomfield").on("select2-selecting", function(e) {
		loadFreebusyList("room", e.choice.id, "api/freebusy/room/" + e.choice.id + ".json");
	}); 
  });
</script>
