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

    /**
     * Adds a hit for the given page
     *
     * @param string $page the page id
     */
    public function add($page) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return;

        // ignore any bot accesses
        if(!class_exists('Jaybizzle\CrawlerDetect\CrawlerDetect')){
            require (__DIR__ . '/CrawlerDetect.php');
        }
        $CrawlerDetect = new Jaybizzle\CrawlerDetect\CrawlerDetect();
        if($CrawlerDetect->isCrawler()) return;
        
        // insert appropriate ACT flag to ignore admin accesses? e.g. if(AUTH_ADMIN) return;

        $translation = plugin_load('helper', 'translation');
        if (!$translation) {
            $lang = '';
        } else {
            $lang = $translation->getLangPart($page);
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
    public function best($lang, $month, $num = 10) {
        $sqlite = $this->getDBHelper();
        if(!$sqlite) return array();

        $sqlbegin  = "SELECT SUM(value) as value, page FROM toppages ";
        $sqlend = "GROUP BY page ORDER BY value DESC LIMIT ?";
        if ($lang === null && $month === null){
            $sql = $sqlbegin . $sqlend;
            $res  = $sqlite->query($sql, $num);
        } elseif ($lang !== null && $month === null) {
            $sql = $sqlbegin . "WHERE lang = ? " . $sqlend;
            $res  = $sqlite->query($sql, $lang, $num);
        } elseif ($lang === null && $month !== null){
            $sql = $sqlbegin . "WHERE month >= ? " . $sqlend;
            $res  = $sqlite->query($sql, intval($month), $num);
        } else {
            $sql = $sqlbegin . "WHERE lang = ? AND month >= ? " . $sqlend;
            $res  = $sqlite->query($sql, $lang, intval($month), $num);
        }
        $list = $sqlite->res2arr($res);
        $sqlite->res_close($res);

        if ($this->getConf('hide_start_pages')) {
            $list = $this->removeStartPages($list);
        }
        return $list;
    }

    public function removeStartPages($list) {
        global $conf;
        $start = $conf['start'];
        $startpages = array();
        $startpages[] = $start;

        if ($conf['plugin']['translation']['translations'] !== '') {
            $translations = explode(' ', $conf['plugin']['translation']['translations']);
            foreach($translations as $translation) {
                $startpages[] = $translation . ':' . $start;
            }
        }

        foreach ($list as $index => $listitem) {
            if (in_array($listitem['page'],$startpages, true) === true ) {
                unset($list[$index]);
            }
        }
        $list = array_values($list);
        return $list;
    }

}

// vim:ts=4:sw=4:et:
