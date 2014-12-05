<?php
	error_reporting(E_ALL);
?>
<html>
<head>
	<title>Homework #6</title>
	<meta charset="UTF-8">
	</head>
	<style>
	.outer_block {
		margin-left: 32%;
		margin-bottom: 30px; 
		text-align: center;
    	width: 490px;
	}
	
	#inner_block {
		border-style: solid; 
		text-align: left; 
		padding-top: 10px; 
		padding-bottom: 10px;
		padding-left: 5px;
		padding-right: 5px;
		margin-left: 10px;
		margin-right: 10px;
		margin-bottom: 10px;
		font-size: 18px;
	}
	
	#inputTextbox {
		width: 250px;
		height: 22px;
	}

	#submitButton{
		height: 22px;
	}

	#example {
		padding-top: 10px;
		font-style: italic;
	}

	.result_header1{
		font-size: 1.5em; 
		font-weight: bolder;
	}

	.result_header2{
		font-size: 19px;
		margin-left: 5px;
		margin-right: 5px;
	}

	/* style for stock quote table */ 
	.quoteStyle{
		font-size: 17px;
		width: 100%;
	}

	.underline {
		border: 0;
		color: black;
		background-color: black;
		height: 4px;
		margin-top: 0;
	}

	.blankcol{
		width: 30px;
	}

	.quotediv{
		margin-left: 24%;
		margin-right: 24%;
		margin-bottom: 10px;
		min-width: 720px;
	}

	.newsdiv{
		margin-left: 24%;
		margin-right: 24%;
		min-width: 720px;
	}

	li {
		font-size: 17px;
		margin-bottom:3px
	}



	</style>
</head>
<body onload='window.resizeTo(1024,720)'>

	
	<div class="outer_block">
		<form id="form1" method="get">
			<h2>Market Stock Search</h2>

			<div id="inner_block">
			Company Symbol: <input type="text" name="inputSym" id="inputTextbox">
			<input type="submit" value="Search" name="submit">
			
			<div id="example">Example: GOOG, MSFT, YHOO, FB, AAPL, ...etc</div>
			</div>
		</form> 
	</div>


<?php 

	function requestStock(){

			//request stock quote XML
            $request_stock =	'http://query.yahooapis.com/v1/public/yql?q=Select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20'.
  								'PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C%20Open%2C%20YearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20'.
  								'OneyrTargetPrice%2C%20MarketCapitalization%2C%20Volume%2C%20Open%2C%20YearLow%20from%20yahoo.finance.quotes%20where%20symbol%3D%22'.
  								$_GET['inputSym'].'%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

			// Make the request
			$stockXml = file_get_contents($request_stock);

			// Retrieve HTTP status code
			list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);

			// Check the HTTP Status code
			switch($status_code) {
				case 200:
			// Success
				break;
			case 503:
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 503. That means: Service unavailable. An internal problem prevented us from returning data to you.');
				break;
			case 403:
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 403. That means: Forbidden. You do not have permission to access this resource, or are over your rate limit.');
				break;
			case 400:
				// You may want to fall through here and read the specific XML error
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 400. That means:  Bad request. The parameters passed to the service did not match as expected. The exact error is returned in the XML response.');
				break;
			default:
				die('Your call to Yahoo Web Services returned an unexpected HTTP status of:' . $status_code);
			}

			return $stockXml;
	}

	function requestFeed(){

			//request rss feeds XML
			$request_feed =	'http://feeds.finance.yahoo.com/rss/2.0/headline?s='.$_GET['inputSym'].'&region=US&lang=en-US';

			$feedXml = file_get_contents($request_feed);
			
			list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);

			// Check the HTTP Status code
			switch($status_code) {
				case 200:
			// Success
				break;
			case 503:
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 503. That means: Service unavailable. An internal problem prevented us from returning data to you.');
				break;
			case 403:
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 403. That means: Forbidden. You do not have permission to access this resource, or are over your rate limit.');
				break;
			case 400:
				// You may want to fall through here and read the specific XML error
				die('Your call to Yahoo Web Services failed and returned an HTTP status of 400. That means:  Bad request. The parameters passed to the service did not match as expected. The exact error is returned in the XML response.');
				break;
			default:
				die('Your call to Yahoo Web Services returned an unexpected HTTP status of:' . $status_code);
			}

			return $feedXml;
	}
	

	if(isset($_GET['submit'])){
		if($_GET['inputSym'] == ""){
			echo "<script type='text/javascript'>alert(\"Please enter a company symbol\");</script>";
		}
		else {
			
			$stock = requestStock();
			$query = new SimpleXMLElement($stock);

			//get stock quote value from each tag
			$bid = $query -> results -> quote -> Bid;
			$change = $query -> results -> quote -> Change;
			$dayslow = $query -> results -> quote -> DaysLow;
			$dayshigh = $query -> results -> quote -> DaysHigh;
			$yearlow = $query -> results -> quote -> YearLow;
			$yearhigh = $query -> results -> quote -> YearHigh;
			$cap = $query -> results -> quote -> MarketCapitalization;
			$last = $query -> results -> quote -> LastTradePriceOnly;
			$name = $query -> results -> quote -> Name;
			$open = $query -> results -> quote -> Open;
			$close = $query -> results -> quote -> PreviousClose;
			$percent = $query -> results -> quote -> ChangeinPercent;
			$symbol = $query -> results -> quote -> Symbol;
			$target = $query -> results -> quote -> OneyrTargetPrice;
			$vol = $query -> results -> quote -> Volume;
			$ask = $query -> results -> quote -> Ask;
			$avg = $query -> results -> quote -> AverageDailyVolume;

			if($change == ""){
				echo "<h2 style=text-align:center>Stock Information Not Available</h2>";
			}
			else {

				//handle change and percentinchange with arrow picture 
				//down arrow http://www-scf.usc.edu/~csci571/2014Spring/hw6/down_r.gif
				//up arrow http://www-scf.usc.edu/~csci571/2014Spring/hw6/up_g.gif
				$sign = substr($change, 0, 1);
				switch ($sign) {
					case '-':
						$new_change = substr($change, 1);
						$format_change = number_format(floatval($new_change),2);
						$format_perc = substr($percent, 1);
						$all_change = '<span class=result_header2 style=color:red>'
						.'<img src=http://www-scf.usc.edu/~csci571/2014Spring/hw6/down_r.gif>'.$format_change
						.'('.$format_perc.')</span>';

						break;

					case '+':
						$new_change = substr($change, 1);
						$format_change = number_format(floatval($new_change),2);
						$format_perc = substr($percent, 1);
						$all_change = '<span class=result_header2 style=color:green>'
						.'<img src=http://www-scf.usc.edu/~csci571/2014Spring/hw6/up_g.gif>'.$format_change
						.'('.$format_perc.')</span>';
						break;
					
					default:
						$format_change = number_format(floatval($change),2);
						$all_change ='<span class=result_header2 style=color:green>'.$format_change
						.'('.$percent.')</span>';
						break;
				}

				
				//handle number format with 2 decimal and blank value
				if ($last == ""){
					$format_last = $last;
				}
				else{
					$format_last = number_format(floatval($last),2);
				}
				
				if ($close == ""){
					$format_close = $close;
				}
				else {
					$format_close = number_format(floatval($close),2);
				}

				if ($open == ""){
					$format_open = $open;
				}
				else {
					$format_open = number_format(floatval($open),2);
				}
				
				if ($bid == "") {
					$format_bid = $bid;
				}
				else {
					$format_bid = number_format(floatval($bid),2);
				}
				
				if ($ask == ""){
					$format_ask = $ask;
				}
				else {
					$format_ask = number_format(floatval($ask),2);
				}

				if ($target == ""){
					$format_target = $target;
				}
				else {
					$format_target = number_format(floatval($target),2);
				}
				
				if ($dayslow == ""){
					$format_dlow = $dayslow;
				}
				else {
					$format_dlow = number_format(floatval($dayslow),2);
				}

				if ($dayshigh == ""){
					$format_dhigh = $dayshigh;
				}
				else {
					$format_dhigh = number_format(floatval($dayshigh),2);
				}

				if ($yearlow == ""){
					$format_ylow = $yearlow;
				}
				else {
					$format_ylow = number_format(floatval($yearlow),2);
				}

				if ($yearhigh == ""){
					$format_yhigh = $yearhigh;
				}
				else {
					$format_yhigh = number_format(floatval($yearhigh),2);
				}


				//handle number format with no decimal (volume and average volume)
				if ($vol == ""){
					$format_vol = $vol;
				}
				else {
					$format_vol = number_format(floatval($vol));
				}

				if ($avg ==""){
					$format_avg = $avg;
				}
				else{
					$format_avg = number_format(floatval($avg));
				}

				//handle regular range value and blank range value
				// days range
				if ($dayslow == "" && $dayshigh == ""){
					$drange = "";
				}
				else {
					$drange = $format_dlow.' - '.$format_dhigh;
				}
				// year range
				if ($yearlow == "" && $yearhigh == ""){
					$yrange = "";
				}
				else{
					$yrange = $format_ylow.' - '.$format_yhigh;
				}
				

				//search results title
				echo "<h2 class=outer_block>Search Results</h2>";
				//Company quote headline
				echo '<div class=quotediv>';
				
				echo "<span class=result_header1>".$name."(".$symbol.")</span><span class=result_header2>".$format_last;
				echo $all_change;
				echo "<hr class=underline>";
				
				//Stock quote info
				echo '<table class=quoteStyle>';
				echo '<tr><td>Prev Close:</td><td style=text-align:right>'.$format_close.'</td><td class=blankcol></td><td>Day&#39s Range:</td><td style=text-align:right>'.$drange.'</td>';
				echo '<tr><td>Open:</td><td style=text-align:right>'.$format_open.'</td><td class=blankcol></td><td>52wk Range:</td><td style=text-align:right>'.$yrange.'</td>';
				echo '<tr><td>Bid:</td><td style=text-align:right>'.$format_bid.'</td><td class=blankcol></td><td>Volume:</td><td style=text-align:right>'.$format_vol.'</td>';
				echo '<tr><td>Ask:</td><td style=text-align:right>'.$format_ask.'</td><td class=blankcol></td><td>Avg Vol (3m):</td><td style=text-align:right>'.$format_avg.'</td>';
				echo '<tr><td>1y Target Est:</td><td style=text-align:right>'.$format_target.'</td><td></td><td>Market Cap:</td><td style=text-align:right>'.$cap.'</td>';
				echo '</table>';
				echo "</div>";


				
				$feeds = requestFeed();
				$news = new SimpleXMLElement($feeds);

				if ($news -> channel -> title == 'Yahoo! Finance: RSS feed not found'){
					echo "<h2 style=text-align:center>Financial company news is not available</h2>";
				}
				else {
					//div class newsStyle
					echo "<div class=newsdiv><span class=result_header1>News Headlines</span>";
					echo "<hr class=underline>";
					echo "<ul>";

					foreach ($news -> channel -> item as $item){
						//get news feed value from each tag
						$title = $item -> title;
						$link = $item -> link;

						//prepare a link
						list($rss, $href) = explode("*", $link);
						echo "<li><a href=".$href." target=_blank>".$title."</a></li>";
					}

					echo "</ul></div>";

				}

			}


		}

	}
	
?>
<noscript>
</body>
</html>