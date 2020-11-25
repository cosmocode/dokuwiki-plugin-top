jQuery(function () {
    if (JSINFO['act'] && JSINFO.act === 'show') {
        jQuery.post(
            DOKU_BASE + 'lib/exe/ajax.php',
            {
                call: 'plugin_top',
                page: JSINFO.id
            }
        );
    }
});
