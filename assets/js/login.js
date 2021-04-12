(function($){

    $(document).ready(() => {
        $('#login').addClass('active');

        login.user_login();
    });

    // Define classes where we'll store all functions
    let login = {

        user_login: () => {
            $(document).on('submit', '.login_form', function (e)
            {
                e.preventDefault();
                let form_data = $('.login_form').serialize();

                $.post('/login/user_login', form_data, function (d){
                    if(d.status == 'Success'){
                        Swal.fire({
                            icon: 'success',
                            title: d.status,
                            text: d.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top'
                        }).then(function (){
                            window.location.replace('invoice');
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
        },

    };

})(jQuery);