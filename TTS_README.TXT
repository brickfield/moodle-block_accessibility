_Instructions for setting up Text-To-Speech_

TTS is currently only possible on Linux servers.
Some of these commands may require root permissions (sudo)

1.  Install the Accessibility block as normal
2.  Install Festival, the Text-To-Speech engine, on your server. This should
    install text2wave in /usr/bin. If not, you'll need to symlink it there.
3.  Install LAME, the MP3 utilities, on your server.
4.  Go to the Moodle root directory (at command line or in a file manager)
5.  From here, find blocks/accessibility/toolbar/server/TTS/SB_generateAudio.sh
    and make it executable. To do this from the terminal, run:
    chmod +x blocks/accessibility/toolbar/server/TTS/SB_generateAudio.sh
6.  Edit SB_generateAudio.sh to set the tmpdir variable at the top to a suitable
    temporary directory, and dirroot to the value of $CFG->dirroot in your
    Moodle config.php
7.  Change the ownership of blocks/accessibility/toolbar/server/TTS/cache to
    make it writable by the user the web server runs as. On Ubuntu, run these
    commands:
    chown -R user:www-data blocks/accessibility/toolbar/server/TTS/cache
    chmod -R 775 blocks/accessibility/toolbar/server/TTS/cache
8.  Load a Moodle page containing the block, click "Launch ATbar", select some
    text, and click the Text-To-Speech button (speaker icon).



