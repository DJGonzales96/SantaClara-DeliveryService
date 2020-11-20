// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
const baseUrl = "http://localhost/cs160/scd";
var commState;
var ignore = false;

// global var to hide/show map for driver
// TODO: Show route of first delivery in table
var map = document.getElementById("map");


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

        // console.log("number deliveries:",numDels);


        //Update table of deliveries
        for(i = 0; i < numDels; i++){
            var table = document.getElementById("delivery-table");
            var tableLen = table.rows.length ;
            // console.log("current table length:",tableLen);

            // only updates table up to two elements
            // table - 1 => header counts as row
            if((tableLen - 1) < 2){
                var currRow = table.insertRow(-1);

                // add addresss of delivery
                var deliveryNum = currRow.insertCell(0);
                var delivery = currRow.insertCell(1);
                var cost = currRow.insertCell(2);
                var button = currRow.insertCell(3);

                // delivery number
                deliveryNum.style.fontWeight = "bold";
                deliveryNum.innerHTML = tableLen;

                // update delivery
                delivery.innerHTML = deliveries[i][5];

                // cost
                cost.innerHTML = deliveries[i][7];
                var btn;

                // button to mark delivery as done
                btn = document.createElement('input');
                btn.type = "button";
                btn.className = "markdone-btn";
                btn.value = "mark as done";
                btn.onclick = function(){
                    let markdone = 1;                   //TODO: delivery id
                    doPost("done",markdone);
                }
                button.innerHTML = "<td><input id="+tableLen+" style='width:100%; background:#007bff; color:white; margin:0; border:none' type='button'value='done' onclick='markDone(this)'>";

            }
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
        document.getElementById("destAddr").innerHTML = commState.deliveryRequestInfo[3] || "not available";
        document.getElementById("deliveryCash").innerHTML = commState.deliveryRequestInfo[5];

        var incoming = document.getElementById("incomingRequest");
        incoming.style.visibility = "visible";

    }
}
// rejects delivery
//Post - addr rejecting
var reject = function(){
    ignore = true;
    hideIncoming();
}
//post -> accept
// why does it reload the page?
var accept = function(){
    // get post data from commstate
    let request_ID = commState.deliveryRequestInfo[0];
    let cost = commState.deliveryRequestInfo[5];
    let post = "request_ID=" + request_ID + "&cost=" + cost;

    // send post
    doPost("accept",post);

    // hide message
    hideIncoming();
    // show map
    document.getElementById("map").style.textAlign = "center";


    console.log("accepted");
}
markDone = (event) => {
    console.log("marking doneeee");

    // get button id = delivery number
    var rowId =  event.id - 1;
    console.log(rowId);

    // get transaction id from currenTransactions
    var t_id = commState.currentTransactions[rowId][0];
    console.log(t_id);

    // send post
    doPost("delivered",t_id);
}

// setup UI to hide some elements
document.getElementById("incomingRequest").style.visibility = "hidden";

//accept/reject a delivery to your route
document.getElementById("buttonAccept").addEventListener("click", accept);
document.getElementById("buttonReject").addEventListener("click", reject);

//get driver curr location
document.getElementById("buttonLocation").addEventListener("click", geoLocate, true); // TODO: How do i get addr from this?


// set UI updating mechanism on GET
xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "STATUS_OK" ) {
            document.getElementById("friendlyName").innerHTML = commState.friendlyName;
            // show incoming message
            updateDeliveryTable();
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
                if(ignore){
                    hideIncoming();
                }
                else{
                    showIncoming();
                }
            }
            // map is hidden in IDLE state only
            else if(commState.clientStatus.valueOf() == "IDLE"){
                console.log("waiting...");
                document.getElementById("servicing").innerHTML = "waiting";
                hideIncoming();

                // hide map so it doesnt take space on screen
                map.style.display = "none";
            }
            // updating status of driver
            else if(commState.clientStatus.valueOf() == "DELIVERING"){
                document.getElementById("servicing").innerHTML = "en route";
                map.style.textAlign = "center";
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