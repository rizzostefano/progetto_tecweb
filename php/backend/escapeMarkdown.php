<?php

/**
 * Classe per la conversione di codice Markdown in codice HTML
 */
class MarkdownConverter {
    
  // Per informazioni sul funzionamento delle regex, consultare: https://www.rexegg.com/regex-quickstart.html
  public static $standardRules = array (
    '/(#+)(.*)/' => 'self::header',                           // headers
    '/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\'>\1</a>',  // links
    '/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',            // bold
    '/(\*|_)(.*?)\1/' => '<em>\2</em>',                       // emphasis
    '/\~\~(.*?)\~\~/' => '<del>\1</del>',                     // del
    '/\:\"(.*?)\"\:/' => '<q>\1</q>',                         // quote
    '/`(.*?)`/' => '<code>\1</code>',                         // inline code
    '/\n\*(.*)/' => 'self::ulList',                           // ul lists
    '/\n[0-9]+\.(.*)/' => 'self::olList',                     // ol lists
    '/\n(&gt;|\>)(.*)/' => 'self::blockquote ',               // blockquotes
    '/\n-{5,}/' => "\n<hr />",                                // horizontal rule
    '/\n([^\n]+)\n/' => 'self::addParagraph',                 // add paragraphs
    '/<\/ul>\s?<ul>/' => '',                                  // fix extra ul
    '/<\/ol>\s?<ol>/' => '',                                  // fix extra ol
    '/<\/blockquote><blockquote>/' => "\n"                    // fix extra blockquote
  );

  public static $customRules = array (
      '/(\(\?)(.+?)(\))/' => 'self::language'                   // lang
  );

  /**
   * @summary: callback per la conversione di custom tag per immagini
   * @param: $regs array di componenti della regex
   * @return: stringa con il nuovo contenuto convertito
   */
  private static function image($regs)
  {
    $imageName = $regs[2];
    return sprintf("<img src='%s_URL' alt='%s_ALT' />", $imageName, $imageName);
  }

  /**
   * @summary: callback per la conversione di parole in lungua inglese
   * @param: $regs array di componenti della regex
   * @return: stringa con il nuovo contenuto convertito
   */
  private static function language($regs)
  {
    $langValue = $regs[2];
    return sprintf("<span lang='en'>%s</span>", $langValue);
  }
    
  /**
   * @summary: callback per la conversione di paragrafi
   * @param: $regs array di componenti della regex
   * @return: stringa con il nuovo paragrafo convertito
   */
  private static function addParagraph ($regs)
  {
		$line = $regs[1];
		$trimmed = trim ($line);
    if (preg_match ('/^<\/?(ul|ol|li|h|p|bl)/', $trimmed))
    {
			return "\n" . $line . "\n";
		}
		return sprintf ("\n<p>%s</p>\n", $trimmed);
	}

  /**
   * @summary: callback per la conversione di liste
   * @param: $regs array di componenti della regex
   * @return: stringa con la nuova lista convertita
   */
  private static function ulList ($regs)
  {
		$item = $regs[1];
		return sprintf ("\n<ul>\n\t<li>%s</li>\n</ul>", trim ($item));
	}

  /**
   * @summary: callback per la conversione di liste
   * @param: $regs array di componenti della regex
   * @return: stringa con la nuova lista convertita
   */
  private static function olList ($regs)
  {
		$item = $regs[1];
		return sprintf ("\n<ol>\n\t<li>%s</li>\n</ol>", trim ($item));
	}

  /**
   * @summary: callback per la conversione di citazioni
   * @param: $regs array di componenti della regex
   * @return: stringa con la citazione convertita
   */
  private static function blockquote ($regs)
  {
		$item = $regs[2];
		return sprintf ("\n<blockquote>%s</blockquote>", trim ($item));
	}

  /**
   * @summary: callback per la conversione di header
   * @param: $regs array di componenti della regex
   * @return: stringa con il nuovo header convertito
   */
  private static function header ($regs)
  {
		list ($tmp, $chars, $header) = $regs;
		$level = strlen ($chars);
		return sprintf ('<h%d>%s</h%d>', $level, trim ($header), $level);
	}

	/**
	 * @summary: aggiunge una nuova regola alla classe
   * @param: $regex nuova regex che si vuole aggiungere
   * @param: $replacement nuovo replacement per la regex sopra indicata
   * @return: void
	 */
  public static function add_rule ($regex, $replacement)
  {
		self::$customRules[$regex] = $replacement;
	}

	/**
	 * @summary: trasforma l'input dato in markdown in codice html
   * @param: stringa contente codice markdown
   * @return: stringa con codice html 
	*/
  public static function render ($input)
  {
		$input = "\n" . $input . "\n";
    foreach (self::$standardRules as $regex => $replacement)
    {
      if (is_callable ( $replacement))
      {
				$input = preg_replace_callback ($regex, $replacement, $input);
      } 
      else 
      {
				$input = preg_replace ($regex, $replacement, $input);
			}
    }
    foreach (self::$customRules as $regex => $replacement)
    {
      if (is_callable ( $replacement))
      {
				$input = preg_replace_callback ($regex, $replacement, $input);
      } 
      else 
      {
				$input = preg_replace ($regex, $replacement, $input);
			}
    }
		return trim ($input);
  }
  
  /**
	 * @summary: trasforma l'input dato in markdown in codice html usando solo la regola
   * per la lingue
   * @param: stringa contente codice markdown
   * @return: stringa con codice html 
	*/
  public static function renderOnlyLanguage($input)
  {
    $input = "\n" . $input . "\n";
    $regex = array_search("self::language", self::$customRules);
    $input = preg_replace_callback ($regex, "self::language", $input);
    return trim ($input);
  }
}