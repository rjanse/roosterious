$(function() {
    $('#side-menu').metisMenu();
    
    //Attach listeners to buttons
    $("#menu_dashboard").click(function() {
      $("#page-wrapper").load("pages/dashboard.phtml");
      $(".menu").removeClass("active");
      $("#menu_dashboard").addClass("active");
    });
    
    $("#menu_lecturer").click(function() {
      $("#page-wrapper").load("pages/search_lecturer.phtml");
      $(".menu").removeClass("active");
      $("#menu_lecturer").addClass("active");
    });
    
    $("#menu_class").click(function() {
      $("#page-wrapper").load("pages/search_class.phtml");
      $(".menu").removeClass("active");
      $("#menu_class").addClass("active");
    });
    
    $("#menu_room").click(function() {
      $("#page-wrapper").load("pages/search_room.phtml");
      $(".menu").removeClass("active");
      $("#menu_room").addClass("active");
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


//Loads a schedule from the given api url and parses it in the given target
function loadSchedule( sApiUrl, oTarget) {
  $.getJSON( sApiUrl , function( data ) {
  		var row = "";
  		var currentdate = "";
  		var currentweek = "";
  		
  		$.each(data.response, function(index, lesson){
  		  oTarget.html("aap");
  		});
  });
}