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

    function test_vanilla_syntax_parsing() {
        $parser_response = p_get_instructions('{{top}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'month' => '',
                        'tag' => 'ul',
                        'score' => 'false',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_ol_syntax_parsing() {
        $parser_response = p_get_instructions('{{top|tag=ol}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'month' => '',
                        'tag' => 'ol',
                        'score' => 'false',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top|tag=ol}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_score_syntax_parsing() {
        $parser_response = p_get_instructions('{{top|score=true}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'month' => '',
                        'tag' => 'ul',
                        'score' => 'true',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top|score=true}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_date_syntax_parsing() {
        $parser_response = p_get_instructions('{{top|month=201501}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => '',
                        'month' => '201501',
                        'tag' => 'ul',
                        'score' => 'false',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top|month=201501}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_lang_syntax_parsing() {
        $parser_response = p_get_instructions('{{top|lang=en}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => 'en',
                        'month' => '',
                        'tag' => 'ul',
                        'score' => 'false',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top|lang=en}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

    function test_datelang_syntax_parsing() {
        $parser_response = p_get_instructions('{{top|month=201501,lang=en}}')[2];
        $expected_response = array(
            0 => 'plugin',
            1 => array(
                0 => 'top',
                1 => array(
                    0 => DOKU_LEXER_SPECIAL,
                    1 => array(
                        'lang' => 'en',
                        'month' => '201501',
                        'tag' => 'ul',
                        'score' => 'false',
                    )
                ),
                2 => DOKU_LEXER_SPECIAL,
                3 => '{{top|month=201501,lang=en}}',
            ),
            2 => 1,
        );
        $this->assertEquals($expected_response, $parser_response);
    }

}
