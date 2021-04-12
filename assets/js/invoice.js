(function($){

    $(document).ready(() => {

        //for active tab
        $('#invoice').addClass('active');

        //for templates modal
        $('.carousel').carousel({
            interval: 0,
        })

        $('#datetimepicker_date').datetimepicker({
            timepicker:false,
            scrollMonth : false,
            format:'F j, Y'
        });

        $('#datetimepicker_date_due').datetimepicker({
            timepicker:false,
            scrollMonth : false,
            format:'F j, Y'
        });

        $('[data-toggle="tooltip"]').tooltip();

        //for font
        let font_selected = $('.font-select').data('selected');
        if(font_selected != '')
        {
            $('.font-select').val(font_selected);
        }

        let curr = $('.selectpicker').attr('data-selected');
        let invoice_curr = $('.selectpicker').attr('data-invoice_currency');

        if($('.selectpicker').attr('data-invoice_id') != '0')
        {
            $('.selectpicker').selectpicker('val', invoice_curr);
        }
        else
        {
            if(curr != '')
            {
                $('.selectpicker').selectpicker('val', curr);
            }
        }

        let font = $('.font-select').data('selected');
        if(font != ''){
            $('.font-select').find('option').val()
        }

        let currency = $('.selectpicker').val();

        $('#amount').html(`0.00 ${currency}`);
        $('#subtotal').html(`0.00 ${currency}`);
        $('#total').html(`0.00 ${currency}`);
        $('#balance_due').html(`0.00 ${currency}`);

        invoice.write_total_values();

        invoice.download_invoice();
        invoice.count();
        invoice.line_item();
        invoice.show_tax();
        invoice.show_discount();
        invoice.show_shipping();
        invoice.upload_logo();
        invoice.remove_logo();
        invoice.modal();
        invoice.templates_modal();
        invoice.send_invoice();
        invoice.remove_row();
        invoice.save_invoice();
        invoice.edit_invoice();
        invoice.change_color();
    });

    // Define classes where we'll store all functions
    let invoice = {

        invoice_data: () => {
            let items = {};
            let row = 0;

            $('.items').each(function (){
                let single_item = $(this).find('.item').val();
                let single_quantity = $(this).find('.quantity').val();
                let single_rate = $(this).find('.rate').val();

                items[row] = {
                    'single_item': single_item,
                    'single_quantity': single_quantity,
                    'single_rate': single_rate,
                };
                row++;
            });

            items = JSON.stringify(items);

            let form_data = $('.invoice_form').serialize();
            let subtotal = $('#subtotal').text();
            let total = $('#total').text();
            let balance_due = $('#balance_due').text();
            let currency = $('.selectpicker').val();
            let file_name = '';
            if ($('#upload_file').prop('files')[0])
            {
                file_name = $('#upload_file').prop('files')[0].name;
            } else {
                if  ($("#picture").attr('src') != ''){
                    file_name = $("#picture").attr('src');
                    let x = file_name.split('/');
                    file_name = x[3];
                }
            }
            let template = $('input[name="template"]:checked').val();
            let head_color = $('#head_color').val();
            let body_color = $('#body_color').val();
            let background_color = $('#background_color').val();
            let font = $('.font-select').val();
            let font_color = $('#font_color').val();
            let logo_position = $('input[name="logo_position"]:checked').val();

            let invoice_data = form_data + `&subtotal=${subtotal}&total=${total}&balance_due=${balance_due}&file_name=${file_name}&currency=${currency}&items=${items}&template=${template}&head_color=${head_color}&body_color=${body_color}&background_color=${background_color}&font=${font}&font_color=${font_color}&logo_position=${logo_position}`;

            return invoice_data;
        },

        send_invoice: () => {
            $(document).on('click', '.send_invoice', function (){

                let form_data = $('.invoice_send').serialize();
                let invoice_data = invoice.invoice_data();

                $.post('/invoice/send_invoice', invoice_data +  `&${form_data}`, function (d){
                    if(d.status == 'Success'){
                        Swal.fire({
                            icon: 'success',
                            title: d.status,
                            text: d.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top'
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
                });
            })
        },

        modal: () => {
            $(document).on('click', '.show_modal', function (){
                $('#exampleModal').modal();
            })
        },

        templates_modal: () => {
            $(document).on('click', '.template_img', function (){
                $('#templatesModal').modal();
            })
        },

        remove_logo: () => {
            $(document).on('click', '.remove_logo', function (){
                $('#upload_file').val('');
                $('#picture').attr('src', '').hide();
                $('.square').removeClass('d-none');
                $('.remove_logo').addClass('d-none');
            });
        },

        upload_logo: () => {
            $(document).on('change', '#upload_file', function (){

                let file_data = $(this).prop('files')[0];
                let form_data = new FormData();
                form_data.append('logo', file_data);

                if(file_data.size > 5242880) // 5MB
                {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        text: "File size is larger than the allowed limit.",
                        showConfirmButton: false,
                        timer: 1500,
                        position: 'top'
                    })
                    return;
                }

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
                            $('#picture').attr('src', path).show();
                            $('.square').addClass('d-none');
                            $('.remove_logo').removeClass('d-none');
                        }
                    }
                });
            })
        },

        calculate_items: () => {

            let currency = $('.selectpicker').val();
            let items_count = 0;
            $('.items').each(function (){
                let quantity = $(this).find('.quantity').val();
                let rate = $(this).find('.rate').val();

                if(rate == ''){
                    rate = 0;
                }
                if(quantity == ''){
                    quantity = 0;
                }

                let s = quantity * rate;
                $(this).find('#amount').html(`${s.toFixed(2)} ${currency}`);
                items_count += s;
            });

            return items_count;
        },

        calculate_total: () => {

            //total_items + tax - discount + shipping - amount_paid
            let total_items = parseFloat(invoice.calculate_items());
            let tax = 0, discount = 0, shipping = 0, amount_paid = 0;

            if($('#tax_checkbox').prop('checked')){
                let tax_value = $('.tax_value').val();
                if (tax_value != ''){
                    tax_value = parseFloat(tax_value);
                    if($('.tax').val() == 'flat'){
                        tax = tax_value;
                    }
                    else{
                        tax = (tax_value / 100) * total_items;
                    }
                }
            }

            if($('#discount_checkbox').prop('checked')){
                let discount_value = $('.discount_value').val();
                if(discount_value != ''){
                    discount_value = parseFloat(discount_value);
                    if($('.discount').val() == 'flat'){
                        discount = discount_value;
                    }
                    else{
                        discount = (discount_value / 100) * total_items;
                    }
                }
            }

            if($('#shipping_checkbox').prop('checked')){
                let shipping_value = $('.shipping_value').val();
                if(shipping_value != ''){
                    shipping = parseFloat(shipping_value);
                }
            }

            if($('#amount_paid').val() != ''){
                amount_paid = $('#amount_paid').val();
                amount_paid = parseFloat(amount_paid);
            }

            let result = total_items + tax - discount + shipping - amount_paid;

            return {
                'subtotal' : total_items.toFixed(2),
                'total' : (total_items + tax - discount + shipping).toFixed(2),
                'balance_due' : result.toFixed(2)
            };
        },

        count: () => {
            $(document).on('keyup change', ".invoice_form input[type='number'], .invoice_form input[type='checkbox'], .invoice_form select.custom-select, select.selectpicker" , function (){
                invoice.write_total_values();
            });
            $(document).on('wheel', ".invoice_form input[type='number']" , function (){
                invoice.write_total_values();
            });
        },

        write_total_values: () => {

            let currency = $('.selectpicker').val();
            let result = invoice.calculate_total();

            $('#subtotal').html(`${result.subtotal} ${currency}`);
            $('#total').html(`${result.total} ${currency}`);
            $('#balance_due').html(`${result.balance_due} ${currency}`);
        },

        download_invoice: () => {
            $(document).on('click', '.download_invoice', function (){

                let invoice_data = invoice.invoice_data();

                $.post('/invoice/generate_pdf', invoice_data, function (d) {
                    window.open(d.data.path);
                    setTimeout(function (){
                        invoice.download_pdf(d.data.download_path, d.data.name);
                        }, 500);
                });
            });
        },

        download_pdf: (fileURL, fileName) => {
            var save = document.createElement('a');
            save.href = fileURL;
            save.target = '_blank';
            var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
            save.download = fileName || filename;
            if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                document.location = save.href;
                // window event not working here
            }else{
                var evt = new MouseEvent('click', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': false
                });
                save.dispatchEvent(evt);
                (window.URL || window.webkitURL).revokeObjectURL(save.href);
            }
        },

        remove_row: () => {
            $(document).on('click', '.remove_row', function (){
                $($(this).parents('tr')).remove();

                invoice.write_total_values();

                let rowCount = $('.items').length;
                if(rowCount < 2){
                    $('.items').find('#remove').addClass('d-none');
                }

                // for delete rows in database who is removed on form
                let row_id = $(this).data('id');
                let input_val = $( ".row_id" ).val();

                if(input_val == '')
                {
                    $( ".row_id" ).val(row_id);
                }else
                {
                    input_val = $( ".row_id" ).val() + "," + row_id;
                    $( ".row_id" ).val(input_val);
                }
            })
        },

        line_item: () => {
            $(document).on('click', '.line_item', function (){
                let currency = $('.selectpicker').val();

                $('.items').find('.d-none').removeClass();

                let x = $('.items_table tr:last').find('.quantity').val();
                let y = $('.items_table tr:last').find('.rate').val();

                let tr = `<tr class="items">
                              <td><textarea rows="1" class="form-control shadow-sm item" placeholder="Description of service or product..."></textarea></td>
                              <td><input type="number" min="0" class="form-control shadow-sm quantity" placeholder="Quantity" value="${x}"></td>
                              <td><input type="number" min="0" class="form-control shadow-sm rate" placeholder="Rate" value="${y}"></td>
                              <td><div class="form-control shadow-sm" id="amount">0 ${currency}</div></td>
                              <td id="remove"><div style="position: relative"><i class="fa fa-times remove_row" aria-hidden="true"></i></div></td>
                          </tr>`;

                $('.items_table tr:last').after(tr);

                invoice.write_total_values();
            })
        },

        show_tax: () => {
            $(document).on('click', '#tax_checkbox', function () {
                if($(this).prop('checked')){
                    $(".tax").show(100);
                } else {
                    $(".tax").hide(100);
                    $('.tax_value').val(0);
                }
            });
        },

        show_discount: () => {
            $(document).on('click', '#discount_checkbox', function () {
                if($(this).prop('checked')){
                    $(".discount").show(100);
                } else {
                    $(".discount").hide(100);
                    $('.discount_value').val(0);
                }
            });
        },

        show_shipping: () => {
            $(document).on('click', '#shipping_checkbox', function () {
                if($(this).prop('checked')){
                    $(".shipping").show(100);
                } else {
                    $(".shipping").hide(100);
                    $('.shipping_value').val(0);
                }
            });
        },

        change_color: () => {
            $(document).on('change', "input[type='radio'][name=template]", function (){
                let template = $(this).val();

                if(template == 1){
                    $('#head_color').val('#87CEFA');
                    $('#body_color').val('#F0F8FF');
                    $('#background_color').val('#FFFFFF');
                }
                else if(template == 2){
                    $('#head_color').val('#4169E1');
                    $('#body_color').val('#cce5ff');
                    $('#background_color').val('#FFFFFF');
                }
                else
                {
                    $('#head_color').val('#4B0082');
                    $('#body_color').val('#E6E6FA');
                    $('#background_color').val('#FFFFFF');
                }
            })
        },

        save_invoice: () => {
            $(document).on('click', '.save_invoice', function (){

                let invoice_data = invoice.invoice_data();

                Swal.fire({
                    position: 'top',
                    title: 'Are you sure?',
                    text: 'This action will save invoice.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/invoices/save_invoice', invoice_data, function (d) {
                            if(d.status == 'Success'){
                                Swal.fire({
                                    icon: 'success',
                                    title: d.status,
                                    text: d.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    position: 'top'
                                }).then(function (){
                                    window.location.replace('invoices');
                                })
                            }
                        });
                    }
                })
            });
        },

        edit_invoice: () => {
            $(document).on('click', '.edit_invoice', function (){

                let items = {};
                let row = 0;

                $('.items').each(function (){
                    let item_id = $(this).find('.item').data('id');
                    let single_item = $(this).find('.item').val();
                    let single_quantity = $(this).find('.quantity').val();
                    let single_rate = $(this).find('.rate').val();

                    items[row] = {
                        'item_id' : item_id,
                        'single_item': single_item,
                        'single_quantity': single_quantity,
                        'single_rate': single_rate
                    };
                    row++;
                });

                items = JSON.stringify(items);

                let invoice_id = $(this).data('id');
                let form_data = $('.invoice_form').serialize();
                let subtotal = $('#subtotal').text();
                let total = $('#total').text();
                let balance_due = $('#balance_due').text();
                let currency = $('.selectpicker').val();
                let file_name = '';
                if($("#picture").attr('src') != '')
                {
                    file_name = $("#picture").attr('src');
                    let x = file_name.split('/');
                    file_name = x[3];
                }
                let template = $('input[name="template"]:checked').val();
                let head_color = $('#head_color').val();
                let body_color = $('#body_color').val();
                let background_color = $('#background_color').val();
                let font = $('.font-select').val();
                let font_color = $('#font_color').val();
                let logo_position = $('input[name="logo_position"]:checked').val();

                let remove_row = $('.row_id').val();

                $.post('/invoices/edit_invoice', form_data + `&invoice_id=${invoice_id}&subtotal=${subtotal}&total=${total}&balance_due=${balance_due}&file_name=${file_name}&currency=${currency}&items=${items}&remove_row=${remove_row}&template=${template}&head_color=${head_color}&body_color=${body_color}&background_color=${background_color}&font=${font}&font_color=${font_color}&logo_position=${logo_position}`, function (d) {
                    if(d.status == 'Success'){
                        Swal.fire({
                            icon: 'success',
                            title: d.status,
                            text: d.message,
                            showConfirmButton: false,
                            timer: 1500,
                            position: 'top'
                        })
                    }
                });
            });
        },
    };

})(jQuery);