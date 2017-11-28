<?php
  //retrieve json data
	$strURL = 'http://api.openweathermap.org/data/2.5/forecast/daily?zip=78613,us&appid=##APPID##&cnt=16&units=imperial'; 
	$json_data = file_get_contents($strURL);
	$json_array = json_decode($json_data,true);

	$cityData = $json_array['city'];
	$strCityName = $cityData['name'];
	$strCount = $json_array['cnt'];

	$myfile = fopen("googleweathercal.ics", "w") or die("Unable to open file!");
	fwrite($myfile,"BEGIN:VCALENDAR" . "\r\n");
	fwrite($myfile,"VERSION:2.0" . "\r\n");
	fwrite($myfile,"CALSCALE:GREGORIAN" . "\r\n");
	fwrite($myfile,"PRODID:-CALWEATHER" . "\r\n");
	fwrite($myfile,"X-WR-CALNAME:" . $strCityName . " Weather" . "\r\n");
	fwrite($myfile,"X-WR-TIMEZONE:America/Chicago" . "\r\n");
	fwrite($myfile,"X-WR-CALDESC:Displays the Forecast for ". $strCount . " days." . "\r\n");
	
  
	$dtTime = new DateTime('now'); //get current time\
	$strCurTime = $dtTime->format("Ymd");
	$intCount = 0;
	
	$weather_nodes = $json_array['list'];
	
  foreach($weather_nodes as $node) {
		
		$strStartTime = $dtTime->format("Ymd");
		$dtTime->modify("+1 day");
		$strEndTime = $dtTime->format("Ymd");
		//dump weather data into variables
		$nTemp = $node['temp'];
		$nWeather = $node['weather'];
		$nWeatherData = $nWeather[0];
		
		$strMainCondition = $nWeatherData['main'];
		$strCondition = $nWeatherData['description'];
		$strIcon = 'http://openweathermap.org/img/w/' . $nWeatherData['icon'] . '.png';
		$strHumidity = $node['humidity'] . '%';
		$strPressure = $node['pressure'] . ' hPa';
		$fltWindDegValue = floatval($node['deg']);	
		$strWindSpeed = $node['speed'] . ' mph';
		
		If (($fltWindDegValue >= 0 AND $fltWindDegValue < 11.25) || $fltWindDegValue >= 348.75)
			$strWindDegCar = "N";
		Else If ($fltWindDegValue >= 11.25 AND $fltWindDegValue < 33.75)
			$strWindDegCar = "NNE";
		Else If ($fltWindDegValue >= 33.75 AND $fltWindDegValue < 56.25)
			$strWindDegCar = "NE";
		Else If ($fltWindDegValue >= 56.25 AND $fltWindDegValue < 78.75)
			$strWindDegCar = "ENE";
		Else If ($fltWindDegValue >= 78.75 AND $fltWindDegValue < 101.25)
			$strWindDegCar = "E";
		Else If ($fltWindDegValue >= 101.25 AND $fltWindDegValue < 123.75)
			$strWindDegCar = "ESE";
		Else If ($fltWindDegValue >= 123.75 AND $fltWindDegValue < 146.25)
			$strWindDegCar = "SE";
		Else If ($fltWindDegValue >= 146.25 AND $fltWindDegValue < 168.75)
			$strWindDegCar = "SSE";
		Else If ($fltWindDegValue >= 168.75 AND $fltWindDegValue < 191.25)
			$strWindDegCar = "S";
		Else If ($fltWindDegValue >= 191.25 AND $fltWindDegValue < 213.75)
			$strWindDegCar = "SSW";
		Else If ($fltWindDegValue >= 213.75 AND $fltWindDegValue < 236.25)
			$strWindDegCar = "SW";
		Else If ($fltWindDegValue >= 236.25 AND $fltWindDegValue < 258.75)
			$strWindDegCar = "WSW";
		Else If ($fltWindDegValue >= 258.75 AND $fltWindDegValue < 281.25)
			$strWindDegCar = "W";
		Else If ($fltWindDegValue >= 281.25 AND $fltWindDegValue < 303.75)
			$strWindDegCar = "WNW";
		Else If ($fltWindDegValue >= 303.75 AND $fltWindDegValue < 326.25)
			$strWindDegCar = "NW";
		Else If ($fltWindDegValue >= 326.25 AND $fltWindDegValue < 348.75)
			$strWindDegCar = "NNW";
		Else
			$strWindDegCar = "ERROR";
		
		$strMaxTemp = $nTemp['max'];	
		$strMinTemp = $nTemp['min'];
		$strSummary = $strMaxTemp . "F | " . $strMinTemp . "F [" . $strMainCondition . "]";
    $strDescription = 'Max Temp: ' . $strMaxTemp . "\r\n";
		$strDescription = $strDescription . 'Min Temp: ' . $strMinTemp . "\r\n";
		$strDescription = $strDescription . 'Condition: ' . $strMainCondition . "\r\n";
		$strDescription = $strDescription . 'Humidity: ' . $strHumidity . "\r\n";
		$strDescription = $strDescription . 'Pressure: ' . $strPressure . "\r\n";
		$strDescription = $strDescription . 'Wind Speed: ' . $strWindSpeed . "\r\n";
		$strDescription = $strDescription . 'Direction: ' . $strWindDegCar . "\r\n";
		
		fwrite($myfile,"BEGIN:VEVENT" . "\r\n");
		fwrite($myfile,"UID:" . $strCurTime . $intCount . "\r\n");
		//fwrite($myfile,"SEQUENCE:0\r\n");
		fwrite($myfile,"SUMMARY:" . $strSummary . "\r\n");
		//fwrite($myfile,"CREATED:" . $strCurTime . "\r\n");
		fwrite($myfile,"DTSTART;VALUE=DATE:" . $strStartTime . "\r\n");
		fwrite($myfile,"DTEND;VALUE=DATE:" . $strEndTime . "\r\n");
		fwrite($myfile,"DESCRIPTION:" . $strDescription . '\r\n');
    fwrite($myfile,"X-GOOGLE-CALENDAR-CONTENT-DISPLAY:chip \r\n");
		fwrite($myfile,"X-GOOGLE-CALENDAR-CONTENT-ICON:" . $strIcon . "\r\n");
		fwrite($myfile,"SEQUENCE:" . $intCount . "\r\n");
		fwrite($myfile,"END:VEVENT" . "\r\n");
		
    $intCount = $intCount  + 1;
  }
	
	fwrite($myfile,"END:VCALENDAR" . "\r\n");
	fclose($myfile);
?>
