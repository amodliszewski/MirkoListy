<?php
    require dirname(__FILE__) . '/../header.php';
    
?>
        <div class="row">&nbsp;</div>

        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo route('getSpamlistUrl', array('uid' => $item['uid'])); ?>"><?php echo $app->make('App\Services\TemplateService')->clearValue($item['name']); ?></a>
                    </li>
                    <li class="active">użytkownicy</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <form class="form-horizontal" action="<?php echo route('postSpamlistPeopleUrl', ['uid' => $item['uid']]); ?>" id="searchForm">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">Sortuj po: </label>
                        <div class="col-sm-4">
                            <select class="form-control" name="sort" id="sortSelect">
                                <option value="joined.desc">dacie dołączenia (od najnowszych)</option>
                                <option value="joined.asc"<?php echo $sortBy === 'joined.asc' ? ' selected="selected"' : ''; ?>>dacie dołączenia (od najstarszych)</option>
                                <option value="nick.asc"<?php echo $sortBy === 'nick.asc' ? ' selected="selected"' : ''; ?>>alfabetycznie</option>
                            </select>
                        </div>

                        <label class="col-sm-1 control-label">Nick: </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="query" value="<?php echo $app->make('App\Services\TemplateService')->clearValue($query); ?>">
                        </div>

                        <div class="col-sm-3">
                            <button class="btn btn-default" type="submit">Szukaj</button>
                            <button class="btn btn-default" type="button" onClick="document.location.href = '<?php echo route('postSpamlistPeopleUrl', ['uid' => $item['uid']]); ?>';">Wyczyść</button>
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
                <div class="alert alert-info" role="alert" style="margin-top: 25px">Brak zapisanych</div>
<?php
    } else {
?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nick</th>
                            <th>Rola</th>
                            <th>Data dołączenia</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
        foreach ($paginator as $key => $foreachItem) {
?>
                        <tr class="<?php echo $foreachItem['rights'] == 2 ? 'danger' : ($foreachItem['rights'] >= 20 ? 'success' : ''); ?>">
                            <td><?php echo $foreachItem['nick']; ?></td>
<?php
            if ($canChangeRights) {
?>
                            <td>
                                <form method="POST" action="<?php echo route('postSpamlistPeopleUrl', array('uid' => $item['uid'])); ?>">
                                    <div class="input-group" style="width: 300px">
                                        <select class="form-control" name="rights">
                                            <option value="2">zbanowany</option>
                                            <option value="10"<?php echo $foreachItem['rights'] == 10 ? ' selected="selected"' : ''; ?>>użytkownik</option>
                                            <option value="20"<?php echo $foreachItem['rights'] == 20 ? ' selected="selected"' : ''; ?>>wołający</option>
                                            <option value="99"<?php echo $foreachItem['rights'] == 99 ? ' selected="selected"' : ''; ?>>administrator</option>
                                            <option value="-1">-- USUŃ Z LISTY --</option>
                                        </select>
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                                        <input type="hidden" name="userId" value="<?php echo $foreachItem['user_id']; ?>" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-share"></i></button>
                                        </span>
                                    </div>
                                </form>
                            </td>
<?php
            } else {
?>
                            <td><?php echo $app->make('App\Services\SpamlistService')->getUserSpamlistRightsText($foreachItem['rights']); ?></td>
<?php
            }
?>
                            <td><?php echo $foreachItem['created_at']; ?></td>
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

            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('#sortSelect').on('change', function() {
                        jQuery('#searchForm').submit();
                    });
                });
            </script>
<?php
    require dirname(__FILE__) . '/../footer.php';