<!DOCTYPE HTML>
<html>
<head>
	<title>ETF World | Design Notes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="css/bootstrap-cosmo.min.css" rel="stylesheet" media="screen">
    <link href="css/custom.css" rel="stylesheet" media="screen">

    <script src="library/my_jquery_functions.js"></script>
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
                    <li><a href="index.php">Home&nbsp;&nbsp;</a></li>
                    <li class="active"><a href="notes.php">Design Notes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row-fluid">

        <div class="span12">
            <!--Body content-->
            <section>

                <h1>Design Notes</h1>

                <h2>Summary</h2>

                <ol>
                    <li>Responsive UI</li>
                    <li>AJAX interface for data retrieval from MySQL database</li>
                    <li>Integration of XML Web Service (RSS news feed)</li>
                    <li>Frameworks and utilities used: jQuery, Bootstrap, and Modernizr</li>
                </ol>

                <h2>Database Schema</h2>

                <ol>
                    <li>I decided to create two tables. `etf_descriptors_table` stores the "static" (i.e.
                        unchanging) ETF data such as the funds&rsquo; Symbol, Cusip, Name,
                        and Market. `etf_prices_table` stores the dynamic price data in columns such as Symbol (a
                        foreign key of `etf_descriptors_table`), Date, Last, Open, Volume, etc.</li>
                    <li>In a production environment, where the quantity of dynamic data would quickly escalate
                        further, I'd likely adopt a schema that further separates price data into one price data
                        table for each symbol in order to reduce data read times.</li>
                </ol>

                <h2>Data Import and Upload</h2>

                <ol>
                    <li>I imported and uploaded the data manually via a one-time execution of
                        <code>scripts/importandupload.php</code>. In a production environment, I'd set up a regular schedule for this task via a cronjob.</li>
                    <li>With the exception of specifying the names of the .json source files,
                        <code>scripts/importandupload.php</code> is written for the general case of <em>n</em> ETFs over <em>m</em> days, rather than the specific case of 7 funds over 3 days.
                    </li>
                    <li>I looked into whether it would be faster to do a bulk INSERT or a LOAD DATA INFILE. An
                        interesting <a href="http://www.mediabandit.co.uk/blog/215_mysql-bulk-insert-vs-load-data" target="_blank">benchmark result</a> suggested the former,
                        which I used also. (<a href="http://stackoverflow.com/questions/3096785/import-bulk-data-into-mysql" target="_blank">Stackoverflow</a> has another interesting discussion on the topic.) With more time,
                        I&rsquo;d run my own test. </li>
                </ol>

                <h2>User Interface</h2>

                <ol>
                    <li>To speed development, I chose a fluid design format using Bootstrap,
                        which I customized to create a simple but unique brand. With more time,
                        I&rsquo;d have done a responsive design.</li>
                    <li>To enhance the speed of the user experience and eliminate a page refresh,
                        I used AJAX (via jQuery) when posting a database
                        query.
                    </li>
                </ol>

                <h2>Additional Enhancements/Features</h2>

                <p>My application could benefit from various enhancements and features. With more time,
                    I would add...</p>
                <ol>
                    <li>Dynamic charting, perhaps using JpGraph or the HTML5 canvas. [<span
                            class='text-error'>Update: </span>I&rsquo;ve since implemented this feature
                        for candlestick charts using AJAX, jQuery, and the <a target="_blank" href="http://www.highcharts.com/products/highstock">Highstock</a> JavaScript library.]
                    </li>
                    <li>Implement <a href="http://dev.markitondemand.com/" target="_blank">market data APIs</a> for features such as company look up and real-time quotes.</li>
                    <li>The application currently uses hard-coded client-side validation of user input (in <code>index.php</code>),
                        rejecting price quotes for ETFs outside the 3-day and 7-symbol availability frame. I could
                        use AJAX to determine the scope of data availability and code the JavaScript data validation
                        dynamically.
                    </li>
                    <li>Data validation on the server-side (<code>scripts/index_slave.php</code>) is currently
                        limited to protect against SQL-injection attacks on the database as well as cross-site
                        scripting.</li>
                </ol>

                <h2>Credits</h2>

                <ol>
                    <li>I had help from <a href="http://www.php.net/manual/en/class.recursivearrayiterator.php" target="_blank"> stackoverflow</a> in applying PHP's <a href="http://www.php.net/manual/en/class.recursivearrayiterator.php" target="_blank">Recursive Array Iterator</a> class.</li>
                </ol>

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
