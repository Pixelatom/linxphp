<?php

/**
 * # Sample model
 *
 * table: items
 * database: default
 * read_only: false
 */
class Header
{
    //@ fields

    /**
     * type: integer
     * primary_key: true
     */
    public $id;


    /**
     * label: Title
     * type: string
     * length: 32
     * description: Title of the item
     * validate:
     *   required: true
     *   length: 8-32
     *   unique: true
     */
    public $title;

    /**
     * type: Item
     * relationship: childs
     * inverse_property: header
     */
    public $items;

    //@ end fields
}
