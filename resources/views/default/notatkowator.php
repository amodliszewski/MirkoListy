<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('set', 'anonymizeIp', true);
    ga('create', 'UA-69280701-2', 'auto');
    ga('send', 'pageview', {
<?php
    if (isset($_GET['nick'])) {
?>
        'dimension1': '<?php echo $_GET['nick']; ?>',
<?php
    }

    if (isset($_GET['notes'])) {
?>
        'dimension2': '<?php echo $_GET['notes']; ?>',
<?php
    }

    if (isset($_GET['blacklist'])) {
?>
        'dimension3': '<?php echo $_GET['blacklist']; ?>',
<?php
    }
?>
    });
</script>