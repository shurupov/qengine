
    $.fn.editableform.buttons =
        '<button type="submit" class="form-control btn btn-primary editable-submit">'+
            '<i class="glyphicon glyphicon-ok"></i>'+
        '</button>'+
        '<button type="button" class="form-control btn btn-default editable-cancel">'+
            '<i class="glyphicon glyphicon-remove"></i>'+
        '</button>';

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
            url: '/e/' + $(this).data('type') + '/edit',
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
            url: '/e/' + $(this).data('type') + '/edit',
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
            url: '/e/' + $(this).data('type') + '/field/remove',
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
            data('type',   $(this).data('type')).
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


        var id = $(this).data('pk');
        var path = $(this).data('path') + '.' + str_rand();
        var inputId = $(this).data('type') + '-' + $(this).data('pk') + '-' + path.split('.').join('-');

        var html = '<div class="pull-left editor-image-list-element" id="' + inputId + '-container">' +
            '<input type="hidden" id="' + inputId + '" data-type="' + $(this).data('type') + '" data-pk="' + id + '" data-path="' + path + '">' +
            '<edim id="' + inputId + '-button" class="edit-image" data-fancybox data-src="/filemanager/dialog.php?type=1&lang=ru&relative_url=1&field_id=' + inputId + '" data-type="iframe"><img id="' + inputId + '-preview" src="/thumbs/previewDefault.jpg"></edim>' +
            '<reel class="btn-remove-element" data-path="' + inputId + '" data-pk="" data-inModal="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></reel>' +
        '</div>';

        $(this).parent().find('.image-list').append(html);

        $.fancybox.open({
            'src': '/filemanager/dialog.php?type=1&lang=ru&relative_url=1&field_id=' + inputId,
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
            url: '/e/page/block/add',
            data: {
                id: $(this).data('pk'),
                type: $('#add-component-select').val()
            },
            success: function () {
                location.reload();
            }
        });
    });

    $('button[role=iconpicker]').change(function (e) {

        if (e.icon == $(this).data('icon')) {
            return;
        }

        $.ajax({
            type: "POST",
            url: '/e/' + $(this).data('type') + '/edit',
            data: {
                name: $(this).data('path'),
                value: e.icon,
                pk: $(this).data('pk')
            },
            success: function () {
            }
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

        var $input = $('#' + fieldId);

        var uri = '/source/' + $input.val();

        $.fancybox.close();

        $.ajax({
            type: "POST",
            url: '/e/' + $input.data('type') + '/edit',
            data: {
                pk : $input.data('pk'),
                name: $input.data('path'),
                value: uri
            },
            success: function () {
                $('#' + fieldId + '-image').attr('src', uri);
                $('#' + fieldId + '-preview').attr('src', uri.replace('/source/', '/thumbs/'));
                $('[image-id=' + $input.data('type') + '-' + $input.data('pk') + '-' + $input.data('path') + ']').attr('src', uri);
                $('[background-id=' + $input.data('type') + '-' + $input.data('pk') + '-' + $input.data('path') + ']').css('background-image', 'url(' + uri + ')');
            },
            dataType: 'json'
        });

    }
