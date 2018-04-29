<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Zaplanuj wpis</h3>
                <form method="POST" action="">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Data publikacji:</label>
                            <input type="text" class="form-control" name="post_at" value="<?php echo Illuminate\Support\Facades\Input::old('post_at'); ?>" placeholder="RRRR-MM-DD HH:ii:ss" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Obrazek/video:</label>
                            <input type="text" class="form-control" name="embed" value="<?php echo Illuminate\Support\Facades\Input::old('embed'); ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Treść:</label>
                            <textarea rows="10" cols="100" class="form-control" name="content" required data-validation-required-message="Musisz uzupełnić treść" maxlength="9999"><?php echo Illuminate\Support\Facades\Input::old('content'); ?></textarea>
                        </div>
                    </div>
<?php
        $callableSpamlists = $app->make('App\Services\SpamlistService')->getUserCallableSpamlists();
?>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Zawołaj listy:</label>
<?php
        if (count($callableSpamlists) > 0) {
            foreach ($callableSpamlists as $loopItem) {
?>
                            <div style="width: 50%; float: left">
                                <label>
                                    <input type="checkbox" class="callManyCheckbox" name="spamlists[]" value="<?php echo $loopItem['uid']; ?>" />
                                    <?php echo $app->make('App\Services\TemplateService')->clearValue($loopItem['name']); ?>
                                </label>
                            </div>
<?php
            }
        }
?>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Dodaj</button>
                </form>
            </div>
        </div>
<?php
    require dirname(__FILE__) . '/../footer.php';