<?php

    /* @var $this \yii\web\View */
    /* @var $panel \common\models\panels\Project */
    /* @var $logItems \my\models\search\ActivitySearch */
    /* @var $filters array */
    /* @var $events array */
    /* @var $activity array */
    /* @var $accounts array */
    /* @var $queryTypes array */
    /* @var $interval integer */



    use yii\helpers\Html;
    use my\assets\HighChartsAssets;
    use my\assets\DatetimepickerAssets;

    DatetimepickerAssets::register($this);
    HighChartsAssets::register($this);

    $this->context->addModule('activityController', [
        'filters' => $filters
    ]);
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= $this->title ?></h2>
    </div>
</div>

<!-- Error -->
<div class="alert alert-danger alert-dismissible hidden" id="errorContainer">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
    <strong>Error!</strong> <span class="message-container"></span>
</div>

<div class="row">
    <div class="col-lg-12">
        <!-- Activity log wrap Start-->
        <div class="activity-log" id="activityLogContainer">
            <div class="activity-log__content">
                <div class="activity-log__content-body">
                    <!-- Chart Start -->
                    <div class="activity-log__chart-body">
                        <div class="activity-log__content-chart">
                            <span class="activity-log__events-count">Events <span id="countItems"></span></span>
                            <div class="activity-log-chart" id="events-count"></div>
                        </div>
                    </div>
                    <!-- Chart END -->
                    <!-- Filters panel Start-->
                    <div class="activity-log__content-header">
                        <div class="activity-log__panel-header">
                            <div class="activity-log__sections-bar">
                                <div class="row">
                                    <form class="form-inline" method="GET" id="activitySearch">
                                        <div class="activity-log__date-block">
                                            <div class="input-group__block input-group__block-from">
                                                <div class="input-group">
                                                    <span class="input-group-addon"  data-toggle="tooltip">From</span>
                                                    <?= Html::textInput('from', $filters['from'], [
                                                        'class' => 'form-control',
                                                        'id' => 'date-from'
                                                    ])?>
                                                </div>
                                            </div>
                                            <div class="input-group__block input-group__block-to">
                                                <div class="input-group">
                                                    <span class="input-group-addon" data-toggle="tooltip">to</span>
                                                    <?= Html::textInput('to', $filters['to'], [
                                                        'class' => 'form-control',
                                                        'id' => 'date-to'
                                                    ])?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="activity-log__30">
                                            <?= Html::dropDownList('account', $filters['account'], [], [
                                                'id' => 'account',
                                                'class' => 'selectpicker',
                                                'data-live-search' => 'true',
                                                'data-max-options' => '100',
                                                'data-actions-box' => 'true',
                                                'data-size' => '10',
                                                'data-title' => 'Accounts',
                                                'multiple' => 'multiple'
                                            ])?>
                                        </div>
                                        <div class="activity-log__30">
                                            <?= Html::dropDownList('event', $filters['event'], [], [
                                                'id' => 'event',
                                                'class' => 'selectpicker',
                                                'data-live-search' => 'true',
                                                'data-max-options' => '100',
                                                'data-actions-box' => 'true',
                                                'data-size' => '10',
                                                'data-title' => 'Events',
                                                'multiple' => 'multiple'
                                            ])?>
                                        </div>
                                        <div class="activity-log__40">
                                            <div class="input-group">
                                                <?= Html::textInput('query', $filters['query'], [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Search'
                                                ])?>
                                                <div class="input-group-btn">
                                                    <?= Html::dropDownList('query_type', $filters['query_type'], $queryTypes, [
                                                        'class' => 'selectpicker',
                                                    ])?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-search__block">
                                            <button class="btn btn-primary btn-search" type="submit"><span class="fa fa-search"></span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filters panel End-->
                    <!-- Activity log Table Start-->
                    <div class="activity-log__content-table table-responsive">
                        <table>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Account</th>
                                <th>Event</th>
                                <th>Details</th>
                                <th>IP address</th>
                            </tr>
                            </thead>
                            <tbody id="itemsContainer"></tbody>
                        </table>
                    </div>
                    <!-- Activity log Table End-->
                </div>
            </div>
            <div class="activity-log__pagination" id="paginationContainer"></div>
        </div>
        <!-- Activity log wrap End-->
    </div>
</div>