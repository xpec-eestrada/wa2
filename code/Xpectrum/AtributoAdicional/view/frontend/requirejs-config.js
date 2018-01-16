var config = {
	paths: {
		'ValidarRut': 'Xpectrum_AtributoAdicional/js/validar.rut',
		'ValidarNumeroContacto': 'Xpectrum_AtributoAdicional/js/validar.numerocontacto'
	},
	shim: {
		'ValidarRut': {
			deps: ['jquery']
		},
		'ValidarNumeroContacto': {
			deps: ['jquery']
		}
    }
};