<?php
/**
 * # Sample model
 *
 * table: item
 * database: default
 * read_only: false
 */
class Item
{
    //@ fields

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
     * label: URI
     * type: string
     * length: 32
     * description: URI of the item
     * validate:
     *   required: true
     *   length: 4-32
     *   unique: true
     *   format: alpha_numeric
     */
    public $uri;

    /**     
     * label: Description
     * type: text
     * description: Description of the item     
     */
    public $description;

    //@ end fields
}
