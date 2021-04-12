(function($){

    $(document).ready(() => {
        $('#register').addClass('active');

        register.register_user();
    });

    // Define classes where we'll store all functions
    let register = {

        register_user: () => {
            $(document).on('submit', '.register_form', function (e)
            {
                e.preventDefault();
                let form_data = $('.register_form').serialize();

                $.post('/register/register_user', form_data, function (d){
                    if(d.status == 'Success'){
                        Swal.fire({
                            icon: 'success',
                            title: d.status,
                            text: d.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top'
                        }).then(function(){
                            window.location.replace('login');
                        })
                    }
                    else
                    {
                        Swal.fire({
                            icon: 'error',
                            title: d.status,
                            text: d.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top'
                        })
                    }
                })
            })
        }

    };

})(jQuery);