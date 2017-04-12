var currentScheduleType = "";
var currentScheduleId = "";

$(function() {
    $('#side-menu').metisMenu();
    
    //Attach listeners to buttons
    $("#menu_dashboard").click(function() {
      $("#page-wrapper").load("pages/dashboard.phtml");
      $(".menu").removeClass("active");
      $("#menu_dashboard").addClass("active");
    });
    
    $("#menu_lecturer").click(function() {
      $("#page-wrapper").load("pages/schedule.php?type=lecturer");
      $(".menu").removeClass("active");
      $("#menu_lecturer").addClass("active");
    });
    
    $("#menu_class").click(function() {
      $("#page-wrapper").load("pages/schedule.php?type=class");
      $(".menu").removeClass("active");
      $("#menu_class").addClass("active");
    });
    
    $("#menu_room").click(function() {
      $("#page-wrapper").load("pages/schedule.php?type=room");
      $(".menu").removeClass("active");
      $("#menu_room").addClass("active");
    });
    
    $("#menu_activity").click(function() {
      $("#page-wrapper").load("pages/schedule.php?type=activity");
      $(".menu").removeClass("active");
      $("#menu_activity").addClass("active");
    });
    
    $("#menu_datetime").click(function() {
      $("#page-wrapper").load("pages/schedule.php?type=datetime");
      $(".menu").removeClass("active");
      $("#menu_datetime").addClass("active");
    });
    
    $("#menu_freebusytool").click(function() {
      $("#page-wrapper").load("pages/freebusy.php");
      $(".menu").removeClass("active");
      $("#menu_freebusytool").addClass("active");
    });
    
    $("#page-wrapper").load("pages/dashboard.phtml");
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
});

//Parse a date to a readable format
function parseDate(nwDate, nwNum) {
	var dayofweek = parseDayOfWeek(nwNum);
	
	var d = new Date(nwDate);
	return dayofweek + " " + d.getDate() + " " + parseMonth(d.getMonth()) + " " + d.getFullYear();
}

//Parse day of week
function parseDayOfWeek(num) {
	if (num == 0) {
		return "Zondag";
	} else if (num == 1) {
		return "Maandag";
	} else if (num == 2) {
		return "Dinsdag";
	} else if (num == 3) {
		return "Woensdag";
	} else if (num == 4) {
		return "Donderdag";
	} else if (num == 5) {
		return "Vrijdag";
	} else if (num == 6) {
		return "Zaterdag";
	}
}

//Parse month
function parseMonth(num) {
	if (num == 0) {
		return "januari";
	} else if (num == 1) {
		return "februari";
	} else if (num == 2) {
		return "maart";
	} else if (num == 3) {
		return "april";
	} else if (num == 4) {
		return "mei";
	} else if (num == 5) {
		return "juni";
	} else if (num == 6) {
		return "juli";
	} else if (num == 7) {
		return "augustus";
	} else if (num == 8) {
		return "september";
	} else if (num == 9) {
		return "oktober";
	} else if (num == 10) {
		return "november";
	} else if (num == 11) {
		return "december";
	}
}

//Loads a schedule from the given api url and parses it in the given target
function loadSchedule( type, id, title ) {
  if (title == undefined) {
  	  if (type == "lecturer") {
		  title = "Rooster van docent <strong>" + id+ "</strong>";
	  } else if (type == "class") {
		  title = "Rooster van klas <strong>" + id + "</strong>";
	  } else if (type == "room") {
		  title = "Rooster van lokaal <strong>" + id + "</strong>";
	  } else if (type == "activity") {
		  title = "Rooster van activiteit <strong>" + id + "</strong>";
	  }
  }
  $('#scheduleplaceholder').empty();
  $("#schedulearea > div:first > span").html(title);
      
  var sApiUrl = "api/schedule/" + type + "/" + id + ".json";
  $.getJSON( sApiUrl , function( data ) {
      var outputHtml = "<table class=\"table table-striped table-bordered table-hover\">";
      outputHtml+= "<thead><tr><th>Tijd</th><th>Activiteit</th><th>Activiteittype</th><th>Docenten</th><th>Klassen</th><th>Lokalen</th></tr></thead>";
      outputHtml+="<tbody>";
  		var row = "";
  		var currentdate = "";
  		var currentweek = "";
  		var firstWeek = true;
  		
  		$.each(data.response, function(index, lesson){
  		  //Check day of lesson date
  			if (currentdate != lesson.date) {
  				var dateDesc = parseDate(lesson.date, lesson.day_of_week);
  				
  				if (currentweek != lesson.weeknr) {
    				//New week
    				if (firstWeek) {
    				  outputHtml+= "<tr class=\"info\"><td colspan=\"6\"><strong>Week " + lesson.weeknr + "</strong></td></tr><tr class=\"success\"><td colspan=\"6\"><small>" + dateDesc + "</small></td></tr><tr>";  
    				  firstWeek = false;
    				} else {
    				  outputHtml+= "</tr><tr><td class=\"schedule_emptyrow\" colspan=\"6\">&nbsp;</td></tr><tr class=\"info\"><td colspan=\"6\"><strong>Week " + lesson.weeknr + "</strong></td></tr><tr class=\"success\"><td colspan=\"6\"><small>" + dateDesc + "</small></td></tr><tr>";
    				}
  				} else {
    				//New day
    				outputHtml+= "</tr><tr class=\"success\"><td colspan=\"6\"><small>" + dateDesc + "</small></td></tr><tr>";
  				}
        } else {
          //New entry on the same day
          outputHtml+="</tr><tr>";
        }
        
        currentdate = lesson.date;
  		currentweek = lesson.weeknr;
  			
        if (lesson.is_beta == "1") {
  		    outputHtml+= "<td class=\"schedule_column schedule_nowrap\">" + lesson.starttime + " - " + lesson.endtime + "<i class=\"fa fa-filter fa-fw\"></i></td>";
  		  } else {
    		  outputHtml+= "<td class=\"schedule_column schedule_nowrap\">" + lesson.starttime + " - " + lesson.endtime + "</td>";
  		  }
  			
  			
  			if (lesson.activity != "") {
  			  outputHtml+= "<td class=\"schedule_column\"><button type=\"button\" class=\"btn btn-warning btn-xs tag\" onClick=\"loadSchedule('activity','" + lesson.activity + "')\">" + lesson.activity + "</button></td>";    			
  			} else {
    			 outputHtml+= "<td class=\"schedule_column\"><button type=\"button\" class=\"btn btn-warning btn-xs tag\">(Onbekend)</button></td>";
  			}
  			
  			if (lesson.activitytype != "") {
    		  outputHtml+= "<td class=\"schedule_column\">" + lesson.activitytype + "</td>";	
  			} else {
    			outputHtml+= "<td class=\"schedule_column\">(Onbekend)</td>";
  			}
  		
    		//Read lesson lecturers
    		outputHtml+= "<td class=\"schedule_column\">";
    		if (lesson.lecturers) {
          $.each(lesson.lecturers.split(","), function(index, lecturer) {
            lecturer = lecturer.trim();
            var lecturerstring;
            if (lecturer != "NULL") {
              lecturerstring = "<button type=\"button\" class=\"btn btn-danger btn-xs tag\" onClick=\"loadSchedule('lecturer','" + lecturer + "')\">" + lecturer + "</button>";
            } else {
              lecturerstring = "<button type=\"button\" class=\"btn btn-danger btn-xs tag\">(ONB)</button>";
            }
            outputHtml+= lecturerstring + "&nbsp;";
          });
        } else {
          outputHtml += "Geen docenten";
        }
        outputHtml+= "</td>";
        
        //Read lesson classes
        outputHtml+= "<td class=\"schedule_column\">";
        if (lesson.classes) {
          $.each(lesson.classes.split(","), function(index, tclass) {
            tclass = tclass.trim();
            var classstring;
            if (tclass != "NULL") {
              classstring = "<button type=\"button\" class=\"btn btn-success btn-xs tag\" onClick=\"loadSchedule('class','" + tclass + "')\">" + tclass + "</button>";
            } else {
              classstring = "<button type=\"button\" class=\"btn btn-success btn-xs tag\" onClick=\"#\">(ONB)</button>";
            }
            outputHtml+= classstring + "&nbsp;";;
          });
        } else {
          outputHtml += "Geen klassen";
        }	
        outputHtml+="</td>";
        
        //Read lesson rooms
        outputHtml+= "<td class=\"schedule_column\">";
        if (lesson.rooms) {
          $.each(lesson.rooms.split(","), function(index, room) {
            room = room.trim();
            var roomstring;
            if (room != "NULL") {
              roomstring = "<button type=\"button\" class=\"btn btn-primary btn-xs tag\" onClick=\"loadSchedule('room','" + room + "')\">" + room + "</button>";
            } else {
              roomstring = "<button type=\"button\" class=\"btn btn-primary btn-xs tag\">(ONB)</button>";
            }
            outputHtml+= roomstring + "&nbsp;";;
          });
        } else {
          outputHtml += "Geen lokalen";
        }
        outputHtml+= "</td>";
  		});
  		outputHtml += "</tbody></table>";
  		$('#scheduleplaceholder').append(outputHtml);
  });
}

//Loads a freebusy list from the given api url and parses it in the given target
function loadFreebusyList( type, title, sApiUrl ) {
	$.getJSON( sApiUrl , function( data ) {
		var currentdate = "";
		var buttoncolor = "";
		var cellcolor = "";
		if (type == "lecturer") {
			buttoncolor = "btn-danger";
			cellcolor = "danger";
		} else if (type == "class") {
			buttoncolor = "btn-success";
			cellcolor = "success";
		} else if (type == "room") {
			buttoncolor = "btn-primary";
			cellcolor = "info";
		}
			
		$(".freebusyday").each(function() {
			var tableId = $(this).attr('id') + "_" + title.replace(/[ \.]/g,"");
			var row = "<div class=\"freebusyrow\">" +
			"<table id=\"" + tableId + "\" class=\"table table-bordered\"><tr><td><button type=\"button\" class=\"btn " + buttoncolor + " tag freebusyrow_button\">" + title + "</button></td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td></tr></table>" +
			"</div>";

			$(this).append(row);			
		});
		
		$.each(data.response, function(index, lesson){
			var id = lesson.date.replace(/-/g,"");
			var tableId = id + "_" + title.replace(/[ \.]/g,"");
			var lecturehours = lesson.lecturehours.split(",");
			
			//Loop through lessonhours
			$("#" + tableId + " td").each(function( index ) {
				var lecturehour = "" + (index);
				if ($.inArray(lecturehour, lecturehours) != -1) {
					$(this).addClass(cellcolor);
					$(this).addClass("freebusycell_selected");
					
					$(this).attr("title", lesson.starttime + " - " + lesson.endtime + ": " + lesson.activitytype + " - " + lesson.activity);
				}
			});
			
			currentdate = lesson.date;
		});
	});
}

//Load external file
function loadExt(extType) {
  if (currentScheduleType != "") {
    if (extType == "iCal") {
      window.location = "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".ical";
    } else if (extType == "json") {
      window.location = "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".json";
    } else if (extType == "csv") {
      window.location = "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".csv";
    } 
  } else {
    alert("Kies eerst een rooster");
  }
}

//Copy link to external file to clipboard
function copyExt(extType) {
	// currentScheduleType = "lecturer"
	// currentScheduleId = "rja01"
	if (currentScheduleType != "") {
		if (extType == "qw"){
			var url = "http://quarterweeks.herokuapp.com/" + currentScheduleId;
		} else {
			var url = "http://www.roosterious.nl/";
			if (extType == "iCal") {
		      url += "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".ical";
		  } else if (extType == "json") {
		    url += "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".json";
		  } else if (extType == "csv") {
		    url += "api/schedule/" + currentScheduleType + "/" + currentScheduleId + ".csv";
		  }
		}
  	$('#copyexturl').html(url);
		$('#copyext').modal({});
	} else {
  	alert("Kies eerst een rooster");
	}
}