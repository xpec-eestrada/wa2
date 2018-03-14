tinyMCE.addI18n({en:{
    powrsocial:
        {
            insert_powr : "Insert POWr"
        }
}});
(function() {
    if(window.powrPlaginFlag == undefined){
        window.powrPlaginFlag = true;
        tinymce.create('tinymce.plugins.PowrsocialPlugin', {
            /**
             * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
             * @param {string} url Absolute URL to where the plugin is located.
             */
            init : function(ed, url) {
                var t = this;
                t.editor = ed;
                customURL = url;

                // ed.contentCSS = [ed.settings.magentoPluginsOptions._object.powr.css];
            },

            createControl: function(n, cm) {
                var t = this;
                switch (n) {
                    case 'powrsocial':

                        var c = cm.createSplitButton('powrsplitbutton', {
                            title : 'Powr menu button',
                            image : customURL + '/img/icon.png',
                            onclick : function(){

                            }
                        });

                        c.onRenderMenu.add(function(c, m) {

                            m.add({title : 'Popular', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'Form Builder', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Ecommerce', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Chat', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Comments', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Media Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Social Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Multi Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Countdown Timer', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Social Media Icons', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Popup', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Price Table', onclick : function(){t._insertPowr(this.title)}});

                            m.add({title : 'Forms & Surveys', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'Contact Form', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Form Builder', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Mailing List', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Order Form', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Poll', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Survey', onclick : function(){t._insertPowr(this.title)}});

                            m.add({title : 'Galleries & Sliders', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'Banner Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Event Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Event Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Flickr Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Image Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Media Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Microblog', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Multi Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Photo Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Video Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Video Slider', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Vimeo Gallery', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Youtube Gallery', onclick : function(){t._insertPowr(this.title)}});

                            m.add({title : 'Social', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'Chat', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Comments', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Facebook Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Instagram Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Pinterest Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Review', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'RSS Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Social Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Social Media Icons', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Tumblr Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Twitter Feed', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Vine Feed', onclick : function(){t._insertPowr(this.title)}});

                            m.add({title : 'eCommerce', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'Ecommerce', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Digital Download', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Paypal Button', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Plan Comparison', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Price Table', onclick : function(){t._insertPowr(this.title)}});

                            m.add({title : 'Miscellaneous', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
                            m.add({title : 'About Us', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Button', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Booking', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Countdown Timer', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Count Up Timer', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'FAQ', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Graph', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Hit Counter', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Holiday Countdown', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Job Board', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Map', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Menu', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Music Player', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Notification Bar', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Popup', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Resume', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Tabs', onclick : function(){t._insertPowr(this.title)}});
                            m.add({title : 'Weather', onclick : function(){t._insertPowr(this.title)}});

                            if(window.powrToolBro !== undefined){window.powrToolBro(m, t);}
                            if(window.formBuilderBro !== undefined){window.formBuilderBro(m, t);}
                        });

                        // Return the new menubutton instance
                        return c;
                }
                return null;
            },

            getInfo : function() {
                return {
                    longname : 'Powr Plugin for TinyMCE 3.x',
                    author : 'Brosolutions',
                    authorurl : 'http://www.brosolutions.net/',
                    infourl : 'http://www.brosolutions.net/',
                    version : "0.1.0"
                };
            },

            uniqueLabel:function (){
                return 'xxxxxxxx_'.replace(/[x]/g, function(c) {
                    var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
                    return v.toString(16);
                }) + new Date().getTime();
            },

            _insertPowr : function (nameElement) {
                var ed = this.editor;
                var app_name = nameElement.replace(/^\s+|\s+$/g, '');
                var app_shortcode = app_name.toLowerCase().replace(/ /g, '-');
                var uniqueLabel = this.uniqueLabel();
                ed.execCommand('mceInsertContent', false, '[powr-' + app_shortcode + ' id='+uniqueLabel+']');
            }

        });


        // Register plugin
        tinymce.PluginManager.add('powrsocial', tinymce.plugins.PowrsocialPlugin);
    }
    else {
        /*
        window.socialFeedBro = function socialFeedBro(m, t){
            m.add({title : 'Social', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
            m.add({title : 'Chat', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Comments', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Facebook Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Instagram Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Pinterest Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Review', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'RSS Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Social Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Social Media Icons', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Tumblr Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Twitter Feed', onclick : function(){t._insertPowr(this.title)}});
            m.add({title : 'Vine Feed', onclick : function(){t._insertPowr(this.title)}});
        }
        */
    }

})();
