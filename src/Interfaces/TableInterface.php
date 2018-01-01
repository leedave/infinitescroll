<?php

namespace Leedch\Website\InfiniteScroll\Interfaces;

/**
 * Description of TableInterface
 *
 * @author leed
 */
interface TableInterface
{
    public function getTableAttributes();
    /*
     * Example 
     * public function getTableAttributes()
        {
            $this->tableAttributes = [
                "id" => "collectionTable",
                "class" => "infiniteScrollTable",
                "data-loadurl" => "/trakt/ratings/load/",
                "data-loadrowurl" => "/blog/entry/row/",
                "data-updateurl" => "/blog/entry/update/",
                "data-blogid" => $blogId,
            ];
            return $this->tableAttributes;
        }
     */
    
    public function getActionLinks();
    /**
     * Example
     * public function getActionLinks()
        {
            $this->actionLinks = [];
            return $this->actionLinks;
        }
     */
    
    public function getHeaderColumns();
    /*
     * Example
     * public function getHeaderColumns()
        {
            $this->headerColumns = [
                "id" => "id",
                "Title" => "tilte",
                "Rated At" => "ratedAt",
            ];
            return $this->headerColumns;
        }
     */
    
    
    
}
