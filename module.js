M.block_accessibility = {
    init: function(Y) {
        // Create Bookmarklet-style link using code from ATbar site
        // http://access.ecs.soton.ac.uk/StudyBar/versions
        var link = Y.Node.create('<a href="javascript:(function(){d=document;lf=d.createElement(\'script\');lf.type=\'text/javascript\';lf.id=\'ToolbarStarter\';lf.text=\'var%20StudyBarNoSandbox=true\';d.getElementsByTagName(\'head\')[0].appendChild(lf);jf=d.createElement(\'script\');jf.src=\'http://access.ecs.soton.ac.uk/ToolBar/channels/toolbar-stable/JTToolbar.user.js\';jf.type=\'text/javascript\';jf.id=\'ToolBar\';d.getElementsByTagName(\'head\')[0].appendChild(jf);})();">'+M.util.get_string('launchtoolbar', 'block_accessibility')+'</a>');
        Y.one('#block_accessibility_placeholder').replace(link);
    }
}
