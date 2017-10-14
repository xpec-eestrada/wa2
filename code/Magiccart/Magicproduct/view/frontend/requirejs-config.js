var config = {

	map: {
		'*': {
			'slick'			: "Magiccart_Magicproduct/js/plugins/slick.min",
			'magicproduct'	: "Magiccart_Magicproduct/js/magicproduct",
		},
	},

	paths: {
		'slick'			: 'Magiccart_Magicproduct/js/plugins/slick.min',
		'magicproduct'	: 'Magiccart_Magicproduct/js/magicproduct',
	},

	shim: {
		'magicproduct': {
			deps: ['jquery', 'slick']
		},

	}
};
