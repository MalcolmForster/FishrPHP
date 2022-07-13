var day_Chk = document.querySelectorAll("input[type=checkbox]");
var submit = document.getElementById("submit");

function postData(input) {
    $.ajax({
        type: "POST",
        url: "http://192.168.7.140/FishScraperMech.py",
        data: { param: input},
        timeout: 2000,
        success: getData()
    
    });
}

function getData() {
    console.log("Working");
    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "http://192.168.7.140/FishScraperMech.py",
        data: { string: output},
        success: console.log(output)
    });
}

function forecast_Check() {
    var len = day_Chk.length;
    var checked_Days = [len];
    var days =["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    var fishing_Location = "\"" + document.getElementById('fishing_Location').value +"\"";
    //console.log(fishing_Location);
    for (i =0; i < len;  i++) {
        var checkbox = day_Chk[i];
        if(checkbox.checked) {
            checked_Days[i] = days[i];
        } else {
            checked_Days[i] = false;
        }
        
    }


    var extract_Days = checked_Days.filter(Boolean);
    var day_String = "";
    for (i=0; i < extract_Days.length; i++) {
        day_String = day_String + " " + extract_Days[i];
    }
    var command = "python FishScraperMech.py " + fishing_Location + day_String;
    //console.log(command);


    postData(command);


    //return forecast_Output;

}

submit.addEventListener("click", function () {
    var checked = forecast_Check();
    //console.log(checked);
});