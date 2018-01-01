<?php

namespace Leedch\Website\InfiniteScroll\Traits;

use Leedch\Html\Html5 as H;

/**
 * Trait Class for InfiniteScroll Tables
 *
 * @author leed
 */
trait TableTrait 
{
    protected $tableAttributes = [];
    protected $actionLinks = [];
    protected $headerColumns = [];
    
    /**
     * Draws the entire Table
     * @return string
     */
    public function renderTable()
    {
        $header = $this->tableHeaders();
        
        $content = H::table($header, $this->getTableAttributes());
        return $content;
    }
    
    /**
     * Render the Header of the overview Table
     * @return string
     */
    protected function tableHeaders() : string
    {
        $arrCells = [];
        foreach ($this->getHeaderColumns() as $key => $val) {
            $arrCells[] = $this->renderHeaderColumn($key, $val);
        }        
        $arrCells[] = "";
        
        $content = H::renderTableHeaderRow($arrCells);
        $full = H::thead($content)
                . H::tbody('');
        return $full;
    }
    
    /**
     * Render a single Header Column
     * @param string $label
     * @param string $attributeName
     * @param string $dataType   set to null for no input, text for input
     * @return string
     */
    protected function renderHeaderColumn(string $label, string $attributeName, string $dataType = "text") : string
    {
        $this->getTableAttributes();
        $inputAttr = [
            "data-parent" => $this->tableAttributes['id'], //"traktCollectionTable",
            "data-attribute" => $attributeName,
            "data-sort" => "ASC",
            "data-type" => $dataType,
            "class" => "infiniteScrollTableHeaderLink",
        ];
        
        $inputField = "";
        if ($dataType !== null) {
            $fieldAttr = [
                "size" => strlen($label),
            ];
            $inputField = H::input($attributeName, $dataType, null, $fieldAttr);
        }
        
        return H::span($label, $inputAttr).$inputField;
    }
    
    /**
     * Fetches Data for Table Rows and returns as json
     * uses POST vars page, pageSize, sort, sortDir
     * @return string JSON Data
     * @throws Exception
     */
    public function jsonRows()
    {
        $arrWhat = array_values($this->getHeaderColumns());
        $arrWhere = [];
        $arrOrder = ['`id` DESC'];
        $start = 0;
        $pageSize = 15;
        if (isset($_POST['page']) && isset($_POST['pageSize'])) {
            $page = (int) $_POST['page'];
            $pageSize = (int) $_POST['pageSize'];
            $start = $page * $pageSize;
        }
        if (isset($_POST['sort']) && isset($_POST['sortDir'])) {
            $sort = (string) $_POST['sort'];
            $sortDir = (string) $_POST['sortDir'];
            if (!in_array($sortDir, ['ASC', 'DESC'])) {
                throw new Exception('Someone is doing something evil here :(');
            }
            if (!in_array($sort, $this->getAllowedPostColumns())) {
                throw new Exception('Someone is doing something evil here :(');
            }
            $arrOrder = ['`'.$sort.'` '.$sortDir];
        }
        foreach ($arrWhat as $filter) {
            if (in_array($filter, $arrWhat) && isset($_POST['filter_'.$filter])) {
                $arrWhere[$filter] = [
                    "operator" => "like",
                    "value" => '%'.$_POST['filter_'.$filter].'%'
                ];
            }
        }
        
        $arrLimit = [$start, $pageSize];
        $rows = $this->loadByPrepStmt($arrWhat, $arrWhere, $arrOrder, $arrLimit);
        if (count($rows) < 1) {
            return "[]";
        }
        foreach ($rows as $key => $columns) {
            foreach ($columns as $cKey => $column) {
                if ($cKey == "createDate") {
                    $rows[$key][$cKey] = strftime("%d.%m.%Y %H:%M:%S", strtotime($column));
                }
            }
            $rows[$key]['actions'] = $this->renderActionLinks();
        }
        $json = json_encode($rows, JSON_UNESCAPED_UNICODE);
        return $json;
    }
    
    /**
     * Gets values from $this->actionLinks and makes links out of them
     * @return string
     */
    protected function renderActionLinks() : string
    {
        $links = "";
        foreach ($this->getActionLinks() as $url => $label) {
            $links .= H::a($label, ["data-url" => $url])." ";
        }
        return $links;
    }
    
    /**
     * Gets all columns of the db table, use them for security checks
     * @return array
     */
    protected function getAllowedPostColumns() : array
    {
        $arrColumns = $this->getTableColumns();
        $arrColumnNames = [];
        //$arrColumnTypes = [];
        
        foreach ($arrColumns as $column) {
            $arrColumnNames[] = $column['Field'];
            //$arrColumnTypes[] = $column['Type'];
        }
        return $arrColumnNames;
    }
}
