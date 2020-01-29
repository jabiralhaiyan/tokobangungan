function cActions() {
    if (Settings.rtl == 1) {
        $(".modal-content .select2").select2({minimumResultsForSearch:6, dir: "rtl"});
    } else {
        $(".modal-content .select2").select2({minimumResultsForSearch:6});
    }
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('.redactor').redactor({
        formatting: ['p', 'blockquote', 'h3', 'h4', 'pre'],
        minHeight: 150,
        maxHeight: 500,
        linebreaks: true,
        tabAsSpaces: 4,
        dragImageUpload: false,
        dragFileUpload: false,
         //plugins: ['newbuttons']
        });
    $(":file").filestyle();
    $('.validation').formValidation({ framework: 'bootstrap', excluded: ':disabled' });
}

$(function () {

    cActions();
    if (Settings.rtl == 1) {
        $(".select2").select2({minimumResultsForSearch:6, dir: "rtl"});
    } else {
        $(".select2").select2({minimumResultsForSearch:6});
    }

    $(document).on('click', '[data-toggle="ajax"]', function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $.get(href, function( data ) {
            $("#posModal").html(data);
            $("#posModal").modal({backdrop:'static'});
            cActions();
            return false;
        });
    });

    $(document).on('click', '[data-toggle="ajax2"]', function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $.get(href, function( data ) { return data; });
    });

    $('.clock').click( function(e){
        e.preventDefault();
        return false;
    });
    function Now() { return new Date().getTime(); }
    var stamp = Math.floor(Now() / 1000);
    var time = date(dateformat+' '+timeformat, stamp);
    $('.clock').text(time);

    window.setInterval(function(){
        var stamp = Math.floor(Now() / 1000);
        var time = date(dateformat+' '+timeformat, stamp);
        $('.clock').text(time);
    }, 10000);

    // $('#myModal, #posModal').on('shown.bs.modal', function (e) {
    //     cActions();
    // });

    $( ".modal" ).each(function(index) {
        $(this).on('show.bs.modal', function (e) {
            var open = $(this).attr('data-easein');
            $('.modal-dialog').velocity('transition.' + open);
        });
    });

    $(document).on('click', '.ajax-pagination a', function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $.get(href, function( data ) {
            $("#posModal").html(data);
            $("#posModal").modal({backdrop:'static'});
            return false;
        });
    });

    $(document).on('change', '#group', function(event) {
        if ($(this).val() == 1) {
            $('#store_id').val('');
            $('#store_id').select2('val', '');
            $('.store-con').hide();
        } else {
            $('.store-con').show();
        }
    });
});
