(function($){

    $(document).ready(() => {

        $('#settings').addClass('active');
        $('[data-toggle="tooltip"]').tooltip();

        let curr = $('.selectpicker').attr('data-selected');
        $('.selectpicker').selectpicker('val', curr);

        settings.user_data();
        settings.upload_logo();
        settings.remove_logo();
        settings.progress_bar();
    });

    // Define classes where we'll store all functions
    let settings = {

        user_data: () => {
            $(document).on('submit', '.settings', function (e){
                e.preventDefault();

                let user_data = $('.settings').serialize();
                let currency = $('.selectpicker').val();
                let file_name = '';
                if ($('#upload_file').prop('files')[0])
                {
                    file_name = $('#upload_file').prop('files')[0].name;
                } else{
                    if  ($("#picture").attr('src') != ''){
                        file_name = $("#picture").attr('src');
                        let x = file_name.split('/');
                        file_name = x[3];
                    }
                }

                $.post('/settings/user_data', user_data + `&currency=${currency}&file_name=${file_name}`, function (d){
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
                })
            })
        },

        upload_logo: () => {
            $(document).on('change', '#upload_file', function (){

                let file_data = $(this).prop('files')[0];
                let form_data = new FormData();
                form_data.append('logo', file_data);

                $.ajax({
                    type: 'post',
                    url: '/invoice/upload_logo',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d){
                        if(d.status == 'Success'){
                            let file_info = d.data;
                            let path = '/assets/uploads/' + file_info;
                            $('#picture').attr('src', path).show().css('float', 'none');
                            $('.square').addClass('d-none');
                            $('.remove_logo_settings').removeClass('d-none');
                        }
                    }
                });
            })
        },

        remove_logo: () => {
            $(document).on('click', '.remove_logo_settings', function (){
                $('#upload_file').val('');
                $('#picture').attr('src', '').hide();
                $('.square').removeClass('d-none');
                $('.remove_logo_settings').addClass('d-none');
            });
        },

        progress_bar: () => {

            let count = 0;
            if($('.first_name').val() == ''){
                count++;
            }
            if($('.last_name').val() == ''){
                count++;
            }
            if($('.address').val() == ''){
                count++;
            }
            if($('.company').val() == ''){
                count++;
            }
            if($('.notes').val() == ''){
                count++;
            }
            if($('.terms').val() == ''){
                count++;
            }
            if($('#picture').attr('src') == '/assets/uploads/'){
                count++;
            }

            let value = 0;
            if(count == 0)
            {
                value = 100;
            }
            else{
                let temp = count / 7 * 100;
                value = Math.round(100 - temp);
            }

            if(value <= 33){
                $('.progress-bar').css('width', value+'%').html(value+'%').addClass('bg-danger');
            } else if(value > 33 && value <= 66) {
                $('.progress-bar').css('width', value+'%').html(value+'%').addClass('bg-warning');
            } else{
                $('.progress-bar').css('width', value+'%').html(value+'%').addClass('bg-success');
            }
        }
    };

})(jQuery);