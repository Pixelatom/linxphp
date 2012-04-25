<?php
/**
 * extension of Mapper for supporting SQLSERVER specific stuff like pagination
 * and autoincrement and that kind of things
 *
 * @author JaViS
 */
class SQLSrvMapperDriver extends SQLMapperDriver{

	protected $escape = '"';

}
?>
