<?php
/**
 * # Sample model
 *
 * table: items
 * database: default
 * read_only: false
 */
class Item extends Model
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
     * type: string
     * description: Description of the item     
     */
    public $description;


    /**
     * type: Header
     * relationship: parent
     * lazy_load: true
     */
    public $header;

    //@ end fields

    /*

    public function __set($name, $value) {
        echo "Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
    }
     */
}
