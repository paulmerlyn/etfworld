<!DOCTYPE HTML>
<html>
<head>
	<title>ETF World | A Programming Assignment for Aspiring Nerds</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="css/bootstrap-cosmo.min.css" rel="stylesheet" media="screen">
    <link href="css/custom.css" rel="stylesheet" media="screen">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">
    </script>

    <script src="highstock/js/highstock.js" type="text/javascript"></script><!--Combines High Charts and High Stock; where you put the high charts library -->
    <script src="highstock/js/modules/exporting.js" type="text/javascript"></script>

    <script src="library/my_jquery_functions.js"></script><!-- _test11.js for dev; rotate with _test9.js -->

    <script src="js/modernizr.custom.54822.js"></script>
    <script>
		yepnope({
			test:Modernizr.input.placeholder,
			nope:'h5f.js',
			callback: function(url, result, key) {
			H5F.setup(document.getElementById("historicpriceform"))
			}
		});

        Modernizr.load({
            test: Modernizr.inputtypes.date,
            nope: ['http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js', 'jquery-ui.css'],
            complete: function () {
                $('input[type=date]').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }
        });
    </script>

</head>


<body>

<div class="container-fluid">

    <header>
    <div class="row-fluid">
        <div class="span12">
            <header id="masthead">
                <img src="images/banner.jpg" width="960" height="148" alt="trading banner">
            </header>
        </div>
    </div>
    </header>

    <nav>
        <div class="navbar pull-right">
            <div class="navbar-inner">
                 <ul class="nav">
                    <li class="active"><a href="index.php">Home&nbsp;</a></li>
                    <li><a href="notes.php">&nbsp;Design Notes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row-fluid">
        <div class="span5">
            <!--Sidebar content-->
            <aside>
            <?php
            $request = "http://feeds.finance.yahoo.com/rss/2.0/category-etfs?region=US&lang=en-US&count=5";
            $response = file_get_contents($request);
            $xml = simplexml_load_string($response);
            echo '<h2>Breaking ETF News</h2>';
            echo '<h4><em>From Yahoo! News</em></h4>';
            $storycount = 0; /* I couldn't find documentation on how to limit the number of stories in the rss
                request, so I rigged up this workaround using a counter variable. */
            foreach($xml -> channel -> item as $story) {
                echo "<a href='".$story -> link."' target='_blank'>".$story -> title."</a><br />";
                echo "<p>".$story -> description."</p><br /><br />";
                ++$storycount;
                if ($storycount == 5) break;
            }
            ?>
            </aside>
            <!--End of sidebar content-->
        </div>

        <div class="span7">
            <!--Body content-->
            <section>

                <h1>ETF Price Data</h1>

                <div style="position: relative;">
                <form id="historicpriceform" class="form-inline form-horizontal">
                    <div class="control-group">
                        <input id="symbol" class="input-small" type="text" name="symbol" placeholder="Enter symbol"
                               required autofocus>

                        <div class="input-append">
                            <input id="date" class="input-medium" type="date" name="date" placeholder="Pick a date" required>
                            <button type="submit" id="getpricebut" class="btn btn-primary"><i
                                        class="icon-search icon-white"></i></button>
                        </div>
                    </div>
                </form>

                <form id="getchartform" class="form-inline form-horizontal">
                    <input type='hidden' name='chartsymbol' id='chartsymbol'>
                    <button type='submit' id='getchartbut' class='btn btn-primary' value='Chart It' style='display:
                    none;'>
                        <img class="icon-white" src='img/glyphicons_041_charts-white.png'>&nbsp;&nbsp;Chart</button><!-- I had to make the inverted (i.e. white) version of this charts icon b/c it
                         wasn't part of the default Bootstrap glyphicons set.  -->
                </form>
                </div>

                <p class="text-error" id="symbol_error" style='display: none;'>Sorry, we don't have data for that ETF symbol. Please try again.</p>
                <p class="text-error" id="date_error" style='display: none;'>Sorry, we don't have data for that particular date.</p>

                <div id="output"></div>
                <div id="container"></div>
            </section>
            <!--Body content-->

        </div>

    </div>

    <footer>
    <p>&copy <?php echo date("Y"); ?> Paul Merlyn | Aspiring Nerd, Inc.</p>
    </footer>

</div>

<script src="js/bootstrap.min.js"></script>

<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
    var sc_project=8892417;
    var sc_invisible=1;
    var sc_security="36c21dfa";
    var scJsHost = (("https:" == document.location.protocol) ?
        "https://secure." : "http://www.");
    document.write("<sc"+"ript type='text/javascript' src='" +
        scJsHost+
        "statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript><div class="statcounter"><a title="hit counter"
                                      href="http://statcounter.com/" target="_blank"><img
                class="statcounter"
                src="http://c.statcounter.com/8892417/0/36c21dfa/1/"
                alt="hit counter"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->

</body>


</html>
