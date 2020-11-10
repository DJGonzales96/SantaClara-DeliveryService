// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
var commState;
var dels;
var numOfDeliveries = 0;


var i = 1;

var geoLocate = function() {
    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        let inputLocation = document.getElementById("CurrentLocation").value || "NULL";
        let postRequest = "lat="+latitude+"&long="+longitude+"&address="+encodeURIComponent(inputLocation);
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

// setup UI to hide some elements
document.getElementById("incomingRequest").style.visibility = "hidden";
// document.getElementById("incomingRequest").addEventListener("click", AcceptDelivery);

// add delivery to current deliveries
document.getElementById("buttonAccept").addEventListener("click", geoLocate, true);


function AcceptDelivery(){
    //POST method with delivery addr


    // call updateDeliveries()
    console.log("accepted")

}
//checks to see if there are any incoming requests
setInterval(showIncoming,3000);

function showIncoming(){
    var incoming = document.getElementById("incomingRequest");
    doGet();
    if(dels != null){
        var driverStatus = dels['driverStatus'];
        // if incoming set accept/reject visisble
        if(driverStatus == "INCOMING"){
            // console.log(driverStatus);
            // TODO:
            // SET FIELDS:
                // - ADDR
                // - $$

            incoming.style.visibility = "visible";
       }
       else{
        // console.log("not incoming");
        incoming.style.visibility = "hidden";
       }
    }

}
//get deliveries + update table every n secs
setInterval(updateDeliveries,2000);

function updateDeliveries(){
    doGet();
    if(dels != null){
        var deliveries = dels['currentTransactions'];
        var numDels = dels['currentTransactions'].length;

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
                deliveryNum.innerHTML = tableLen + 1;
                delivery.innerHTML = deliveries[i];

            }
            //UPDATE number of deliveries in table
            numOfDeliveries = numDels;
        }
    }
    else
        console.log("nothing yet");
}

// set UI updating mechanism on GET
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "STATUS_OK" ) {
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            if (commState.location)
                document.getElementById("CurrentLocation").value = commState.location;

            console.log("Call number:" + i++); // DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE
        } else {
            console.log("Error getting JSON");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
            clearInterval(intervalGet);
        }
    }
};

// POST result
xhrPost.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "UPDATE_OK" ) {
            console.log("Update okay."); // DEBUG REMOVE DEBUG REMOVE DEBUG REMOVE
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
    xhrPost.open("POST", "http://localhost/scm/api.php/driver/" + route, true);
    xhrPost.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPost.send(data);
}

// GET request
function doGet(){
    xhrGet.open("GET", "http://localhost/scm/api.php/driver", true);
    xhrGet.onload = () => {
        var resp = xhrGet.responseText;
        dels = JSON.parse(resp);
        //console.log(dels);
    }
    xhrGet.send();
}
// call doGet first time
//doGet();




