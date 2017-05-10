<?php
/**
 * DokuWiki Plugin top (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_top extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'protected';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 200;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\\{\\{top(?:\|.+?)?\\}\\}', $mode, 'plugin_top');
    }

    /**
     * Handle matches of the top syntax
     *
     * @param string $match The match of the syntax
     * @param int $state The state of the handler
     * @param int $pos The position in the document
     * @param Doku_Handler $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        if ($state==DOKU_LEXER_SPECIAL) {
            $options = array('lang'         => null,
                             'month'        => null,
                             'tag'          => 'ul',
                             'score'        => 'false',
                             'blacklist'    => null,
                             'whitelist'    => null);
            $match = rtrim($match,'\}');
            $match = substr($match,5);
            if ($match != '') {
                $match = ltrim($match,'\|');
                $match = explode(",", $match);
                foreach($match as $option) {
                    list($key, $val) = explode('=', $option);
                    $options[$key] = $val;
                }
            }
            return array($state, $options);
        } else {
            return array($state, '');
        }
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string $mode Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array $data The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'metadata') return false;
        if($data[0] != DOKU_LEXER_SPECIAL) return false;

        /** @var helper_plugin_top $hlp */
        $hlp  = plugin_load('helper', 'top');
        $list = $hlp->best($data[1]['lang'],$data[1]['month'], 20);

        if($data[1]['tag'] == 'ol') {
            $renderer->listo_open();
        } else {
            $renderer->listu_open();
        }

        $num_items=0;
        foreach($list as $item) {

            // Filter by ACL
            if ($this->getConf('show_only_public')) {
                if (auth_aclcheck($item['page'],'',null) < AUTH_READ) continue;
            } else {
                if (auth_quickaclcheck($item['page']) < AUTH_READ) continue;
            }

            // Filter by page display availability
            if (!page_exists($item['page'])) continue;
            if (isHiddenPage($item['page'])) continue;

            // Filter by page blacklist
            $arg_blacklist = $data[1]['blacklist'];
            $blacklist_ok = true;
            if ($arg_blacklist !== null) {
                foreach (explode(" ",$arg_blacklist) as $blacklist_term) {
                    if (strpos($item['page'], $blacklist_term) !== false) {
                        dbglog("plugin:top:blacklist: excluded '".$item['page'].
                               "' because '".$blacklist_term."'");
                        $blacklist_ok = false;
                    }
                }
                if ($blacklist_ok !== true) {
                    continue;
                }
            }

            // Filter by page whitelist
            $arg_whitelist = $data[1]['whitelist'];
            $whitelist_ok = false;
            if ($arg_whitelist !== null) {
                foreach (explode(" ",$arg_whitelist) as $whitelist_term) {
                    if (strpos($item['page'], $whitelist_term) !== false) {
                        dbglog("plugin:top:whitelist: included '".$item['page'].
                               "' because '".$whitelist_term."'");
                        $whitelist_ok = true;
                    }
                }
                if ( $whitelist_ok !== true ) { 
                    continue;
                }
            }

            // Display this page
            $num_items = $num_items +1;
            $renderer->listitem_open(1);
            if (strpos($item['page'],':') === false) {
                $item['page'] = ':' . $item['page'];
            }
            $renderer->internallink($item['page']);
            if ($data[1]['score'] === 'true') $renderer->cdata(' (' . $item['value'] . ')');
            $renderer->listitem_close();
            if ($num_items >= 10) break;
        }

        if($data[1]['tag'] == 'ol') {
            $renderer->listo_close();
        } else {
            $renderer->listu_close();
        }
        return true;
    }
}

// vim:ts=4:sw=4:et:
