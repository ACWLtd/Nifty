<?php namespace kJamesy\Cms\Helpers;

class PagesHelper
{

    public function __construct($pages)
    {
        $this->pages = (object) $pages;
        $this->pagesArray = $this->getConvertedToArray($pages);
    }

    public function getConvertedToArray($pages)
    {
        $pagesArray = [];
        foreach ($pages as $page) {
            $pagesArray[] = $page;
        }

        return $pagesArray;
    }

    public function getParents()
    {
        $pages = $this->pagesArray;
        $parents = [];

        foreach ( $pages as $page ) {
            if ( $page->parent_id == NULL )
                $parents[] = $page;
        }

        return $parents;
    }

    public function getHighestPages()
    {
        $pages = $this->pagesArray;
        $depth = 999;

        $highestLevel = [];

        foreach ($pages as $page) {
            if ( (int) $page->depth < $depth ) {
                $depth = $page->depth;
            }
        }

        foreach ($pages as $page) {
            if ( $page->depth == $depth )
                $highestLevel[] = $page;
        }

        return $highestLevel;
    }

    public function getDirectChildren($parent)
    {
        $pages = $this->pagesArray;
        $children = [];

        foreach ( $pages as $child ) {
            if ( $child->parent_id == $parent->id )
                $children[] = $child;
        }

        return $children;
    }

    public function getPagesTree()
    {
        $pagesTree = [];

        $parents = $this->getHighestPages();
        $this->pagesArray = array_diff($this->pagesArray, $parents);


        foreach ($parents as $parent) {
            $pagesTree[] = $parent;

            if( ! $parent->isLeaf() ) {
                $pagesTree[] = $this->getChildrenTree($parent);
            }
        }

        return $pagesTree;
    }

    public function getChildrenTree($parent)
    {
        $childrenTree = [];
        $children = $this->getDirectChildren($parent);

        foreach ($children as $child) {
            $childrenTree[] = $child;

            if( ! $child->isLeaf() ) {
                $childrenTree[] = $this->getChildrenTree($child);
            }
        }

        return $childrenTree;
    }

}