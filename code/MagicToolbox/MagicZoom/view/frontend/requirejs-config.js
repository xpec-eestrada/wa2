
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'MagicToolbox_MagicZoom/js/swatch-renderer': true
            },
            /* NOTE: for Magento v2.0.x */
            'Magento_Swatches/js/SwatchRenderer': {
                'MagicToolbox_MagicZoom/js/swatch-renderer': true
            }
        }
    },
    map: {
        '*': {
            magicToolboxThumbSwitcher: 'MagicToolbox_MagicZoom/js/thumb-switcher',
            configurable:              'MagicToolbox_MagicZoom/js/configurable'
        }
    }
};
