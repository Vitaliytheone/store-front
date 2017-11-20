<section class="min-height">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-title">Cart</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="cart bg-white table-responsive">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Package name</th>
                            <th>Details</th>
                            <th class="col-lg-1">Price</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td>
                                        <?= $item['id'] ?>
                                    </td>
                                    <td>
                                        <?= $item['package_name'] ?>
                                    </td>
                                    <td>
                                        <?= $item['link'] ?>
                                    </td>
                                    <td>
                                        <?= $item['price'] ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="/cart/remove/<?= $item['key'] ?>" class="btn btn-xs btn-default">
                                            Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-offset-8 col-lg-4">
                <div class="cart bg-white cart-total">
                    <strong>Total price </strong><div class="cart-total__price pull-right"><?= $total ?></div>
                </div>
            </div>
        </div>

        <form id="orderForm" class="form" method="POST" action="/cart">
            <div class="row">
                <div class="col-lg-offset-8 col-lg-4">
                    <div class="cart bg-white">
                        <?php if (!empty($error)) : ?>
                            <div class="error-summary alert alert-danger"><?= $errorText ?></div>
                        <?php endif; ?>
                        <?php if (1 < count($methods)) : ?>
                            <div class="form-group">
                                <label>Payment method</label>

                                <div>
                                    <?php foreach ($methods as $methodId => $methodName) : ?>
                                        <label class="radio-inline">
                                            <input type="radio" name="OrderForm[method]" id="inlineRadio1" value="<?= $methodId?>"> <?= $methodName ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="control-label" for="orderform-email">Email address</label>
                            <input id="orderform-email" class="form-control" name="OrderForm[email]" value="<?= $data['email'] ?>" placeholder="mail@gmail.com" aria-required="true" aria-invalid="true" type="text">
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-6">
                    <a href="/" class="btn btn-default">Continue shopping</a>
                </div>
                <div class="col-lg-6 text-right">
                    <button type="submit" id="orderButton" class="btn btn-primary" name="order-button">Proceed to Checkout</button>
                </div>
            </div>

            <input type="hidden" name="<?= $csrfname ?>" value="<?= $csrftoken ?>">
        </form>
    </div>
</section>