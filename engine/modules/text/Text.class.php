<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

require_once(Config::Get('path.root.engine').'/lib/external/Jevix/jevix.class.php');

/**
 * Модуль обработки текста на основе типографа Jevix
 * Позволяет вырезать из текста лишние HTML теги и предотвращает различные попытки внедрить в текст JavaScript
 * <pre>
 * $sText=$this->Text_Parser($sTestSource);
 * </pre>
 * Настройки парсинга находятся в конфиге /config/jevix.php
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleText extends Module {
	/**
	 * Объект типографа
	 *
	 * @var Jevix
	 */
	protected $oJevix;

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {
		/**
		 * Создаем объект типографа и запускаем его конфигурацию
		 */
		$this->oJevix = new Jevix();
		$this->JevixConfig();
	}
	/**
	 * Конфигурирует типограф
	 *
	 */
	protected function JevixConfig() {
		// загружаем конфиг
		$this->LoadJevixConfig();
	}
	/**
	 * Загружает конфиг Jevix'а
	 *
	 * @param string $sType Тип конфига
	 * @param bool $bClear	Очищать предыдущий конфиг или нет
	 */
	public function LoadJevixConfig($sType='default',$bClear=true) {
		if ($bClear) {
			$this->oJevix->tagsRules=array();
		}
		$aConfig=Config::Get('jevix.'.$sType);
		if (is_array($aConfig)) {
			foreach ($aConfig as $sMethod => $aExec) {
				foreach ($aExec as $aParams) {
					if (in_array(strtolower($sMethod),array_map("strtolower",array('cfgSetTagCallbackFull','cfgSetTagCallback')))) {
						if (isset($aParams[1][0]) and $aParams[1][0]=='_this_') {
							$aParams[1][0]=$this;
						}
					}
					call_user_func_array(array($this->oJevix,$sMethod), $aParams);
				}
			}
			/**
			 * Хардкодим некоторые параметры
			 */
			unset($this->oJevix->entities1['&']); // разрешаем в параметрах символ &
			if (Config::Get('view.noindex') and isset($this->oJevix->tagsRules['a'])) {
				$this->oJevix->cfgSetTagParamDefault('a','rel','nofollow',true);
			}
		}
	}
	/**
	 * Возвращает объект Jevix
	 *
	 * @return Jevix
	 */
	public function GetJevix() {
		return $this->oJevix;
	}
	/**
	 * Парсинг текста с помощью Jevix
	 *
	 * @param string $sText	Исходный текст
	 * @param array $aError	Возвращает список возникших ошибок
	 * @return string
	 */
	public function JevixParser($sText,&$aError=null) {
		// Если конфиг пустой, то загружаем его
		if (!count($this->oJevix->tagsRules)) {
			$this->LoadJevixConfig();
		}
		$sResult=$this->oJevix->parse($sText,$aError);
		return $sResult;
	}
	/**
	 * Парсинг текста на предмет видео
	 * Находит теги <pre><video></video></pre> и реобразовываетих в видео
	 *
	 * @param string $sText	Исходный текст
	 * @return string
	 */
	public function VideoParser($sText) {
		
		// Размеры и атрибуты окна вставленного видео.
        $iWidth = 680;
        $iHeight = 420;
        $iAlign = "center";
        $sIframeAttr = 'frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen="allowfullscreen"';
		/**
		* любое видео
		*/		
		$sText = preg_replace('/<vid>(.*)<\/vid>/Ui', '<div align="'.$iAlign.'">
		<video width="95%" controls="controls">
<source type="video/webm" src="$1" preload="auto"></source>
<source type="video/mp4" src="$1" preload="auto"></source>
<source type="video/avi" src="$1" preload="auto"></source>
<source type="video/ogg" src="$1" preload="auto"></source>
<source type="video/flv" src="$1" preload="auto"></source>
<source type="video/3gp" src="$1" preload="auto"></source>
<p>Ваш пользовательский агент не поддерживает элемент HTML5 Video</p>
</video>
		</div>',
		$sText
		);
		/**
		 * youtu.be
		 */
		$sText = preg_replace('/<video>https:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)(&.+)?<\/video>/Ui', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $sText);
		/**
		 * youtube Shorts
		 */
		$sText = preg_replace('/<video>https:\/\/(?:www\.|)youtube\.com\/shorts\/([a-zA-Z0-9_\-]+)(&.+)?<\/video>/Ui', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $sText);
		/**
         * youtube.com fixed
         */
        $sText = preg_replace(
        '/<video>(?:http(?:s|):|)(?:\/\/|)(?:www\.|m.|)youtu(?:\.|)be(?:-nocookie|)(?:\.com|)\/(?:e(?:mbed|)\/|v\/|watch\?(?:.+&|)v=|)([a-zA-Z0-9_\-]+?)(&.+)?<\/video>/Ui',
        '<div align="'.$iAlign.'"><iframe width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.' src="//www.youtube.com/embed/$1?modestbranding=1&" frameborder="0" allowfullscreen ></iframe></div>',
        $sText
        );
		/**
         * vimeo.com fixed
         */
        $sText = preg_replace(
		    '/<video>http(?:s|):\/\/(?:www\.|)vimeo\.com\/(\d+).*<\/video>/i',
            '<div align="'.$iAlign.'"><iframe src="//player.vimeo.com/video/$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>',
            $sText
        );
		/**
         * rutube.ru fixed
         */
        $sText = preg_replace(
            '/<video>http(?:s|):\/\/(?:www\.|)rutube\.ru\/tracks\/(\d+)\.html.*<\/video>/Ui',
            '<div align="'.$iAlign.'"><iframe src="//rutube.ru/play/embed/$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>',
            $sText
        );
        $sText = preg_replace(
            '/<video>http(?:s|):\/\/(?:www\.|)rutube\.ru\/video\/(\w+)\/?<\/video>/Ui',
            '<div align="'.$iAlign.'"><iframe src="//rutube.ru/play/embed/$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>',
            $sText
        );
		/**
	    * coub.com fixed
	    */
	    $sText = preg_replace(
		    '/<video>http(?:s|):\/\/(?:www\.|)coub\.com\/view\/(\w+).*<\/video>/i', 
		    '<div align="'.$iAlign.'"><iframe src="//coub.com/embed/$1?muted=false&autostart=false&originalSize=false&hideTopBar=false&noSiteButtons=false&startWithHD=false" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>', 
		$sText
		);
		/**
	    * ok.ru fixed
	    */
	    $sText = preg_replace(
		    '/<video>http(?:s|):\/\/(?:www\.|)ok\.ru\/video\/(\w+).*<\/video>/i', 
		    '<div align="'.$iAlign.'"><iframe src="//ok.ru/videoembed/$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>', 
		$sText
		);
		/**
	    * vk.com HASH not working, embeded link only
	    */
		$sText = preg_replace(
		    '/<video>(https:\/\/(?:www\.|)vk\.com\/video_ext\.php.*)<\/video>/i', 
			'<div align="'.$iAlign.'"><iframe src="$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>', 
		$sText
		);
		/**
	    * dailymotion.com fixed
	    */
	    $sText = preg_replace(
		    '/<video>http(?:s|):\/\/(?:www\.|)dai\.ly\/(\w+).*<\/video>/i','<div align="'.$iAlign.'"><iframe src="//dailymotion.com/embed/video/$1" width="'.$iWidth.'" height="'.$iHeight.'" '.$sIframeAttr.'></iframe></div>', 
		$sText
		);
		return $sText;
	}
	/**
	 * Парсит текст, применя все парсеры
	 *
	 * @param string $sText Исходный текст
	 * @return string
	 */
	public function Parser($sText) {
		if (!is_string($sText)) {
			return '';
		}
		$sResult=$this->FlashParamParser($sText);
		$sResult=$this->JevixParser($sResult);
		$sResult=$this->VideoParser($sResult);
		$sResult=$this->CodeSourceParser($sResult);
		return $sResult;
	}
	/**
	 * Заменяет все вхождения короткого тега <param/> на длиную версию <param></param>
	 * Заменяет все вхождения короткого тега <embed/> на длиную версию <embed></embed>
	 *
	 * @param string $sText Исходный текст
	 * @return string
	 */
	protected function FlashParamParser($sText) {
		if (preg_match_all("@(<\s*param\s*name\s*=\s*(?:\"|').*(?:\"|')\s*value\s*=\s*(?:\"|').*(?:\"|'))\s*/?\s*>(?!</param>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$str_new=$str.'></param>';
				$sText=str_replace($aMatch[0][$key],$str_new,$sText);
			}
		}
		if (preg_match_all("@(<\s*embed\s*.*)\s*/?\s*>(?!</embed>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$str_new=$str.'></embed>';
				$sText=str_replace($aMatch[0][$key],$str_new,$sText);
			}
		}
		/**
		 * Удаляем все <param name="wmode" value="*"></param>
		 */
		if (preg_match_all("@(<param\s.*name=(?:\"|')wmode(?:\"|').*>\s*</param>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$sText=str_replace($aMatch[0][$key],'',$sText);
			}
		}
		/**
		 * А теперь после <object> добавляем <param name="wmode" value="opaque"></param>
		 * Решение не фантан, но главное работает :)
		 */
		if (preg_match_all("@(<object\s.*>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$sText=str_replace($aMatch[0][$key],$aMatch[0][$key].'<param name="wmode" value="opaque"></param>',$sText);
			}
		}
		return $sText;
	}
	/**
	 * Подсветка исходного кода
	 *
	 * @param string $sText Исходный текст
	 * @return mixed
	 */
	public function CodeSourceParser($sText) {
		$sText=str_replace("<code>",'<pre class="prettyprint"><code>',$sText);
		$sText=str_replace("</code>",'</code></pre>',$sText);
		return $sText;
	}
	/**
	 * Производить резрезание текста по тегу cut.
	 * Возвращаем массив вида:
	 * <pre>
	 * array(
	 * 		$sTextShort - текст до тега <cut>
	 * 		$sTextNew   - весь текст за исключением удаленного тега
	 * 		$sTextCut   - именованное значение <cut>
	 * )
	 * </pre>
	 *
	 * @param  string $sText Исходный текст
	 * @return array
	 */
	public function Cut($sText) {
		$sTextShort = $sText;
		$sTextNew   = $sText;
		$sTextCut   = null;

		$sTextTemp=str_replace("\r\n",'[<rn>]',$sText);
		$sTextTemp=str_replace("\n",'[<n>]',$sTextTemp);

		if (preg_match("/^(.*)<cut(.*)>(.*)$/Ui",$sTextTemp,$aMatch)) {
			$aMatch[1]=str_replace('[<rn>]',"\r\n",$aMatch[1]);
			$aMatch[1]=str_replace('[<n>]',"\r\n",$aMatch[1]);
			$aMatch[3]=str_replace('[<rn>]',"\r\n",$aMatch[3]);
			$aMatch[3]=str_replace('[<n>]',"\r\n",$aMatch[3]);
			$sTextShort=$aMatch[1];
			$sTextNew=$aMatch[1].' <a name="cut"></a> '.$aMatch[3];
			if (preg_match('/^\s*name\s*=\s*"(.+)"\s*\/?$/Ui',$aMatch[2],$aMatchCut)) {
				$sTextCut=trim($aMatchCut[1]);
			}
		}

		return array($sTextShort,$sTextNew,$sTextCut ? htmlspecialchars($sTextCut) : null);
	}
	/**
	 * Обработка тега ls в тексте
	 * <pre>
	 * <ls user="admin" />
	 * </pre>
	 *
	 * @param string $sTag	Тег на ктором сработал колбэк
	 * @param array $aParams Список параметров тега
	 * @return string
	 */
	public function CallbackTagLs($sTag,$aParams) {
		$sText='';
		if (isset($aParams['user'])) {
			if ($oUser=$this->User_getUserByLogin($aParams['user'])) {
				$sText.="<a href=\"{$oUser->getUserWebPath()}\" class=\"ls-user\">{$oUser->getLogin()}</a> ";
			}
		}
		return $sText;
	}
}
?>
