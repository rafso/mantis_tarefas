<?php

	/* TODO: Add code here */

?>
	<?
@include($_SERVER['DOCUMENT_ROOT']."/config/log_visitors.inc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
	<?
	$keywords = "DHTML Suite for applications,DHTML Calendar,Javascript calendar,calendar";
	@include($_SERVER['DOCUMENT_ROOT']."/config/metatags.inc");
	?>	
	<title>Demo 1: Calendar</title>
	<link rel="stylesheet" href="css/demos.css" media="screen" type="text/css">
	<style type="text/css">
	/* CSS for the demo. CSS needed for the scripts are loaded dynamically by the scripts */

	
	#mainContainer{
		width:600px;
		margin:0 auto;
		margin-top:10px;
		border:1px double #000;
		padding:3px;

	}
	#calendarDiv,#calendarDiv2{
		width:240px;
		height:240px;
		float:left;
	}
	.clear{
		clear:both;
	}
	</style>	
	<script type="text/javascript" src="plugins/calendar/dhtml-suite-for-applications/js/separateFiles/dhtmlSuite-common.js"></script>
	<script type="text/javascript">
	DHTMLSuite.include("calendar");
	function calendarMonthChange(inputArray)
	{
		var calendarRef = inputArray.calendarRef;
		
		var month = inputArray.month;
		var year = inputArray.year;
		month++;
		if(month>12){
			month=1;
			year++;
		}
		
		var objectToChange = false;
		switch(calendarRef.id)
		{
			case "calendar1":
				objectToChange = myCalendar2;
				break;
			case "calendar2":	
				objectToChange = myCalendar3;
				break;
			case "calendar3":
				month-=3;
				if(month<1){
					month=12 + month;
					year--;	
				}
				objectToChange = myCalendar;
				break;
		}
		objectToChange.setDisplayedMonth(month);
		objectToChange.setDisplayedYear(year);
	}
	</script>

</head>
<body>
	<div id="header">
		<img src="../images/logo.png">
	</div>	
	<p>This calendar widget can be used by either including dhtml-suite-for-applications.js or by including only dhtmlSuite-common.js and by using the
	<a href="/index.html?dhtml-suite-page=dhtmlSuite-include">DHTMLSuite.include()</a> function, i.e. DHTMLSuite.include("calendar")</p>
	<h2>3 connected calendars </h2>
	<div id="calendarDiv"></div>

	<div id="calendarDiv2"></div>
	<div id="calendarDiv3"></div>
	
	<script type="text/javascript">
	
	var myCalendarModel = new DHTMLSuite.calendarModel({ initialYear:2004,initialMonth:5,initialDay:20 });
	myCalendarModel.setLanguageCode('no');
	var myCalendar = new DHTMLSuite.calendar({ id:'calendar1', callbackFunctionOnMonthChange:'calendarMonthChange',displayCloseButton:false,numberOfRowsInYearDropDown:12 } );
	myCalendar.setCalendarModelReference(myCalendarModel);
	myCalendar.setTargetReference('calendarDiv');
	myCalendar.display()
	
	
	var myCalendarModel2 = new DHTMLSuite.calendarModel({ initialYear:2004,initialMonth:6,initialDay:11 });
	myCalendarModel2.setWeekStartsOnMonday(false);
	myCalendarModel2.setLanguageCode('en');
	var myCalendar2 = new DHTMLSuite.calendar({ id:'calendar2', callbackFunctionOnMonthChange:'calendarMonthChange',displayCloseButton:false });
	myCalendar2.setCalendarModelReference(myCalendarModel2);
	myCalendar2.setTargetReference('calendarDiv2');
	myCalendar2.display();
	
	var myCalendarModel3 = new DHTMLSuite.calendarModel({ initialYear:2004,initialMonth:7,initialDay:15 });
	myCalendarModel3.setLanguageCode('en');
	var myCalendar3 = new DHTMLSuite.calendar({ id:'calendar3', callbackFunctionOnMonthChange:'calendarMonthChange',displayCloseButton:false });
	myCalendar3.setCalendarModelReference(myCalendarModel3);
	myCalendar3.setTargetReference('calendarDiv3');
	myCalendar3.display();
	
	</script>
	
	<div class="clear"></div>
	<!-- A DATE PICKER FOR FORMS -->
	
	<h2>A date picker for form</h2>
	<script type="text/javascript">
	var calendarObjForForm = new DHTMLSuite.calendar({minuteDropDownInterval:10,numberOfRowsInHourDropDown:5,callbackFunctionOnDayClick:'getDateFromCalendar',isDragable:true,displayTimeBar:true}); 
	calendarObjForForm.setCallbackFunctionOnClose('myOtherFunction');
	
	function myOtherFunction()
	{
		
		
	}
	function pickDate(buttonObj,inputObject)
	{
		calendarObjForForm.setCalendarPositionByHTMLElement(inputObject,0,inputObject.offsetHeight+2);	// Position the calendar right below the form input
		calendarObjForForm.setInitialDateFromInput(inputObject,'yyyy-mm-dd hh:ii');	// Specify that the calendar should set it's initial date from the value of the input field.
		calendarObjForForm.addHtmlElementReference('myDate',inputObject);	// Adding a reference to this element so that I can pick it up in the getDateFromCalendar below(myInput is a unique key)
		if(calendarObjForForm.isVisible()){
			calendarObjForForm.hide();
		}else{
			calendarObjForForm.resetViewDisplayedMonth();	// This line resets the view back to the inital display, i.e. it displays the inital month and not the month it displayed the last time it was open.
			calendarObjForForm.display();
		}		
	}	
	/* inputArray is an associative array with the properties
	year
	month
	day
	hour
	minute
	calendarRef - Reference to the DHTMLSuite.calendar object.
	*/
	function getDateFromCalendar(inputArray)
	{
		var references = calendarObjForForm.getHtmlElementReferences(); // Get back reference to form field.
		references.myDate.value = inputArray.year + '-' + inputArray.month + '-' + inputArray.day + ' ' + inputArray.hour + ':' + inputArray.minute;
		calendarObjForForm.hide();	
		
	}	
	</script>

	<div id="calendarForForm">
		<form name="myForm">
		<table>
			<tr>
				<td>Select a date:</td>
				<td><input type="text" name="myDate" value="2004-12-24 12:00" onclick=""></td>
				<td><input type="button" value="Pick date" onclick="pickDate(this,document.forms[0].myDate);"></td>
			</tr>

			<tr>
				<td>Select a date:</td>
				<td><input type="text" name="myDate2" value="2004-12-24 12:00" onclick=""></td>
				<td><input type="button" value="Pick date" onclick="pickDate(this,document.forms[0].myDate2);"></td>
			</tr>
			<tr>
				<td colspan="3"><select style="width:300px"><option value="">This calendar covers select boxes</option><option value="">This calendar covers select boxes</option></select>

			</td>
		</table>
		</form>
	</div>
	
	<h2>A calendar where you only can select dates in 2004</h2>
	<div id="calendarDiv4"></div>
	<p>This is done by adding invalid date ranges: </p>
	<pre>

	myCalendarModel5.addInvalidDateRange(false,{year: 2003,month:12,day:31});
	myCalendarModel5.addInvalidDateRange({year: 2005,month:1,day:1},false);
	</pre>
	<script type="text/javascript">
	
	var myCalendarModel5 = new DHTMLSuite.calendarModel({ initialYear:2004,initialMonth:5,initialDay:20 });
	myCalendarModel5.addInvalidDateRange(false,{year: 2003,month:12,day:31});
	myCalendarModel5.addInvalidDateRange({year: 2005,month:1,day:1},false);
	myCalendarModel5.setLanguageCode('en');
	var myCalendar5 = new DHTMLSuite.calendar({ id:'calendar4',displayCloseButton:false,numberOfRowsInYearDropDown:12 } );
	myCalendar5.setCalendarModelReference(myCalendarModel5);
	myCalendar5.setTargetReference('calendarDiv4');
	myCalendar5.display();
	</script>
		

<?
@include($_SERVER['DOCUMENT_ROOT']."/config/kontera.php");
?>	
</body>
</html>