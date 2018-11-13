<?php
    require dirname(__FILE__) . '/../header.php';
?>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    <?php echo $app->make('App\Services\TemplateService')->clearValue($item['name']); ?>
                </h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
<?php
    if ($callsPaginator->count() === 0) {
?>
                <div class="alert alert-info" role="alert" style="margin-top: 25px">Brak wpisów</div>
<?php
    } else {
        foreach ($callsPaginator as $key => $foreachItem) {
?>
                <p><i class="fa fa-clock-o"></i> Dodany: <?php echo $foreachItem['posted_at']; ?> | Ostatnio wołany: <?php echo $foreachItem['updated_at']; ?></p>

                <hr />
<?php
            if (!empty($foreachItem['image_url'])) {
?>
                <div style="width: 100%;">
                    <a href="#" data-toggle="modal" data-target="#callImagePreview<?php echo $key; ?>">
                        <img class="img-responsive img-hover" style="margin-left: auto; margin-right: auto;" src="<?php echo $foreachItem['image_url']; ?>" />
                    </a>
                </div>
                <div id="callImagePreview<?php echo $key; ?>" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <img src="<?php echo $foreachItem['big_image_url']; ?>" class="img-responsive" />
                            </div>
                        </div>
                    </div>
                </div>

                <hr />
<?php
            }
?>
                <p><?php echo $app->make('App\Services\TemplateService')->parseContent($app->make('App\Services\TemplateService')->clearValue($foreachItem->content)); ?></p>
<?php
            if ($foreachItem['link_id'] !== null) {
?>
                <a class="btn btn-primary" rel="nofollow" href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>link/<?php echo $foreachItem['link_id']; ?>">Przejdź do znaleziska <i class="fa fa-angle-right"></i></a>
<?php
            } else {
?>
                <a class="btn btn-primary" rel="nofollow" href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>wpis/<?php echo $foreachItem['entry_id']; ?>">Przejdź do wpisu <i class="fa fa-angle-right"></i></a>
<?php
            }
?>
                <hr style="border-top: 1px solid #eee; border-bottom: 1px solid #ADACAC; height: 1px;" />
<?php
        }
    }
?>

                <div style="text-align: center">
<?php
    echo $callsPaginator->appends(\Illuminate\Support\Facades\Input::except('page'))->render();
?>
                </div>
            </div>

            <div class="col-md-4">

                <div class="well">
                    <form method="POST" action="<?php echo route('postSpamlistJoinUrl', array('uid' => $item['uid'])); ?>">
                        <div class="input-group">
                            Założona przez:<br />
                            <p>
                                <a href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>ludzie/<?php echo $item->user->nick; ?>" rel="nofollow" target="_blank" style="color: <?php echo $app->make('WykoCommon\Services\TemplateService')->getGroupColor($item->user->color); ?>">
                                    <img src="<?php echo $item->user->avatar_url; ?>" class="img-polaroid <?php echo $app->make('App\Services\TemplateService')->getSexClass($item->user->sex); ?>" />
                                    <span style="margin-left: 10px;"><?php echo $item->user->nick; ?></span>
                                </a>
                            </p>
                            Osób na liście: <a href="<?php echo route('getSpamlistPeopleUrl', array('uid' => $item['uid'])); ?>"><strong><?php echo $item['joined_count']; ?></strong></a><br />
                            Wołań: <strong><?php echo $item['called_count']; ?></strong><br />
<?php
    if ($canEdit) {
?>
                            <a href="<?php echo route('getSpamlistEditUrl', array('uid' => $item['uid'])); ?>" class="btn btn-default">Edytuj</a>
<?php
    }
?>
                        </div>
<?php
    if ($joinable) {
?>
                        <div class="input-group" style="padding-top: 15px">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <div>
                                <button class="btn btn-default" type="submit"><?php echo $isOnList === true ? 'opuść' : 'dołącz'; ?></button>
                            </div>
                        </div>
<?php
    } else if (!session()->has('wykopNick')) {
?>
                        <div class="input-group" style="padding-top: 15px">
                            <div>
                                <button class="btn btn-default" onclick="document.location='<?php echo $app->make('WykoCommon\Services\WykopService')->getLoginUrl(true); ?>'; return false;" type="submit">zaloguj się jeśli chcesz dołączyć</button>
                            </div>
                        </div>
<?php
    }
?>
                    </form>
                </div>

                <div class="well">
                    <?php echo $app->make('App\Services\TemplateService')->clearValue($item['description']); ?>
<?php
    if (!empty($item->category) || !empty($item->city)) {
?>
                    <br /><br />
<?php
    }

    if (!empty($item->category)) {
?>
                    <strong>Kategoria:</strong> <?php echo $item->category->name; ?><br />
<?php
    }

    if (!empty($item->city)) {
?>
                    <strong>Miasto:</strong> <?php echo $item->city->name; ?><br />
<?php
    }
?>
                </div>
<?php
    if ($canCall) {
?>
                <div class="well">
                    <h4>Wołaj</h4>
                    <div class="input-group">
                        <div class="alert alert-info">
                            Organizujesz imprezę, turniej lub inne wydarzenie?<br />
                            Dodaj je na <a href="http://wykoevent.pl">WykoEvent</a>
                        </div>
                        <p>Wklej link do Twojego wpisu i zawołaj zainteresowanych!</p>
                    </div>
                    <form method="POST" action="<?php echo route('postSpamlistCallUrl', array('uid' => $item['uid'])); ?>">
                        <div>
                            <select name="sex" class="form-control" style="width: 100%">
                                <option value="0">zawołaj wszystkich</option>
                                <option value="1">zawołaj tylko niebieskie paski</option>
                                <option value="2">zawołaj tylko różowe paski</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <input type="text" name="entryUrl" class="form-control" placeholder="tu wklej link do wpisu" />
                            <input type="hidden" name="spamlists[]" value="<?php echo $item['uid']; ?>" />
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="fa fa-share"></i></button>
                            </span>
                        </div>

<?php
        $callableSpamlists = $app->make('App\Services\SpamlistService')->getUserCallableSpamlists();

        if (count($callableSpamlists) >= 2) {
?>
                        <div id="callManyContainer" style="display: none">
<?php
            foreach ($callableSpamlists as $loopItem) {
                if ($loopItem['uid'] === $item['uid']) {
                    continue;
                }
?>
                            <div style="width: 50%; float: left">
                                <label>
                                    <input type="checkbox" class="callManyCheckbox" name="spamlists[]" value="<?php echo $loopItem['uid']; ?>" />
                                    <?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem['name']); ?>
                                </label>
                            </div>
<?php
            }
?>
                            <div style="clear: both">&nbsp;</div>
                        </div>
                        <div class="input-group" style="width: 100%; padding-top: 5px">
                            <button type="button" class="btn btn-primary btn-lg btn-block" id="callManyButton">Wołaj kilka list</button>
                        </div>

                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#callManyButton').on('click', function() {
                            jQuery(this.parentNode).fadeOut(function() {
                                jQuery('#callManyContainer').fadeIn();
                            });
                        });

                        jQuery('.callManyCheckbox').on('click', function() {
                            if (jQuery('.callManyCheckbox:checked').length >= 2) {
                                jQuery('.callManyCheckbox:not(:checked)').attr('disabled', 'disabled');
                            } else {
                                jQuery('.callManyCheckbox:not(:checked)').removeAttr('disabled');
                            }
                        });
                    });
                </script>
<?php
        }
?>
                    </form>
                </div>
<?php
    }
?>

<?php
    if ($isOwner && $rightsExtended) {
?>
                <div class="well">
                    <h4>Import listy</h4>
                    <div class="input-group">
                        <p>Poniżej możesz wkleić listę osób do zaimportowania (oddzielone przecinkami, spacjami lub nowymi liniami; z lub bez @)</p>
                    </div>
                    <form method="POST" action="<?php echo route('postSpamlistImportUrl', array('uid' => $item['uid'])); ?>" id="importForm">
                        <div class="input-group">
                            <textarea cols="30" rows="5" name="importUsers" class="form-control"></textarea>
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                            <span class="input-group-addon btn btn-default" id="importButton">
                                <i class="fa fa-share"></i>
                            </span>
                        </div>
                    </form>
                </div>

                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#importButton').on('click', function() {
                            jQuery('#importForm').submit();
                        });
                    });
                </script>
<?php
    }

    if ($isOwner) {
?>
                <div class="well">
                    <h4>Usunięcie listy</h4>
                    <div class="input-group">
                        <p>Jeśli zdecydowałeś/aś o zamknięciu listy możesz zrobić to klikając w poniższy przycisk.</p>
                        <p>Pamiętaj, że ta decyzja jest ostateczna!</p>
                    </div>
                    <form method="POST" action="<?php echo route('deleteSpamlistUrl', array('uid' => $item['uid'])); ?>" id="deleteForm">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                    </form>

                    <div class="input-group">
                        <span class="input-group-addon btn btn-default" id="deleteButton">
                            <i class="fa fa-remove"></i>
                        </span>
                    </div>
                </div>

                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('#deleteButton').on('click', function() {
                            if (confirm('Na pewno chcesz usunąć listę?')) {
                                jQuery('#deleteForm').submit();
                            }
                        });
                    });
                </script>
<?php
    }
?>

<?php
    if (is_object($logs) && $logs->count() > 0) {
?>
                <div class="well">
                    <h4>Ostatnie wydarzenia <small>(<a href="<?php echo route('spamlistLogsUrl', ['uid' => $item['uid']]); ?>">zobacz wszystkie</a>)</small></h4>
<?php
        foreach ($logs as $log) {
?>
                    <hr />

                    <p><i class="fa fa-clock-o"></i> <small><?php echo $log->created_at; ?></small><br /><?php echo $app->make('App\Services\LogService')->getLogText($log); ?></p>

<?php
        }
?>
                </div>
<?php
    }
?>

            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';
