<?php
$highlight="APP";
$title="restaurantApp - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<style>
@media (max-width: 1024px){
#clientAddress{
    width: 60px !important;
}
#food{
    width: 60px !important;
}
/* #deliveries td{
    width: 100% !important;
} */

</style>
<main class="page login-page" style="height:100%">
    <section class="clean-block clean-form dark" style="min-height: 100%">
        <div class="container">
            <div class="block-heading" style="margin-left:auto;margin-right:auto">
              <h2 class="text-info" style="display:inline-block; margin-top:60px">Welcome </h2>
              <h2 class="text-info" id="friendlyName" style="display:inline-block; margin-left: 20px">[RestaurantUserName]</h2>
            </div>
            <form style="max-width:75%">
              <label for="status" style="margin-right:5%">Restaurant Address:</label>
              <h6 class="status" id="restAddr" style="display:inline-block; color:#343a40">Restaurant Address</h6>
              <h6 class="status" id="wallet" style="display:inline-block; color:#343a40; margin-left: 100px">Wallet: </h6>
              <h6 class="status" id="money" style="display:inline-block; color:#343a40;">$0.00</h6>
                <div class="form-group" style="display:flex; flex-flow: row warp; align-items: center">
                  <label for="status" style="margin-right:5%">Client address:</label>
                  <input class="form-control form-control-lg" type="text" id="clientAddress" name="clientAddress" style="width: 400px">

                  <button class="btn btn-primary" id="request" style=" height: calc(1.5em + .75rem + 2px); margin-left: 50px" type="submit" onclick="doPost(event)" disabled>Request</button>
                </div>

                <div>
                  <div class="form-group" style="display:flex; flex-flow: row warp; align-items: center">
                    <label for="status" style="margin-right:5%">Food Ordered:</label>
                    <input class="form-control form-control-lg" type="text" id="food" name="food" style="width: 400px">
                    </div>
                </div>

                <div style="display:inline-block">
                  <span style="margin-right: 20px">Current deliveries:</span>
                </div>
                <table class="table table-bordered" id="deliveries">
                  <thead>
                    <tr>
                        <th>Delivery</th>
                        <th>Address</th>
                        <th>Order</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td> </td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  </tbody>
                </table>
                </form>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Request Submitted!</h5>
          </div>
          <div class="modal-body">
          <div style="display:inline-block">
           <p>Searching for drivers for <span id = "foodRequest" style="color:#343a40">[food]</span> <span> to </span><span id = "addressRequest" style="color:#343a40">[address]</span><span> for $</span><span id = "costRequest" style="color:#343a40">[$$$]</span></p>
           <p>Please wait for a driver.</p>
           <p>Thank you very much for your patience.</p>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="cancelbtn" onclick="doCancel()">Cancel Order</button>
          </div>
        </div>
      </div>
    </div>
</main>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/restaurantapp.js"></script>
<?php
include 'includes/footer.php';
?>
