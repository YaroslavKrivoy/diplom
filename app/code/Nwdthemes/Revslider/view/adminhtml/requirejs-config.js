var config = {
    paths: {
        admin:                  'Nwdthemes_Revslider/admin/assets/js/admin.min',
        contextMenu:            'Nwdthemes_Revslider/admin/assets/js/context_menu.min',
        cssEditor:              'Nwdthemes_Revslider/admin/assets/js/css_editor.min',
        editLayers:             'Nwdthemes_Revslider/admin/assets/js/edit_layers.min',
        editLayersTimeline:     'Nwdthemes_Revslider/admin/assets/js/edit_layers_timeline.min',
        revAddonAdmin:          'Nwdthemes_Revslider/admin/assets/js/rev_addon-admin',
        revAdmin:               'Nwdthemes_Revslider/admin/assets/js/rev_admin',
        settings:               'Nwdthemes_Revslider/admin/assets/js/settings.min',
        tipsy:                  'Nwdthemes_Revslider/admin/assets/js/jquery.tipsy',
        rsCodeMirror:           'Nwdthemes_Revslider/admin/assets/js/codemirror/rs_codemirror',
        codemirror:             'Nwdthemes_Revslider/admin/assets/js/codemirror/codemirror',
        cmMatchHighlighter:     'Nwdthemes_Revslider/admin/assets/js/codemirror/util/match-highlighter',
        cmCss:                  'Nwdthemes_Revslider/admin/assets/js/codemirror/css',
        cmXml:                  'Nwdthemes_Revslider/admin/assets/js/codemirror/xml',
        cmSearchCursor:         'Nwdthemes_Revslider/admin/assets/js/codemirror/util/searchcursor',
        perfectScrollbar:       'Nwdthemes_Revslider/framework/js/perfectScrollbar.min',
        colorPicker:            'Nwdthemes_Revslider/framework/js/color-picker.min',
        galleryBrowser:         'Nwdthemes_Revslider/framework/js/browser',
        iris:                   'Nwdthemes_Revslider/framework/js/iris.min',
        loading:                'Nwdthemes_Revslider/framework/js/loading',
        wpUtil:                 'Nwdthemes_Revslider/framework/js/wp-util.min',
        touchSwipe:             'Nwdthemes_Revslider/public/assets/js/tools/TouchSwipe',
        themepunchGS:           'Nwdthemes_Revslider/public/assets/js/tools/themepunch_gs',
        TweenLite:              'Nwdthemes_Revslider/public/assets/js/tools/TweenLite',
        TimelineLite:           'Nwdthemes_Revslider/public/assets/js/tools/TimelineLite',
        EasePack:               'Nwdthemes_Revslider/public/assets/js/tools/easing/EasePack',
        CSSPlugin:              'Nwdthemes_Revslider/public/assets/js/tools/CSSPlugin',
        SplitText:              'Nwdthemes_Revslider/public/assets/js/tools/SplitText',
        waitForImages:          'Nwdthemes_Revslider/public/assets/js/tools/waitForImages',
        themepunchTools:        'Nwdthemes_Revslider/public/assets/js/jquery.themepunch.tools',
        themepunchRevolution:   'Nwdthemes_Revslider/public/assets/js/jquery.themepunch.revolution.min',
        tpColorPicker:          'Nwdthemes_Revslider/public/assets/js/tp-color-picker.min',
        'jquery/file-uploader': 'jquery/fileUploader/jquery.fileupload-fp',
        prototype:              'legacy-build.min'
    },
    shim: {
        admin: {
            deps: ['jquery', 'galleryBrowser', 'rev_lang', 'loading', 'themepunchTools', 'settings'],
            exports: 'UniteAdminRev'
        },
        contextMenu: {
            deps: ['jquery', 'themepunchTools'],
            exports: 'tpLayerContextMenu'
        },
        cssEditor: {
            deps: ['jquery', 'rsCodeMirror', 'admin'],
            exports: 'UniteCssEditorRev'
        },
        editLayers: {
            deps: ['jquery', 'tpColorPicker', 'themepunchTools', 'loading', 'wpUtil', 'perfectScrollbar', 'contextMenu', 'admin', 'editLayersTimeline', 'settings', 'cssEditor'],
            exports: 'UniteLayersRev'
        },
        editLayersTimeline: {
            deps: ['jquery', 'themepunchTools', 'settings', 'admin'],
            exports: 'tpLayerTimelinesRev'
        },
        galleryBrowser: {
            deps: ['Magento_Variable/variables']
        },
        revAddonAdmin: {
            deps: ['jquery', 'admin', 'themepunchTools', 'loading', 'rev_slider_addon']
        },
        revAdmin: {
            deps: ['jquery', 'settings', 'admin', 'rsCodeMirror', 'rev_lang', 'loading', 'editLayers', 'editLayersTimeline', 'perfectScrollbar'],
            exports: 'RevSliderAdmin'
        },
        settings: {
            deps: ['jquery', 'tipsy', 'themepunchTools'],
            exports: 'RevSliderSettings'
        },
        tipsy: {
            deps: ['jquery']
        },
        codemirror: {
            exports: 'CodeMirror'
        },
        cmMatchHighlighter: {
            deps: ['codemirror'],
            exports: 'CodeMirror'
        },
        cmSearchCursor: {
            deps: ['codemirror'],
            exports: 'CodeMirror'
        },
        cmCss: {
            deps: ['codemirror'],
            exports: 'CodeMirror'
        },
        cmXml: {
            deps: ['codemirror'],
            exports: 'CodeMirror'
        },
        rsCodeMirror: {
            deps: ['codemirror'],
            exports: 'CodeMirror'
        },
        perfectScrollbar: {
            deps: ['jquery']
        },
        colorPicker: {
            deps: ['jquery/ui', 'iris']
        },
        iris: {
            deps: ['jquery/ui']
        },
        loading: {
            deps: ['jquery', 'themepunchTools'],
            exports: 'showWaitAMinute'
        },
        wpUtil: {
            deps: ['jquery', 'underscore']
        },
        themepunchGS: {
            exports: 'punchgsSandbox'
        },
        TweenLite: {
            deps: ['themepunchGS']
        },
        TimelineLite: {
            deps: ['themepunchGS']
        },
        EasePack: {
            deps: ['themepunchGS']
        },
        CSSPlugin: {
            deps: ['themepunchGS']
        },
        SplitText: {
            deps: ['themepunchGS']
        },
        themepunchTools: {
            deps: ['TweenLite', 'TimelineLite', 'EasePack', 'CSSPlugin', 'SplitText', 'waitForImages', 'touchSwipe', 'perfectScrollbar'],
            exports: 'punchgs'
        },
        themepunchRevolution: {
            deps: ['jquery', 'themepunchTools'],
            exports: 'jQuery'
        },
        tpColorPicker: {
            deps: ['colorPicker']
        }
    }
};