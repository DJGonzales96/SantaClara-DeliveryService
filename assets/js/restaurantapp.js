// globals
const xhrGet = new XMLHttpRequest();
const xhrPost = new XMLHttpRequest();
const baseUrl = window.location.protocol + "//" + window.location.host + "/SantaClara-DeliveryService";
//const baseUrl = "http://localhost:8080/cs160/scd";
var commState;
var requestInfo;
var modalopen = false;

// bind UI elements
$('#request').click(function(e){
    e.preventDefault();
    sendRequest();
});

$('#cancelbtn').click(function(e){
    e.preventDefault();
    cancelOrder();
});

// Request a delivery
var sendRequest = function() {
        let address = document.getElementById("clientAddress").value;
        let food = document.getElementById('food').value;
        doPost("request", "address=" + encodeURIComponent(address) +"&food=" +  food);
}

// Cancel request for the user
var cancelOrder = function() {
    doPost("cancel", "request_ID=" + requestInfo[0]);

}

xhrGet.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
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
            if(commState.location) {
                document.getElementById("restAddr").innerHTML = commState.location[3];
                // update the wallet
                document.getElementById("money").innerHTML = commState.wallet;
            }

            if(commState.deliveryRequestInfo && commState.deliveryRequestInfo.length > 0) {
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
        commState = JSON.parse(this.responseText);
        if(commState.status.valueOf() == "UPDATE_OK" ) {
            doGet();
        } else if(commState.status.valueOf() == "UPDATE_FAILED" ) {
            alert("Sorry. We currently support only deliveries that are less than 40 minutes away.");
        } else {
            console.log("Error with update");
            Object.keys(commState).forEach(key => {
                console.log(key, commState[key]);
            });
        }
    }
};

// POST request to update the model, url is the routing of the request
var doPost = function(url, data){
    xhrPost.open("POST", baseUrl + "/api.php/restaurant/" + url ,true);
    xhrPost.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPost.send(data);
}

// GET request for the Table
var doGet = function(){
    xhrGet.open("GET", baseUrl + "/api.php/restaurant/", true);
    xhrGet.send();
}

var $food = $("#food");
$food.keyup(function () {
    if ($food.val().length > 0 ) {
        if($address.val().length > 0 ){
            $("#request").removeAttr("disabled");
        }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});

var $address = $("#clientAddress");
$address.keyup(function () {
    if ($address.val().length > 0 ) {
        if($food.val().length > 0 ){
            $("#request").removeAttr("disabled");
        }
    } else {
        $("#request").attr("disabled", "disabled");
    }
});

// call doGet update every 5 seconds
var intervalGet = setInterval(doGet, 5000);
// call doGet first time
doGet();
