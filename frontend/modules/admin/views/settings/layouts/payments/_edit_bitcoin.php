<!-- BEGIN: Subheader -->
<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                Edit Bitcoin
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="img/bitcoin.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <ol>
                    <li>Sign up at <a href="https://gear.mycelium.com/" target="_blank">Mycelium Gear</a></li>
                    <li>Create new gateway <a href="https://admin.gear.mycelium.com/gateways/new" target="_blank">https://admin.gear.mycelium.com/gateways/new</a>
                        <ul>
                            <li>Callback url: <code>http://twig.perfectpanel.net/bitcoin</code>
                            </li>
                            <li>After payment redirect to: <code>http://twig.perfectpanel.net/addfunds</code>
                            </li>
                            <li>Back url: <code>http://twig.perfectpanel.net/addfunds</code>
                            </li>
                            <li>Default currency for prices: <i>Choose your panel currency</i>
                            </li>
                        </ul>
                    </li>
                    <li>Enter your Gateway secret and API Gateway ID below.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="bitcoin_api_gateway_id">API Gateway ID</label>
        <input type="text" class="form-control" id="bitcoin_api_gateway_id" placeholder="">
    </div>
    <div class="form-group">
        <label for="bitcoin_geteway_secret">Gateway secret</label>
        <input type="text" class="form-control" id="bitcoin_geteway_secret" placeholder="">
    </div>

    <hr>

    <button class="btn btn-success m-btn--air">Save changes</button>
    <a href="settings-payments.html" class="btn btn-secondary m-btn--air">Cancel</a>

</div>
