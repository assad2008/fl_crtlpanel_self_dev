//将数字按3位以逗号隔开
function fmoney(s, n)  {  
	if(s==null||s==""){
		return "";
	}
   n = n >0 && n <= 20 ? n : 2;  
   s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";  
   var l = s.split(".")[0].split("").reverse(),  
   r = s.split(".")[1];  
   t = "";  
   for(i = 0; i < l.length; i ++ )  
   {  
      t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");  
   }  
   return t.split("").reverse().join("") ;  
} 

//y轴数据统一为数字，例如4k-->4000
function yStyleNum(num){
	var num=num.toString();
	if(num==null||num==""){
		return 0;
	}
	if(num.indexOf("k")!=-1){
		return num.substring(0,num.length-1)*1000;
	}else{
		return num;
	}
}
//保留2位小数
function formatFloat(src, pos)
{
    return Math.round(src*Math.pow(10, pos))/Math.pow(10, pos);
}

