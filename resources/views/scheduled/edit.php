<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Edytuj wpis</h3>
                <form method="POST" action="">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Data publikacji:</label>
                            <input type="text" class="form-control" name="post_at" value="<?php echo !empty(Illuminate\Support\Facades\Input::old('post_at')) ? Illuminate\Support\Facades\Input::old('post_at') : $item->post_at; ?>" placeholder="RRRR-MM-DD HH:ii:ss" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Obrazek/video:</label>
                            <input type="text" class="form-control" name="embed" value="<?php echo !empty(Illuminate\Support\Facades\Input::old('embed')) ? Illuminate\Support\Facades\Input::old('embed') : $item->embed; ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Treść:</label>
                            <textarea rows="10" cols="100" class="form-control" name="content" required data-validation-required-message="Musisz uzupełnić treść" maxlength="9999"><?php echo !empty(Illuminate\Support\Facades\Input::old('content')) ? Illuminate\Support\Facades\Input::old('content') : $item->content; ?></textarea>
                        </div>
                    </div>
<?php
        $callableSpamlists = $app->make('App\Services\SpamlistService')->getUserCallableSpamlists();
        $selectedSpamlists = explode(',', $item->spamlists);
?>
                    <div class="control-group form-group">
                        <div class="controls">
                            <div>
                                <label>Zawołaj listy:</label>
                            </div>
<?php
        if (count($callableSpamlists) > 0) {
            foreach ($callableSpamlists as $loopItem) {
                $selected = in_array($loopItem['uid'], $selectedSpamlists);
?>
                            <div style="width: 50%; float: left">
                                <label>
                                    <input type="checkbox" class="callManyCheckbox"<?php echo $selected ? ' checked="checked"' : ''; ?> name="spamlists[]" value="<?php echo $loopItem['uid']; ?>" />
                                    <?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem['name']); ?>
                                </label>
                            </div>
<?php
            }
        }
?>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <select name="spamlistSex" class="form-control" style="width: 100%">
                            <option value="0">zawołaj wszystkich</option>
                            <option value="1"<?php echo $item->spamlist_sex == 1 ? ' selected="selected"' : ''; ?>>zawołaj tylko niebieskie paski</option>
                            <option value="2"<?php echo $item->spamlist_sex == 2 ? ' selected="selected"' : ''; ?>>zawołaj tylko różowe paski</option>
                        </select>
                    </div>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </form>
            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';