<?php

/**
 *
 *
 * @author Michael Große <grosse@cosmocode.de>
 *
 * @group Michael Große <grosse@cosmocode.de>
 * @group plugin_top
 * @group plugins
 */

class best_top_test extends DokuWikiTest {
    protected $pluginsEnabled = array('top','sqlite');

    function test_best() {
        global $ID;

        $top_helper = new helper_plugin_top();
        $sqlite = $top_helper->getDBHelper();
        $sql = "INSERT INTO toppages (page, value, lang, month) VALUES
            ( ?, ?, ?, ?);";
        $sqlite->query($sql,'wiki:start',3,'','201407');
        $sqlite->query($sql,'wiki:start',2,'','201401');
        $sqlite->query($sql,'wiki:start',6,'','201201');
        $sqlite->query($sql,'wiki:start',1,'',null);
        $sqlite->query($sql,'en:wiki:start',8,'en','201201');
        $sqlite->query($sql,'en:wiki:start',1,'en','201303');
        $sqlite->query($sql,'de:wiki:start',6,'de','201201');

        $result = $top_helper->best(null,201312);
        $this->assertEquals(
            5,$result[0]['value'],
            'We should see the sum of all visitis since the given month, not month by month.'
        );

        $result = $top_helper->best(null,null);
        $this->assertEquals(
            12,$result[0]['value'],
            'We should see the total number of visitis. Including visits without date'
        );

        $result = $top_helper->best('en',null);
        $this->assertEquals(
            9,$result[0]['value'],
            'We should see only pages with from given language.'
        );

        $result = $top_helper->best('en',201302);
        $this->assertEquals(
            1,$result[0]['value'],
            'Time and language restrictions should work together.'
        );
    }
}
