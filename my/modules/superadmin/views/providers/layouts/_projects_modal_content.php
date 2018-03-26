<?php
    /* @var $this yii\web\View */
    /* @var $projects[] common\models\panels\Project */
    /* @var $project common\models\panels\Project */
?>

<?php foreach ($projects as $project) : ?>
    <div class="row">
       <div class="col-md-12">
           <?= $project->name ?>
       </div>
    </div>
<?php endforeach; ?>
