$(document).ready(function () {
    $.fn.editableform.buttons =
        '<button type="submit" class="form-control btn btn-primary editable-submit">'+
            '<i class="glyphicon glyphicon-ok"></i>'+
        '</button>'+
        '<button type="button" class="form-control btn btn-default editable-cancel">'+
            '<i class="glyphicon glyphicon-remove"></i>'+
        '</button>';

    $(document).ready(function() {
        $('e').editable({
            'emptytext' : 'Пусто',
            'inputclass': ''
        });
        $('es').editable({
            'emptytext' : 'Пусто',
            'mode' : 'inline',
            'inputclass': ''
        });
        $('.btn-apply-now').click(function () {
            location.reload();
        });
        $('.btn-add-element').click(function () {
            console.log($(this).data('path'));
            console.log(str_rand());

            $.ajax({
                type: "POST",
                url: '/change',
                data: {
                    name: $(this).data('path') + '.' + str_rand() + '.title',
                    value: null,
                    pk: $(this).data('pk')
                },
                success: function (e) {
                    location.reload();
                }//,
                // dataType: dataType
            });

        });
        $('reel').click(function () {
            $.ajax({
                type: "POST",
                url: '/e/remove',
                data: {
                    name: $(this).data('path'),
                    pk: $(this).data('pk')
                },
                success: function (e) {
                    location.reload();
                }
            });
        });
    });
});

function str_rand() {
    var result       = '';
    var words        = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    var max_position = words.length - 1;
    for( i = 0; i < 5; ++i ) {
        position = Math.floor ( Math.random() * max_position );
        result = result + words.substring(position, position + 1);
    }
    return result;
}

function responsive_filemanager_callback(fieldId){

    var uri = '/source/' + $('#' + fieldId).val();

    $.fancybox.close();

    $.ajax({
        type: "POST",
        url: '/change',
        data: {
            pk : location.pathname.substr(1),
            name: fieldId.split('-').join('.'),
            value: uri
        },
        success: function () {
            $('#' + fieldId + '-image').attr('src', uri);
            $('#' + fieldId + '-preview').attr('src', uri.replace('/source/', '/thumbs/'));
        },
        dataType: 'json'
    });

}
