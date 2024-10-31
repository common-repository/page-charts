=== Page Charts ===
Contributors: sidov 
Tags: highcharts, chart, line, pie, column, polygon, funnel, combo
Requires at least: 5.8.3
Tested up to: 6.0
Stable tag: 1.0
License: GPLv2 or later

This plagin lets add charts of some libraries to any page or post of your site.
Now you can use charts of Chart and Highcharts softwares.
This plagin does not contain any libraries.
You should include library in the "js" directiory of the plagin and use it in accordance with their license. 
This plugin does NOT substitute HighCharts terms of use.

== Description ==
This plagin lets you to embed charts of Chart and Highcharts softwares on any page or post of your site.
For this you should include one of above mentioned libraries in the "js" directiory of this plagin.	
For every chart you must prepare two files : one of them it's a javascript file with the Highchart object, 
other one it's a php file with data for chart. Interface between php and js represents array with the name "data".
To this array you can include all variables and arrays necessary for the Highchart object.
	
	For example in the php file:
	$data= [['name'=> 'Installation','data'=> [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]], 
		[
        'name'=> 'Manufacturing',
        'data'=> [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
    ], [
        'name'=> 'Sales & Distribution',
        'data'=> [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
    ], [
        'name'=> 'Project Development',
        'data'=> [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
    ], [
        'name'=> 'Other',
        'data'=> [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
    ]];
	
	In the javascript file:
	
	new Chart(document.getElementById("container"), {
	...
	  data: data
	...  
	}
	
If you want to use static data right in the js you should prepare a php file with void array "data".
All js files you should include in the folder "js" and php files in the "php" folder.
For rendering the chart on the chosen page You should put necessary HTML tag with the corresponding id 
in the chosen line.

For Chart library you should wrap canvas tags in div tag.

For example:

&lt; div style="width:800px; height = 450px !important;" &gt; &lt; canvas id="container"  class = "container" width="800" height="450" &gt; &lt; /canvas &gt; &lt; /div &gt;

After activating plagin you should select field "Page Charts" in the menu "Plagins" of your admin panel.
In the drop-down list with the label "Choose a chart library" you should choose appropriate software.
Do not forget to include chosen software library in the "js" folder of the plagin!
In the table on the the plagin page you should select page or post where you want to insert your chart by clicking
"add new chart".
In the appeared line you should put the names of your php and js files and click "change".
Be shure that id in your div on the page and container name of the javascript chart object are the same. 	
If you want to use several charts on the same page use "duplicate" button and do the same as above. 
Don't forget to use different container's ids on the same page!
You can preview your chart on the admin page by clicking "preview the chart".	

== Installation ==

1. Upload the "page-charts" folder to the '/wp-content/plugins' directory.
2. Upload the necessary library software(Chart or Highcharts) in the "js" folder of the plagin.
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

=How to choose post where I want to render my chart?

In the table of the the plagin page you should select page or post where you want to insert your chart by clicking 
"add new chart".

=How to embed my chart on the chosen page?

To embed the chart on the chosen page you should put HTML tag "canvas" for Chart library or "div" tag for the Highcharts
library with the necessary id in the chosen line of the page. 

For example:
	
Text text text...
&lt; div style="width:800px; height = 450px !important;" &gt; &lt; canvas id="container"  class = "container" width="800" height="450" &gt; &lt; /canvas &gt; &lt; /div &gt;
text text text...

=How to prepare chart for rendering?

For every chart you must prepare two files : one of them it's javascript file with the javascript chart object, 
other one it's a php file with data for chart. Interface between php and js represents array with the name "data".
To this array you can include all variables and arrays necessary for the javascript chart object.

== Screenshots ==

1. Initial administration page of this plugin.
2. Administration page of this plugin after choosing of the library, inputing the file names and clicking on "preview the chart".
