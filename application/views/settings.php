<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card bg-light mt-4 pb-3 shadow">
                <form class="settings">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7">
                                <label class="mt-2"><b>First name:</b></label>
                                <input type="text" class="form-control shadow-sm first_name" name="first_name" placeholder="Enter your first name" value="<?=$user['first_name'] ?>">
                                <label class="mt-2"><b>Last name:</b></label>
                                <input type="text" class="form-control shadow-sm last_name" name="last_name" placeholder="Enter your last name" value="<?=$user['last_name'] ?>">
                                <label class="mt-2"><b>Address:</b></label>
                                <input type="text" class="form-control shadow-sm address" name="address" placeholder="Enter your address" value="<?=$user['address'] ?>">
                                <label class="mt-2"><b>Company name:</b></label>
                                <input type="text" class="form-control shadow-sm company" name="company" placeholder="Enter your company name" value="<?=$user['company'] ?>">
                                <label class="mt-2"><b>Notes:</b></label>
                                <input type="text" class="form-control shadow-sm notes" name="notes" placeholder="Notes" value="<?=$user['notes'] ?>">
                                <label class="mt-2"><b>Terms:</b></label>
                                <input type="text" class="form-control shadow-sm terms" name="terms" placeholder="Terms" value="<?=$user['terms'] ?>">
                                <label class="mt-2 mr-5"><b>Currency:</b></label><br>
                                <select class="selectpicker show-tick" data-live-search="true" data-selected="<?=$user['currency'] ?>" data-size="10">
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
                                </select >
                                <br>
                                <button type="submit" class="btn btn-primary shadow-sm mt-3"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                            </div>
                            <div class="col-md-5 text-center">
                                <label for="upload_file" class="mt-3 rounded square <?= $user['logo'] != '' ? 'd-none' : ''?> text-center" style="float: none"><i class="fa fa-plus" aria-hidden="true"></i> Add Your Logo
                                    <input type="file" class="custom-file-input d-none" id="upload_file" enctype="multipart/form-data">
                                </label>
                                <img id="picture" class="shadow-sm rounded" src="<?="/assets/uploads/".$user['logo'] ?>" alt="Logo" <?= $user['logo'] == '' ? 'style="display: none"' : 'style="float: none;"' ?>>
                                <div class="remove_logo_settings <?= $user['logo'] == '' ? 'd-none' : '' ?>"><i class="fa fa-times" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Remove logo"></i></div>

                                <label class="rounded text-center" ><b>Profile completed percentage:</b></label>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%;">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

