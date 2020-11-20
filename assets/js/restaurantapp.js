// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
//const baseUrl = "http://localhost/cs160/scd";
const baseUrl = "http://localhost:8080/cs160/scd";
var commState;

var geoLocate = function() {
    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        let inputLocation = document.getElementById("CurrentLocation").value || "NULL";
        let postRequest = "lat=" + latitude + "&long=" + longitude
            + "&address=" + encodeURIComponent(inputLocation);
        doPost("location", postRequest);
        console.log(latitude + " " + longitude);
    }
    function error() {
        console.log("Unable to retrieve your location");
    }
    if(!navigator.geolocation) {
        console.log("Geolocation is not supported by your browser");
    } else {
        console.log("Locating…");
        navigator.geolocation.getCurrentPosition(success, error);
    }
}

// // bind buttons of the UI to functions
// document.getElementById("buttonLocation").addEventListener("click", geoLocate, true);

// set UI updating mechanism on GET
/*
{"status":"STATUS_OK","user_id":"1","isRestaurant":true,"friendlyName":"yohei","location":["1","0.000000","0.000000","1 Washington Sq, San Jose, CA 95192"],"clientStatus":"PENDING","currentTransactions":[["6","delivery_req","1","1","0.000000","0.000000","1 Washington Sq, San Jose, CA 95192","37.335186","-121.881073","1 Washington Sq, San Jose, CA 95192, USA","2020-11-20 00:52:52","pizza","3.00","0","in-progress"]],"deliveryRequestInfo":null,"error":"none"}
*/
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
        commState = JSON.parse(this.responseText);

        if(commState.status.valueOf() == "STATUS_OK" ) {
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            // if (commState.location)
            //     document.getElementById("CurrentLocation").value = commState.location[2];

            // DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE
            //to do
            $("#deliveries tbody").empty();
            if(commState.currentTransactions)
            {
              $.each(commState.currentTransactions, function(index,value){
                  $('#deliveries tbody').append("<tr><th scope=\"row\">"+ (index+1) +"</th><td>"+ value[9] + "</td><td>"+ value[11]+" </td><td>"+value[12]+"</td></tr>");
              });
            }
            console.log("AJAX Get call");
        } else {
            console.log("Error getting JSON");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
            clearInterval(intervalGet); // stops AJAX on error
        }
    }
};

// POST result
xhrPost.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "UPDATE_OK" ) {
            // DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE
            //to do
            alert("request submitted!");

            console.log("Update okay.");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
        } else {
            console.log("Error with update");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
        }
    }
};
// POST request to update the model, route is the url of the request
var doPost = function(e){
    e.preventDefault();
    var address = document.getElementById("clientAddress").value;
    var food = document.querySelector('input[name="food"]:checked').value;
    //console.log(data);
    xhrPost.open("POST", baseUrl + "/api.php/restaurant/request" ,true);
    xhrPost.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPost.send("address=" + encodeURIComponent(address) +"&food="+food);
}

// GET request for the Table
var doGet = function(){
    xhrGet.open("GET", baseUrl + "/api.php/restaurant/", true);
    xhrGet.send();
}

// call doGet update every 5 seconds
var intervalGet = setInterval(doGet, 5000);
// call doGet first time
doGet();

var $radio = $("input:radio");
$radio.change(function () {
    if ($radio.filter(':checked').length == 1) {
      if ($address.val().length > 0 )
      {
        $("#request").removeAttr("disabled");
      }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});
var $address = $("#clientAddress");
$address.change(function () {
    if ($address.val().length > 0 ) {
      if($radio.filter(':checked').length == 1){
        $("#request").removeAttr("disabled");
      }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});
