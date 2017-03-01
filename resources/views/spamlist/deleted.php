<?php
    require dirname(__FILE__) . '/../header.php';
?>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    <?php echo $app->make('App\Services\TemplateService')->clearValue($item->name); ?>
                </h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="jumbotron">
                    <p>Lista została usunięta <strong><?php echo $item->deleted_at; ?></strong> przez <strong><?php echo $app->make('App\Services\TemplateService')->getUserProfileUrl($item->deletedBy); ?></strong></p>

                    <a href="<?php echo route('spamlistLogsUrl', ['uid' => $item->uid]); ?>">zobacz wydarzenia z tej listy</a>
                </div>
            </div>
        </div>

<?php
    require dirname(__FILE__) . '/../footer.php';