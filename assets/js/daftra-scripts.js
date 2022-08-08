/*------------------------ 
Backend related javascript
------------------------*/
jQuery(function($) {

    // $('#check1').click(function() {
    //     alert("Checkbox state (method 1) = " + $('#check1').prop('checked'));
    //     alert("Checkbox state (method 2) = " + $('#check1').is(':checked'));
    // });

var frm = $('#daftra_sync_form');

var SyncDaftra = function () {

    $.ajax({
        type: frm.attr('method'),
        url: ajax_wpx.ajax_url,
        data: {
            action: 'daftra_sync_data', 
            sync_users: $('#check1').is(':checked'),
            sync_products: $('#check2').is(':checked'),
            sync_orders: $('#check3').is(':checked'),
        },
        beforeSend: function(){
            $('.sync__loader').show();
            $('.sync__msg').hide();
            $('.sync__msg_error').hide();
        },
        complete: function(){
            
        },
        success: function (response) {
            if( response.repeat ) {
                $('.sync__msg').html("<p> " + response.msg + " </p> ").show();
                SyncDaftra();
                return;
            }
            if( response.success ) {
                $('.sync__loader').hide();
                $('.sync__msg').html("<p> " + response.msg + " </p> ").show();
            }
            if( !response.success ) {
                $('.sync__loader').hide();
                $('.sync__msg_error').html("<p> " + response.msg + " </p> ").show();
            }
 
        },
        error: function(response) {  
            console.log(response);
        },
    });
}

    frm.submit(function (e) {

        e.preventDefault();
        SyncDaftra();

    });
});
