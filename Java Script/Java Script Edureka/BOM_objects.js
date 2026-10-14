function windowop(){
    var newWindow=window.open("http://www.google.com","newWindow"," height=100,width=200");
    newWindow.moveTo(200,150);
    newWindow.close();
    window.close();
}

function navigatorProp(){

        document.write("<br> appCodeName:"+navigator.appcodename);
        document.write("<br> appCodeName:"+navigator.appName);
        document.write("<br> appCodeName:"+navigator.platform);
        document.write("<br> appCodeName:"+navigator.appcodename);



}

navigatorProp()
function locationProp(){

    console.log("hreaf of location: "+location.href);
}
locationProp();