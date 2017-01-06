<?php
    require dirname(__FILE__) . '/../header.php';
?>

        <div class="row">
            <div class="col-md-12">
                <h3>Nowa spamlista</h3>
                <form method="POST" action="">
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Nazwa:</label>
                            <input type="text" class="form-control" name="name" required data-validation-required-message="Musisz podać nazwę" value="<?php echo Illuminate\Support\Facades\Input::old('name'); ?>" />
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Opis:</label>
                            <textarea rows="10" cols="100" class="form-control" name="description" required data-validation-required-message="Musisz uzupełnić opis" maxlength="999"><?php echo Illuminate\Support\Facades\Input::old('description'); ?></textarea>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Kategoria (opcjonalna)</label>
                            <select name="categoryId" class="form-control">
                                <option value="">-- brak --</option>
<?php
    foreach ($categories as $loopItem) {
?>
                                <option value="<?php echo $loopItem->id; ?>"><?php echo $loopItem->name; ?></option>
<?php
    }
?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Miasto (opcjonalne)</label>
                            <input type="hidden" name="city" id="city" value="<?php echo isset($item) && isset($item->city) && empty(Illuminate\Support\Facades\Input::old('city')) ? $item->city->name : Illuminate\Support\Facades\Input::old('city'); ?>" />
                            <input type="text" id="cityAutocomplete" name="cityAutocomplete" placeholder="Miasto" class="form-control" value="<?php echo isset($item) && isset($item->city) && empty(Illuminate\Support\Facades\Input::old('city')) ? $item->city->name : Illuminate\Support\Facades\Input::old('city'); ?>" />
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

                    <button type="submit" class="btn btn-primary">Dodaj</button>
                </form>
            </div>
        </div>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB479zd63DRkpiyY1Bb7u2o63Lkjb47a9I&libraries=places&language=pl-PL"></script>

        <script type="text/javascript">
            var autocomplete;

            function initialize() {
                var options = {
                    types: ['(cities)'],
                    componentRestrictions: {country: "pl"}
                };

                var input = document.getElementById('cityAutocomplete');
                autocomplete = new google.maps.places.Autocomplete(input, options);

                autocomplete.addListener('place_changed', citySelected);
            }

            function citySelected() {
                var place = autocomplete.getPlace();

                if (typeof place !== 'undefined' && typeof place.name !== 'undefined') {
                    jQuery('#city').val(place.name);
                } else {
                    jQuery('#city').val('');
                }
            }

            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
<?php
    require dirname(__FILE__) . '/../footer.php';