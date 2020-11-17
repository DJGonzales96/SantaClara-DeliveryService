// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
const baseUrl = "http://localhost/SantaClara-DeliveryService";
var commState;
var numOfDeliveries = 0;

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
// updates deilvery table and for new 
// transactions/deliveries in drivers queue
var updateDeliveryTable = function() {
    if(commState != null){
        var deliveries = commState['currentTransactions'];
        var numDels = deliveries.length;

        console.log(deliveries,numDels);

        // only update table if new deliveries
        if(numDels > numOfDeliveries){
                //Update table of deliveries
            for(i = 0; i < numDels; i++){
                var table = document.getElementById("delivery-table");
                var tableLen = table.rows.length;
                var currRow = table.insertRow(-1);

                // add addresss of delivery
                var deliveryNum = currRow.insertCell(0);
                var delivery = currRow.insertCell(1);
                deliveryNum.style.fontWeight = "bold";
                deliveryNum.innerHTML = tableLen;
                delivery.innerHTML = deliveries[i];

            }
            //UPDATE number of deliveries in table
            numOfDeliveries = numDels;
        }
    }
    else
        console.log("nothing yet");
   
}
var hideIncoming = function(){
    var incoming = document.getElementById("incomingRequest");
    incoming.style.visibility = "hidden";
}
var showIncoming = function(){
    if(commState != null){
        var incoming = document.getElementById("incomingRequest");
        // TODO:
             // SET FIELDS:
                // - ADDR - document.getElementById("destAddr").innerHTML;
                // - $$ -   document.getElementById("deliveryCash").innerHTML;
        incoming.style.visibility = "visible";

    }
}
// rejects delivery
//Post - addr rejecting
var reject = function(){
    var rejAddr = document.getElementById("destAddr").innerHTML;
    doPost("reject",rejAddr)
}
//post -> accept 
// why does it reload the page?
var accept = function(){
    //POST method with delivery addr
    var dstAddr = document.getElementById("destAddr").innerHTML; // send to Post

    // var cost = document.getElementById("deliveryCash").innerHTML;

    // data => address of delivery
    doPost("accept",dstAddr)

    console.log("accepted");
}

// setup UI to hide some elements
document.getElementById("incomingRequest").style.visibility = "hidden";

//accept/reject a delivery to your route
document.getElementById("buttonAccept").addEventListener("click", accept);
document.getElementById("buttonReject").addEventListener("click", reject);

//get driver curr location
document.getElementById("buttonLocation").addEventListener("click", geoLocate, true);


// set UI updating mechanism on GET
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "STATUS_OK" ) {
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            // show incoming message
            updateDeliveryTable();
            if (commState.location)
                // keeps updating location
                document.getElementById("CurrentLocation").value = commState.location;
            if(commState.clientStatus.valueOf() == "INCOMING"){ 
                // checks they can only service 2 at a time
                if(numOfDeliveries <= 2){
                    // show incoming message
                    showIncoming();
                }
                else{
                    console.log("cannot get new requests");
                    // post to server, driver rejected
                    // doPost("reject",data); ------
                }
            }
            else if(commState.clientStatus.valueOf() == "IDLE"){
                console.log("waiting...");
                hideIncoming();
            } 
        }
        else if(commState.status.valueOf() == "UPDATE_OK"){
            console.log("update ok");

        }
        else {
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
var doPost = function(route,data){
    console.log(data);
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




