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
        return 'substition';
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
        $this->Lexer->addSpecialPattern('\\{\\{top\\}\\}', $mode, 'plugin_top');
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
        $data = array();

        return $data;
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

        /** @var helper_plugin_top $hlp */
        $hlp  = plugin_load('helper', 'top');
        $list = $hlp->best();

        $renderer->listu_open();
        foreach($list as $item) {
            $renderer->listitem_open(1);
            $renderer->internallink($item['page']);
            $renderer->listitem_close();
        }
        $renderer->listu_close();
        return true;
    }
}

// vim:ts=4:sw=4:et:
