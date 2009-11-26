<?php
class CsvTranslationStorer implements ITranslationStorer{
    /**
     * Holds the current language
     *
     * @var string
     */
    private $_language;
    /**
     * Array with all translations
     *
     * @var array
     */
    private $_translations = array();
    /**
     * The path where to save the translation files
     *
     * @var string
     */
    private $_path = "translations";

   
   
   
    /**
     * Save translations back to csv
     *
     * @access public
     * @return void
     */
    public function save() {
        $array = "";
        foreach($this->_translations as $key => $value) {
            $array[]= $key . ";" . $value;
        }
        $path  = $this->_path . "/" . $this->_language . ".csv";
        file_put_contents($path, implode("\n", $array));
    }
    /**
     * Set the path where to save the translation files
     *
     * @access public
     * @param string $path
     * @return void
     */
    public function set_save_path($path) {
        $this->_path = $path;
    }
    /**
     * Load language file
     *
     * @access public
     * @param string $language
     * @return void
     */
    public function load($language) {
        $this->_language=$language;
        $path  = $this->_path . "/" . $language . ".csv";
        if(file_exists($path)) {
            
            $content = file_get_contents($path);
            $content = explode("\n", $content);
            foreach($content as $line) {
                $parts = explode(";", $line);
                $key = isset($parts[0]) ? $parts[0] : "";
                if(isset($parts[1])) {
                //$value = iconv("ISO-8859-1", "UTF-8", $parts[1]);
                    $value = htmlentities($parts[1], ENT_COMPAT, 'UTF-8');
                    $value = mb_convert_encoding($value,"UTF-8");
                }else {
                    $value = "";
                }
                $this->_translations[$key] = $value;
            }
        }
    }
    public function get($string){

        if(isset($this->_translations[$string]))
            return $this->_translations[$string];
        else
            return false;
    }
    public function set($string,$translation){
        $this->_translations[$string] = $translation;
        $this->save();
    }
}