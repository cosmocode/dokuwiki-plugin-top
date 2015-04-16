<?php

/**
 *
 *
 * @author Michael Große <grosse@cosmocode.de>
 *
 * @group plugin_top
 * @group plugins
 */

class test_plugin_top_removeStartpages extends DokuWikiTest {
    protected $pluginsEnabled = array('top', 'sqlite','translation');

    function test_removeStartpages_noTranslation() {
        $helper = plugin_load('helper', 'top');
        $list = array(
            array(
                'value' => 252,
                'page'  => 'start',
            ),

            array(
                'value' => 106,
                'page'  => 'namespace12:start',
            ),

            array(
                'value' => 95,
                'page'  => 'snippets:test1',
            ),

            array(
                'value' => 74,
                'page'  => 'statistiken:start',
            ),

            array(
                'value' => 72,
                'page'  => 'statistiken:top',
            ),

            array(
                'value' => 62,
                'page'  => 'sidebar',
            ),

            array(
                'value' => 49,
                'page'  => 'snippets:start',
            ),

            array(
                'value' => 40,
                'page'  => 'unittests',
            ),

            array(
                'value' => 35,
                'page'  => 'pluginsurvey2014:start',
            ),

            array(
                'value' => 31,
                'page'  => 'snippets:foo:start',
            ),

            array(
                'value' => 27,
                'page'  => 'plugins:navi',
            ),

            array(
                'value' => 27,
                'page'  => 'snippets:foo:test1',
            ),

            array(
                'value' => 26,
                'page'  => 'snippets',
            ),

            array(
                'value' => 23,
                'page'  => 'imagemap',
            ),

            array(
                'value' => 23,
                'page'  => 'pluginsurvey2014:codestyle',
            ),

            array(
                'value' => 23,
                'page'  => 'snippets:test3',
            ),

            array(
                'value' => 22,
                'page'  => 'tabbox',
            ),

            array(
                'value' => 18,
                'page'  => 'snippets:test2',
            ),

            array(
                'value' => 17,
                'page'  => 'user:michaelsuper',
            ),

            array(
                'value' => 16,
                'page'  => 'calenders',
            ),

        );
        $actual_list = $helper->removeStartPages($list);
        array_shift($list);
        $expected_list = $list;
        $this->assertSame($expected_list,$actual_list);
    }

    function test_removeStartpages_Translation() {
        global $conf;
        $conf['plugin']['translation']['translations'] = 'de en';
        $helper = plugin_load('helper', 'top');
        $list = array(
            array(
                'value' => 252,
                'page'  => 'start',
            ),

            array(
                'value' => 106,
                'page'  => 'namespace12:start',
            ),

            array(
                'value' => 95,
                'page'  => 'snippets:test1',
            ),

            array(
                'value' => 74,
                'page'  => 'statistiken:start',
            ),

            array(
                'value' => 72,
                'page'  => 'statistiken:top',
            ),

            array(
                'value' => 62,
                'page'  => 'sidebar',
            ),

            array(
                'value' => 49,
                'page'  => 'snippets:start',
            ),

            array(
                'value' => 40,
                'page'  => 'de:start',
            ),

            array(
                'value' => 35,
                'page'  => 'pluginsurvey2014:start',
            ),

            array(
                'value' => 31,
                'page'  => 'snippets:foo:start',
            ),

            array(
                'value' => 27,
                'page'  => 'plugins:navi',
            ),

            array(
                'value' => 27,
                'page'  => 'snippets:foo:test1',
            ),

            array(
                'value' => 26,
                'page'  => 'en:start',
            ),

            array(
                'value' => 23,
                'page'  => 'imagemap',
            ),

            array(
                'value' => 23,
                'page'  => 'pluginsurvey2014:codestyle',
            ),

            array(
                'value' => 23,
                'page'  => 'snippets:test3',
            ),

            array(
                'value' => 22,
                'page'  => 'tabbox',
            ),

            array(
                'value' => 18,
                'page'  => 'snippets:test2',
            ),

            array(
                'value' => 17,
                'page'  => 'user:michaelsuper',
            ),

            array(
                'value' => 16,
                'page'  => 'calenders',
            ),

        );
        $actual_list = $helper->removeStartPages($list);
        $expected_list = array(
            array(
                'value' => 106,
                'page'  => 'namespace12:start',
            ),

            array(
                'value' => 95,
                'page'  => 'snippets:test1',
            ),

            array(
                'value' => 74,
                'page'  => 'statistiken:start',
            ),

            array(
                'value' => 72,
                'page'  => 'statistiken:top',
            ),

            array(
                'value' => 62,
                'page'  => 'sidebar',
            ),

            array(
                'value' => 49,
                'page'  => 'snippets:start',
            ),

            array(
                'value' => 35,
                'page'  => 'pluginsurvey2014:start',
            ),

            array(
                'value' => 31,
                'page'  => 'snippets:foo:start',
            ),

            array(
                'value' => 27,
                'page'  => 'plugins:navi',
            ),

            array(
                'value' => 27,
                'page'  => 'snippets:foo:test1',
            ),

            array(
                'value' => 23,
                'page'  => 'imagemap',
            ),

            array(
                'value' => 23,
                'page'  => 'pluginsurvey2014:codestyle',
            ),

            array(
                'value' => 23,
                'page'  => 'snippets:test3',
            ),

            array(
                'value' => 22,
                'page'  => 'tabbox',
            ),

            array(
                'value' => 18,
                'page'  => 'snippets:test2',
            ),

            array(
                'value' => 17,
                'page'  => 'user:michaelsuper',
            ),

            array(
                'value' => 16,
                'page'  => 'calenders',
            ),

        );
        $this->assertSame($expected_list,$actual_list);
    }


}
