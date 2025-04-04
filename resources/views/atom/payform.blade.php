<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Atom Payment Form</h3>
            </div>
            <div class="card-body">
                <form method="post" name="customerData" action="https://payment.ssus.ac.in/api/payment/initiate">
                    @csrf <!-- CSRF Token -->

                    <h5 class="text-primary">Compulsory Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tid" class="form-label">TID</label>
                            <input type="text" id="tid" name="tid" class="form-control" readonly />
                        </div>
                         <div class="col-md-6">
                            <label for="tid" class="form-label">Register Number</label>
                            <input type="text" id="merchant_param1" name="merchant_param1" class="form-control" value="ADMAPPLI25" />
                        </div>
                        <div class="col-md-6">
                            <label for="merchant_id" class="form-label">Merchant ID</label>
                            <input type="text" id="merchant_id" name="merchant_id" class="form-control" value="3147015" />
                        </div>
                        <div class="col-md-6">
                            <label for="order_id" class="form-label">Order ID</label>
                            <input type="text" id="order_id" name="order_id" class="form-control" value="123654789" />
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" id="amount" name="amount" class="form-control" value="10.00" />
                        </div>
                        <div class="col-md-6">
                            <label for="currency" class="form-label">Currency</label>
                            <input type="text" id="currency" name="currency" class="form-control" value="INR" />
                    </div>
                        <div class="col-md-6">
                            <label for="redirect_url" class="form-label">Redirect URL</label>
                            <input type="text" id="redirect_url" name="redirect_url" class="form-control" value="https://payment.ssus.ac.in/payment/response" />
                        </div>
                        <div class="col-md-6">
                            <label for="cancel_url" class="form-label">Cancel URL</label>
                            <input type="text" id="cancel_url" name="cancel_url" class="form-control" value="https://payment.ssus.ac.in/payment/response" />
                        </div>
                        <div class="col-md-6">
                            <label for="language" class="form-label">Language</label>
                            <input type="text" id="language" name="language" class="form-control" value="EN" />
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary">Billing Information (Optional)</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="billing_name" class="form-label">Billing Name</label>
                            <input type="text" id="billing_name" name="billing_name" class="form-control" value="Charli" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_address" class="form-label">Billing Address</label>
                            <input type="text" id="billing_address" name="billing_address" class="form-control" value="Room no 1101, near Railway station Ambad" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_city" class="form-label">Billing City</label>
                            <input type="text" id="billing_city" name="billing_city" class="form-control" value="Indore" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_state" class="form-label">Billing State</label>
                            <input type="text" id="billing_state" name="billing_state" class="form-control" value="MP" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_zip" class="form-label">Billing Zip</label>
                            <input type="text" id="billing_zip" name="billing_zip" class="form-control" value="425001" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_country" class="form-label">Billing Country</label>
                            <input type="text" id="billing_country" name="billing_country" class="form-control" value="India" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_tel" class="form-label">Billing Tel</label>
                            <input type="text" id="billing_tel" name="billing_tel" class="form-control" value="9876543210" />
                        </div>
                        <div class="col-md-6">
                            <label for="billing_email" class="form-label">Billing Email</label>
                            <input type="email" id="billing_email" name="billing_email" class="form-control" value="test@test.com" />
                        </div>
                    </div>

                    <hr>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Pay Atom Gateway >>>>></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
