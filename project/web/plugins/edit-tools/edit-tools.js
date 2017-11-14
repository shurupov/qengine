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
        $('.btn-add-object').click(function () {

            $.ajax({
                type: "POST",
                url: '/change',
                data: {
                    name: $(this).data('path') + '.' + str_rand() + '.title',
                    value: null,
                    pk: $(this).data('pk')
                },
                success: function () {
                    location.reload();
                }
            });

        });

        $('.btn-add-item').click(function () {

            $.ajax({
                type: "POST",
                url: '/change',
                data: {
                    name: $(this).data('path') + '.' + str_rand(),
                    value: null,
                    pk: $(this).data('pk')
                },
                success: function () {
                    location.reload();
                }
            });

        });

        $('#confirm-delete-button').click(function () {

            var that = this;

            $.ajax({
                type: "POST",
                url: '/e/remove',
                data: {
                    name: $(this).data('path'),
                    pk: $(this).data('pk')
                },
                success: function () {
                    if ($(that).data('reload') == 'true') {
                        location.reload();
                    } else {
                        $('#' + $(that).data('path').split('.').join('-') + '-container').remove();
                    }
                }
            });
        });

        $( document ).on('click', '.btn-remove-element', function () {

            $('#delete-item-modal').modal();
            $('#confirm-delete-button').
                data('path',   $(this).data('path')).
                data('pk',     $(this).data('pk')).
                data('reload', $(this).data('inmodal') ? 'false' : 'true' );

        });

        $( document ).on('click', '.remove-page-button', function () {

            $('#remove-page-confirm-modal').modal();
            $('#remove-page-confirm-button').
                attr('href', $(this).attr('href'));

            return false;

        });

        $('.btn-add-image-preview').click(function () {

            var id = $(this).data('path').split('.').join('-') + '-' + str_rand();

            var html = '<div class="pull-left editor-image-list-element" id="' + id + '-container">' +
                '<input type="hidden" id="' + id + '">' +
                '<edim id="' + id + '-button" class="edit-image" data-fancybox data-src="/filemanager/dialog.php?type=1&lang=ru&relative_url=1&field_id=' + id + '" data-type="iframe"><img id="' + id + '-preview" src="/thumbs/previewDefault.jpg"></edim>' +
                '<reel class="btn-remove-element" data-path="' + id + '" data-pk="" data-inModal="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></reel>' +
            '</div>';

            $(this).parent().find('.image-list').append(html);

            $.fancybox.open({
                'src': '/filemanager/dialog.php?type=1&lang=ru&relative_url=1&field_id=' + id,
                'type': 'iframe'
            });

        });


        if (componentsList != undefined) {
            $.each(componentsList, function (key, value) {
                $('#add-component-select').append('<option value="' + key + '">' + value + '</option>');
            });
        }

        $('#add-component-button').click(function () {
            $.ajax({
                type: "POST",
                url: '/e/add-block',
                data: {
                    slug: $(this).data('slug'),
                    type: $('#add-component-select').val()
                },
                success: function () {
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
    var position;
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
