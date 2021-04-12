(function($){

    $(document).ready(() => {
        $('#invoices').addClass('active');

        $('.invoices_table').DataTable({
            "responsive": true,
            "pageLength": 10
        });

        invoices.delete_invoice();
    });

    // Define classes where we'll store all functions
    let invoices = {
        delete_invoice: () => {
            $(document).on('click', '.delete_invoice', function (){
                let invoice_id = $(this).data('id');

                Swal.fire({
                    position: 'top',
                    title: 'Are you sure?',
                    text: 'This action will delete invoice.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/invoices/delete_invoice', `invoice_id=${invoice_id}`, function (d) {
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
                            } else {
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
                    }
                });
            })
        }
    };

})(jQuery);