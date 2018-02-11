
(function ($) {
    "use strict";

    var ECode = function (options) {
        this.init('code', options, ECode.defaults);
    };

    $.fn.editableutils.inherit(ECode, $.fn.editabletypes.abstractinput);

    $.extend(ECode.prototype, {
        render: function () {
            this.setClass();
            this.setAttr('placeholder');
            this.setAttr('rows');

            //ctrl + enter
            this.$input.keydown(function (e) {
                if (e.ctrlKey && e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        },

        //using `white-space: pre-wrap` solves \n  <--> BR conversion very elegant!
         value2html: function(value, element) {
             $(element).html('Код');
         },

         /*html2value: function(html) {
         if(!html) {
         return '';
         }

         var regex = new RegExp(String.fromCharCode(10), 'g');
         var lines = html.split(/<br\s*\/?>/i);
         for (var i = 0; i < lines.length; i++) {
         var text = $('<div>').html(lines[i]).text();

         // Remove newline characters (\n) to avoid them being converted by value2html() method
         // thus adding extra <br> tags
         text = text.replace(regex, '');

         lines[i] = text;
         }
         return lines.join("\n");
         },*/
        activate: function() {
            $.fn.editabletypes.text.prototype.activate.call(this);
        }
    });

    ECode.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
         @property tpl
         @default <textarea></textarea>
         **/
        tpl:'<textarea></textarea>',
        /**
         @property inputclass
         @default input-large
         **/
        inputclass: 'input-large',
        /**
         Placeholder attribute of input. Shown when input is empty.

         @property placeholder
         @type string
         @default null
         **/
        placeholder: null,
        /**
         Number of rows in textarea

         @property rows
         @type integer
         @default 7
         **/
        rows: 7
    });

    $.fn.editabletypes.code = ECode;

}(window.jQuery));