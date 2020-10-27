<html>
<body>
<div id="ilan">WHAT???</div>
<a href="javascript:void(0)" id="bluhLink">BLUH?</a>
</body>
</html>

<script>
var xhttp = new XMLHttpRequest();
var obj;
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       
       obj = JSON.parse(xhttp.responseText);
	document.getElementById("ilan").innerHTML = obj.isRestaurant;

	Object.keys(obj).forEach(key => {
		  console.log(key, obj[key]);
		});
	}
};

xhttp.open("GET", "http://localhost/cs160/scd/api.php", true);

document.getElementById("bluhLink").addEventListener("click", function(){
	xhttp.send();
}, true);
</script>
