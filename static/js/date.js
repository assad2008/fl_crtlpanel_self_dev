// JavaScript Document

//日期选择
var productid,clickflag=0,todaytime;
		$(document).ready(function(){
			var tmptimeflag=30;
			if(tmptimeflag==0){
				document.getElementById("start").innerHTML=getToday();
				document.getElementById("middle").innerHTML="";
				document.getElementById("end").innerHTML="";
			}else if(tmptimeflag==1){
				document.getElementById("start").innerHTML=getDateAgo(1);
				document.getElementById("middle").innerHTML="";
				document.getElementById("end").innerHTML="";
			}else if(tmptimeflag==7){
				document.getElementById("middle").innerHTML="~";
				document.getElementById("start").innerHTML=getDateAgo(7);
				document.getElementById("end").innerHTML=getToday();
			}else if(tmptimeflag==30){
				document.getElementById("middle").innerHTML="~";
				document.getElementById("start").innerHTML=getDateAgo(30);
				document.getElementById("end").innerHTML=getToday();
			}else if(tmptimeflag==99){
				document.getElementById("middle").innerHTML="~";
				if(registertime){
						var diff=DateDiff(getToday(),registertime,365);
						if(diff==false){
							document.getElementById("start").innerHTML=getDateAgo(365);
						}else{
							document.getElementById("start").innerHTML=strTOStr(registertime);
						}
				}else{
						document.getElementById("start").innerHTML=getDateAgo(365);
				}
				document.getElementById("end").innerHTML=getToday();
			}else if(tmptimeflag==6){
				document.getElementById("middle").innerHTML="~";
				document.getElementById("start").innerHTML="null";
				document.getElementById("end").innerHTML="null";
			} 			
			todaytime=document.getElementById("end").innerHTML;
		}); 
		
		function saveTime(vtf){
			var starttime = document.getElementById("start").innerHTML;
			var endtime = document.getElementById("end").innerHTML;
			var url=basePath+'servlet/TenddataSaveServlet';
			vtimecc=Math.random();
			var params = {
		         starttime:starttime,
				 endtime:endtime,
				 timeflag:vtimeflag,
		         timecc:vtimecc
			};
			$.getJSON(url,params,callback);
		}
		
		function callback(){
		}
		
		function searchTimeFlag(vtf){
			vlineName="不对比";
			vdayStartUp="不对比";
			var obj=document.getElementById("contraststarttime");
			if(obj!=null&&obj!=""){
				document.getElementById("contraststarttime").innerHTML="";
				document.getElementById("contrastmiddle").innerHTML="请选择日期";
				document.getElementById("contrastendtime").innerHTML="";
			}
			var obj2=document.getElementById("contraststarttimeSec");
			if(obj2!=null&&obj2!=""){
				document.getElementById("contraststarttimeSec").innerHTML="";
				document.getElementById("contrastmiddleSec").innerHTML="请选择日期";
				document.getElementById("contrastendtimeSec").innerHTML="";
			}
			if(vtf!=6){
				document.getElementById("middle").innerHTML="~";
			    document.getElementById("end").innerHTML=getToday();
				if(vtf==0){
					document.getElementById("start").innerHTML=getToday();
					document.getElementById("middle").innerHTML="";
					document.getElementById("end").innerHTML="";
				}else if(vtf==1){
					document.getElementById("start").innerHTML=getDateAgo(1);
					document.getElementById("middle").innerHTML="";
					document.getElementById("end").innerHTML="";
				}else if(vtf==7){
					document.getElementById("start").innerHTML=getDateAgo(7);
				}else if(vtf==30){
					document.getElementById("start").innerHTML=getDateAgo(30);
				}else if(vtf==99){
					if(registertime!=null&&registertime!=""){
						var diff=DateDiff(getToday(),registertime,365);
						if(diff==false){
							document.getElementById("start").innerHTML=getDateAgo(365);
						}else{
							document.getElementById("start").innerHTML=strTOStr(registertime);
						}
					}else{
						document.getElementById("start").innerHTML=getDateAgo(365);
					}
				}
				document.getElementById("datamessage").style.display="none";
				
				timet('time','timecolse');
				document.getElementById("startTime").value="";
				document.getElementById("endTime").value="";
				if(pagename=="版本分布"){
					verifyForVersionInfo(vtf);
				}else{
					verify(vtf);
				}
			}else{
				var s=document.getElementById("startTime").value;
				var e=document.getElementById("endTime").value;
				if(s==null||s==""||e==null||e==""){
					document.getElementById("datamessage").style.display="block";
				}else{
					document.getElementById("start").innerHTML=s;
					document.getElementById("end").innerHTML=e;
					document.getElementById("datamessage").style.display="none";
					var diff=DateDiff(e,s,365);
					if(DateDiff(getToday(),s,0)){
						vtf=0;
					}
					if(diff==false){
						if(pagename=="版本分布"){
							verifyForVersionInfo(vtf);
						}else{
							verify(99);
						}
					}else{
						timet('time','timecolse');
						if(pagename=="版本分布"){
							verifyForVersionInfo(vtf);
						}else{
							verify(vtf);
						}
					}
				}
			}
			saveTime(vtf);
		}
		
		function selectMonthf(){
			document.getElementById("middle").innerHTML="~";
			var date = new Date();
			var tmpval=$("#selectMonth").val();
			var strYear = tmpval.split('-')[0];
			var month=tmpval.split('-')[1];
			var daysInMonth = new Array([0],[31],[28],[31],[30],[31],[30],[31],[31],[30],[31],[30],[31]);
			if(parseInt(strYear)%4 == 0 && parseInt(strYear)%100 != 0){   
		        daysInMonth[2] = 29;   
		    }   
		    if(parseInt(month)==-1){
				month=1;
		    }
		    var endday=daysInMonth[month];
		    var startvalue=strYear+"-"+month+"-01";
		    var endvalue=strYear+"-"+month+"-"+endday;
		    document.getElementById("start").innerHTML=startvalue;
			document.getElementById("end").innerHTML=endvalue;
			starttime=startvalue;
			endtime=endvalue;
			document.getElementById("datamessage").style.display="none";
			timet('time','timecolse');
			if(pagename=="版本分布"){
				verifyForVersionInfo(vtf);
			}else{
				verify(6);
			}
		}
		
		
		function getMonths(){
			if(clickflag==0){
				document.getElementById("selectMonth").options.length = 0;
				var url=basePath+"servlet/ProductServlet";
				var timecc=Math.random();
				var params={
					servertype:7,
					timecc:timecc
				}
				$.get(url,params,callBackMonths);
			}
			
		}
		
		function callBackMonths(data){
			if(data!=null&&data!=""){
				var array=data.split(",");
				for(var i=0;i<array.length;i++){
					$("#selectMonth").append("<option value='"+array[i]+"' id='"+array[i]+"'>"+array[i].split('-')[1]+"月</option>");
				}
				clickflag=clickflag+1;
			}
		}
		
		function closeWin(){
			document.getElementById("datamessage").style.display="none";
		}
		
		function refresh(data){
				registertime=data;
			}
	function tagsLine(str){
			vlineName=str;
			document.getElementById("datamessage2").style.display="none";
			if(str!="不对比"){
				var datestart=document.getElementById("start").innerHTML;
				document.getElementById("contrastmiddle").innerHTML="~";
				document.getElementById("contrastendtime").innerHTML=getToday();
				var contrastStartTime;
				if(str=="前一天"){
					contrastStartTime=getDateAgo2(datestart,1);
				}else if(str=="上周同期"){
					contrastStartTime=getDateAgo2(datestart,7);
				}else if(str=="上月同期"){
					contrastStartTime=getDateAgo2(datestart,30);
				}else if(str=="自定义"){
					var tmptime=document.getElementById("contrasttime").value;
					if(tmptime==null||tmptime==""){
						document.getElementById("datamessage2").style.display="block";
					}else{
						document.getElementById("datamessage2").style.display="none";
						contrastStartTime=tmptime;
					}
				}
				var datediff=30;
				if(vtimeflag==0||vtimeflag==1){
					datediff=0;
				}else{
					var starttime = document.getElementById("start").innerHTML;
					var endtime = document.getElementById("end").innerHTML;
					var cdy=DateDifflong(endtime,starttime);
					datediff=cdy;
				}
				
				if(document.getElementById("datamessage2").style.display=="block"){
					document.getElementById("contraststarttime").innerHTML="";
					document.getElementById("contrastmiddle").innerHTML="请选择日期";
					document.getElementById("contrastendtime").innerHTML="";
				}else{
					var contrastEndTime=getContrastEndTime(contrastStartTime,datediff);
					document.getElementById("contraststarttime").innerHTML=contrastStartTime;
					document.getElementById("contrastendtime").innerHTML=contrastEndTime;
					timet('time2','timecolse2');
					setDIYContrastLine(contrastStartTime,contrastEndTime);
				}
			}else{
				document.getElementById("contraststarttime").innerHTML="";
				document.getElementById("contrastmiddle").innerHTML="请选择日期";
				document.getElementById("contrastendtime").innerHTML="";
				timet('time2','timecolse2');
				setLine(vlistKey);
			}
		}
		
		function closeWin(){
			document.getElementById("datamessage2").style.display="none";
		}
//筛选
var versions="";
		var partners="";
		var allversions="";
		var partnerNames="";
		var usergroups="";
	
		function selectChose(){
			$("#version").html('');
			$("#partner").html('');
			versions="";
			partners="";
			partnerNames="";
			if(versions!=""&&partners!=""){
				document.getElementById("showVersion").style.display="block";
				var tmppartnerNames=setNameFont(partnerNames);
				var tmpversions=setNameFont(versions);
				$("#selectVersion").html("<font>版本：</font>"+tmpversions+"<br/><font>渠道：</font>"+tmppartnerNames);     
			}else if(versions!=""&&partners==""){
				document.getElementById("showVersion").style.display="block";
				var tmpversions=setNameFont(versions);
				$("#selectVersion").html("<font>版本：</font>"+tmpversions);     
			}else if(versions==""&&partners!=""){
				document.getElementById("showVersion").style.display="block";
				var tmppartnerNames="";
				tmppartnerNames=setNameFont(partnerNames);
				$("#selectVersion").html("<font>渠道：</font>"+tmppartnerNames);     
			}else{
				document.getElementById("showVersion").style.display="none";
			}
			
			choseVersion();
			chosePartner();
			
		}
		
		function setNameFont(str){
			var tmppartnerNames="";
				
			if(str){
				var tmpinfo=str.split(",");
				for(var i=0;i<tmpinfo.length;i++){
					tmppartnerNames=tmppartnerNames+"<font>"+tmpinfo[i]+"</font>";
				}
			}
			return tmppartnerNames;
		}
		
		//获取版本列表
		function choseVersion(){
			var checkValue=$("#productTxt").val();
			var checkpValue=$("#productList").val();
			var url=basePath+'servlet/TenddataChoseVersionServlet';
			var vtimecc=Math.random();
			var params = {
				 productid:checkValue,
        		 platformid:checkpValue,
				serviceCode:"9996",
				timecc:vtimecc
			};
			$.getJSON(url,params,setVersions);
		}
		
		function setVersions(data){
			var versionarray=data.data9996;
			for(var i=0;i<versionarray.length;i++){
				$("#version").append('<a href="javascript:void(0);" id="'+versionarray[i].value+'" onclick="changeVersionStyle(\''+versionarray[i].value+'\')">'+versionarray[i].value+'</a>');
				allversions=allversions+versionarray[i].value+",";
			}
			if(allversions!=null&&allversions!=""){
				allversions=allversions.substring(0,allversions.length-1);
			}
			
			if(versions!=""){
				var tmpversions=versions.split(',');
				if(tmpversions){
					for(var k=0;k<tmpversions.length;k++){
						if(tmpversions[k]){
							changeVersionStyle(tmpversions[k]);
						}
					}
				} 
			}
		}
		//选择版本时改变样式
		function changeVersionStyle(version){
			if(document.getElementById(version).className=="border"){
				document.getElementById(version).className="";
			}else{
				document.getElementById(version).className="border";
			}
			document.getElementById("yorversion").className="r";
		}
		//获取渠道列表
		function chosePartner(){
			var checkValue=$("#productTxt").val();
			var checkpValue=$("#productList").val();
			var url=basePath+'servlet/TenddataChosePartnerServlet';
			var vtimecc=Math.random();
			var params = {
				productid:checkValue,
        		platformid:checkpValue,
				serviceCode:"9995",
				timecc:vtimecc
			};
			$.getJSON(url,params,setPartners);
		}
		
		function setPartners(data){
			var partnerArray=data.data9995;
			for(var i=0;i<partnerArray.length;i++){
				$("#partner").append('<a href="javascript:void(0);" id="'+partnerArray[i].key+'"onclick="changePartnerStyle(\''+partnerArray[i].key+'\')">'+partnerArray[i].value+'</a>');
			}
			
			if(partners!=""){
				var tmppartners=partners.split(',');
				if(tmppartners){
					for(var k=0;k<tmppartners.length;k++){
						if(tmppartners[k]){
							changeVersionStyle(tmppartners[k]);
						}
					}
				} 
			}
		}
		//选择渠道时候改变样式
		function changePartnerStyle(partner){
			if(document.getElementById(partner).className=="border"){
				document.getElementById(partner).className="";
			}else{
				document.getElementById(partner).className="border";
			}
			document.getElementById("yorpartner").className="r";
		}
		//是否选择筛选
		function yorchose(id){
			if(id==1){
				$("#yorversion").addClass("brde");
				$("#version a").removeClass("border");
			}else{
				$("#yorpartner").addClass("brde");
				$("#partner a").removeClass("border");
			}
		}
		//选择钩时，获取版本号、渠道号
		function commitVersionPartner(){
			versions="";
			partners="";
			partnerNames="";
			var tagversion = document.getElementById("version").getElementsByTagName("a");
			for(i=0; i<tagversion.length; i++){
				if(tagversion[i].className =="border"){
					versions=versions+tagversion[i].id+",";
				}
			}
			var tagpartner = document.getElementById("partner").getElementsByTagName("a");
			for(i=0; i<tagpartner.length; i++){
				if(tagpartner[i].className =="border"){
					partners=partners+tagpartner[i].id+",";
					partnerNames=partnerNames+tagpartner[i].innerHTML+",";
				}
			}
			if(versions!=null&&versions!=""){
				versions=versions.substring(0,versions.length-1);
			}
			if(partners!=null&&partners!=""){
				partners=partners.substring(0,partners.length-1);
				partnerNames=partnerNames.substring(0,partnerNames.length-1);
			}
			
			if(versions!=""&&partners!=""){
				document.getElementById("showVersion").style.display="block";
				var tmppartnerNames=setNameFont(partnerNames);
				var tmpversions=setNameFont(versions);
				$("#selectVersion").html("<font>版本：</font>"+tmpversions+"<br/><font>渠道：</font>"+tmppartnerNames);     
			}else if(versions!=""&&partners==""){
				document.getElementById("showVersion").style.display="block";
				var tmpversions=setNameFont(versions);
				$("#selectVersion").html("<font>版本：</font>"+tmpversions);     
			}else if(versions==""&&partners!=""){
				document.getElementById("showVersion").style.display="block";
				var tmppartnerNames="";
				tmppartnerNames=setNameFont(partnerNames);
				$("#selectVersion").html("<font>渠道：</font>"+tmppartnerNames);     
			}else{
				document.getElementById("showVersion").style.display="none";
			}
			saveInfo();
			chose();
		}
		
		function noVersionPartner(){
			if(document.getElementById("showVersion").style.display=="none"){
				$("#version a").removeClass("border");
				$("#partner a").removeClass("border");
			}
		}
		
		function saveInfo(){
			var url=basePath+'servlet/TenddataSaveChoseServlet';
			vtimecc=Math.random();
			//alert(partnerNames);
			var params = {
		         versions:versions,
				 partners:partners,
				 partnerNames:partnerNames,
		         timecc:vtimecc
			};
			$.getJSON(url,params,callback);
		}
		
		function callback(){
			
		}

function timet(txt, colse) {
	var txt = txt;
	$("#" + txt).slideToggle("show");
	$("." + colse).click(function() {
		$("#" + txt).hide(200);
	})
}

//求今天的日期
function getToday() {
	var myDate = new Date();
	var year = myDate.getFullYear();
	var month = myDate.getMonth() + 1;
	var day = myDate.getDate();
	if(day < 10) {
		day = "0" + day;
	}
	return year + "-" + month + "-" + day;
}

//求今天的日期
function getTodayItem(itenvaluenum) {
	var myDate = new Date();
	var t = myDate.getTime();
	var num=0;
	if(itenvaluenum=="1"){
		num=num+7;
	}else if(itenvaluenum=="2"){
		num=num+30;
	}
	var t2 = t - num * 1000 * 3600 * 24;
	var weekago = new Date(t2);
	var year = weekago.getFullYear();
	var month = weekago.getMonth() + 1;
	var day = weekago.getDate();
	if(day < 10) {
		day = "0" + day;
	}
	return year + "-" + month + "-" + day;
}

//求几天前的日期
function getSevenDateAgo(num) {
	var myDate = new Date();
	var t = myDate.getTime();
	var tmpnum=parseInt(num)+7;
	var t2 = t - tmpnum * 1000 * 3600 * 24;
	var weekago = new Date(t2);
	var y = weekago.getFullYear();
	var m = weekago.getMonth() + 1;
	var d = weekago.getDate();
	if(parseInt(d,10) < 10) {
		d = "0" + d;
	}
	return y + "-" + m + "-" + d;
}

//求几天前的日期
function getDateAgo(num) {
	var myDate = new Date();
	var t = myDate.getTime();
	var t2 = t - num * 1000 * 3600 * 24;
	var weekago = new Date(t2);
	var y = weekago.getFullYear();
	var m = weekago.getMonth() + 1;
	var d = weekago.getDate();
	if(parseInt(d,10) < 10) {
		d = "0" + d;
	}
	return y + "-" + m + "-" + d;
}

//求几天前的日期
function getDateAgoItem(num,itenvaluenum) {
	var myDate = new Date();
	var t = myDate.getTime();
	if(itenvaluenum=="1"){
		num=num+7;
	}else if(itenvaluenum=="2"){
		num=num+30;
	}
	var t2 = t - num * 1000 * 3600 * 24;
	var weekago = new Date(t2);
	var y = weekago.getFullYear();
	var m = weekago.getMonth() + 1;
	var d = weekago.getDate();
	if(parseInt(d,10) < 10) {
		d = "0" + d;
	}
	return y + "-" + m + "-" + d;
}

//格式化天数据格式
function getframeDateAgo(str){
	var days=str.split("-");
	return days[1]+"月"+days[2]+"日";
}

//求某一天几天前的日期
function getDateAgo2(date,datediff){
	var oDate1 = strToDate(date);
	var t=oDate1.getTime()-datediff*24*60*60*1000;
	var contrasttime = new Date(t);
	var y = contrasttime.getFullYear();
	var m = contrasttime.getMonth() + 1;
	var d = contrasttime.getDate();
	if(parseInt(d,10) < 10) {
		d = "0" + d;
	}
	return y + "-" + m + "-" + d;
}

//将str转成日期date
function strToDate(str) {
	var arys = new Array();
	if(!str)
		return null;
	arys = str.split('-');
	var newDate;
	if(parseInt(arys[1],10) < 10) {
		arys[1] = '0' + arys[1];
	}
	var tmpdate=arys[0]+"/"+arys[1]+"/"+arys[2];
	newDate = new Date(tmpdate);
	return newDate;
}

function stringDateToLong(str){
	if(str.indexOf(".0")!=-1){
		str=str.substring(0,str.indexOf(".0"));
	}
	var dt=strToDate(str);
	var ux = Date.UTC(dt.getFullYear(),dt.getMonth(),dt.getDay(),dt.getHours(),dt.getMinutes(),dt.getSeconds())/1000;
	return ux;
}

//求两个字符串日期相隔天数是否小于一个值
function DateDiff(sDate1, sDate2, daynm) {
	var oDate1, oDate2, iDays;
	oDate1 = strToDate(sDate1);
	oDate2 = strToDate(sDate2);
	iDays = parseInt((oDate1 - oDate2) / 1000 / 60 / 60 / 24);
	if(iDays > daynm) {
		return false;
	} else {
		return true;
	}
}


//求两个字符串日期相隔天数
function DateDifflong(sDate1,sDate2){
	var oDate1, oDate2, iDays;
	oDate1 = strToDate(sDate1);
	oDate2 = strToDate(sDate2);
	iDays = parseInt((oDate1 - oDate2) / 1000 / 60 / 60 / 24);
	return iDays;
}

//求某个字符串日期与今天相隔天数
function DateDifflongone(sDate2){
	var oDate2, iDays;
	var oDate1 = new Date();
	oDate2 = strToDate(sDate2);
	iDays = parseInt((oDate1 - oDate2) / 1000 / 60 / 60 / 24);
	return iDays;
}

//求一个日期后多少天的日期
function getContrastEndTime(sDate1,datediff){
	var oDate1 = strToDate(sDate1);
	var t=oDate1.getTime()+datediff*24*60*60*1000;
	var contrasttime = new Date(t);
	var y = contrasttime.getFullYear();
	var m = contrasttime.getMonth() + 1;
	var d = contrasttime.getDate();
	if(parseInt(d,10) < 10) {
		d = "0" + d;
	}
	return y + "-" + m + "-" + d;
}


