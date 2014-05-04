var barModel,contrastbarModel;
var vtimecc,tmpdate=0,vtimeflag=30;
eval("var chartline = null;");
var vplatformtype=0,vplatforms="",vlistKey=0;
var vlineName="不对比";
var tmpnum=0; //里程碑点击开的框的id,需要不停的增长，这个作为全局变量
var serviceCode="0209";

function initSearch(){
	pagename="趋势统计";
	selSearch();
}

function destroy() {
    if(chartline!=null){
    	chartline.destroy();
    }
    delete chartline;
}

function askServlet(serviceCode,callback){
	var productid=$("#productTxt").val();
	var platformid=$("#productList").val();
	var starttime = document.getElementById("start").innerHTML;
	var endtime = document.getElementById("end").innerHTML;
	var url=basePath+'servlet/TenddataTrendServlet';
	vtimecc=Math.random();
	var params = {
		 productid:productid,
         platformid:platformid,
         starttime:starttime,
		 endtime:endtime,
		 timeflag:vtimeflag,
		 serviceCode:serviceCode,
         versionid:versions,
         partnerid:partners,
         groupUserCriteriaID:usergroups,        
         timecc:vtimecc
	};
	$.getJSON(url,params,callback);
}
var ccs=0;
function verify(vtf){
	getMonths();
	$('#0209').html('<div class="login"><img src="../images/loading.gif" height="25px"></img><br>正在加载统计数据...</div>');
	getloading("000102");
	getloading("000202");
	getloading("000302");
	getloading("100202");
	getloading("900502");
	getImgloading("000322");
	getImgloading("108622");
	getImgloading("000122");
	getloadingbox("0212");
	vtimeflag=vtf;
	if(usergroups!=""){             //如果一开始session中已经有用户分群的信息就影藏某些信息
		hideSomeButtom();
	}
	askServlet('0209',getLine);
	
}

function chose(){
	verify(vtimeflag);
}

function setSimpleDate(data){
	if(isEmpty(data)){
		var tmpGridInfo=data.data0210;
		if(isEmpty(tmpGridInfo)){
			$('#000122').html(fmoney(tmpGridInfo.data0001));
		}
	}
	askServlet('0211',setStartUpDate);
}	

function setStartUpDate(data){
	if(isEmpty(data)){
		var tmpGridInfo=data.data0211;
		if(isEmpty(tmpGridInfo)){
			$('#000322').html(fmoney(tmpGridInfo.data0003));
			$('#108622').html(tmpGridInfo.data1086);
		}
	}
	askServlet('0212',setClearTable);
}

//创建table数据
function setClearTable(data){
	var tmpGridInfo=data.data0212;
	if(tmpGridInfo!=null&&tmpGridInfo!=""){
	var tmpvalue=tmpGridInfo.split("^");
		var gridinfo=new Array(tmpvalue.length);
		var col;
		for(var i=0;i<tmpvalue.length;i++){
			col=tmpvalue[i].split(",");
			gridinfo[i]=col;
		}
		
		var paginateflag=true;
		var bInfofalg=false;
		if(gridinfo.length<=10){
			paginateflag=false;
			bInfofalg=false;
		}
		
		var bVisible=true;
		if(usergroups!=""){
			bVisible=false;
		}
		
		$('#0212').html( '<table cellpadding="0px" cellspacing="0px" width="100%" height="100%" border="0" id="grid" class="table_style1" ></table>' );
		$('#grid').dataTable( {
		"fnDrawCallback" : function(oSettings) {
				if(oSettings.aiDisplay.length == 0) {
					return;
				}
				var ftdt = [0.2,0.15,0.1,0.2,0.2,0.15];
				setthwidth('grid', ftdt);
				highlight('grid');
			},
			"aaData": gridinfo,
			"bPaginate": paginateflag,//去掉选择显示多少条
			"sDom": 'rt<"page"flp>',
			"bLengthChange": true,
			"bFilter": false,//去掉右上方查询查询
			"aaSorting": [[ 0, "desc" ]],   //排序
			"bInfo": bInfofalg,
			"iDisplayLength":10,
			'sPaginationType':'full_numbers', //分页样式 
			"aoColumns": [
				{ "sTitle": "<div class='tdtxtwidth' style='margin-left: 1px;'>日期</div>" , "sClass": "left",
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return "<div class='tdtxtwidth' style='margin-left: 1px;'>"+changeStringDateToDate3(info)+"</div>";
		    }},
				{ "sTitle": "<div class='tdtxtwidth'>新增用户</div>","sClass": "center",
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return info;
		    }},
				{ "sTitle": "<div class='tdtxtwidth'>活跃用户</div>" ,"sClass": "center",
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return info;
		    }},
				{ "sTitle": "<div class='tdtxtwidth'>平均单次使用时长</div>","sClass": "center",
				"bVisible":bVisible,
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return intTotime(info);
		    }},
				{ "sTitle": "<div class='tdtxtwidth'>启动次数</div>" ,"sClass": "center",
				"bVisible":bVisible,
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return info;
		    }},
				{ "sTitle": "<div class='tdtxtwidth'>累计用户总量</div>","sClass": "center",
				"bVisible":bVisible,
			"fnRender": function(obj) {
			   var info=obj.aData[obj.iDataColumn];
			   return info;
		    }}
			],
			"oLanguage": {  
			     "sLengthMenu": "<b class='l'>每页显示</b> _MENU_ <b class='l'>条记录</b>",  
			     "sZeroRecords": "对不起，查询不到任何相关数据",  
			     "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",  
			     "sInfoEmtpy": "找不到相关数据",  
			     "sInfoFiltered": "数据表中共为 _MAX_ 条记录)",  
			     "sProcessing": "正在加载中...",  
			     "sSearch": "搜索",  
			     "sUrl": "", //多语言配置文件，可将oLanguage的设置放在一个txt文件中，例：Javascript/datatable/dtCH.txt  
			     "oPaginate": {  
			         "sFirst":    "第一页",  
			         "sPrevious": " 上一页 ",  
			         "sNext":     " 下一页 ",  
			         "sLast":     " 最后一页 "  
			     }  
		 	 } //多语言配置  
		} );
		if(gridinfo.length<=10){
			$("#grid").parent().children(".page").remove();
		}
		$('#grid_page').sSelect();
		datasort("#grid",0);
	}else{
		$('#0212').html( '<table cellpadding="0px" cellspacing="0px" width="100%" height="20px" border="0" id="example" class="table_style1" ><tr><td>未查询到数据</td><tr></table>' );
	}
	
	tobaidu();
}

function getLine(data){
	if(isEmpty(data)){
		barModel=data.data0209;
		setLine(vlistKey);
	}
	if(vlineName!="不对比"){
		var contrastStartTime=document.getElementById("contraststarttime").innerHTML;
		var contrastEndTime =document.getElementById("contrastendtime").innerHTML;
		setDIYContrastLine(contrastStartTime,contrastEndTime);
	}
	askServlet('0210',setSimpleDate);
}

function setDIYContrastLine(contrastStartTime,contrastEndTime){
	getloading("000102");
	getloading("000202");
	getloading("000302");
	getloading("100202");
	getloading("900502");
	var productid=$("#productTxt").val();
	var platformid=$("#productList").val();
	var url=basePath+'servlet/TenddataTrendServlet';
	vtimecc=Math.random();
	var params = {
		 productid:productid,
         platformid:platformid,
         starttime:contrastStartTime,
		 endtime:contrastEndTime,
		 timeflag:6,
		 serviceCode:'0209',
         versionid:versions,
         partnerid:partners,
         groupUserCriteriaID:usergroups,    
         timecc:vtimecc
	};
	$.getJSON(url,params,getcontrastLine);
}

function getcontrastLine(data){
	if(isEmpty(data)){
		contrastbarModel=data.data0209;
		setLine(vlistKey);
	}
}



function setLine(listKey){
	var info,time,renderTo,contrastinfo,contrasttime;
	var vname="";
	var vtypef="";
	vlistKey=listKey;
	var vdefaultSeriesType="area";
	switch(listKey)
    {
	   case 0:
	     vname="新增用户";
	     vtypef="个";
	     renderTo="000102";
	     info=barModel.data0001;
	     time=barModel.data0001Categorie;
	     if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data0001;
     			contrasttime=contrastbarModel.data0001Categorie;
	     }
	     break;
	   case 1:
	   	 vname="活跃用户";
	   	 vtypef="个";
	   	 renderTo="000202";
	   	 info=barModel.data0002;
	     time=barModel.data0002Categorie;
	      if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data0002;
     			contrasttime=contrastbarModel.data0002Categorie;
	     }
	     break;
	   case 2:
	   	 vname="启动次数";
	   	 vtypef="次";
	   	 renderTo="000302";
	   	 info=barModel.data0003;
	     time=barModel.data0003Categorie;
	      if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data0003;
     			contrasttime=contrastbarModel.data0003Categorie;
	     }
	     break;
	   case 3:
	   	 vname="平均使用时长";
	   	 vtypef="秒";
	   	 renderTo="100202";
	   	 info=barModel.data1002;
	     time=barModel.data1002Categorie;
	      if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data1002;
     			contrasttime=contrastbarModel.data1002Categorie;
	     }
	     break;
	   case 4:
	     vname="累计用户总数";
	     vtypef="个";
	     renderTo="900502";
	      info=barModel.data9005;
	     time=barModel.data9005Categorie;
	     if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data9005;
     			contrasttime=contrastbarModel.data9005Categorie;
	     }
	     break;
	   default:
	   	 vname="新增用户";
	     vtypef="个";
	     renderTo="000102";
	     info=barModel.data0001;
	     time=barModel.data0001Categorie;
	      if(contrastbarModel!=null&&contrastbarModel!=""){
          		contrastinfo=contrastbarModel.data0001;
     			contrasttime=contrastbarModel.data0001Categorie;
	     }
	     break;
    }
    
     if(info!=null&&info!=""&&info.length==1){
	   	vdefaultSeriesType="column";
	 }
    //将数据转变成json格式
//	info=changeStrToJson(info);
//	contrastinfo=changeStrToJson(contrastinfo);
		if(listKey!=1){
			if(listKey!=4){
				chartline = new Highcharts.Chart({
					chart: {
						renderTo: renderTo,
						defaultSeriesType: vdefaultSeriesType,
						backgroundColor:backgroundColorinfo,
						shadow: false,
						marginTop:20
					},
					title: {
						text: ''
					},
					xAxis: [{
						categories:time,
						tickInterval: getTickInterval(info),
						 tickmarkPlacement: 'on',
						labels: {
							align: 'center',
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					},{
						categories:contrasttime,
						lineColor: backgroundColorinfo,
						tickInterval: getTickInterval(info),
						labels: {
							enabled:false,
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					}],
					yAxis: {
						title: {
							text: ''
						},
						min:0,
						labels: {
							formatter: function() {
								if(listKey==3){
									return intTotime(this.value)+' s';
								}else{
									return this.value;
								}
							}
						}
					},
					credits:{ 
						enabled: false 
					},
					tooltip: {
						style: {
							padding: '5px'
						},
						formatter: function() {
							var name=this.series.name;
							var y;
							if(name=='平均使用时长'){
								y=intTotime(this.y);
							}else{
								y=this.y;
							}
							var x=changeStringDateToDate(this.x);
							if(this.point.text!=null&&this.point.text!=""){
								return '<b>'+ this.series.name +'<br/>'+x+' : '+y+vtypef+"<br/><strong>点击查看</strong>";
							}else{
								return '<b>'+ this.series.name +'<br/>'+x+' : '+y+vtypef;
							}
						}
					},
					legend: {
						enabled: false 
					},
					plotOptions : {
			            series: {
			            	fillOpacity: 0.03,
			            	 dataLabels: {
				               shadow: false
				            },
							cursor: 'pointer',
							point: {
								events: {
									click: function() {
										var textcallback=getMilePostContent(this,tmpnum);
										tmpnum++;
										if(textcallback!=''){
											hs.htmlExpand(null, {
												pageOrigin: {
													x: this.pageX, 
													y: this.pageY
												},
												headingText: "里程碑",
												maincontentText: textcallback,
												width: 250,
												height:160
											});
										}
									}
								}
							},
							marker: {
								lineWidth: 1
							}
						},
						area:{
							marker : {
                                enabled : true,
                              	 states : {
                                    hover : {
                                        enabled : false
                                    }
                                }
                           } 
						}
					},
					series: [{
						name: "对比"+vname,
						legendIndex:1,
						color: '#AADFF3',
						xAxis:1,
						states : {
							hover : {
								lineWidth : 1
							}
						},
						data: contrastinfo,
						lineWidth:1
					},{
						name: vname,
						legendIndex:0,
						xAxis:0,
						color:'#0066CC',
						data: info,
						lineWidth:2
					}]	
					});
			}else{
				chartline = new Highcharts.Chart({
					chart: {
						renderTo: renderTo,
						defaultSeriesType: vdefaultSeriesType,
						backgroundColor:backgroundColorinfo,
						shadow: false,
						marginTop:20
					},
					title: {
						text: ''
					},
					xAxis: [{
						categories:time,
						tickInterval: getTickInterval(info),
						 tickmarkPlacement: 'on',
						labels: {
							align: 'left',
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					},{
						categories:contrasttime,
						lineColor: backgroundColorinfo,
						tickInterval: getTickInterval(info),
						labels: {
							enabled:false,
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					}],
					yAxis: {
						title: {
							text: ''
						},
						min:0,
						labels: {
							formatter: function() {
								if(listKey==3){
									return intTotime(this.value);
								}else{
									return this.value;
								}
							}
						}
					},
					credits:{ 
						enabled: false 
					},
					tooltip: {
						enabled: true,
						formatter: function() {
							var name=this.series.name;
							var y;
							if(name=='平均使用时长'){
								y=intTotime(this.y);
							}else{
								y=this.y;
							}
							var x=changeStringDateToDate(this.x);
							if(this.point.text!=null&&this.point.text!=""){
								return '<b>'+ this.series.name +'<br/>'+x+' : '+y+vtypef+"<br/><strong>点击查看</strong>";
							}else{
								return '<b>'+ this.series.name +'<br/>'+x+' : '+y+vtypef;
							}
						}
					},
					legend: {
						enabled: false 
					},
					plotOptions : {
						series: {
			            	fillOpacity: 0.3,
			            	 dataLabels: {
				               shadow: false
				            },
							cursor: 'pointer',
							point: {
								events: {
									click: function() {
										var textcallback=getMilePostContent(this,tmpnum);
										tmpnum++;
										if(textcallback!=""){
											hs.htmlExpand(null, {
												pageOrigin: {
													x: this.pageX, 
													y: this.pageY
												},
												headingText: "里程碑",
												maincontentText: textcallback,
												width: 250,
												height:160
											});
										}
									}
								}
							},
							marker: {
								lineWidth: 1
							}
						},
						area:{
							marker : {
                                enabled : true,
                              	 states : {
                                    hover : {
                                        enabled : false
                                    }
                                }
                           } 
						}
					},
					series: [{
						name: "对比"+vname,
						legendIndex:1,
						color: '#AADFF3',
						xAxis:1,
						states : {
							hover : {
								lineWidth : 1
							}
						},
						data: contrastinfo,
						lineWidth:1
					},{
						name: vname,
						legendIndex:0,
						xAxis:0,
						color:'#0066CC',
						data:info,
						lineWidth:1
					}]	
					});
			}
		      
		}else{
			var newuser,activeuser,keepuser,contrastnewuser,contrastactiveuser,contrastkeepuser;
			 if(barModel!=null&&barModel!=""){
			 	newuser=barModel.data0001;
			 	activeuser=barModel.data0002;
			 	keepuser=new Array();
		        var jsonvalue;
			 	for(var c in activeuser){
			 		var i=activeuser[c].y-newuser[c].y;
			 		if(i<0){
			 			i=0;
			 		}
					jsonvalue=new JsonDataBean();
					jsonvalue.text=activeuser[c].text;
					jsonvalue.jsonid=activeuser[c].jsonid;
					jsonvalue.y=i;
					jsonvalue.marker.enable=activeuser[c].marker.enable;
					jsonvalue.marker.symbol=activeuser[c].marker.symbol;
					jsonvalue.marker.radius=activeuser[c].marker.radius;
			 		keepuser.push(jsonvalue);
			 	}
			 	//keepuser=eval(keepuser);
			 	//newuser=changeStrToJson(newuser);
			 }
			
			 if(contrastbarModel!=null&&contrastbarModel!=""){
			 	contrastnewuser=contrastbarModel.data0001;
     			contrastactiveuser=contrastbarModel.data0002;
     			contrastkeepuser=jQuery.extend(true, {}, contrastactiveuser);
			 	for(var c in contrastkeepuser){
			 		var i=contrastkeepuser[c].y-contrastnewuser[c].y;
			 		if(i<0){
			 			i=0;
			 		}
			 		contrastkeepuser[c].y=i;
			 	}
//			 	contrastkeepuser=eval(tmpkeepuser);
//          	contrastnewuser=changeStrToJson(contrastnewuser);
	   		  }
			 vdefaultSeriesType="area";
			 if(info!=null&&info!=""&&info.length==1){
				   	vdefaultSeriesType="column";
			  }
	   		 if(vlineName=="不对比"){
	   		 	chartline= new Highcharts.Chart({
					chart: {
						renderTo: renderTo,
						defaultSeriesType: vdefaultSeriesType,
						backgroundColor:backgroundColorinfo
					},
					title: {
						text: ''
					},
					xAxis: [{
						categories:time,
						tickInterval: getTickInterval(info),
						 tickmarkPlacement: 'on',
						labels: {
							align: 'left',
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					}],
					yAxis: {
						title: {
							text: ''
						}
					},
					tooltip: {
						formatter: function() {
							var strTime="";
							var x=changeStringDateToDate(this.x);
							if(this.point.text!=null&&this.point.text!=""){
								return '<b>'+this.series.name +'<br/>'+x+' : '+ this.y+vtypef+"<br/><strong>点击查看</strong>";
							}else{
								return '<b>'+this.series.name +'<br/>'+x+' : '+ this.y+vtypef;
							}
						}
					},
					plotOptions: {
			            series: {
			            	fillOpacity: 0.6,
			            	stacking: 'normal',
			            	 dataLabels: {
				               shadow: false
				            },
							cursor: 'pointer',
							point: {
								events: {
									click: function() {
										var textcallback=getMilePostContent(this,tmpnum);
										tmpnum++;
										if(textcallback!=""){
											hs.htmlExpand(null, {
												pageOrigin: {
													x: this.pageX, 
													y: this.pageY
												},
												headingText: "里程碑",
												maincontentText: textcallback,
												width: 250,
												height:160
											});
										}
									}
								}
							},
							marker: {
								lineWidth: 1
							}
						},
						area: {
							stacking: 'normal',
							lineWidth: 1,
							marker: {
								enabled : true,
								lineWidth: 1,
								states : {
                                    hover : {
                                        enabled : false
                                    }
                                }
							}
						}
					},
					legend: {
							enabled: true
						},
					credits: {
						enabled: false
					},
					series: [{
						name: '新用户',
						xAxis:0,
						color: '#0066CC',
						data:newuser
					},{
						name: '老用户',
						xAxis:0,
						color: '#CB4B4B',
						data:keepuser
					}]
					});
	   		 }else{
	   		 	chartline= new Highcharts.Chart({
					chart: {
						renderTo: renderTo,
						defaultSeriesType: vdefaultSeriesType,
						backgroundColor:backgroundColorinfo
					},
					title: {
						text: ''
					},
					xAxis: [{
						categories:time,
						tickInterval: getTickInterval(info),
						 tickmarkPlacement: 'on',
						labels: {
							align: 'left',
							formatter: function() {
								return changeStringDateToDate(this.value);
							}
						}
					},{
						categories:contrasttime,
						tickInterval: getTickInterval(info),
						labels: {
								enabled:false,
								formatter: function() {
									return changeStringDateToDate(this.value);
								}
						}
					}],
					yAxis: {
						title: {
							text: ''
						}
					},
					tooltip: {
						formatter: function() {
							var strTime="";
							var x=changeStringDateToDate(this.x);
							if(this.point.text!=null&&this.point.text!=""){
								return '<b>'+this.series.name +'<br/>'+x+' : '+ this.y+vtypef+"<br/><strong>点击查看</strong>";
							}else{
								return '<b>'+this.series.name +'<br/>'+x+' : '+ this.y+vtypef;
							}
						}
					},
					plotOptions: {
						series: {
			            	fillOpacity: 0.6,
			            	 dataLabels: {
				               shadow: false
				            },
							cursor: 'pointer',
							point: {
								events: {
									click: function() {
										var textcallback=getMilePostContent(this,tmpnum);
										tmpnum++;
										if(textcallback!=""){
											hs.htmlExpand(null, {
												pageOrigin: {
													x: this.pageX, 
													y: this.pageY
												},
												headingText: "里程碑",
												maincontentText: textcallback,
												width: 250,
												height:160
											});
										}
									}
								}
							},
							marker: {
								lineWidth: 1
							}
						},
						area: {
							marker : {
								enabled : true,
								states : {
                                    hover : {
                                        enabled : false
                                    }
                                }
							}
						}
					},
					legend: {
							enabled: true
						},
					credits: {
						enabled: false
					},
					series: [{
						name: '活跃用户',
						xAxis:0,
						color: '#0066CC',
						data:activeuser
					},{
						name: '对比活跃',
						xAxis:1,
						fillOpacity: 0.5,
						color: '#AADFF3',
						data: contrastactiveuser
					}]
					});
	   		 }
		}
		selectLine(vlineName);
}

//如果选择的用户分群，有些图表进行影藏或者不允许点击
function hideSomeButtom(){
	$("#aStartUp").attr("class","bottun nonehover");
	$("#aAvgUseTime").attr("class","bottun nonehover");
	$("#aSumUser").attr("class","bottun2 nonehover");
	$("#abstractContent").css("display","none");
	$("#clearTable").css("display","none");
	askServlet('0212',setClearTable);
}

function showSomeButtom(){
	$("#aStartUp").attr("class","bottun");
	$("#aAvgUseTime").attr("class","bottun");
	$("#aSumUser").attr("class","bottun2");
	$("#abstractContent").css("display","block");
	$("#clearTable").css("display","block");
	askServlet('0212',setClearTable);
}

// 操作标签
function selectTag(showContent,selfObj,linekey){
	if(usergroups!=""&&(linekey==2||linekey==3||linekey==4)){         //选择用户分群，启动次数不能点击
		return false;
	}
	var tag = document.getElementById("tags").getElementsByTagName("li");
	var taglength = tag.length;
	for(i=0; i<taglength; i++){
		tag[i].className = "";
	}
	selfObj.parentNode.className = "hover";
	// 操作内容
	for(i=0; j=document.getElementById("tagContent"+i); i++){
		j.style.display = "none";
	}
	document.getElementById(showContent).style.display = "block";
	setLine(linekey);
}


//导出数据
function exportInfoExcel(){
	vtimecc=Math.random();
	var productid=$("#productTxt").val();
	var platformid=$("#productList").val();
	var starttime = document.getElementById("start").innerHTML;
	var endtime = document.getElementById("end").innerHTML;
	var url=basePath+"servlet/ExcelServlet";
	var params = {
		 productid:productid,
	     platformid:platformid,
	     starttime:starttime,
		 endtime:endtime,
		 timeflag:vtimeflag,
	 	 serviceCode : '0212',
	 	 versionid:versions,
         partnerid:partners,
         groupUserCriteriaID:usergroups, 
         timecc:vtimecc
	};
	$.get(url,params,exportcallback);
}

			
//lineName为：不对比或前一天或上周 同期或上月同期
function selectLine(lineName){
		vlineName=lineName;
		if(vlistKey!=1){
			if(vlineName=='不对比'){
				chartline.series[1].show();
				chartline.series[0].hide();
			}else{
				chartline.series[0].show();
				chartline.series[1].show();
			}
		}else{
			if(vlineName=='不对比'){
				chartline.series[0].show();
				chartline.series[1].show();
			}else{
				chartline.series[0].show();
				chartline.series[1].show();
			}
		}
	}