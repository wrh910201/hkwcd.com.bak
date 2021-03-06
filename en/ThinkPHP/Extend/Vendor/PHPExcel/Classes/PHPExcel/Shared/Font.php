.html#customlog">CustomLog</a></code>
    or <code class="directive"><a href="./mod/core.html#errorlog">ErrorLog</a></code>
    directives are placed inside a
    <code class="directive"><a href="./mod/core.html#virtualhost">&lt;VirtualHost&gt;</a></code>
    section, all requests or errors for that virtual host will be
    logged only to the specified file. Any virtual host which does
    not have logging directives will still have its requests sent
    to the main server logs. This technique is very useful for a
    small number of virtual hosts, but if the number of hosts is
    very large, it can be complicated to manage. In addition, it
    can often create problems with <a href="vhosts/fd-limits.html">insufficient file
    descriptors</a>.</p>

    <p>For the access log, there is a very good compromise. By
    adding information on the virtual host to the log format
    string, it is possible to log all hosts to the same log, and
    later split the log into individual files. For example,
    consider the following directives.</p>

    <pre class="prettyprint lang-config">LogFormat "%v %l %u %t \"%r\" %&gt;s %b" comonvhost
CustomLog logs/access_log comonvhost</pre>


    <p>The <code>%v</code> is used to log the name of the virtual
    host that is serving the request. Then a program like <a href="programs/other.html">split-logfile</a> can be used to
    post-process the access log in order to split it into one file
    per virtual host.</p>
  </div><div class="top"><a href="#page-header"><img alt="top" src="./images/up.gif" /></a></div>
<div class="section">
<h2><a name="other" id="other">Other Log Files</a></h2>
    

    <table class="related"><tr><th>Related Modules</th><th>Related Directives</th></tr><tr><td><ul><li><code class="module"><a href="./mod/mod_logio.html">mod_logio</a></code></li><li><code class="module"><a href="./mod/mod_log_config.html">mod_log_config</a></code></li><li><code class="module"><a href="./mod/mod_log_forensic.html">mod_log_forensic</a></code></li><li><code class="module"><a href="./mod/mod_cgi.html">mod_cgi</a></code></li></ul></td><td><ul><li><code class="directive"><a href="./mod/mod_log_config.html#logformat">LogFormat</a></code></li><li><code class="directive"><a href="./mod/mod_log_config.html#bufferedlogs">BufferedLogs</a></code></li><li><code class="directive"><a href="./mod/mod_log_forensic.html#forensiclog">ForensicLog</a></code></li><li><code class="directive"><a href="./mod/mpm_common.html#pidfile">PidFile</a></code></li><li><code class="directive"><a href="./mod/mod_cgi.html#scriptlog">ScriptLog</a></code></li><li><code class="directive"><a href="./mod/mod_cgi.html#scriptlogbuffer">ScriptLogBuffer</a></code></li><li><code class="directive"><a href="./mod/mod_cgi.html#scriptloglength">ScriptLogLength</a></code></li></ul></td></tr></table>

    <h3>Logging actual bytes sent and received</h3>
      

      <p><code class="module"><a href="./mod/mod_logio.html">mod_logio</a></code> adds in two additional
         <code class="directive"><a href="./mod/mod_log_config.html#logformat">LogFormat</a></code> fields
         (%I and %O) that log the actual number of bytes received and sent
         on the network.</p>
    

    <h3>Forensic Logging</h3>
      

      <p><code class="module"><a href="./mod/mod_log_forensic.html">mod_log_forensic</a></code> provides for forensic logging of
         client requests. Logging is done before and after processing a
         request, so the forensic log contains two log lines for each
         request. The forensic logger is very strict with no customizations.
         It can be an invaluable debugging and security tool.</p>
    

    <h3><a name="pidfile" id="pidfile">PID File</a></h3>
      

      <p>On startup, Apache httpd saves the process id of the parent
      httpd process to the file <code>logs/httpd.pid</code>. This
      filename can be changed with the <code class="directive"><a href="./mod/mpm_common.html#pidfile">PidFile</a></code> directive. The
      process-id is for use by the administrator in restarting and
      terminating the daemon by sending signals to the parent
      process; on Windows, use the -k command line option instead.
      For more information see the <a href="stopping.html">Stopping
      and Restarting</a> page.</p>
    

    <h3><a name="scriptlog" id="scriptlog">Script Log</a></h3>
      

      <p>In order to aid in debugging, the
      <code class="directive"><a href="./mod/mod_cgi.html#scriptlog">ScriptLog</a></code> directive
      allows you to record the input to and output from CGI scripts.
      This should only be used in testing - not for live servers.
      More information is available in the <a href="mod/mod_cgi.html">mod_cgi</a> documentation.</p>
    

  </div></div>
<div class="bottomlang">
<p><span>Available Languages: </span><a href="./en/logs.html" title="English">&nbsp;en&nbsp;</a> |
<a href="./fr/logs.html" hreflang="fr" rel="alternate" title="Fran�ais">&nbsp;fr&nbsp;</a> |
<a href="./ja/logs.html" hreflang="ja" rel="alternate" title="Japanese">&nbsp;ja&nbsp;</a> |
<a href="./ko/logs.html" hreflang="ko" rel="alternate" title="Korean">&nbsp;ko&nbsp;</a> |
<a href="./tr/logs.html" hreflang="tr" rel="alternate" title="T�rk�e">&nbsp;tr&nbsp;</a></p>
</div><div class="top"><a href="#page-header"><img src="./images/up.gif" alt="top" /></a></div><div class="section"><h2><a id="comments_section" name="comments_section">Comments</a></h2><div class="warning"><strong>Notice:</strong><br />This is not a Q&amp;A section. Comments placed here should be pointed towards suggestions on improving the documentation or server, and may be removed again by our moderators if they are either implemented or considered invalid/off-topic. Questions on how to manage the Apache HTTP Server should be directed at either our IRC channel, #httpd, on Freenode, or sent to our <a href="http://httpd.apache.org/lists.html">mailing lists</a>.</div>
<script type="text/javascript"><!--//--><![CDATA[//><!--
var comments_shortname = 'httpd';
var comments_identifier = 'http://httpd.apache.org/docs/2.4/logs.html';
(function(w, d) {
    if (w.location.hostname.toLowerCase() == "httpd.apache.org") {
        d.write('<div id="comments_thread"><\/div>');
        var s = d.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = 'https://comments.apache.org/show_comments.lua?site=' + comments_shortname + '&page=' + comments_identifier;
        (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
    }
    else { 
        d.write('<div id="comments_thread">Comments are disabled for this page at the moment.<\/div>');
    }
})(window, document);
//--><!]]></script></div><div id="footer">
<p class="apache">Copyright 2014 The Apache Software Foundation.<br />Licensed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License, Version 2.0</a>.</p>
<p class="menu"><a href="./mod/">Modules</a> | <a href="./mod/directives.html">Directives</a> | <a href="http://wiki.apache.org/httpd/FAQ">FAQ</a> | <a href="./glossary.html">Glossary</a> | <a href="./sitemap.html">Sitemap</a></p></div><script type="text/javascript"><!--//--><![CDATA[//><!--
if (typeof(prettyPrint) !== 'undefined') {
    prettyPrint();
}
//--><!]]></script>
</body></html>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          Excel_Style_Font $defaultFont = null) {

		// If it is rich text, use plain text
		if ($cellText instanceof PHPExcel_RichText) {
			$cellText = $cellText->getPlainText();
		}

		// Special case if there are one or more newline characters ("\n")
		if (strpos($cellText, "\n") !== false) {
			$lineTexts = explode("\n", $cellText);
			$lineWitdhs = array();
			foreach ($lineTexts as $lineText) {
				$lineWidths[] = self::calculateColumnWidth($font, $lineText, $rotation = 0, $defaultFont);
			}
			return max($lineWidths); // width of longest line in cell
		}

		// Try to get the exact text width in pixels
		try {
			// If autosize method is set to 'approx', use approximation
			if (self::$autoSizeMethod == self::AUTOSIZE_METHOD_APPROX) {
				throw new PHPExcel_Exception('AutoSize method is set to approx');
			}

			// Width of text in pixels excl. padding
			$columnWidth = self::getTextWidthPixelsExact($cellText, $font, $rotation);

			// Excel adds some padding, use 1.07 of the width of an 'n' glyph
			$columnWidth += ceil(self::getTextWidthPixelsExact('0', $font, 0) * 1.07); // pixels incl. padding

		} catch (PHPExcel_Exception $e) {
			// Width of text in pixels excl. padding, approximation
			$columnWidth = self::getTextWidthPixelsApprox($cellText, $font, $rotation);

			// Excel adds some padding, just use approx width of 'n' glyph
			$columnWidth += self::getTextWidthPixelsApprox('n', $font, 0);
		}

		// Convert from pixel width to column width
		$columnWidth = PHPExcel_Shared_Drawing::pixelsToCellDimension($columnWidth, $defaultFont);

		// Return
		return round($columnWidth, 6);
	}

	/**
	 * Get GD text width in pixels for a string of text in a certain font at a certain rotation angle
	 *
	 * @param string $text
	 * @param PHPExcel_Style_Font
	 * @param int $rotation
	 * @return int
	 * @throws PHPExcel_Exception
	 */
	public static function getTextWidthPixelsExact($text, PHPExcel_Style_Font $font, $rotation = 0) {
		if (!function_exists('imagettfbbox')) {
			throw new PHPExcel_Exception('GD library needs to be enabled');
		}

		// font size should really be supplied in pixels in GD2,
		// but since GD2 seems to assume 72dpi, pixels and points are the same
		$fontFile = self::getTrueTypeFontFileFromFont($font);
		$textBox = imagettfbbox($font->getSize(), $rotation, $fontFile, $text);

		// Get corners positions
		$lowerLeftCornerX  = $textBox[0];
		$lowerLeftCornerY  = $textBox[1];
		$lowerRightCornerX = $textBox[2];
		$lowerRightCornerY = $textBox[3];
		$upperRightCornerX = $textBox[4];
		$upperRightCornerY = $textBox[5];
		$upperLeftCornerX  = $textBox[6];
		$upperLeftCornerY  = $textBox[7];

		// Consider the rotation when calculating the width
		$textWidth = max($lowerRightCornerX - $upperLeftCornerX, $upperRightCornerX - $lowerLeftCornerX);

		return $textWidth;
	}

	/**
	 * Get approximate width in pixels for a string of text in a certain font at a certain rotation angle
	 *
	 * @param string $columnText
	 * @param PHPExcel_Style_Font $font
	 * @param int $rotation
	 * @return int Text width in pixels (no padding added)
	 */
	public static function getTextWidthPixelsApprox($columnText, PHPExcel_Style_Font $font = null, $rotation = 0)
	{
		$fontName = $font->getName();
		$fontSize = $font->getSize();

		// Calculate column width in pixels. We assume fixed glyph width. Result varies with font name and size.
		switch ($fontName) {
			case 'Calibri':
				// value 8.26 was found via interpolation by inspecting real Excel files with Calibri 11 font.
				$columnWidth = (int) (8.26 * PHPExcel_Shared_String::CountCharacters($columnText));
				$columnWidth = $columnWidth * $fontSize / 11; // extrapolate from font size
				break;

			case 'Arial':
				// value 7 was found via interpolation by inspecting real Excel files with Arial 10 font.
				$columnWidth = (int) (7 * PHPExcel_Shared_String::CountCharacters($columnText));
				$columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size
				break;

			case 'Verdana':
				// value 8 was found via interpolation by inspecting real Excel files with Verdana 10 font.
				$columnWidth = (int) (8 * PHPExcel_Shared_String::CountCharacters($columnText));
				$columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size
				break;

			default:
				// just assume Calibri
				$columnWidth = (int) (8.26 * PHPExcel_Shared_String::CountCharacters($columnText));
				$columnWidth = $columnWidth * $fontSize / 11; // extrapolate from font size
				break;
		}

		// Calculate approximate rotated column width
		if ($rotation !== 0) {
			if ($rotation == -165) {
				// stacked text
				$columnWidth = 4; // approximation
			} else {
				// rotated text
				$columnWidth = $columnWidth * cos(deg2rad($rotation))
								+ $fontSize * abs(sin(deg2rad($rotation))) / 5; // approximation
			}
		}

		// pixel width is an integer
		$columnWidth = (int) $columnWidth;
		return $columnWidth;
	}

	/**
	 * Calculate an (approximate) pixel size, based on a font points size
	 *
	 * @param 	int		$fontSizeInPoints	Font size (in points)
	 * @return 	int		Font size (in pixels)
	 */
	public static function fontSizeToPixels($fontSizeInPoints = 11) {
		return (int) ((4 / 3) * $fontSizeInPoints);
	}

	/**
	 * Calculate an (approximate) pixel size, based on inch size
	 *
	 * @param 	int		$sizeInInch	Font size (in inch)
	 * @return 	int		Size (in pixels)
	 */
	public static function inchSizeToPixels($sizeInInch = 1) {
		return ($sizeInInch * 96);
	}

	/**
	 * Calculate an (approximate) pixel size, based on centimeter size
	 *
	 * @param 	int		$sizeInCm	Font size (in centimeters)
	 * @return 	int		Size (in pixels)
	 */
	public static function centimeterSizeToPixels($sizeInCm = 1) {
		return ($sizeInCm * 37.795275591);
	}

	/**
	 * Returns the font path given the font
	 *
	 * @param PHPExcel_Style_Font
	 * @return string Path to TrueType font file
	 */
	public static function getTrueTypeFontFileFromFont($font) {
		if (!file_exists(self::$trueTypeFontPath) || !is_dir(self::$trueTypeFontPath)) {
			throw new PHPExcel_Exception('Valid directory to TrueType Font files not specified');
		}

		$name		= $font->getName();
		$bold		= $font->getBold();
		$italic		= $font->getItalic();

		// Check if we can map font to true type font file
		switch ($name) {
			case 'Arial':
				$fontFile = (
					$bold ? ($italic ? self::ARIAL_BOLD_ITALIC : self::ARIAL_BOLD)
						  : ($italic ? self::ARIAL_ITALIC : self::ARIAL)
				);
				break;

			case 'Calibri':
				$fontFile = (
					$bold ? ($italic ? self::CALIBRI_BOLD_ITALIC : self::CALIBRI_BOLD)
						  : ($italic ? self::CALIBRI_ITALIC : self::CALIBRI)
				);
				break;

			case 'Courier New':
				$fontFile = (
					$bold ? ($italic ? self::COURIER_NEW_BOLD_ITALIC : self::COURIER_NEW_BOLD)
						  : ($italic ? self::COURIER_NEW_ITALIC : self::COURIER_NEW)
				);
				break;

			case 'Comic Sans MS':
				$fontFile = (
					$bold ? self::COMIC_SANS_MS_BOLD : self::COMIC_SANS_MS
				);
				break;

			case 'Georgia':
				$fontFile = (
					$bold ? ($italic ? self::GEORGIA_BOLD_ITALIC : self::GEORGIA_BOLD)
						  : ($italic ? self::GEORGIA_ITALIC : self::GEORGIA)
				);
				break;

			case 'Impact':
				$fontFile = self::IMPACT;
				break;

			case 'Liberation Sans':
				$fontFile = (
					$bold ? ($italic ? self::LIBERATION_SANS_BOLD_ITALIC : self::LIBERATION_SANS_BOLD)
						  : ($italic ? self::LIBERATION_SANS_ITALIC : self::LIBERATION_SANS)
				);
				break;

			case 'Lucida Console':
				$fontFile = self::LUCIDA_CONSOLE;
				break;

			case 'Lucida Sans Unicode':
				$fontFile = self::LUCIDA_SANS_UNICODE;
				break;

			case 'Microsoft Sans Serif':
				$fontFile = self::MICROSOFT_SANS_SERIF;
				break;

			case 'Palatino Linotype':
				$fontFile = (
					$bold ? ($italic ? self::PALATINO_LINOTYPE_BOLD_ITALIC : self::PALATINO_LINOTYPE_BOLD)
						  : ($italic ? self::PALATINO_LINOTYPE_ITALIC : self::PALATINO_LINOTYPE)
				);
				break;

			case 'Symbol':
				$fontFile = self::SYMBOL;
				break;

			case 'Tahoma':
				$fontFile = (
					$bold ? self::TAHOMA_BOLD : self::TAHOMA
				);
				break;

			case 'Times New Roman':
				$fontFile = (
					$bold ? ($italic ? self::TIMES_NEW_ROMAN_BOLD_ITALIC : self::TIMES_NEW_ROMAN_BOLD)
						  : ($italic ? self::TIMES_NEW_ROMAN_ITALIC : self::TIMES_NEW_ROMAN)
				);
				break;

			case 'Trebuchet MS':
				$fontFile = (
					$bold ? ($italic ? self::TREBUCHET_MS_BOLD_ITALIC : self::TREBUCHET_MS_BOLD)
						  : ($italic ? self::TREBUCHET_MS_ITALIC : self::TREBUCHET_MS)
				);
				break;

			case 'Verdana':
				$fontFile = (
					$bold ? ($italic ? self::VERDANA_BOLD_ITALIC : self::VERDANA_BOLD)
						  : ($italic ? self::VERDANA_ITALIC : self::VERDANA)
				);
				break;

			default:
				throw new PHPExcel_Exception('Unknown font name "'. $name .'". Cannot map to TrueType font file');
				break;
		}

		$fontFile = self::$trueTypeFontPath . $fontFile;

		// Check if file actually exists
		if (!file_exists($fontFile)) {
			throw New PHPExcel_Exception('TrueType Font file not found');
		}

		return $fontFile;
	}

	/**
	 * Returns the associated charset for the font name.
	 *
	 * @param string $name Font name
	 * @return int Character set code
	 */
	public static function getCharsetFromFontName($name)
	{
		switch ($name) {
			// Add more cases. Check FONT records in real Excel files.
			case 'EucrosiaUPC':		return self::CHARSET_ANSI_THAI;
			case 'Wingdings':		return self::CHARSET_SYMBOL;
			case 'Wingdings 2':		return self::CHARSET_SYMBOL;
			case 'Wingdings 3':		return self::CHARSET_SYMBOL;
			default:				return self::CHARSET_ANSI_LATIN;
		}
	}

	/**
	 * Get the effective column width for columns without a column dimension or column with width -1
	 * For example, for Calibri 11 this is 9.140625 (64 px)
	 *
	 * @param PHPExcel_Style_Font $font The workbooks default font
	 * @param boolean $pPixels true = return column width in pixels, false = return in OOXML units
	 * @return mixed Column width
	 */
	public static function getDefaultColumnWidthByFont(PHPExcel_Style_Font $font, $pPixels = false)
	{
		if (isset(self::$defaultColumnWidths[$font->getName()][$font->getSize()])) {
			// Exact width can be determined
			$columnWidth = $pPixels ?
				self::$defaultColumnWidths[$font->getName()][$font->getSize()]['px']
					: self::$defaultColumnWidths[$font->getName()][$font->getSize()]['width'];

		} else {
			// We don't have data for this particular font and size, use approximation by
			// extrapolating from Calibri 11
			$columnWidth = $pPixels ?
				self::$defaultColumnWidths['Calibri'][11]['px']
					: self::$defaultColumnWidths['Calibri'][11]['width'];
			$columnWidth = $columnWidth * $font->getSize() / 11;

			// Round pixels to closest integer
			if ($pPixels) {
				$columnWidth = (int) round($columnWidth);
			}
		}

		return $columnWidth;
	}

	/**
	 * Get the effective row height for rows without a row dimension or rows with height -1
	 * For example, for Calibri 11 this is 15 points
	 *
	 * @param PHPExcel_Style_Font $font The workbooks default font
	 * @return float Row height in points
	 */
	public static function getDefaultRowHeightByFont(PHPExcel_Style_Font $font)
	{
		switch ($font->getName()) {
			case 'Arial':
				switch ($font->getSize()) {
					case 10:
						// inspection of Arial 10 workbook says 12.75pt ~17px
						$rowHeight = 12.75;
						break;

					case 9:
						// inspection of Arial 9 workbook says 12.00pt ~16px
						$rowHeight = 12;
						break;

					case 8:
						// inspection of Arial 8 workbook says 11.25pt ~15px
						$rowHeight = 11.25;
						break;

					case 7:
						// inspection of Arial 7 workbook says 9.00pt ~12px
						$rowHeight = 9;
						break;

					case 6:
					case 5:
						// inspection of Arial 5,6 workbook says 8.25pt ~11px
						$rowHeight = 8.25;
						break;

					case 4:
						// inspection of Arial 4 workbook says 6.75pt ~9px
						$rowHeight = 6.75;
						break;

					case 3:
						// inspection of Arial 3 workbook says 6.00pt ~8px
						$rowHeight = 6;
						break;

					case 2:
					case 1:
						// inspection of Arial 1,2 workbook says 5.25pt ~7px
						$rowHeight = 5.25;
						break;

					default:
						// use Arial 10 workbook as an approximation, extrapolation
						$rowHeight = 12.75 * $font->getSize() / 10;
						break;
				}
				break;

			case 'Calibri':
				switch ($font->getSize()) {
					case 11:
						// inspection of Calibri 11 workbook says 15.00pt ~20px
						$rowHeight = 15;
						break;

					case 10:
						// inspection of Calibri 10 workbook says 12.75pt ~17px
						$rowHeight = 12.75;
						break;

					case 9:
						// inspection of Calibri 9 workbook says 12.00pt ~16px
						$rowHeight = 12;
						break;

					case 8:
						// inspection of Calibri 8 workbook says 11.25pt ~15px
						$rowHeight = 11.25;
						break;

					case 7:
						// inspection of Calibri 7 workbook says 9.00pt ~12px
						$rowHeight = 9;
						break;

					case 6:
					case 5:
						// inspection of Calibri 5,6 workbook says 8.25pt ~11px
						$rowHeight = 8.25;
						break;

					case 4:
						// inspection of Calibri 4 workbook says 6.75pt ~9px
						$rowHeight = 6.75;
						break;

					case 3:
						// inspection of Calibri 3 workbook says 6.00pt ~8px
						$rowHeight = 6.00;
						break;

					case 2:
					case 1:
						// inspection of Calibri 1,2 workbook says 5.25pt ~7px
						$rowHeight = 5.25;
						break;

					default:
						// use Calibri 11 workbook as an approximation, extrapolation
						$rowHeight = 15 * $font->getSize() / 11;
						break;
				}
				break;

			case 'Verdana':
				switch ($font->getSize()) {
					case 10:
						// inspection of Verdana 10 workbook says 12.75pt ~17px
						$rowHeight = 12.75;
						break;

					case 9:
						// inspection of Verdana 9 workbook says 11.25pt ~15px
						$rowHeight = 11.25;
						break;

					case 8:
						// inspection of Verdana 8 workbook says 10.50pt ~14px
						$rowHeight = 10.50;
						break;

					case 7:
						// inspection of Verdana 7 workbook says 9.00pt ~12px
						$rowHeight = 9.00;
						break;

					case 6:
					case 5:
						// inspection of Verdana 5,6 workbook says 8.25pt ~11px
						$rowHeight = 8.25;
						break;

					case 4:
						// inspection of Verdana 4 workbook says 6.75pt ~9px
						$rowHeight = 6.75;
						break;

					case 3:
						// inspection of Verdana 3 workbook says 6.00pt ~8px
						$rowHeight = 6;
						break;

					case 2:
					case 1:
						// inspection of Verdana 1,2 workbook says 5.25pt ~7px
						$rowHeight = 5.25;
						break;

					default:
						// use Verdana 10 workbook as an approximation, extrapolation
						$rowHeight = 12.75 * $font->getSize() / 10;
						break;
				}
				break;

			default:
				// just use Calibri as an approximation
				$rowHeight = 15 * $font->getSize() / 11;
				break;
		}

		return $rowHeight;
	}

}
