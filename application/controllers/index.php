<?php


class IndexController extends Controller {
    function index() {

        //die (Mapper::count('item'));

        
        //Mapper::get('Item', "header.title = 'test' ");
//        Mapper::get('Header', "items.title = 'another object' ");

//        echo '<pre>';
//        $item = Mapper::get('Item', "item.title = 'another object' ");
//        var_dump(is_array($item[0]->header->items));
//        die();


        
        // testing loading
        echo '<pre>';
        var_dump(Mapper::get('Item', null, 'id desc'));
        die();
        

        /*
        // testing removing item 1
        $object = new Item();

        $object->id = 1;

        $object->title = 'is a test LOL';

        $object->uri = 'lol';


        Mapper::save($object);
        Mapper::delete($object);



        $object = new Item();

        $object->id = 2;

        $object->title = 'another object';

        Mapper::save($object);
        */

        
        // testing cache in item # 2
        /*
        $object = Mapper::get_by_id('Item', 2);
        $object->description = ':(';
        echo '<pre>';
        var_dump(Mapper::get_by_id('Item', 2));
        */
        
        // testing relationships
        $header = new Header();
        $header->id = 2;
        $header->title = 'test';
        
        $object = new Item();

        $object->id = 1;

        $object->title = 'an object';
        
        $header->items[] = $object;
        
        $object = new Item();

        $object->id = 2;

        $object->title = 'another object';
        
        $header->items[] = $object;

        Mapper::save($header);

//        Mapper::delete($header);

    }
}
?>