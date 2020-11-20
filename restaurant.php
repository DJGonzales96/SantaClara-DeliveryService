<?php
$highlight="APP";
$title="restaurantApp - Santa Clara Menus";
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'includes/header.php';
?>
<style>
@media (min-width: 576px)
.clean-navbar.fixed-top+.page {
    padding-top: 0;
}
</style>
<main class="page login-page" style="height:100%">
    <section class="clean-block clean-form dark" style="min-height: 100%">
        <div class="container">
            <div class="block-heading" style="margin-left:auto;margin-right:auto">
              <h2 class="text-info" style="display:inline-block">Welcome </h2>
              <h2 class="text-info" id="friendlyName" style="display:inline-block; margin-left: 20px">[RestaurantUserName]</h2>
            </div>
            <form style="max-width:75%">
                <div class="form-group" style="display:flex; flex-flow: row warp; align-items: center">
                  <label for="status" style="margin-right:5%">Client address:</label>
                  <input class="form-control form-control-lg" type="text" id="clientAddress" name="clientAddress" style="width: 400px">

                  <button class="btn btn-primary" id="request" style=" height: calc(1.5em + .75rem + 2px); margin-left: 50px" type="submit" onclick="doPost(event)" disabled>Request</button>
                </div>

                <div>
                  <div class="form-check form-check-inline" style="margin-bottom: 20px">
                   <input type="radio" class="form-check-input" id="pizza" name="food" value="pizza">
                    <label class="form-check-label" for="pizza">pizza</label>
                  </div>
                  <div class="form-check form-check-inline" style="margin-bottom: 20px">
                    <input type="radio" class="form-check-input" id="hamberger" name="food" value="hamberger">
                    <label class="form-check-label" for="pizza">hamberger</label>
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
                    </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td> </td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  </tbody>
                </table>
                </form>
        </div>
    </section>
</main>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/restaurantapp.js"></script>
<?php
include 'includes/footer.php';
?>
