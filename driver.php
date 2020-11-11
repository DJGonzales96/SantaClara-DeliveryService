<?php
$highlight="APP";
$title="Home - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<main class="page login-page" style="height: 100%">
        <section class="clean-block clean-form dark" style="height: 100%">
            <div class="container">
                <div class="block-heading" style="margin-left:auto;margin-right:auto">
                  <h2 class="text-info" style="display:inline-block">Welcome </h2>
                  <h2 id="friendlyName" class="text-info" style="display:inline-block"></h2>
                </div>
                <form style="max-width:75%">
                    <div class="form-group" style="display:flex; flex-flow: row warp; align-items: center">
                      <label for="status" style="margin-right:5%">Status:</label>
                      <h6 class="status" style="display:inline-block; color:#007bff">Servicing 1 delivery</h6>
                    </div>

                    <div class="form-group" style="display:flex; flex-flow: row warp; align-items: center"><label for="currentLocation" style="margin-right:5%; min-width: 140px">Current Location:</label><input class="form-control item" type="currentLocation" id="CurrentLocation" placeholder="Enter location (or leave empty) and click Set Location"></div>
                    <button id="buttonLocation" class="btn btn-primary" style="height: calc(1.5em + .75rem + 2px); margin-right: 15px" type="button">Set Location</button>

                    <div id="incomingRequest" class="form-group" style="display:inline-block; width:100%">
                      <div style="display:inline-block;width:60%">
                      <br/>
  				             <span>New dispatch avaiable to </span><span id="destAddr" style="color:#ffc107">[destination address]</span> <span>for </span><span id="deliveryCash" style="color:#3b99e0">$37</span><span>, what would you like to do?</span>
                     </div>
                      <div style="display:inline-block">
                       <button id="buttonAccept" class="btn btn-primary" style="height: calc(1.5em + .75rem + 2px); margin-right: 15px" type="button">Accept</button>
                         <button id="buttonReject" class="btn btn-primary" style=" height: calc(1.5em + .75rem + 2px); margin-right: 15px" type="button">Reject</button>
                       </div>
                    </div>

                    <div style="display:inline-block">
                      <span style="margin-right: 20px">Current deliveries:</span>
                    </div>
                    <table id="delivery-table", class="table table-bordered">
                      <!-- add table headers -->
                      <tbody>
                        <tr>
                          <th>Delivery Number</th>
                          <th>Delivery Address</th>
                          <!-- TODO: <th>Status</th> -->
                        </tr>                      
                      </tbody>
                    </table>
                    </form>
            </div>
            </div>
        </section>
    </main>
<script src="assets/js/driverapp.js"></script>
<?php
include 'includes/footer.php';
?>