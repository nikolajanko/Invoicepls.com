<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="mt-4 table-responsive-md">
                <table class="table table-bordered mt-3 shadow invoices_table">
                    <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-md-center"><b>#</b></th>
                        <th width="55%" class="text-md-left"><b>Invoice Name</b></th>
                        <th width="20%" class="text-md-left"><b>Date Created</b></th>
                        <th width="10%" class="text-md-center"><b>Download</b></th>
                        <th width="10%" class="text-md-center"><b>Delete</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $x = 1;
                        foreach ($invoices as $invoice){ ?>
                    <tr>
                        <td class="text-md-center"><?= $x ++;?></td>
                        <td><a class="text-decoration-none" href="invoice/view/<?=$invoice['id']?>"><?=$invoice['invoice_name']?></a></td>
                        <td><?=date("F jS, Y", strtotime($invoice['date_created']));?></td>
                        <td class="text-md-center"><a href="invoices/download_pdf?pdf=<?=$invoice['invoice_pdf_link']?>" target="_blank"><i style="font-size: 20px;" class="fa fa-download" aria-hidden="true"></i></a></td>
                        <td class="text-md-center"><i class="fa fa-trash-o delete_invoice" data-id="<?=$invoice['id']?>" aria-hidden="true"></i></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


