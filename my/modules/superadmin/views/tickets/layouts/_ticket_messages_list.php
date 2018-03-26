<?php
    /* @var $this yii\web\View */
    /* @var $ticketMessages \common\models\panels\TicketMessages */
    /* @var $message \common\models\panels\TicketMessages */

    use yii\helpers\ArrayHelper;
    use yii\bootstrap\Html;
    use my\helpers\Url;
?>

<?php foreach ($ticketMessages as $message) : ?>
    <?php if ($message->cid != 0): ?>

        <?php
            $customer = $message->customer;
            $projects = [];
            foreach ($customer->actualProjects as $project) {
                $projects[] = Html::a($project->getSite(), Url::toRoute([
                    $project->child_panel ? '/child-panels' : '/panels',
                    'id' => $project->id
                ]), [
                    'target' => '_blank'
                ]);
            }
            ArrayHelper::getColumn($customer->actualProjects, 'site');
        ?>
        <div class="row m-b">
            <div class="col-sm-11 user">
                <div class="message"><?= nl2br(htmlspecialchars($message->message)) ?></div>
                <div class="info">

                    <span class="author"><?= Html::a($customer->email, Url::toRoute(['/customers', 'id' => $customer->id])) ?></span> <?= (!empty($projects) ? " (" . implode(", ", $projects) . ")": "") ?>
                    <span class="time"><?= $message->getFormattedDate('date') ?></span>
                </div>
            </div>
            <div class="col-sm-1"></div>
        </div>
    <?php else: ?>
        <div class="row m-b">
            <div class="col-sm-1"></div>
            <div class="col-sm-11 support">
                <div class="message"><?= nl2br(htmlspecialchars($message->message)) ?></div>
                <div class="info text-right">
                    <span class="author"><?= $message->admin->getFullName() ?></span>
                    <span class="time"><?= $message->getFormattedDate('date') ?></span>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endforeach ?>