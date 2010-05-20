<?php 

class Language {

    /**
     * detects and return the user's languaje
     */
    public static function UserLanguage(){
        // get the languages
	$a_languages = self::$languages;
	$index = '';
	$complete = '';
	$found = false;// set to default value
	//prepare user language array
	$user_languages = array();

	//check to see if language is set
	if ( isset( $_SERVER["HTTP_ACCEPT_LANGUAGE"] ) )
	{
		$languages = strtolower( $_SERVER["HTTP_ACCEPT_LANGUAGE"] );
		// $languages = ' fr-ch;q=0.3, da, en-us;q=0.8, en;q=0.5, fr;q=0.3';
		// need to remove spaces from strings to avoid error
		$languages = str_replace( ' ', '', $languages );
		$languages = explode( ",", $languages );
		//$languages = explode( ",", $test);// this is for testing purposes only

		foreach ( $languages as $language_list )
		{
			// pull out the language, place languages into array of full and primary
			// string structure:
			$temp_array = array();
			// slice out the part before ; on first step, the part before - on second, place into array
			$temp_array[0] = substr( $language_list, 0, strcspn( $language_list, ';' ) );//full language
			$temp_array[1] = substr( $language_list, 0, 2 );// cut out primary language
			//place this array into main $user_languages language array
			$user_languages[] = $temp_array;
		}

		//start going through each one
		for ( $i = 0; $i < count( $user_languages ); $i++ )
		{
			foreach ( $a_languages as $index => $complete )
			{
				if ( $index == $user_languages[$i][0] )
				{
					// complete language, like english (canada)
					$user_languages[$i][2] = $complete;
					// extract working language, like english
					$user_languages[$i][3] = substr( $complete, 0, strcspn( $complete, ' (' ) );
				}
			}
		}

                /*@todo: ver porque esto ocurre en ie */
                if (!isset($user_languages[1][0])){
                    return false;
                }

                return $user_languages[1][0];
	}
	else// if no languages found
	{
		return false;
	}

	

    }

    /**
     * Holds the current language
     *
     * @var string
     */
    private static $_language;

    /**
     * Holds the default language
     *
     * @var string
     */
    private static $_defaultlanguage = "en";


    private static $_translation_store = null;
    private static $_translator = null;



    /**
     * Enable/Disable auto google translation
     *
     * @var bool
     */
    private static $_auto = FALSE;

    /**
     * Possible languages
     *
     * @var arrays
     */
    public static $languages = array( 	"auto" => "automatic",
    "sq" => "albanian",
    "ar" => "arabic",
    "bg" => "bulgarian",
    "ca" => "catalan",
    "zh-CN" => "chinese",
    "hr" => "croatian",
    "cs" => "czech",
    "da" => "danish",
    "nl" => "dutch",
    "en" => "english",
    "et" => "estonian",
    "tl" => "filipino",
    "fi" => "finnish",
    "fr" => "french",
    "gl" => "galician",
    "de" => "german",
    "el" => "greek",
    "iw" => "hebrew",
    "hi" => "hindi",
    "hu" => "hungarian",
    "id" => "indonesian",
    "it" => "italian",
    "ja" => "japanese",
    "ko" => "korean",
    "lv" => "latvian",
    "lt" => "lithuanian",
    "mt" => "maltese",
    "no" => "norwegian",
    "fa" => "persian alpha",
    "pl" => "polish",
    "pt" => "portuguese",
    "ro" => "romanian",
    "ru" => "russian",
    "sr" => "serbian",
    "sk" => "slovak",
    "sl" => "slovenian",
    "es" => "spanish",
    "sv" => "swedish",
    "th" => "thai",
    "tr" => "turkish",
    "uk" => "ukrainian",
    "vi" => "vietnamese"
    );

    static public function SetStorer(ITranslationStorer $storer){
        self::$_translation_store = $storer;
    }

    static private function setup() {
        if (empty(self::$_translation_store))
            self::$_translation_store = new CsvTranslationStorer();

        if (empty(self::$_translator))
            self::$_translator = new GoogleTranslator();

        if (empty(self::$_language))
            self::$_language = self::$_defaultlanguage;
    }

    /**
     * Method to translate a string
     *
     * @access public
     * @param string $string
     * @return string
     */
    public static function _($string) {
        self::setup();


        // If selected language is default
        // just return the value
        if(self::$_language == self::$_defaultlanguage) {
            return $string;
        }
        $value = self::$_translation_store->get($string);
        if(!$value) {
        // Add string to translations
            if(self::$_auto) {
                $value = self::translate(self::$_defaultlanguage, self::$_language, $string);
            }
        }
        
        if(!$value) {
            $value =  '<!-- TRANSLATE THIS -->'.$string.'<!-- /TRANSLATE THIS -->';
        }
        
        self::$_translation_store->set($string,$value);
        return $value;
    }









    /**
     * Set the default language
     *
     * @access public
     * @param string $language
     * @return void
     */
    public static function SetDefault($language) {
        self::setup();
        self::$_defaultlanguage = $language;
    }

    /**
     * Set the current language
     *
     * @access public
     * @param string $language
     * @return void
     */
    public static function Set(&$language) {
        self::setup();
        if(!empty($language)) {
            self::$_translation_store->load($language);
            self::$_language = $language;
        }else {
            self::$_translation_store->load(self::$_defaultlanguage);
            $language = self::$_defaultlanguage;
            self::$_language = $language;
        }
    }

    /**
     * Get the current language
     *
     * @access public
     * @return string
     */
    public static function Get() {
        self::setup();
        return self::$_language;
    }

    /**
     * Enable auto google translation
     *
     * @access public
     * @param bool $bool
     * @return string
     */
    public static function SetAuto($bool) {
        self::setup();
        self::$_auto = $bool;
    }

    /**
     * Translate a string using google translator
     *
     * @access private
     * @param string $from
     * @param string $to
     * @param string $string
     * @return string
     */
    private static function translate($from, $to, $string) {
        self::setup();

        return self::$_translator->translate($from, $to, $string);

    }

/**
 * Write content to file
 *
 * @access
 */

}

function _t($string) {
    return Language::_($string);
}



