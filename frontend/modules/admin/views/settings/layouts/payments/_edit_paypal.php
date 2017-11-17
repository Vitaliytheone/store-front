<!-- BEGIN: Subheader -->
<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                Edit PayPal
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">
    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="img/paypal.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <ol>
                    <li>Login to your PayPal account.</li>
                    <li>Get your <a
                                href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true"
                                target="_blank">API Credentials.</a></li>
                    <li>Enter your PayPal API details below.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="paypal_api_username">Api username</label>
        <input type="text" class="form-control" id="paypal_api_username" placeholder="">
    </div>
    <div class="form-group">
        <label for="paypal_api_password">Api password</label>
        <input type="password" class="form-control" id="paypal_api_password" placeholder="">
    </div>
    <div class="form-group">
        <label for="paypal_api_signature">Api signature</label>
        <input type="text" class="form-control" id="paypal_api_signature" placeholder="">
    </div>
    <div class="form-check">
        <label class="form-check-label">
            <input type="checkbox" class="form-check-input">
            Use test mode
        </label>
    </div>

    <hr>

    <button class="btn btn-success">Save changes</button>
    <a href="settings-payments.html" class="btn btn-secondary">Cancel</a>
</div>

