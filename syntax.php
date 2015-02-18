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
        $this->Lexer->addEntryPattern('\\{\\{top\\|?(?=.*?\\}\\})', $mode, 'plugin_top');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('.*?\\}\\}', 'plugin_top');
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
    public function handle($match, $state, $pos, Doku_Handler &$handler) {
        if ($state==DOKU_LEXER_EXIT) {
            $options = array('lang' => null, 'month' => null );
            $match = rtrim($match,'\}');
            if ($match != '') {
                $match = explode(",", $match);
                foreach($match as $option) {
                    $options[explode('=', $option)[0]] = explode('=', $option)[1];
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
    public function render($mode, Doku_Renderer &$renderer, $data) {
        if($mode == 'metadata') return false;
        if($data[0] != DOKU_LEXER_EXIT) return false;

        /** @var helper_plugin_top $hlp */
        $hlp  = plugin_load('helper', 'top');
        $list = $hlp->best($data[1]['lang'],$data[1]['month'], 20);

        $renderer->listo_open();
        $num_items=0;
        foreach($list as $item) {
            if (auth_aclcheck($item['page'],'',null) < AUTH_READ) continue;
            $num_items = $num_items +1;
            $renderer->listitem_open(1);
            if (strpos($item['page'],':') === false) {
                $item['page'] = ':' . $item['page'];
            }
            $renderer->internallink($item['page']);
            $renderer->cdata(' (' . $item['value'] . ')');
            $renderer->listitem_close();
            if ($num_items >= 10) break;
        }
        $renderer->listo_close();
        return true;
    }
}

// vim:ts=4:sw=4:et:
