<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Roosterious</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="css/plugins/morris.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="css/plugins/select2/select2.css" rel="stylesheet">
    <link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">
	
	<link href="css/plugins/bootstrap-datetimepicker.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="css/roosterious.css" rel="stylesheet">
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Roosterious v0.5beta</a>
            </div>
            <!-- /.navbar-header -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <!--
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                        </li>
                        -->
                        <li>
                            <a class="active menu" href="#" id="menu_dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                         &nbsp;
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_lecturer"><i class="fa fa-user fa-fw"></i> Docent</a>
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_class"><i class="fa fa-users fa-fw"></i> Klas</a>
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_room"><i class="fa fa-building fa-fw"></i> Lokaal</a>
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_activity"><i class="fa fa-graduation-cap fa-fw"></i> Activiteit</a>
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_datetime"><i class="fa fa-clock-o fa-fw"></i> Datum en tijd</a>
                        </li>
                        <li>
                         &nbsp;
                        </li>
                        <li>
                            <a href="#" class="menu" id="menu_freebusytool"><i class="fa fa-calendar-o fa-fw"></i> Planningtool</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

	<!-- Modal for external schedule -->
	<div class="modal fade" id="copyext" tabindex="-1" role="dialog" aria-labelledby="loadextlabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	        <h4 class="modal-title" id="loadextlabel">Roosterious in een extern programma</h4>
	      </div>
	      <div class="modal-body">
	        <p>Kopieer onderstaande link  in het programma waarin je het rooster wilt gebruiken (b.v. Apple Calendar):</p>
	        <div id="copyexturl">
		        
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Sluit</button>
	      </div>
	    </div>
	  </div>
	</div>

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    
    <!-- Select2 Javascript -->
    <script src="js/plugins/select2/select2.min.js"></script>
    <script src="js/plugins/select2/select2_locale_nl.js"></script>
    
    <script src="js/plugins/bootstrap-datetimepicker.min.js"></script>
          
    <!-- Morris Charts JavaScript -->
    <!--
    <script src="js/plugins/morris/raphael.min.js"></script>
    <script src="js/plugins/morris/morris.min.js"></script>
    <script src="js/plugins/morris/morris-data.js"></script>
    -->

    <!-- Custom Theme JavaScript -->
    <script src="js/roosterious.js"></script>
</body>

</html>
