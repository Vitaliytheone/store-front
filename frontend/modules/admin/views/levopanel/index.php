<?php

/* @var $panels array */
/* @var $this yii\web\View */

?>

<div class="page-container">
    <div class="m-container-sommerce container-fluid">

        <div class="row">

            <div class="col">

                <div class="tab-content">
                    <div class="" id="all-orders">

                        <div class="m_datatable m-datatable m-datatable--default">

                            <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Domain</th>
                                        <th>Server IP</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody class="m-datatable__body">
                                <?php foreach ($panels as $panel): ?>
                                    <tr>
                                        <td><?= $panel['id'] ?></td>
                                        <td><?= $panel['domain'] ?></td>
                                        <td><?= $panel['server_ip'] ?></td>
                                        <td><?= $panel['status_name'] ?></td>
                                        <td><?= $panel['created_at_f'] ?></td>
                                        <td><?= $panel['updated_at_f'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>