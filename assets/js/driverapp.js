// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
const baseUrl = window.location.protocol + "//" + window.location.host + "/cs160/scd";
// const baseUrl = "http://localhost/SantaClara-DeliveryService";
var commState;
var ignoreRequest = false;
var isMapChange = true;


var geoLocate = function() {
    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        let inputLocation = document.getElementById("CurrentLocation").value || "NULL";
        let postRequest;
        if(inputLocation == "NULL"){
            postRequest = "lat=" + latitude + "&long=" + longitude;
        }
        else{
            postRequest = "address=" + encodeURIComponent(inputLocation);
        }
        document.getElementById("CurrentLocation").value = "";
        document.getElementById("CurrentLocation").placeholder = "Enter location (or leave empty) and click Set Location"; // set txt box to empty
        doPost("location", postRequest);
    }
    function error() {
        console.log("Unable to retrieve your location");
    }
    if(!navigator.geolocation) {
        console.log("Geolocation is not supported by your browser");
    } else {
        navigator.geolocation.getCurrentPosition(success, error);
    }
}

// updates deilvery table and for new
// transactions/deliveries in drivers queue
var updateDeliveryTable = function() {
    $("#deliveries tbody").empty();
    $.each(commState.currentTransactions, function(index,value){
        $('#deliveries tbody').append(
            "<tr><th scope=\"row\">"+ value[0] +"</th><td>"+ value[6] +
            "</td><td>"+ value[9] + "</td><td>"+ value[11]+
            " </td><td>"+value[12]+"</td>"+"<td>"+
            "<input style='width:100%; background:#007bff; color:white; margin:0; border:none' type='button'value='done' onclick='markDone("+value[0]+")'>"+"</td>"+"</tr>");
    });
}

var hideIncoming = function(){
    var incoming = document.getElementById("incomingRequest");
    incoming.style.visibility = "hidden";
}

var showIncoming = function(){
    if(commState != null){
        document.getElementById("destAddr").innerHTML = commState.deliveryRequestInfo[3] || "not available";
        document.getElementById("deliveryCash").innerHTML = commState.deliveryRequestInfo[5];
        var incoming = document.getElementById("incomingRequest");
        incoming.style.visibility = "visible";
    }
}

// rejects delivery
var reject = function(){
    ignoreRequest = true;
    hideIncoming();
}

// accept
var accept = function(){
    // get post data from commstate
    let request_ID = commState.deliveryRequestInfo[0];
    let cost = commState.deliveryRequestInfo[5];
    let post = "request_ID=" + request_ID + "&cost=" + cost;
    // send post
    doPost("accept",post);
    // hide message
    hideIncoming();
}

var markDone = function(t_id){
    doPost("delivered","request_ID="+t_id);
}

var refreshMap = function(){
    document.getElementById("map").innerHTML =
        " <iframe width=\"100%\"\n" +
        "                            height=\"350\"\n" +
        "                            src=\"https://www.google.com/maps/embed/v1/directions?origin="+
        commState.location[1] + "," + commState.location[2] +
        "&destination=" + commState.currentTransactions[0][7] +
        "," + commState.currentTransactions[0][8] +
        "&waypoints=" + commState.currentTransactions[0][4] +
        "," + commState.currentTransactions[0][5] +
        "&key=AIzaSyByH9xCzlvuJOBZkKgJgLnCn2xe9mFd-Tg\"  allowfullscreen>" +
        "</iframe>"
}

// setup UI button elements and visibility

//accept/reject a delivery to your route
document.getElementById("buttonAccept").addEventListener("click", accept);
document.getElementById("buttonReject").addEventListener("click", reject);
//get driver curr location
document.getElementById("buttonLocation").addEventListener("click", geoLocate, true);
// hide map initially
document.getElementById("map").style.display = "none";

// set UI updating mechanism on GET
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        commState = JSON.parse(this.responseText);
        updateDeliveryTable();
        if(commState.status.valueOf() == "STATUS_OK" ) {
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            // show wallet
            document.getElementById("driver-wallet").innerHTML = commState.wallet; // should be wallet

            if (commState.location){
                // update addr if there
                if(commState.location[3] != " "){
                    document.getElementById("Location").innerHTML = commState.location[3];
                }
                else{
                    // update coords if no addresss
                    document.getElementById("Location").innerHTML = [commState.location[1],commState.location[2]];
                }
            }
            if(commState.clientStatus.valueOf() == "INCOMING"){
                if(!ignoreRequest){
                    showIncoming();
                }
            }
            else if(commState.clientStatus.valueOf() == "IDLE"){
                // hide request and set not to ignore requests
                ignoreRequest = false;
                document.getElementById("incomingRequest").style.visibility = "hidden";
                // hide map so it doesnt take space on screen
                document.getElementById("map").style.display = "none";
                // show status
                document.getElementById("servicing").innerHTML = "waiting";
            }
            // updating status of driver
            else if(commState.clientStatus.valueOf() == "DELIVERING"){
                document.getElementById("servicing").innerHTML = "en route";
                // show map
                document.getElementById("map").style.display = "block";
                // to prevent heavy loading of google map in iframe
                if (isMapChange)
                    refreshMap();
            }
            isMapChange = false;
        } else { // error with JSON
            console.log("Error getting JSON");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
            // clearInterval(intervalGet); // stops AJAX on error - disabled in DEBUG mode
        }
    }
};

// POST result
xhrPost.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "UPDATE_OK" ) {
            isMapChange = true;
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
var doPost = function(route,data){
    xhrPost.open("POST", baseUrl + "/api.php/driver/" + route, true);
    xhrPost.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPost.send(data);
}


// GET request
var doGet = function(){
    xhrGet.open("GET", baseUrl + "/api.php/driver/", true);
    xhrGet.send();
}

// call doGet update every 5 seconds
var intervalGet = setInterval(doGet, 5000);
// call doGet first time
doGet();

//window.onload = geoLocate();