/*
生成图表
*/
document.write(""); 

function create(obj,chartData,color,x,y,arr){
	var chart;
	var graph;
	// note, some of tada points don't have value field
	
/*var chartData = [ 
	<{foreach  from=$out_user item=list }>
	{date:<{$list.datetime}> ,week:<{$list.runoff_week_users}>,twoweek:<{$list.runoff_twoweek_users}>,month:<{$list.runoff_month_users}>},
	<{/foreach}>
	{}];
*/
	AmCharts.ready(function () {
		// SERIAL CHART  div1
		chart = new AmCharts.AmSerialChart();
		chart.pathToImages = "../static/amcharts/images/";
		chart.marginTop = 0;
		chart.marginRight = 0;
		chart.dataProvider = chartData;
		chart.categoryField = x;
		chart.zoomOutButton = {
			backgroundColor: '#0000F',
			backgroundAlpha: 0.15
		};

		// AXES
		// category
		var categoryAxis = chart.categoryAxis;
		//categoryAxis.parseDates = false; // as our data is date-based, we set parseDates to true
		//categoryAxis.minPeriod = "YYYY"; // our data is yearly, so we set minPeriod to YYYY
		categoryAxis.dashLength = 1;
		categoryAxis.axisColor = "#DADADA";

		// value
		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.axisAlpha = 0;
		valueAxis.dashLength = 1;
		valueAxis.inside = true;
		chart.addValueAxis(valueAxis);
		
		if(!arr){
			// GRAPH red
			graph = new AmCharts.AmGraph();
			graph.lineColor = color;
			graph.negativeLineColor = "#487dac"; // this line makes the graph to change color when it drops below 0
			graph.bullet = "round";
			graph.bulletSize = 5;
			graph.connect = false; // this makes the graph not to connect data points if data is missing
			graph.lineThickness = 2;
			graph.valueField = y;
			chart.addGraph(graph);
		}else{
			$.each(arr, function(i, val){     
				var  value = val;
				  $.each(value, function(y,color){
						graph = new AmCharts.AmGraph();
						graph.lineColor = color;
						graph.negativeLineColor = "#487dac"; // this line makes the graph to change color when it drops below 0
						graph.bullet = "round";
						graph.bulletSize = 5;
						graph.connect = false; // this makes the graph not to connect data points if data is missing
						graph.lineThickness = 2;
						graph.valueField = y;
						chart.addGraph(graph);
				  })
			  });   
		}
		 

		// CURSOR  
		var chartCursor = new AmCharts.ChartCursor();
		chartCursor.cursorAlpha = 0;
		chartCursor.cursorPosition = "mouse";
		chartCursor.categoryBalloonDateFormat = "YYYY";
		chart.addChartCursor(chartCursor);

		// WRITE
		chart.write(obj);
	});
}


//曲线图（实时在线在线的那种曲线图[通用]）
/*
*
*         调用方法
*           var red = 'red'; blue='blue';  yellow = 'yellow';  div = 'chartdiv'; x='date';
             var  arr  = [{'新增':red},{'活跃':blue},{'收入':yellow}];
             create_local(div,chartData,x,arr);
 *
 *         */
function create_local(div,chartData,x,arr){
    var chart;
    var count = ''
    var average ='';
    AmCharts.ready(function () {

        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();
        chart.pathToImages = "../static/amcharts/images/";
        chart.zoomOutButton = {
            backgroundColor: '#000000',
            backgroundAlpha: 0.15
        };
        chart.dataProvider = chartData;
        chart.categoryField = x;

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.dashLength = 1;
        categoryAxis.gridAlpha = 0.15;
        categoryAxis.axisColor = "#DADA";

        // value
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisColor = "#DADA";
        valueAxis.dashLength = 1;
        chart.addValueAxis(valueAxis);

        // GUIDE for average  平均值（暂时无用）
        /* var guide = new AmCharts.Guide();
         guide.value = average;
         guide.lineColor = "#CC0000";
         guide.dashLength = 4;
         guide.inside = true;
         guide.lineAlpha = 1;
         valueAxis.addGuide(guide);*/

        $.each(arr,function(i,v){
            var val=v;
            // alert(i);
            $.each(val,function(k,va){
                var graph = new AmCharts.AmGraph();
                graph.type = "smoothedLine";
                graph.bullet = "round";
                graph.bulletColor = "#FFFFFF";
                graph.bulletBorderColor = va;
                graph.bulletBorderThickness = 2;
                graph.bulletSize = 7;
                graph.title =k;
                graph.valueField = k;
                graph.lineThickness = 2;
                graph.lineColor = va;
                chart.addGraph(graph);
            })
        })

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorPosition = "mouse";
        chart.addChartCursor(chartCursor);

        // SCROLLBAR
        var chartScrollbar = new AmCharts.ChartScrollbar();
        chart.addChartScrollbar(chartScrollbar);
        // WRITE
        chart.write(div);
    });
}
