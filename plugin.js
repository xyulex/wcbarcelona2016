/**
 * Plugin Name: WordCamp Barcelona 2016
 * Description: Create annotations on your posts or pages
 * Version:     1.0
 * Author:      xyulex
 * Author URI:  https://profiles.wordpress.org/xyulex/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

(function($) {
    tinymce.PluginManager.add('wcbcn2016_annotate', function(editor, url) {

        var state;


        // Create annotation
        editor.addButton('wcbcn2016_annotate', {
            title: wcbcn2016.tooltips.annotation_create,
            image: url + '/img/annotation.png',
            onclick: function() {
                annotation = '';
                color = '#F0E465';
                node = editor.selection.getNode();
                nodeName = node.nodeName;

                if (nodeName == 'SPAN') {
                    nodeDataAnnotation = $(node).attr("data-annotation");
                    nodeDataStyle = $(node).css("background-color");

                    // Retrieve annotation and color
                    if (nodeDataAnnotation) {
                        annotation = nodeDataAnnotation;
                        var ctx = document.createElement('canvas').getContext('2d');
                        ctx.strokeStyle = nodeDataStyle;
                        var color = ctx.strokeStyle;
                    }
                }

                var selectedText = editor.selection.getContent();
                var selectedTextLength = selectedText.length;

                if (selectedTextLength > 0 || node.className == 'annotation') {
                    if (node.className == 'annotation') {
                        selectedText = node.innerHTML;
                    }
                    editor.windowManager.open({
                        title: wcbcn2016.tooltips.annotation_settings,
                        body: [{
                            type: 'textbox',
                            name: 'annotation',
                            label: wcbcn2016.settings.setting_annotation,
                            value: annotation
                        }, {
                            type: 'colorpicker',
                            name: 'annotationbg',
                            label: wcbcn2016.settings.setting_background,
                            value: color
                        }],

                        onsubmit: function(e) {
                            if (e.data.annotation) {
                                var dataAnnotation = e.data.annotation;

                                if ($(node).attr("data-annotation")) {
                                    editor.dom.remove(node);
                                }
                               editor.selection.setContent('<span class="annotation" data-author="' + wcbcn2016.author + '" data-annotation="' + dataAnnotation.replace(/"/g,'&quot;') + '" style="background-color:' + e.data.annotationbg + '">' + selectedText + '</span>');

                            } else {
                                editor.windowManager.alert(wcbcn2016.errors.missing_fields);
                                return false;
                            }
                        }
                    });
                } else {
                    editor.windowManager.alert(wcbcn2016.errors.missing_annotation, false);
                }
            }
        });

    });
})(jQuery);