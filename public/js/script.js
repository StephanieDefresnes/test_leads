function loader() {
    $('#alert').removeClass().addClass('d-flex justify-content-center mt-4')
            .html('<div class="spinner-border text-secondary" role="status">'
                    +'<span class="visually-hidden">Loading...</span>'
                +'</div>')
}
function alertAction(success, msg) {
    return  '<div class="alert alert-'
                + ( success ? 'success' : 'warning')
            +' w-100" role="alert">'+ msg +'</div>'
}

jQuery(function($) {

    // Import contacts
    $('#import').click(function() {        
        loader()
        $.ajax({
            url: '/ajaxImport',
            method: 'GET',
            success: function(data) {
                // console.log(data)
                $('#alert').removeClass().addClass('d-flex justify-content-center mt-4')
                        .html(alertAction(data.success, data.msg))
            }
        })
    })

    // Campaign simulation
    $('#campagne').click(function() {      
        loader()
        $.ajax({
            url: '/ajaxCampaign',
            method: 'GET',
            success: function(data) {
                // console.log(data)                
                $('#alert').removeClass().addClass('d-flex justify-content-center mt-4')
                        .html(alertAction(data.success, data.msg))
            }
        })
    })

    // Send Leads
    $('#leads').click(function() {  
        $('#alert').empty()

        let loader = $('<div role="status" class="spinner-border spinner-border-sm text-secondary mt-2 ms-3"></div>');
        $(this).parent().append(loader)
        
        $.ajax({
            url: '/ajaxSendLeads',
            method: 'GET',
            success: function(data) {
                // console.log(data)
                $.each(data, function( index, value ) {
                    if(value.success) {
                        $('#alert').removeClass().addClass('d-flex flex-column mt-4')
                                .prepend(alertAction(value.success, value.msg))
                        return
                    }
                    $('#alert').removeClass().addClass('d-flex justify-content-center mt-4')
                            .html(alertAction(value.success, value.msg))
                })
                loader.remove()
            }
        })
    })

});