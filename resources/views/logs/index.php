<?php
    require dirname(__FILE__) . '/../header.php';
    
?>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-lg-4">
                <ol class="breadcrumb">
                    <li class="active">
                        wydarzenia <?php echo $spamlist !== null ? 'ze spamlisty "<a href="' . route('getSpamlistUrl', array('uid' => $spamlist->uid)) . '">' . $app->make('App\Services\TemplateService')->clearValue($spamlist->name) . '</a>"' : ''; ?>
                    </li>
                </ol>
            </div>

            <div class="col-lg-8">
                <form class="form-horizontal" action="<?php echo $spamlist !== null ? route('spamlistLogsUrl', ['uid' => $spamlist->uid]) : route('logsUrl'); ?>" id="searchForm">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">Nick: </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="query" value="<?php echo $app->make('App\Services\TemplateService')->clearValue($query); ?>">
                        </div>

                        <div class="col-sm-3">
                            <button class="btn btn-default" type="submit">Szukaj</button>
                            <button class="btn btn-default" type="button" onClick="document.location.href = '<?php echo $spamlist !== null ? route('spamlistLogsUrl', ['uid' => $spamlist->uid]) : route('logsUrl'); ?>';">Wyczyść</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
<?php
    if ($paginator->count() === 0) {
?>
                <div class="alert alert-info" role="alert" style="margin-top: 25px">Brak wydarzeń</div>
<?php
    } else {
?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Wydarzenie</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
        foreach ($paginator as $key => $foreachItem) {
?>
                        <tr>
                            <td><?php echo $foreachItem['created_at']; ?></td>
                            <td>
<?php
    if ($spamlist === null && is_object($foreachItem->spamlist()->withTrashed()->first())) {
        $logSpamlist = $foreachItem->spamlist()->withTrashed()->first();
?>
                    <a href="<?php echo route('getSpamlistUrl', array('uid' => $logSpamlist->uid)); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($logSpamlist->name); ?></a> - 
<?php
    }
?>
                                <?php echo $app->make('App\Services\LogService')->getLogText($foreachItem); ?>
                            </td>
                        </tr>
<?php
        }
?>
                    </tbody>
                </table>
<?php
    }
?>

                <div style="text-align: center">
<?php
    echo $paginator->render();
?>
                </div>
            </div>

<?php
    require dirname(__FILE__) . '/../footer.php';