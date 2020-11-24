<?php
/**
 * DokuWiki Plugin top (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

class action_plugin_top extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        global $ACT;
        global $JSINFO;
        $JSINFO['act'] = $ACT;

        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handleAjax');
        $controller->register_hook('FEED_MODE_UNKNOWN', 'BEFORE', $this, 'handleFeed');
    }

    /**
     * Handle Ajax calls intended for this plugin
     *
     * @param Doku_Event $event  event object by reference
     * @return void
     */

    public function handleAjax(Doku_Event $event)
    {
        if ($event->data != 'plugin_top') return;
        $event->preventDefault();
        $event->stopPropagation();

        global $INPUT;
        $page = cleanID($INPUT->str('page'));
        if (!$page) return;

        /** @var helper_plugin_top $hlp */
        $hlp = plugin_load('helper', 'top');
        $hlp->add($page);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'counted';
    }

    /**
     * Fetch and add top pages to FeedCreator.
     *
     * Supported feed parameters:
     * - mode: plugin-top (required)
     * - lang: optional
     * - month: optional (YYYYMM)
     * - num: optional number of results (default = 10)
     *
     * @param Doku_Event $event
     */
    public function handleFeed(Doku_Event $event)
    {
        $opt = $event->data['opt'];
        if ($opt['feed_mode'] !== 'plugin-top') return;

        $event->preventDefault();

        // set defaults as expected by the helper's best() method
        $lang = isset($opt['lang']) ? $opt['lang'] : null;
        $month = isset($opt['month']) ? $opt['month'] : null;
        $num = isset($opt['num']) ? $opt['num'] : 10;

        /** @var helper_plugin_top $hlp */
        $hlp = plugin_load('helper', 'top');
        $pages = $hlp->best($lang, $month, $num);

        if (empty($pages)) return;

        foreach ($pages as $page) {
            $event->data['data'][] = [
                'id' => $page['page'],
                'score' => $page['value'],
            ];
        }
    }
}
// vim:ts=4:sw=4:et:
