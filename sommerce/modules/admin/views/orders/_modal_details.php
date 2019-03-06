<div id="suborder-details-modal" class="modal fade order-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="order-detail-provider">
                        <?= Yii::t('admin', 'orders.details_provider') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-provider" value="" readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-order-id">
                        <?= Yii::t('admin', 'orders.details_order_id') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-provider-order-id" value="" readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-response">
                        <?= Yii::t('admin', 'orders.details_response') ?>
                    </label>
                    <pre class="sommerce-pre readonly" id="order-detail-provider-response"></pre>
                </div>
                <div class="form-group">
                    <label for="order-detail-lastupdate">
                        <?= Yii::t('admin', 'orders.details_last_update') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-lastupdate" value="" readonly>
                </div>
            </div>
        </div>
    </div>
</div>