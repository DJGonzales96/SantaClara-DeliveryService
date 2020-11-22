// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
const xhrCancel = new XMLHttpRequest();
//const baseUrl = "http://localhost/cs160/scd";
const baseUrl = "http://localhost:8080/cs160/scd";
var commState;
var requestInfo;
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

var modalopen = false;
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
        commState = JSON.parse(this.responseText);

        if(commState.status.valueOf() == "STATUS_OK" ) {
           requestInfo=commState.deliveryRequestInfo;
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            $("#deliveries tbody").empty();
            if(commState.currentTransactions)
            {
              $.each(commState.currentTransactions, function(index,value){
                  $('#deliveries tbody').append("<tr><th scope=\"row\">"+ value[0] +"</th><td>"+ value[9] + "</td><td>"+ value[11]+" </td><td>"+value[12]+"</td><td>"+value[14]+"</td></tr>");
              });
            }
            if(commState.location)
            {
                document.getElementById("restAddr").innerHTML = commState.location[3];

                //This is temporary
                document.getElementById("money").innerHTML = commState.friendlyName;
            }

            // if(commState.wallet)
            // {
            //     document.getElementById("money").innerHTML = commState.wallet[?];
            // }
            if(commState.deliveryRequestInfo && commState.deliveryRequestInfo.length > 0)
            {
                if(!modalopen)
                {
                  modalopen=true;
                  $('#modal').modal({backdrop:"static"});
                  document.getElementById("foodRequest").innerHTML = commState.deliveryRequestInfo[4];
                  document.getElementById("addressRequest").innerHTML = commState.deliveryRequestInfo[3];
                  document.getElementById("costRequest").innerHTML = commState.deliveryRequestInfo[5];
                }
            }
            else {
              if(modalopen)
              {
                modalopen=false;
                $('#modal').modal("hide");
              }
            }

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
            doGet();
        } else {
            console.log("Error with update");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
        }
    }
};

// Cancel order
xhrCancel.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "UPDATE_OK" ) {
            doGet();
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
    var address = document.getElementById("clientAddress").value;
    var food = document.getElementById('food').value;
    xhrPost.open("POST", baseUrl + "/api.php/restaurant/request" ,true);
    xhrPost.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPost.send("address=" + encodeURIComponent(address) +"&food="+food);
}

// GET request for the Table
var doGet = function(){
    xhrGet.open("GET", baseUrl + "/api.php/restaurant/", true);
    xhrGet.send();
}

// Cancel request for the user
var doCancel = function(){
    xhrCancel.open("POST", baseUrl + "/api.php/restaurant/cancel", true);
    xhrCancel.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrCancel.send("request_ID="+requestInfo[0]);
}
// call doGet update every 5 seconds
var intervalGet = setInterval(doGet, 5000);
// call doGet first time
doGet();

var $food = $("#food");
$food.change(function () {
    if ($food.val().length > 0 ) {
      if($address.val().length > 0 ){
        $("#request").removeAttr("disabled");
      }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});
var $address = $("#clientAddress");
$address.change(function () {
    if ($address.val().length > 0 ) {
      if($food.val().length > 0 ){
        $("#request").removeAttr("disabled");
      }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});
