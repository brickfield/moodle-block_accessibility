M.block_accessibility = {
    init: function(Y) {
        // Create Bookmarklet-style link using code from ATbar site
        // http://access.ecs.soton.ac.uk/StudyBar/versions
        var launchbutton = Y.one('#block_accessibility_launchtoolbar');
        launchbutton.on('click', function() {
            d = document;
            lf = d.createElement('script');
            lf.type = 'text/javascript';
            lf.id = 'ToolbarStarter';
            lf.text = 'var StudyBarNoSandbox = true';
            d.getElementsByTagName('head')[0].appendChild(lf);
            jf = d.createElement('script');
            jf.src = M.cfg.wwwroot+'/blocks/accessibility/toolbar/client/JTToolbar.user.js';
            jf.type = 'text/javascript';
            jf.id = 'ToolBar';
            d.getElementsByTagName('head')[0].appendChild(jf);
        });
    }
}
