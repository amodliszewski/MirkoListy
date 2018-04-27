<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Profil użytkownika <?php echo $app->make('App\Services\TemplateService')->clearValue($item->nick); ?></h3>
                <br /><br />
                <div class="panel panel-default">
                    <div class="panel-body">
                        <a href="<?php echo $_ENV['WYKOP_BASE_URL']; ?>ludzie/<?php echo $item->nick; ?>" rel="nofollow" target="_blank" style="color: <?php echo $app->make('WykoCommon\Services\TemplateService')->getGroupColor($item->color); ?>">
                            <img src="<?php echo $item->avatar_url; ?>" class="img-polaroid <?php echo $app->make('App\Services\TemplateService')->getSexClass($item->sex); ?>" />
                            <span style="margin-left: 10px;"><?php echo $item->nick; ?></span>
                        </a>

                        <br /><br />

                        Data dołączenia: <strong><?php echo $item->created_at; ?></strong><br />
                        Utworzone listy: <strong><?php echo $item->created_count; ?></strong><br />
                        Członek list: <strong><?php echo $item->joined_count; ?></strong><br />
                        Ilość wołań: <strong><?php echo $item->called_count; ?></strong><br />
                        Opt-out z wołań: <strong><?php echo $item->call_optout == 1 ? '<span style="color: red">wypisany</span>' : '<span style="color: green">zapisany</span>'; ?></strong><br />
                    </div>
                </div>

                <br /><br />

                <div class="panel panel-default">
                    <div class="panel-heading">Utworzył listy</div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data utworzenia</th>
                                    <th>Lista</th>
                                    <th>Ilość osób</th>
                                    <th>Ilość wołań</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
        $profileCreatedSpamlists = $app->make('App\Services\SpamlistService')->getUserCreatedSpamlists($item->id);
        if (is_array($profileCreatedSpamlists) && !empty($profileCreatedSpamlists)) {
            foreach ($profileCreatedSpamlists as $foreachItem) {
?>
                                <tr>
                                    <td>
                                        <?php echo $foreachItem['created_at']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo route('getSpamlistUrl', array('uid' => $foreachItem['uid'])); ?>">
                                            <?php echo $app->make('App\Services\TemplateService')->clearValue($foreachItem['name']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $foreachItem['joined_count']; ?>
                                    </td>
                                    <td>
                                        <?php echo $foreachItem['called_count']; ?>
                                    </td>
                                </tr>
<?php
            }
        }
?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br /><br />

                <div class="panel panel-default">
                    <div class="panel-heading">Członek list</div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data dołączenia</th>
                                    <th>Lista</th>
                                    <th>Prawa</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
        $profileSpamlists = $app->make('App\Services\SpamlistService')->getUserJoinedSpamlists($item->id);
        if (is_array($profileSpamlists) && !empty($profileSpamlists)) {
            foreach ($profileSpamlists as $foreachItem) {
?>
                                <tr>
                                    <td>
                                        <?php echo $foreachItem['joined_at']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo route('getSpamlistUrl', array('uid' => $foreachItem['uid'])); ?>">
                                            <?php echo $app->make('App\Services\TemplateService')->clearValue($foreachItem['name']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="<?php echo route('postSpamlistPeopleUrl', array('uid' => $foreachItem['uid'])); ?>">
                                            <div class="input-group" style="width: 300px">
                                                <select class="form-control" name="rights">
                                                    <option value="2">zbanowany</option>
                                                    <option value="10"<?php echo $foreachItem['rights'] == 10 ? ' selected="selected"' : ''; ?>>użytkownik</option>
                                                    <option value="20"<?php echo $foreachItem['rights'] == 20 ? ' selected="selected"' : ''; ?>>wołający</option>
                                                    <option value="99"<?php echo $foreachItem['rights'] == 99 ? ' selected="selected"' : ''; ?>>administrator</option>
                                                    <option value="-1">-- USUŃ Z LISTY --</option>
                                                </select>
                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
                                                <input type="hidden" name="userId" value="<?php echo $item->id; ?>" />
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="submit"><i class="fa fa-share"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
<?php
            }
        }
?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br /><br />

                <form method="POST" action="<?php echo route('changeProfileRightsUrl'); ?>">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Uprawnienia:</label>
                            <select class="form-control" name="rights">
                                <option value="2">zbanowany</option>
                                <option value="10"<?php echo $item->rights === 10 ? ' selected="selected"' : ''; ?>>użytkownik</option>
                                <option value="20"<?php echo $item->rights === 20 ? ' selected="selected"' : ''; ?>>użytkownik+</option>
                                <option value="20"<?php echo $item->rights === 50 ? ' selected="selected"' : ''; ?>>moderator</option>
                                <option value="99"<?php echo $item->rights === 99 ? ' selected="selected"' : ''; ?>>administrator</option>
                            </select>
                            <p class="help-block"></p>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Opt-out z wołań:</label>
                            <select class="form-control" name="callOptOut">
                                <option value="0">zapisany</option>
                                <option value="1"<?php echo $item->call_optout == 1 ? 'selected="selected"' : '' ?>>WYPISANY</option>
                            </select>
                            <p class="help-block"></p>
                        </div>
                    </div>

                    <input type="hidden" name="userId" value="<?php echo $item->id; ?>" />
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Zmień</button>
                </form>
            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';