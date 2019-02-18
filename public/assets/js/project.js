$(function(){
    /** Clear app alerts block. */
    $('.btn').not('.btn-danger').click(function(e){
            clearAlerts();
    });

    /**
     *Delete user or group
     */
    $('.btn-danger.delete-user-group').on('click',function(e){
        e.preventDefault();
        var tr = $(this).closest('tr');

        if(confirm('Are you sure?')){
            $.ajax({
                url: $(this).data('url'),
                method: 'DELETE'
            }).done(function( data ) {
                if (data) { //processing results
                    clearAlerts();
                    if(!!data.error){
                        $('#app-json-alert').removeClass('d-none').addClass('alert-danger').html(data.error);
                    }
                    else if(!!data.success){ // adding tabs and tab content
                        $('#app-json-alert').removeClass('d-none').addClass('alert-success').html(data.success);
                        tr.fadeOut();
                    }
                }
                return;
            });
        }
    });

    /**
     * function clear alerts
     */
    function clearAlerts(){
        $('.alert-success').addClass('d-none').empty();
        $('#app-json-alert').removeClass('alert-danger alert-succes alert-warning  d-none').addClass('alert d-none').empty(); //clears alerts
    }

});