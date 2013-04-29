$(document).ready(function(){

    $('.text-error').hide();
    $('#getchartbut').hide();

    $("#getpricebut").click(function() {
        $('.text-error').hide();
        // Validate symbol and data user inputs
        // First validate symbol
        var symbols = ['spy', 'vxf', 'vtv', 'bsv', 'vo', 'vot', 'vti'];
        var symbol = $("input#symbol").val();
        if (symbols.indexOf(symbol.toLowerCase()) == -1) {
            $(".text-error#symbol_error").show();
            $("input#symbol").focus();
            return false;
        }

        var date = $("input#date").val();
        // Next validate date
        var dates = ['2007-05-30', '2007-05-31', '2007-06-01'];
        var date = $("input#date").val();
        if (dates.indexOf(date) == -1) {
            $(".text-error#date_error").show();
            $("input#date").focus();
            return false;
        }

        $('#getchartbut').show();
        $("#chartsymbol").val(symbol);
        var dataString = 'symbol='+ symbol + '&date=' + date;
        // alert (dataString);
        $.ajax({
            type: "POST",
            url: "scripts/index_slave9.php",
            data: dataString,
            dataType: 'json',
            success: function(data) {
                var open = Number(data[0]); //get Open. Need to convert from string (sourced from DB) to number
                var high = Number(data[1]); //get High
                var low = Number(data[2]); //get Low
                var last = Number(data[3]); //get Last
                var volume = data[4]; //get Volume
                volume = numberWithCommas(volume);
                var changeFromLastClose = data[5]; //get ChangeFromLastClose
                changeFromLastClose = changeFromLastClose.replace('-', '&#8722;'); // Reformat hyphen into minus sign
                var percentChangeFromLastClose = data[6]; //get PercentChangeFromLastClose
                percentChangeFromLastClose = percentChangeFromLastClose.replace('-', '&#8722;');
                var currency = data[7]; //get ETF fund name
                var name = data[8]; //get ETF fund name
                var symbol = data[9]; //get ETF fund name
                // Update html content
                $('#output').html("<h3>" + name + "</h3>" +
                    "<strong>Last: </strong>" + decPlacesRound(last, 2) + "<br />" +
                    "<strong>Open: </strong>" + decPlacesRound(open, 2) + "<br />" +
                    "<strong>High: </strong>" + decPlacesRound(high, 2) + "<br />" +
                    "<strong>Low: </strong>" + decPlacesRound(low, 2) + "<br />" +
                    "<strong>Volume: </strong>" + volume + "<br />" +
                    "<strong>Change from last close: </strong>" + changeFromLastClose + "<br />" +
                    "<strong>Change from last close: </strong>" + percentChangeFromLastClose + "&#37;<br />" +
                    "<small>[Price data in " + currency + "]</small>"
                );
            }
        });
        return false;

    });

    $("#getchartbut").click(function() {
        var chartsymbol = $("input#chartsymbol").val();
        var dataString2 = 'chartsymbol='+ chartsymbol;
        //alert (dataString2);
        $.ajax({
            type: "POST",
            url: "scripts/chart_slave.php",
            data: dataString2,
            dataType: 'json',
            cache: false,
            success: function(rows) {
                var row = new Array();
                var candles = new Array();

                for (var i=0; i < rows.length; i++) {
                    candles[i] = new Array();
                    candles[i].push(i);
                }

                var i = 0; // Reset i counter
                for (var j in rows) {
                    var row = rows[j];

                    var date = row['Date'];
                    var open = Number(row['Open']); // Need to convert from string (sourced from DB) to number
                    var high = Number(row['High']);
                    var low = Number(row['Low']);
                    var close = Number(row['Close']);
                    var symbol = row['Symbol'];
                    var symbol_uc = symbol.toUpperCase();
                    var currency = row['Currency'];
                    candles[i].push(open, high, low, close);
                    i = i + 1;
                }
                //dump(candles);
                    chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'container',
                        width: 400
                    },
                    title: {
                        text: symbol_uc + ' Prices (OHLC)'
                    },
                    xAxis: {
                        categories: ['May 30, 2007', 'May 31, 2007', 'June 1, 2007'], // Hard-coded for now but could easily pull from DB
                        min:0,
                        max:2
                    },
                    yAxis: {
                        title: {
                            text: null,
                            rotation: 270,
                            align: 'middle'
                        }
                    },
                    plotOptions: {
                        candlestick: {
                           color: 'white',
                           upColor: '#1976D4'
                        }
                    },                    navigator: {
                        enabled: false
                    },
                    legend: {
                        enabled: false
                    },
                    series: [{
                        type: 'candlestick',
                        name: symbol,
                        data: [
                            candles[0],
                            candles[1],
                            candles[2]
                        ]
                    }]
                });

            }
        });
        return false;

    });

}); // End of (document).ready()

/* Function to format integer with commas e.g. from 10765432 to 10,765,432. Courtesy: http://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript */
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/* Function to format price data ("num") to force "dp" (an integer, e.g. two) decimal places with round up (e.g. 1.341 -> 1.34; 1.345 -> 1.34; and 1 -> 1.00). Courtesy: http://stackoverflow.com/questions/6134039/format-number-to-always-show-2-decimal-places */
function decPlacesRound(num, dp) {
    return parseFloat(Math.round(num * 100) / 100).toFixed(dp);
;
}

/* A JS-equivalent of PHP var_dump, courtesy: http://stackoverflow.com/questions/323517/is-there-an-equivalent-for-var-dump-php-in-javascript */
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);

    // or, if you wanted to avoid alerts...

    //var pre = document.createElement('pre');
    //pre.innerHTML = out;
    //document.body.appendChild(pre)
}
