/**
 Address editable input.
 Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}

 @class address
 @extends abstractinput
 @final
 @example
 <a href="#" id="address" data-type="address" data-pk="1">awesome</a>
 <script>
 $(function(){
    $('#address').editable({
        url: '/post',
        title: 'Enter city, street and building #',
        value: {
            city: "Moscow",
            street: "Lenina",
            building: "15"
        }
    });
});
 </script>
 **/
(function ($) {
    "use strict";

    var ELink = function (options) {
        this.init('link', options, ELink.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(ELink, $.fn.editabletypes.abstractinput);

    $.extend(ELink.prototype, {
        /**
         Renders input from tpl

         @method render()
         **/
        render: function() {
            this.$input = this.$tpl.find('input');
        },

        /**
         Default method to show value in element. Can be overwritten by display option.

         @method value2html(value, element)
         **/
        value2html: function(value, element) {
            var html;
            if (!value || (!value.url && !value.text)) {
                html = this.options.emptytext;
            } else if (!value.url) {
                html = 'URL пуст';
            } else if (!value.text) {
                html = 'Текст пуст';
            } else {
                html = value.text;
            }
            $(element).html(html);
        },

        /**
         Gets value from element's html

         @method html2value(html)
         **/
        html2value: function(html) {
            /*
             you may write parsing method to get value by element's html
             e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
             but for complex structures it's not recommended.
             Better set value directly via javascript, e.g.
             editable({
             value: {
             city: "Moscow",
             street: "Lenina",
             building: "15"
             }
             });
             */
            return null;
        },

        /**
         Converts value to string.
         It is used in internal comparing (not for sending to server).

         @method value2str(value)
         **/
        value2str: function(value) {
            var str = '';
            if(value) {
                for(var k in value) {
                    str = str + k + ':' + value[k] + ';';
                }
            }
            return str;
        },

        /*
         Converts string to value. Used for reading value from 'data-value' attribute.

         @method str2value(str)
         */
        str2value: function(str) {
            /*
             this is mainly for parsing value defined in data-value attribute.
             If you will always set value by javascript, no need to overwrite it
             */
            return str;
        },

        /**
         Sets value of input.

         @method value2input(value)
         @param {mixed} value
         **/
        value2input: function(value) {

            console.log(value);

            if(!value) {
                return;
            }

            this.$input.filter('[name="url"]').val(value.url);
            this.$input.filter('[name="text"]').val(value.text);
        },

        /**
         Returns value of input.

         @method input2value()
         **/
        input2value: function() {
            return {
                url: this.$input.filter('[name="url"]').val(),
                text: this.$input.filter('[name="text"]').val()
            };
        },

        /**
         Activates input: sets focus on the first field.

         @method activate()
         **/
        activate: function() {
            this.$input.filter('[name="url"]').focus();
        },

        /**
         Attaches handler to submit form in case of 'showbuttons=false' mode

         @method autosubmit()
         **/
        autosubmit: function() {
            this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        }
    });

    ELink.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '' +
            '<div class="editable-input editable-link">' +
                '<div><input type="text" name="url" class="form-control input-sm" placeholder="http://www.yandex.ru"></div>' +
                '<div><input type="text" name="text" class="form-control input-sm" placeholder="Заголовок ссылки"></div>' +
            '</div>',

        inputclass: ''
    });

    $.fn.editabletypes.link = ELink;

}(window.jQuery));