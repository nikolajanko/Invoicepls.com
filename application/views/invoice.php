<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card bg-light mt-4 pb-3 shadow">
                <form class="invoice_form">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" class="form-control shadow-sm mt-3" id="invoice_name" name="invoice_name" value="<?= !empty($invoice_data['invoice_name']) ? $invoice_data['invoice_name'] : 'INVOICE #1'?>">
                            </div>
                            <div class="col-md-5">
                                <?php
                                    if(isset($invoice_data) && !empty($invoice_data)){ ?>
                                    <label for="upload_file" class="mt-3 shadow-sm rounded square <?= $invoice_data['logo'] != '' ? 'd-none' : ''?>"><i class="fa fa-plus" aria-hidden="true"></i> Add Your Logo
                                        <input type="file" class="custom-file-input d-none" id="upload_file">
                                    </label>
                                    <img id="picture" class="shadow-sm rounded" src="<?="/assets/uploads/".$invoice_data['logo'] ?>" alt="Logo" <?= $invoice_data['logo'] == '' ? 'style="display: none"' : ''?>>
                                    <div class="remove_logo <?= $invoice_data['logo'] == '' ? "d-none" : '' ?>"><i class="fa fa-times" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Remove logo"></i></div>
                                    <?php }
                                    else
                                    { ?>
                                    <label for="upload_file" class="mt-3 shadow-sm rounded square <?= isset($user['logo']) && $user['logo'] != '' ? 'd-none' : ''?>"><i class="fa fa-plus" aria-hidden="true"></i> Add Your Logo
                                        <input type="file" class="custom-file-input d-none" id="upload_file">
                                    </label>
                                    <img id="picture" class="shadow-sm rounded" src="<?="/assets/uploads/".$user['logo'] ?>" alt="Logo" <?= $user['logo'] == '' ? 'style="display: none"' : ''?>>
                                    <div class="remove_logo <?= $user['logo'] == '' ? "d-none" : '' ?>"><i class="fa fa-times" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Remove logo"></i></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7">
                                <label class="mt-2"><b>From</b></label>
                                <input type="text" class="form-control shadow-sm" name="from" placeholder="Who is this invoice from? (required)" value="<?php if (isset($user['first_name']) || isset($user['last_name'])) { echo $user['first_name'].' '.$user['last_name']; } ?><?php if (isset($user['address']) && !empty($user['address'])) { echo ', '.$user['address']; } ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mt-2"><b>Bill to</b></label>
                                        <textarea class="form-control shadow-sm" name="bill_to" placeholder="Who is this invoice to? (required)"><?= !empty($invoice_data['bill_to']) ? $invoice_data['bill_to'] : '' ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mt-2"><b>Ship to</b></label>
                                        <textarea class="form-control shadow-sm" name="ship_to" placeholder="(optional)"><?= !empty($invoice_data['ship_to']) ? $invoice_data['ship_to'] : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="mt-2"><b>Date</b></label>
                                <input type="text" class="form-control shadow-sm" name="date" id="datetimepicker_date" placeholder="" value="<?= !empty($invoice_data['date']) ? $invoice_data['date'] : ''?>">

                                <label class="mt-2"><b>Due date</b></label>
                                <input type="text" class="form-control shadow-sm" name="date_due" id="datetimepicker_date_due" placeholder="" value="<?= !empty($invoice_data['date_due']) ? $invoice_data['date_due'] : '' ?>">

                                <label class="mt-2"><b>Payment terms</b></label>
                                <input type="text" class="form-control shadow-sm" name="payment_terms" placeholder="" value="<?= !empty($invoice_data['payment_terms']) ? $invoice_data['payment_terms'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="row_id" value="">

                    <table class="table table-sm items_table mt-3">
                        <thead>
                        <tr>
                            <th width="40%" class="text-md-center">Item</th>
                            <th width="20%" class="text-md-center">Quantity</th>
                            <th width="20%" class="text-md-center">Rate</th>
                            <th width="20%" class="text-md-center">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($invoice_items))
                        {
                            foreach ($invoice_items as $item)
                            { ?>
                                <tr class="items">
                                    <td><textarea rows="1" class="form-control shadow-sm item" placeholder="Description of service or product..." data-id="<?= $item['id'] ?>"><?= $item['item'] ?></textarea></td>
                                    <td><input type="number" min="0" class="form-control shadow-sm quantity" placeholder="Quantity" value="<?= $item['quantity'] ?>"></td>
                                    <td><input type="number" min="0" class="form-control shadow-sm rate" placeholder="Rate" value="<?= $item['rate'] ?>"></td>
                                    <td><div class="form-control shadow-sm" id="amount"><?=$item['quantity'] * $item['rate'] . ' ' . $invoice_data['currency'] ?></div></td>
                                    <td id="remove"><div style="position: relative"><i class="fa fa-times remove_row" aria-hidden="true" data-id="<?= $item['id'] ?>"></i></div></td>
                                </tr>
                                <?php
                            }
                        }
                        else
                        { ?>
                            <tr class="items">
                                <td><textarea rows="1" class="form-control shadow-sm item" placeholder="Description of service or product..."></textarea></td>
                                <td><input type="number" min="0" class="form-control shadow-sm quantity" placeholder="Quantity"></td>
                                <td><input type="number" min="0" class="form-control shadow-sm rate" placeholder="Rate"></td>
                                <td><div class="form-control shadow-sm" id="amount"></div></td>
                                <td class="d-none" id="remove"><div style="position: relative"><i class="fa fa-times remove_row" aria-hidden="true"></i></div></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-sm shadow-sm line_item"><i class="fa fa-plus" aria-hidden="true"></i> Line Item</button>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7">
                                <?php if (isset($invoice_data) && !empty($invoice_data)){ ?>
                                    <label class="mt-3"><b>Notes</b></label>
                                    <textarea class="form-control shadow-sm" name="notes" placeholder="Notes - any relevant information not already covered"><?= !empty($invoice_data['notes']) ? $invoice_data['notes'] : '' ?></textarea>
                                    <label class="mt-3"><b>Terms</b></label>
                                    <textarea class="form-control shadow-sm" name="terms" placeholder="Terms and conditions - late fees, payment methods, delivery schedule"><?= !empty($invoice_data['terms']) ? $invoice_data['terms'] : '' ?></textarea>
                                <?php } else { ?>
                                    <label class="mt-3"><b>Notes</b></label>
                                    <textarea class="form-control shadow-sm" name="notes" placeholder="Notes - any relevant information not already covered"><?= !empty($user['notes']) ? $user['notes'] : '' ?></textarea>
                                    <label class="mt-3"><b>Terms</b></label>
                                    <textarea class="form-control shadow-sm" name="terms" placeholder="Terms and conditions - late fees, payment methods, delivery schedule"><?= !empty($user['terms']) ? $user['terms'] : '' ?></textarea>
                                <?php } ?>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="mt-3 input-control"><b>Subtotal</b></label>
                                        <div class="form-control shadow-sm" id="subtotal" name="subtotal"></div>
                                    </div>
                                </div>

                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="tax_checkbox" <?= isset($invoice_data['tax_value']) && $invoice_data['tax_value'] != "0" ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="tax_checkbox"><b>Tax</b></label>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <select class="custom-select shadow-sm tax" name="tax" <?= !isset($invoice_data['tax_value']) || $invoice_data['tax_value'] == 0 ? 'style="display: none"' : ''?>>
                                            <option value="flat" <?= isset($invoice_data['tax']) && $invoice_data['tax'] == 'flat' ? 'selected' : '' ?>>Flat ($)</option>
                                            <option value="percent" <?= isset($invoice_data['tax']) && $invoice_data['tax'] == 'percent' ? 'selected' : '' ?>>Percent (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" min="0" class="form-control shadow-sm tax tax_value" name="tax_value" <?= !isset($invoice_data['tax_value']) || $invoice_data['tax_value'] == 0 ? 'style="display: none"' : '' ?> placeholder="Value..." value="<?= isset($invoice_data['tax_value']) ? $invoice_data['tax_value'] : '' ?>">
                                    </div>
                                </div>

                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="discount_checkbox" <?= isset($invoice_data['discount_value']) && $invoice_data['discount_value'] != 0 ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="discount_checkbox"><b>Discount</b></label>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <select class="custom-select shadow-sm discount" name="discount" <?= !isset($invoice_data['discount_value']) || $invoice_data['discount_value'] == 0 ? 'style="display: none"' : '' ?>>
                                            <option value="flat" <?= isset($invoice_data['discount']) && $invoice_data['discount'] == 'flat' ? 'selected' : '' ?> >Flat ($)</option>
                                            <option value="percent" <?= isset($invoice_data['discount']) && $invoice_data['discount'] == 'percent' ? 'selected' : '' ?>>Percent (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" min="0" class="form-control shadow-sm discount discount_value" name="discount_value" <?= !isset($invoice_data['discount_value']) || $invoice_data['discount_value'] == 0 ? 'style="display: none"' : '' ?> placeholder="Value..." value="<?= isset($invoice_data['discount_value']) ? $invoice_data['discount_value'] : '' ?>">
                                    </div>
                                </div>

                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="shipping_checkbox" <?= isset($invoice_data['shipping_value']) && $invoice_data['shipping_value'] != 0 ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="shipping_checkbox"><b>Shipping</b></label>
                                </div>
                                <div>
                                    <input type="number" min="0" class="form-control shadow-sm shipping shipping_value" name="shipping_value" <?= !isset($invoice_data['shipping_value']) || $invoice_data['shipping_value'] == 0 ? 'style="display: none"' : '' ?> placeholder="Value..." value="<?= isset($invoice_data['shipping_value']) ? $invoice_data['shipping_value'] : '' ?>">
                                </div>

                                <label class="mt-2 input-control"><b>Total</b></label>
                                <div class="form-control shadow-sm" id="total" name="total"></div>

                                <label class="mt-2 input-control"><b>Amount Paid</b></label>
                                <input type="number" min="0" class="form-control shadow-sm" id="amount_paid" name="amount_paid" value="<?= isset($invoice_data['amount_paid']) ? $invoice_data['amount_paid'] : '' ?>">

                                <label class="mt-2 input-control"><b>Balance Due</b></label>
                                <div class="form-control shadow-sm" id="balance_due" name="balance_due"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-3 text-center">
            <div class="card bg-light mt-4 shadow">
                <div class="col-md-12 mt-3 mb-3">
                    <button type="button" class="btn btn-primary shadow-sm download_invoice mb-2"><i class="fa fa-download" aria-hidden="true"></i> Download Invoice</button>
                    <br>
                    <button type="button" class="btn btn-secondary shadow-sm show_modal"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Send Invoice</button>
                    <br>
                    <label class="mt-2 mb-2"><b>Currency:</b></label>
                    <select class="col-md-12 selectpicker show-tick" data-live-search="true" data-selected="<?= isset($user['currency']) ? $user['currency'] : '' ?>" data-invoice_id="<?= isset($invoice_data['id']) ? $invoice_data['id']: '0' ?>" data-invoice_currency="<?= isset($invoice_data['currency']) ? $invoice_data['currency'] : '' ?>" data-size="10">
                        <option value="AED" data-subtext="AED">United Arab Emirates dirham</option>
                        <option value="AFN" data-subtext="AFN">Afghan afghani</option>
                        <option value="ALL" data-subtext="ALL">Albanian lek</option>
                        <option value="AMD" data-subtext="AMD">Armenian dram</option>
                        <option value="ANG" data-subtext="ANG">Netherlands Antillean guilder</option>
                        <option value="AOA" data-subtext="AOA">Angolan kwanza</option>
                        <option value="ARS" data-subtext="ARS">Argentine peso</option>
                        <option value="AUD" data-subtext="AUD">Australian dollar</option>
                        <option value="AWG" data-subtext="AWG">Aruban florin</option>
                        <option value="AZN" data-subtext="AZN">Azerbaijani manat</option>
                        <option value="BAM" data-subtext="BAM">BiH convertible mark</option>
                        <option value="BBD" data-subtext="BBD">Barbadian dollar</option>
                        <option value="BDT" data-subtext="BDT">Bangladeshi taka</option>
                        <option value="BGN" data-subtext="BGN">Bulgarian lev</option>
                        <option value="BHD" data-subtext="BHD">Bahraini dinar</option>
                        <option value="BIF" data-subtext="BIF">Burundian franc</option>
                        <option value="BMD" data-subtext="BMD">Bermudian dollar</option>
                        <option value="BND" data-subtext="BND">Brunei dollar</option>
                        <option value="BOB" data-subtext="BOB">Bolivian boliviano</option>
                        <option value="BRL" data-subtext="BRL">Brazilian real</option>
                        <option value="BSD" data-subtext="BSD">Bahamian dollar</option>
                        <option value="BTN" data-subtext="BTN">Bhutanese ngultrum</option>
                        <option value="BWP" data-subtext="BWP">Botswana pula</option>
                        <option value="BYN" data-subtext="BYN">Belarusian ruble</option>
                        <option value="BZD" data-subtext="BZD">Belize dollar</option>
                        <option value="CAD" data-subtext="CAD">Canadian dollar</option>
                        <option value="CDF" data-subtext="CDF">Congolese franc</option>
                        <option value="CHF" data-subtext="CHF">Swiss franc</option>
                        <option value="CLP" data-subtext="CLP">Chilean peso</option>
                        <option value="CNY" data-subtext="CNY">Chinese yuan</option>
                        <option value="COP" data-subtext="COP">Colombian peso</option>
                        <option value="CRC" data-subtext="CRC">Costa Rican colón</option>
                        <option value="CUC" data-subtext="CUC">Cuban convertible peso</option>
                        <option value="CUP" data-subtext="CUP">Cuban peso</option>
                        <option value="CVE" data-subtext="CVE">Cape Verdean escudo</option>
                        <option value="CZK" data-subtext="CZK">Czech koruna</option>
                        <option value="DJF" data-subtext="DJF">Djiboutian franc</option>
                        <option value="DKK" data-subtext="DKK">Danish krone</option>
                        <option value="DOP" data-subtext="DOP">Dominican peso</option>
                        <option value="DZD" data-subtext="DZD">Algerian dinar</option>
                        <option value="EGP" data-subtext="EGP">Egyptian pound</option>
                        <option value="ERN" data-subtext="ERN">Eritrean nakfa</option>
                        <option value="ETB" data-subtext="ETB">Ethiopian birr</option>
                        <option value="EUR" data-subtext="EUR">EURO</option>
                        <option value="FJD" data-subtext="FJD">Fijian dollar</option>
                        <option value="FKP" data-subtext="FKP">Falkland Islands pound</option>
                        <option value="GBP" data-subtext="GBP">British pound</option>
                        <option value="GEL" data-subtext="GEL">Georgian lari</option>
                        <option value="GGP" data-subtext="GGP">Guernsey pound</option>
                        <option value="GHS" data-subtext="GHS">Ghanaian cedi</option>
                        <option value="GIP" data-subtext="GIP">Gibraltar pound</option>
                        <option value="GMD" data-subtext="GMD">Gambian dalasi</option>
                        <option value="GNF" data-subtext="GNF">Guinean franc</option>
                        <option value="GTQ" data-subtext="GTQ">Guatemalan quetzal</option>
                        <option value="GYD" data-subtext="GYD">Guyanese dollar</option>
                        <option value="HKD" data-subtext="HKD">Hong Kong dollar</option>
                        <option value="HNL" data-subtext="HNL">Honduran lempira</option>
                        <option value="HRK" data-subtext="HRK">Croatian kuna</option>
                        <option value="HTG" data-subtext="HTG">Haitian gourde</option>
                        <option value="HUF" data-subtext="HUF">Hungarian forint</option>
                        <option value="IDR" data-subtext="IDR">Indonesian rupiah</option>
                        <option value="ILS" data-subtext="ILS">Israeli new shekel</option>
                        <option value="IMP" data-subtext="IMP">Manx pound</option>
                        <option value="INR" data-subtext="INR">Indian rupee</option>
                        <option value="IQD" data-subtext="IQD">Iraqi dinar</option>
                        <option value="IRR" data-subtext="IRR">Iranian rial</option>
                        <option value="ISK" data-subtext="ISK">Icelandic króna</option>
                        <option value="JEP" data-subtext="JEP">Jersey pound</option>
                        <option value="JMD" data-subtext="JMD">Jamaican dollar</option>
                        <option value="JOD" data-subtext="JOD">Jordanian dinar</option>
                        <option value="JPY" data-subtext="JPY">Japanese yen</option>
                        <option value="KES" data-subtext="KES">Kenyan shilling</option>
                        <option value="KGS" data-subtext="KGS">Kyrgyzstani som</option>
                        <option value="KHR" data-subtext="KHR">Cambodian riel</option>
                        <option value="KID" data-subtext="KID">Kiribati dollar</option>
                        <option value="KMF" data-subtext="KMF">Comorian franc</option>
                        <option value="KPW" data-subtext="KPW">North Korean won</option>
                        <option value="KRW" data-subtext="KRW">South Korean won</option>
                        <option value="KWD" data-subtext="KWD">Kuwaiti dinar</option>
                        <option value="KYD" data-subtext="KYD">Cayman Islands dollar</option>
                        <option value="KZT" data-subtext="KZT">Kazakhstani tenge</option>
                        <option value="LAK" data-subtext="LAK">Lao kip</option>
                        <option value="LBP" data-subtext="LBP">Lebanese pound</option>
                        <option value="LKR" data-subtext="LKR">Sri Lankan rupee</option>
                        <option value="LRD" data-subtext="LRD">Liberian dollar</option>
                        <option value="LSL" data-subtext="LSL">Lesotho loti</option>
                        <option value="LYD" data-subtext="LYD">Libyan dinar</option>
                        <option value="MAD" data-subtext="MAD">Moroccan dirham</option>
                        <option value="MDL" data-subtext="MDL">Moldovan leu</option>
                        <option value="MGA" data-subtext="MGA">Malagasy ariary</option>
                        <option value="MKD" data-subtext="MKD">Macedonian denar</option>
                        <option value="MMK" data-subtext="MMK">Burmese kyat</option>
                        <option value="MNT" data-subtext="MNT">Mongolian tögrög</option>
                        <option value="MOP" data-subtext="MOP">Macanese pataca</option>
                        <option value="MRU" data-subtext="MRU">Mauritanian ouguiya</option>
                        <option value="MUR" data-subtext="MUR">Mauritian rupee</option>
                        <option value="MVR" data-subtext="MVR">Maldivian rufiyaa</option>
                        <option value="MWK" data-subtext="MWK">Malawian kwacha</option>
                        <option value="MXN" data-subtext="MXN">Mexican peso</option>
                        <option value="MYR" data-subtext="MYR">Malaysian ringgit</option>
                        <option value="MZN" data-subtext="MZN">Mozambican metical</option>
                        <option value="NAD" data-subtext="NAD">Namibian dollar</option>
                        <option value="NGN" data-subtext="NGN">Nigerian naira</option>
                        <option value="NIO" data-subtext="NIO">Nicaraguan córdoba</option>
                        <option value="NOK" data-subtext="NOK">Norwegian krone</option>
                        <option value="NPR" data-subtext="NPR">Nepalese rupee</option>
                        <option value="NZD" data-subtext="NZD">New Zealand dollar</option>
                        <option value="OMR" data-subtext="OMR">Omani rial</option>
                        <option value="PAB" data-subtext="PAB">Panamanian balboa</option>
                        <option value="PEN" data-subtext="PEN">Peruvian sol</option>
                        <option value="PGK" data-subtext="PGK">Papua New Guinean kina</option>
                        <option value="PHP" data-subtext="PHP">Philippine peso</option>
                        <option value="PKR" data-subtext="PKR">Pakistani rupee</option>
                        <option value="PLN" data-subtext="PLN">Polish złoty</option>
                        <option value="PRB" data-subtext="PRB">Transnistrian ruble</option>
                        <option value="PYG" data-subtext="PYG">Paraguayan guaraní</option>
                        <option value="QAR" data-subtext="QAR">Qatari riyal</option>
                        <option value="RON" data-subtext="RON">Romanian leu</option>
                        <option value="RSD" data-subtext="RSD">Serbian dinar</option>
                        <option value="RUB" data-subtext="RUB">Russian ruble</option>
                        <option value="RWF" data-subtext="RWF">Rwandan franc</option>
                        <option value="SAR" data-subtext="SAR">Saudi riyal</option>
                        <option value="SEK" data-subtext="SEK">Swedish krona</option>
                        <option value="SGD" data-subtext="SGD">Singapore dollar</option>
                        <option value="SHP" data-subtext="SHP">Saint Helena pound</option>
                        <option value="SLL" data-subtext="SLL">Sierra Leonean leone</option>
                        <option value="SLS" data-subtext="SLS">Somaliland shilling</option>
                        <option value="SOS" data-subtext="SOS">Somali shilling</option>
                        <option value="SRD" data-subtext="SRD">Surinamese dollar</option>
                        <option value="SSP" data-subtext="SSP">South Sudanese pound</option>
                        <option value="STN" data-subtext="STN">São Tomé and Príncipe dobra</option>
                        <option value="SYP" data-subtext="SYP">Syrian pound</option>
                        <option value="SZL" data-subtext="SZL">Swazi lilangeni</option>
                        <option value="THB" data-subtext="THB">Thai baht</option>
                        <option value="TJS" data-subtext="TJS">Tajikistani somoni</option>
                        <option value="TMT" data-subtext="TMT">Turkmenistan manat</option>
                        <option value="TND" data-subtext="TND">Tunisian dinar</option>
                        <option value="TOP" data-subtext="TOP">Tongan paʻanga</option>
                        <option value="TRY" data-subtext="TRY">Turkish lira</option>
                        <option value="TTD" data-subtext="TTD">Trinidad and Tobago dollar</option>
                        <option value="TVD" data-subtext="TVD">Tuvaluan dollar</option>
                        <option value="TWD" data-subtext="TWD">New Taiwan dollar</option>
                        <option value="TZS" data-subtext="TZS">Tanzanian shilling</option>
                        <option value="UAH" data-subtext="UAH">Ukrainian hryvnia</option>
                        <option value="UGX" data-subtext="UGX">Ugandan shilling</option>
                        <option value="USD" data-subtext="USD" selected>United States dollar</option>
                        <option value="UYU" data-subtext="UYU">Uruguayan peso</option>
                        <option value="UZS" data-subtext="UZS">Uzbekistani soʻm</option>
                        <option value="VES" data-subtext="VES">Venezuelan bolívar soberano</option>
                        <option value="VND" data-subtext="VND">Vietnamese đồng</option>
                        <option value="VUV" data-subtext="VUV">Vanuatu vatu</option>
                        <option value="WST" data-subtext="WST">Samoan tālā</option>
                        <option value="XAF" data-subtext="XAF">Central African CFA franc</option>
                        <option value="XCD" data-subtext="XCD">Eastern Caribbean dollar</option>
                        <option value="XOF" data-subtext="XOF">West African CFA fran</option>
                        <option value="XPF" data-subtext="XPF">CFP franc</option>
                        <option value="ZAR" data-subtext="ZAR">South African rand</option>
                        <option value="ZMW" data-subtext="ZMW">Zambian kwacha</option>
                        <option value="ZWB" data-subtext="ZWB">Zimbabwean bonds</option>
                    </select>
                </div>
            </div>
                <?php
                if(isset($user['logo']) && $user['user_id'] != ''){ ?>
                    <div class="col-md-12 mt-3">
                        <?php
                        if(isset($invoice_data['id'])){ ?>
                            <button type="button" class="btn btn-warning shadow-sm edit_invoice" data-id="<?= $invoice_data['id']?>"><i class="fa fa-floppy-o" aria-hidden="true"></i> Edit Invoice</button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-success shadow-sm save_invoice"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Invoice</button>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>
            <div class="card bg-light mt-3 shadow">
                <div class="options pb-3">
                    <label class="mt-2"><b>Choose colors for invoice: <i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="First select invoice template" style="cursor: pointer"></i></b></label><br>
                    <div class="float-left ml-4">
                        <input type="color" id="head_color" value="<?= isset($invoice_data['head_color']) && !empty($invoice_data['head_color']) ? $invoice_data['head_color'] : '#87CEFA' ?>">
                        <label for="head_color">Head Color</label>
                    </div>
                    <div class="float-left ml-4">
                        <input type="color" id="body_color" value="<?= isset($invoice_data['body_color']) && !empty($invoice_data['body_color']) ? $invoice_data['body_color'] : '#F0F8FF' ?>">
                        <label for="body_color">Body Color</label>
                    </div>
                    <div class="float-left ml-4 ">
                        <input type="color" id="background_color" value="<?= isset($invoice_data['background_color']) && !empty($invoice_data['background_color']) ? $invoice_data['background_color'] : '#FFFFFF' ?>">
                        <label for="background_color">Background Color</label>
                    </div>
                    <br>
                    <br>
                    <br>
                    <label class="mt-2"><b>Choose font:</b></label>
                    <div class="mr-3 ml-3">
                        <select class="form-control font-select" data-selected="<?= isset($invoice_data['font']) && !empty($invoice_data['font']) ? $invoice_data['font'] : '' ?>">
                            <option value="opensans" style="font-family: 'Open Sans', sans-serif;">Open Sans</option>
                            <option value="helvetica" style="font-family: 'Helvetica'">Helvetica</option>
                            <option value="times" style="font-family: 'Times New Roman'">Times New Roman</option>
                            <option value="roboto" style="font-family: 'Roboto', sans-serif;">Roboto</option>
                            <option value="lato" style="font-family: 'Lato', sans-serif;">Lato</option>
                        </select>
                    </div>

                    <label class="mt-2"><b>Font color:</b></label>
                    <div>
                        <input type="color" id="font_color" value="<?= isset($invoice_data['font_color']) && !empty($invoice_data['font_color']) ? $invoice_data['font_color'] : '#000000' ?>">
                    </div>

                    <label class="mt-2"><b>Logo position:</b></label>
                    <div class="custom-control custom-radio" id="logo_left_position">
                        <input class="custom-control-input" id="logo_left" name="logo_position" type="radio" value="left" <?= isset($invoice_data['logo_position']) && $invoice_data['logo_position'] == 'left' ? 'checked' : '' ?> checked>
                        <label class="custom-control-label" for="logo_left">Left</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" id="right_logo" name="logo_position" type="radio" value="right" <?= isset($invoice_data['logo_position']) && $invoice_data['logo_position'] == 'right' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="right_logo">Right</label>
                    </div>
                </div>
            </div>
            <div class="card bg-light mt-3 pb-3 shadow">
                <label class="mt-2"><b>Invoice templates: </b></label>
                <div class="custom-control custom-radio radio_btn mb-2">
                    <input id="template_1" name="template" type="radio" class="custom-control-input" value="1" <?= isset($invoice_data['template']) && $invoice_data['template'] == '1' ? 'checked' : '' ?> checked>
                    <label class="custom-control-label" for="template_1">Template 1</label>
                </div>
                <img class="template_img" src="/assets/Template1.jpg" alt="Template_1" data-target="#carouselExample" data-slide-to="0">
                <div class="custom-control custom-radio radio_btn mb-2 mt-2">
                    <input id="template_2" name="template" type="radio" class="custom-control-input" value="2" <?= isset($invoice_data['template']) && $invoice_data['template'] == '2' ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="template_2">Template 2</label>
                </div>
                <img class="template_img" src="/assets/Template2.jpg" alt="Template_2" data-target="#carouselExample" data-slide-to="1">
                <div class="custom-control custom-radio radio_btn mb-2 mt-2">
                    <input id="template_3" name="template" type="radio" class="custom-control-input" value="3" <?= isset($invoice_data['template']) && $invoice_data['template'] == '3' ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="template_3">Template 3</label>
                </div>
                <img class="template_img" src="/assets/Template3.jpg" alt="Template_3" data-target="#carouselExample" data-slide-to="2">
            </div>
        </div>
    </div>
</div>
<div class="mb-5">

</div>

<!-- Modal for templates -->
<!--
This part is straight out of Bootstrap docs. Just a carousel inside a modal.
-->
<div class="modal fade" id="templatesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Invoice templates</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="carouselExample" class="carousel slide carousel-fade" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExample" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExample" data-slide-to="1"></li>
                        <li data-target="#carouselExample" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="/assets/Template1.jpg" alt="Template 1">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="/assets/Template2.jpg" alt="Template 2">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="/assets/Template3.jpg" alt="Template 3">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for send invoice-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Send Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="invoice_send">
                    <h6>To: </h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        </div>
                        <input type="email" class="form-control" name="email_to" placeholder="Your client's email address">
                    </div>
                    <h6 class="mt-1">From: </h6>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                        </div>
                        <input type="email" class="form-control" name="email_from" placeholder="Your email address">
                    </div>
                    <h6 class="mt-1">Subject: </h6>
                    <input type="text" class="form-control" placeholder="Subject..." name="email_subject">
                    <h6 class="mt-1">Message: </h6>
                    <textarea class="form-control" name="email_message" placeholder="Message for client"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light mr-auto"><i class="fa fa-paperclip" aria-hidden="true"></i>  Invoice Attached</button>
                <button type="button" class="btn btn-secondary float-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
                <button type="button" class="btn btn-primary send_invoice"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Send</button>
            </div>
        </div>
    </div>
</div>