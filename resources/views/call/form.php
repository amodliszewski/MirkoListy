<?php
    require dirname(__FILE__) . '/../header.php';
?>
        <div class="row">
            <div class="col-md-12">
                <h3>Wykluczenie z wołania</h3>
                <p>Jeśli nie chcesz być wołany do pojedynczych wpisów (np. jako komentujący czy plusujący) użyj poniższego przycisku.</p>
                <p>Opcja ta nie dotyczy list, na które się zapisujesz (z nich nadal będziesz otrzymywał powiadomienia).</p>

                <form method="POST" action="/call/optout">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
<?php
    $currentUser = $app->make('WykoCommon\Services\UserService')->getCurrentUser();

    if ($currentUser->call_optout == 1) {
?>
                    <span style="color: red">Obecnie NIE otrzymujesz powiadomień z pojedynczego wołania</span><br /><br />

                    <input type="hidden" name="type" value="0" />
                    <button type="submit" class="btn btn-primary">Wyłącz blokadę</button>
<?php
    } else {
?>
                    <span style="color: green">Obecnie otrzymujesz powiadomienia z pojedynczego wołania</span><br /><br />

                    <input type="hidden" name="type" value="1" />
                    <button type="submit" class="btn btn-primary">Włącz blokadę</button>
<?php
    }
?>
                </form>

                <br /><br />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Pojedyncze wołanie</h3>
                <p>Ta opcja pozwala Ci na zwołanie osób komentujących lub plusujących Twój wpis lub komentarz.</p>
                <p>Jeśli wybierzesz typ "plusujący" będziesz mógł podać link do konkretnego komentarza.</p>
<?php
    if (!$app->make('WykoCommon\Services\UserService')->isTrusted()) {
?>
                <p style="padding-top: 10px; padding-bottom: 25px"><strong>W celu uniknięcia spamu musisz być autorem wpisu/komentarza źrodłowego!</strong></p>

                <p style="padding-top: 10px; padding-bottom: 25px; color: red"><strong>Zakazane jest wołanie do wpisów z tagu #rozdajo!<br />Wszelkie próby obejścia tego zakazu (np. dodanie tagu po wołaniu) będą karane banem.</strong></p>
<?php
    }
?>

                <form method="POST" action="">
                    <div class="control-group form-group" id="sourceEntryUrlContainer">
                        <div class="controls">
                            <label>Wpis źrodłowy (z niego będzie pobrana lista osób do zawołania):</label>
                            <input type="text" class="form-control" name="sourceEntryUrl" required data-validation-required-message="Musisz podać adres" value="<?php echo Illuminate\Support\Facades\Input::old('sourceEntryUrl'); ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Nowy wpis (do niego zostaną zawołani użytkownicy):</label>
                            <input type="text" class="form-control" name="entryUrl" required data-validation-required-message="Musisz podać adres" value="<?php echo Illuminate\Support\Facades\Input::old('entryUrl'); ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Typ:</label>
                            <select class="form-control" name="type" required="required" id="callType">
                                <option disabled="disabled">-- mirko --</option>
                                <option value="1">plusujący</option>
                                <option value="2">komentujący</option>
                                <option value="3">plusujących i komentujący</option>
                                <option disabled="disabled">-- główna --</option>
                                <option value="10">wykopujący</option>
                                <option value="11">zakopujący</option>
                                <option value="12">komentujący</option>
                                <option value="13">wykopujących, zakopujący i komentujący</option>
<?php
    if ($app->make('WykoCommon\Services\UserService')->isAdmin()) {
?>
                                <option disabled="disabled">-- dodatkowe --</option>
                                <option value="20">właściciele list i wołający</option>
<?php
    }
?>
                            </select>
                            <p class="help-block"></p>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Wołaj</button>
                </form>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('#callType').on('change', function() {
                    if (this.value <= 15) {
                        jQuery('#sourceEntryUrlContainer').fadeIn();
                        jQuery('#sourceEntryUrlContainer input').removeAttr('disabled');
                    } else {
                        jQuery('#sourceEntryUrlContainer').fadeOut();
                        jQuery('#sourceEntryUrlContainer input').attr('disabled', 'disabled');
                    }
                });
            });
        </script>
<?php
    require dirname(__FILE__) . '/../footer.php';
