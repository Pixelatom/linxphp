<?php
/*
 * Linx PHP Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
 
class PDOQueryIterator implements Iterator, Countable, SeekableIterator{
	
	protected $_query="";
	protected $_pdo; // connection to the database.
	protected $_fetchmode=PDO::FETCH_BOTH;
	protected $_fetchargument1=null;
	protected $_fetchargument2=null;
	
	private $_query_position = 0; // position from where the query will take the results
	private $_query_limit = 10; // quantity of items that will be cached 
	private $_index = 0; // position in the cached results array
	private $_cached_result = null; // cached results array
		
	function __construct($pdo,$query,$fetchmode=null,$fetchargument1=null,$fetchargument2=null){
		$this->_query=$query;
		$this->_pdo=$pdo;
		if (!empty($fetchmode)){
			$this->_fetchmode=$fetchmode;
		}
		if (!empty($fetchargument1)){
			$this->_fetchargument1=$fetchargument1;
		}
		if (!empty($fetchargument2)){
			$this->_fetchargument2=$fetchargument2;
		}
	}
	
	private $_cached_count=null;
	function count(){
		
		if ($this->_cached_count===null){			
			$row=$this->_pdo->query('select count(*) from ('.$this->_query.') as __query_');
			$row=$row->fetch();
			$this->_cached_count=intval($row[0]);	
			unset($row);
		}	
		
		return $this->_cached_count;
	}
	
	function seek($index){
		if ($index<0 or $index>=$this->count())
		throw new OutOfBoundsException('Index Out of Bound');
		
		$this->_query_position=$index; // limit position in query
		$this->_index=0; // position in cached results
	}
	
	function rewind(){
		$this->_query_position=0;
		$this->_index=0;
	}
	
	function key(){
		return $this->_query_position+$this->_index;
	}
	
	function current(){
		if ($this->_index>$this->_query_limit){
			$this->_query_position=$this->_query_position+$this->_index;
			$this->_cached_result=null;
		}
		if (!is_array($this->_cached_result)){
			
			$limiter='';
			if ($this->count()>1){
				$limiter=" LIMIT {$this->_query_position},{$this->_query_limit}";
			}
			
			if (!empty($this->_fetchargument2)){
				$stmt=$this->_pdo->query("select * from ({$this->_query}) as __query_".$limiter,$this->_fetchmode,$this->_fetchargument1,$this->_fetchargument2);
			}
			elseif (!empty($this->_fetchargument1)){
				$stmt=$this->_pdo->query("select * from ({$this->_query}) as __query_".$limiter,$this->_fetchmode,$this->_fetchargument1);
			}
			else{				
				$stmt=$this->_pdo->query("select * from ({$this->_query}) as __query_".$limiter,$this->_fetchmode);
			}
			
			if ($this->count()==1)
			$this->_cached_result=array($stmt->fetchAll());
			else
			$this->_cached_result=$stmt->fetchAll();
			
						
			unset($stmt);
			$this->_index=0;
		}
		
		return $this->_cached_result[$this->_index];
	}
	
	
	function next(){
		$this->_index++;
	}
	
	
	function valid(){
		if ($this->count()==0) return false;
		return $this->key()<$this->count();
	} 
}
?>