<?php
/**
 * DokuWiki Plugin top (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_top extends DokuWiki_Plugin {

    /** @var helper_plugin_sqlite */
    protected $sqlite = null;

    /**
     * initializes the DB connection
     *
     * @return helper_plugin_sqlite|null
     */
    public function getDBHelper() {
        if(!is_null($this->sqlite)) return $this->sqlite;

        $this->sqlite = plugin_load('helper', 'sqlite');
        if(!$this->sqlite) {
            msg('The top plugin requires the sqlite plugin', -1);
            $this->sqlite = null;
            return null;
        }

        $ok = $this->sqlite->init('top', __DIR__ . '/db');
        if(!$ok) {
            msg('rating plugin sqlite initialization failed', -1);
            $this->sqlite = null;
            return null;
        }

        return $this->sqlite;
    }


    public function add($page) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return;

        try {
            $translation = new helper_plugin_translation();
            $lang = $translation->getLangPart($page);
        } catch (Exception $e){
            $lang = '';
        }

        $month = date('Ym');

        $sql = "INSERT OR REPLACE INTO toppages (page, value, lang, month)
                  VALUES ( ?, COALESCE( (SELECT value FROM toppages WHERE page = ? and month = ? ) + 1, 1), ?, ?)";
        $res = $sqlite->query($sql, $page, $page, $month, $lang, $month);
        $sqlite->res_close($res);
    }

    /**
     * Get the most visited pages
     *
     * @param int $num
     * @return array
     */
    public function best($num = 10) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return array();

        $sql  = "SELECT value, page FROM toppages ORDER BY value DESC LIMIT ?";
        $res  = $sqlite->query($sql, $num);
        $list = $sqlite->res2arr($res);
        $sqlite->res_close($res);
        return $list;
    }

}

// vim:ts=4:sw=4:et:
