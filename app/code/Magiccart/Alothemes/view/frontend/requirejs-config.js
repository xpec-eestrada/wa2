var config = {

	map: {
		'*': {
			'alothemes': 'Magiccart_Alothemes/js/alothemes',
		},
	},

	paths: {
		'easing'		: 'Magiccart_Alothemes/js/plugins/jquery.easing.min',
		'ddslick'		: 'Magiccart_Alothemes/js/plugins/jquery.ddslick',
		'fancybox'		: 'Magiccart_Alothemes/js/plugins/jquery.fancybox.pack',
		'socialstream'	: 'Magiccart_Alothemes/js/plugins/jquery.socialstream',
		'slick'			: 'Magiccart_Alothemes/js/plugins/slick.min',
		'alothemes'		: 'Magiccart_Alothemes/js/alothemes',
		'magiccart/zoom': 'Magiccart_Alothemes/js/plugins/jquery.zoom.min',
	},

	shim: {
		'easing': {
			deps: ['jquery']
		},
		'ddslick': {
			deps: ['jquery']
		},
		'fancybox': {
			deps: ['jquery']
		},
		'socialstream': {
			deps: ['jquery']
		},
		'slick': {
			deps: ['jquery']
		},
        'alothemes': {
            deps: ['jquery', 'easing', 'fancybox', 'ddslick', 'slick']
        },

	}

};
