
            <hr>

            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <p>
                            Copyright &copy; mirkolisty.pvu.pl 2015-<?php echo date('Y'); ?> | Administrator: <a href="http://wykop.pl/ludzie/IrvinTalvanen" rel="nofollow">IrvinTalvanen</a>
<?php
    if (@$_ENV['APP_ENV'] !== 'production') {
        echo ' | devel';
    }
?>
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <script type="text/javascript">
            var style = <?php echo $app->make('App\Services\TemplateService')->getStyleId(); ?>;
            function changeStyle() {
                if (style === 0) {
                    if (jQuery('#blackCSS').length === 0) {
                        jQuery('head').append('<link rel="stylesheet" href="/css/black.css" type="text/css" id="blackCSS" />');
                    } else {
                        jQuery('#blackCSS').removeAttr('disabled');
                    }

                    style = 1;
                } else {
                    jQuery('#blackCSS').attr('disabled', 'disabled');

                    style = 0;
                }

                Cookies('style', style, {
                    expires: 30
                });
            }

            jQuery(document).ready(function() {
                jQuery('.change-style').on('click', function() {
                    changeStyle();

                    return false;
                });

                if (jQuery('.countdownTimer').length > 0) {
                    jQuery('.countdownTimer').countdown(jQuery('.countdownTimer').text(), function(event) {
                        jQuery(this).text(
                            event.strftime('%M:%S')
                        );
                    });
                }

                jQuery.notify.addStyle('donation', {
                    html: '<div><div class="notifyjs-wrapper notifyjs-hidable">\
	<div class="notifyjs-arrow" style=""></div>\
	<div class="notifyjs-container"><div class="notifyjs-bootstrap-base notifyjs-bootstrap-error">\
<span data-notify-html></span>\
</div></div>\
</div></div>'
                });

                /*jQuery.notify('Uważasz MirkoListy za przydatne narzędzie, bez którego życie byłoby ciężkie?<br /><br />Pomóż w jego utrzymaniu!<br /><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G9RBBPAMKD738" style="color: green">Przeznacz kilka złotych na opłacenie serwera i domeny klikając tutaj!<a/><br /><br />Każda złotówka się liczy.', {
                    position: 'bottom left',
                    autoHide: false,
                    style: 'donation'
                });*/

                jQuery.notify.addStyle('info', {
                    html: '<div><div class="notifyjs-wrapper notifyjs-hidable">\
	<div class="notifyjs-arrow" style=""></div>\
	<div class="notifyjs-container"><div class="notifyjs-bootstrap-base notifyjs-bootstrap-info">\
<span data-notify-html></span>\
</div></div>\
</div></div>'
                });

                jQuery.notify('Chcesz być na bieżąco ze zmianami na stronie<br />i dodatkowo zmotywować autora do dalszego jej rozwoju?<br /><br />Obserwuj <a href="http://www.wykop.pl/tag/mirkolisty/" style="color: green">#mirkolisty</a> i/lub <a href="http://www.wykop.pl/ludzie/IrvinTalvanen/" style="color: green">@IrvinTalvanen</a>', {
                    position: 'bottom left',
                    autoHide: false,
                    style: 'info'
                });
            })
        </script>

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-69280701-1', 'auto');
            ga('send', 'pageview');
        </script>
    </body>
</html>
