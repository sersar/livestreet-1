<?php

return array(
	'default' => array(
		// Разрешённые теги
		'cfgAllowTags' => array(
			// вызов метода с параметрами
			array(
				array('ls','cut','a','color', 'img', 'i', 'b', 'u', 's', 'video', 'em',  'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'sub', 'acronym', 'h4', 'h5', 'h6', 'br', 'hr', 'pre', 'code', 'object', 'param', 'embed', 'blockquote', 'iframe','table','th','tr','td','spoiler','font','vid','script'),
			
			
			),
			
		),
		
		// Коротие теги типа
		'cfgSetTagShort' => array(
			array(
				array('br','img', 'hr', 'cut','ls')
			),
		),
		// Преформатированные теги
		'cfgSetTagPreformatted' => array(
			array(
				array('pre','code','video','vid','script')
			),
		),
		
		// Разрешённые параметры тегов
		'cfgAllowTagParams' => array(
			// вызов метода
			// Colors
			array(
                                'font',
                                array('color'=> array('orange','red','blue','purple','green','gray','black',)
													  )
			),
			array(
				'img',
				array('src', 'alt' => '#text', 'title', 'align' => array('right', 'left', 'center', 'middle'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int', 'class'=> array('image-center'))
			),
			// следующий вызов метода
			array(
				'a',
				array('title', 'href', 'rel' => '#text', 'name' => '#text', 'target' => array('_blank'))
			),
			// и т.д.
			array(
				'cut',
				array('name')
			),
			array(
				'object',
				array('width' => '#int', 'height' => '#int', 'data' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','coub.com','ok.ru','vk.com','dailymotion.com')), 'type' => '#text')
			),
			array(
				'param',
				array('name' => '#text', 'value' => '#text')
			),
			array(
				'embed',
				array('src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','coub.com','ok.ru','vk.com','dailymotion.com')), 'type' => '#text','allowscriptaccess' => '#text', 'allowfullscreen' => '#text','width' => '#int', 'height' => '#int', 'flashvars'=> '#text', 'wmode'=> '#text')
			),
			array(
				'acronym',
				array('title')
			),
			array(
				'abbr',
				array('title')
			),
			array(
				'script',
				array('type'=>'#text')
			),
			array(
				'iframe',
				array('width' => '#int', 'height' => '#int', 'src' => array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','coub.com','ok.ru','vk.com','dailymotion.com')))
			),
			array(
				'ls',
				array('user'=>'#text')
			),
			array(
				'td',
				array('colspan'=>'#int','rowspan'=>'#int','align'=>array('right', 'left', 'center', 'justify'),'height'=>'#int','width'=>'#int')
			),
			array(
				'table',
				array('border'=>'#int','cellpadding'=>'#int','cellspacing'=>'#int','align'=>array('right', 'left', 'center'),'height'=>'#int','width'=>'#int')
			),
			array(
				'spoiler',
				array('title'=>'#text')
			),
		),
		// допустимые комбинации значений у параметров
		'cfgSetTagParamCombination' => array(
			array(
				'param',
				'name',
				array(
					'allowScriptAccess' => array(
						'value'=>array('sameDomain'),
					),
					'movie' => array(
						'value'=>array('#domain'=>array('youtube.com','rutube.ru','vimeo.com','coub.com','ok.ru','vk.com','dailymotion.com')),
					),
					'align' => array(
						'value'=>array('bottom','middle','top','left','right'),
					),
					'base' => array(
						'value'=>true,
					),
					'bgcolor' => array(
						'value'=>true,
					),
					'border' => array(
						'value'=>true,
					),
					'devicefont' => array(
						'value'=>true,
					),
					'flashVars' => array(
						'value'=>true,
					),
					'hspace' => array(
						'value'=>true,
					),
					'quality' => array(
						'value'=>array('low','medium','high','autolow','autohigh','best'),
					),
					'salign' => array(
						'value'=>array('L','T','R','B','TL','TR','BL','BR'),
					),
					'scale' => array(
						'value'=>array('scale','showall','noborder','exactfit'),
					),
					'tabindex' => array(
						'value'=>true,
					),
					'title' => array(
						'value'=>true,
					),
					'type' => array(
						'value'=>true,
					),
					'vspace' => array(
						'value'=>true,
					),
					'wmode' => array(
						'value'=>array('window','opaque','transparent'),
					),
				),
				true, // Удалять тег, если нет основного значения параметра в списке комбинаций
			),
		),
		// Параметры тегов являющиеся обязательными
		'cfgSetTagParamsRequired' => array(
			array(
				'img',
				'src'
			),			
		),
		// Теги которые необходимо вырезать из текста вместе с контентом
		'cfgSetTagCutWithContent' => array(
			array(
				array(  'style')
			),
		),
		// Вложенные теги
		'cfgSetTagChilds' => array(
			array(
				'ul',
				array('li'),
				false,
				true
			),
			array(
				'ol',
				array('li'),
				false,
				true
			),
			array(
				'object',
				'param',
				false,
				true
			),
			array(
				'object',
				'embed',
				false,
				false
			),
			array(
				'table',
				array('tr'),
				false,
				true
			),
			array(
				'tr',
				array('td','th'),
				false,
				true
			),
		),
		// Если нужно оставлять пустые не короткие теги
		'cfgSetTagIsEmpty' => array(
			array(
				array('param','embed','a','iframe')
			),
		),
		// Не нужна авто-расстановка <br>
		'cfgSetTagNoAutoBr' => array(
			array(
				array('ul','ol','object','table','tr')
			)
		),
		// Теги с обязательными параметрами
		'cfgSetTagParamDefault' => array(
			array(
				'embed',
				'wmode',
				'opaque',
				true
			),
		),
		// Отключение авто-добавления <br>
		'cfgSetAutoBrMode' => array(
			array(
				true
			)
		),
		// Автозамена
		'cfgSetAutoReplace' => array(
			array(
				array('+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)'),
				array('±', '©', '©', '®', '©', '©', '®')
			)
		),
		// Список допустимых протоколов для ссылок
		'cfgSetLinkProtocolAllow' => array(
			array(
				array('http','https','ftp')
			)
		),
		'cfgSetTagNoTypography' => array(			
			array(
				array('code','video','object','vid','script',)
			),
		),
		// Теги, после которых необходимо пропускать одну пробельную строку
		'cfgSetTagBlockType' => array(
			array(
				array('h4','h5','h6','ol','ul','blockquote','pre','table','iframe')
			)
		),
		'cfgSetTagCallbackFull' => array(
			array(
				'ls',
				array('_this_','CallbackTagLs'),
			),
		),
	),
	
	// настройки для обработки текста в результатах поиска
	'search' => array(
		// Разрешённые теги
		'cfgAllowTags' => array(
			// вызов метода с параметрами
			array(
				array('span'),
			),			
		),
		// Разрешённые параметры тегов
		'cfgAllowTagParams' => array(
			array(
				'span',
				array('class' => '#text')
			),			
		),
	),
);
