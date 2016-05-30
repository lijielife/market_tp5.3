function createAjax(){
    var request=false;
    //window对象中有XMLHttpRequest存在就是非IE，包括（IE7，IE8）
    if(window.XMLHttpRequest){
        request=new XMLHttpRequest();
        if(request.overrideMimeType){
            request.overrideMimeType("text/xml");
        }
        //window对象中有ActiveXObject属性存在就是IE
    }else if(window.ActiveXObject){
        var versions=['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Msxml2.XMLHTTP.7.0','Msxml2.XMLHTTP.6.0','Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
        for(var i=0; i<versions.length; i++){
            try{
                request=new ActiveXObject(versions[i]);
                if(request){
                    return request;
                }
            }catch(e){
                request=false;
            }
        }
    }
    return request;
}

//注意： 要每次请求都要使用一个新的XMLHttpRequest
/*
 如果使用get将数据传给服务器，则服务器就使用$_GET
 就直接通过Url将数据传给服务器

 使用POST时一定要使用	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");


 */
var ajax=null;

/*function main(left){
    var helloobj=document.getElementById("mainajax");
    ajax=createAjax();
    ajax.onreadystatechange=function(){
        if(ajax.readyState==4){
            if(ajax.status==200){
               var data=ajax.responseText;
                //alert(data);
                helloobj.innerHTML=data;
            }else{
                alert("页面请求失败");
            }
        }
    }
    ajax.open("get", "http://localhost/admin/index.php/Admin/"+left, true);
    //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.send("null");
}*/

/*function ajaxdel(left){
    if(confirm('是否确认删除？')){
    var helloobj=document.getElementById("mainajax");
    ajax=createAjax();
    ajax.onreadystatechange=function(){
        if(ajax.readyState==4){
            if(ajax.status==200){
                var data=ajax.responseText;
                //alert(data);
                helloobj.innerHTML=data;

            }else{
                alert("页面请求失败");
            }
        }
    }
    ajax.open("get", "http://localhost/admin/index.php/Admin/"+left, true);
    //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.send("null");
    }else{
        return;
    }
}

function pageajax(id){    //test函数名 一定要和action中的第三个参数一致上面有
    var id = id;
    $.get('http://localhost/admin/index.php/Admin/Ceshi/ajaxindex', {'p':id}, function(data){  //用get方法发送信息到CeshiController中的index方法
    $("#pageajax").replaceWith("<div  id='pageajax'>"+data+"</div>"); //test一定要和tpl中的一致
    });
}
*/

$(window).on("load", function() {
    var sidebar=$("#sidebar").css('width');
    // alert(sidebar);
    if(sidebar=='43px'){
    $("#mainajax").css("margin-left","-147px");
    }
});
function adb(){
    var marginleft= $("#mainajax").css('margin-left');
    //alert(marginleft);
    if(marginleft=='-147px'){
    $("#mainajax").css("margin-left","0px");
    }else{
    $("#mainajax").css("margin-left","-147px");
    }
}

