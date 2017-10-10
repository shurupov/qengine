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
    });
});

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
