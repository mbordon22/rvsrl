@extends('layouts.invoice.master')

@section('css')
@endsection

@section('style')
    <style type="text/css">
        body {
            font-family: 'Montserrat', sans-serif;
            display: block;

        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Ecommerce</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Invoice</li>
                        <li class="breadcrumb-item active">Invoice-5</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts  -->
    <div class="container-fluid"> </div>
    <table style="width:1160px;margin:0 auto;">
        <tbody>
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tbody>
                            <tr
                                style="padding: 28px 0 5px; display: flex; justify-content: space-between; align-items: center;">
                                <td><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                        alt="logo-light"><img class="img-fluid for-dark"
                                        src="{{ asset('assets/images/logo/logo.png') }}" alt="logo-dark"></td>
                                <td>
                                    <ul
                                        style="list-style: none; display: flex; align-items: center; background: linear-gradient(291deg,  #006666 21.2%, #006666 83.92%); padding: 31px 80px; border-bottom-left-radius: 100px; gap:28px;">
                                        <li>
                                            <svg class="stroke-icon"
                                                style="height:14px; width: 14px; fill:#fff; margin-right: 10px;">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#call') }}"></use>
                                            </svg><span style="color: #FFFFFF; font-weight: 500; font-size: 16px;">(239)
                                                555-0108</span>
                                        </li>
                                        <li
                                            style="border-left: 1px solid rgba(255, 255, 255, 0.3); border-right: 1px solid rgba(255, 255, 255, 0.3); padding: 0 22px;">
                                            <svg class="stroke-icon"
                                                style="height:16px; width: 16px; fill:#fff; margin-right: 10px;">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#email-box') }}"></use>
                                            </svg><span
                                                style="color: #FFFFFF; font-weight: 500; font-size: 16px;">Riho@themesforest.com</span>
                                        </li>
                                        <li>
                                            <svg class="stroke-icon"
                                                style="height:16px; width: 16px; fill:#fff; margin-right: 10px;">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#web') }}"></use>
                                            </svg><span style="color: #FFFFFF; font-weight: 500; font-size: 16px;">Website:
                                                www.Rihothemes.com</span>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr style="display: flex; justify-content: space-between;">
                                <td> <span
                                        style="font-size: 18px; display:block; font-weight: 600; color:#006666 ; margin-bottom: 14px;">Invoice
                                        to :</span><span
                                        style="font-size: 18px; display:block; font-weight: 600;  margin-bottom: 8px;">Brooklyn
                                        Simmons</span><span
                                        style="font-size: 18px; display:block;  opacity: 0.8; margin-bottom: 8px; width: 72%; line-height: 1.4;">2972
                                        Westheimer Rd. Santa Ana, Illinois 85486 </span><span
                                        style="font-size: 18px; display:block;  opacity: 0.8; margin-bottom: 8px;">Phone :
                                        (219) 555-0114</span><span
                                        style="font-size: 18px; display:block;  opacity: 0.8; margin-bottom: 8px;">Email :
                                        yourmail@themesforest.com</span><span
                                        style="font-size: 18px; display:block;  opacity: 0.8; margin-bottom: 8px;">Website:
                                        www.websites.com</span></td>
                                <td>
                                    <h4 style="font-size:42px; font-weight: 600;  margin:0 0 12px 0;">INVOICE</h4><span
                                        style="font-size: 18px;color: #006666; display: block; font-weight: 600; margin-bottom: 20px;">Bank
                                        Transfer</span><span
                                        style=" display: block; margin-bottom: 12px; font-weight: 600;">Leslie
                                        Alexander</span><span
                                        style="display: block;  font-size: 18px; margin-bottom: 12px;">Bank Account :
                                        0981234098765</span><span
                                        style="display: block;  font-size: 18px; margin-bottom: 12px;">Code :
                                        LEF098756</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; border-spacing: 4px; margin-bottom: 20px;">
                        <tbody>
                            <tr>
                                <td class="invoice-table-data" style="padding: 15px 25px;">
                                    <p
                                        style="font-size:16px; font-weight:500; color:#52526C; opacity:0.8; margin:0;line-height: 2;">
                                        Date :</p><span style="font-size: 18px; font-weight: 600;">10 Mar, 2024</span>
                                </td>
                                <td class="invoice-table-data" style="padding: 15px 25px;">
                                    <p
                                        style="font-size:16px; font-weight:500; color:#52526C; opacity:0.8; margin:0;line-height: 2;">
                                        Invoice No :</p><span style="font-size: 18px; font-weight: 600;">#VL25000365</span>
                                </td>
                                <td class="invoice-table-data" style="padding: 15px 25px;">
                                    <p
                                        style="font-size:16px; font-weight:500; color:#52526C; opacity:0.8; margin:0;line-height: 2;">
                                        Account No :</p><span
                                        style="font-size: 18px; font-weight: 600;">0981234098765</span>
                                </td>
                                <td class="invoice-table-data" style="padding: 15px 25px;">
                                    <p
                                        style="font-size:16px; font-weight:500; color:#52526C; opacity:0.8; margin:0;line-height: 2;">
                                        Due Amount :</p><span style="font-size: 18px; font-weight: 600;">$7860.00 </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; border-spacing:0;">
                        <thead>
                            <tr style="background: #006666;">
                                <th
                                    style="padding: 18px 15px;text-align: center; position: relative; border-top-left-radius: 10px;">
                                    <span style="color: #fff; font-size: 18px; font-weight: 600;">No.</span>
                                </th>
                                <th style="padding: 18px 16px;text-align: left;"><span
                                        style="color: #fff; font-size: 18px; font-weight: 600;">Description</span></th>
                                <th style="padding: 18px 15px;text-align: center"><span
                                        style="color: #fff; font-size: 18px; font-weight: 600;">Unite Price</span></th>
                                <th style="padding: 18px 15px;text-align: center"><span
                                        style="color: #fff; font-size: 18px; font-weight: 600;">Quantity</span></th>
                                <th
                                    style="padding: 18px 15px;text-align: center;position: relative; border-top-right-radius: 10px;">
                                    <span style="color: #fff; font-size: 18px; font-weight: 600;">Subtotal</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-dataopacity: 0.8; font-weight: 600;">1</span>
                                </td>
                                <td style="padding: 16px; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <h4 style="font-weight:600; margin:4px 0px; font-size: 18px; ">Proposal & Brochure
                                        Design</h4><span style="opacity: 0.8; font-size: 16px;">Regular License</span>
                                </td>
                                <td style="width: 12%; text-align: center;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data-weight: 600;">$300.00</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style="font-weight: 600;">1</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data font-weight: 600;opacity: 0.9;">$300.00</span>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-dataopacity: 0.8;font-weight: 600;">2</span>
                                </td>
                                <td style="padding: 16px;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <h4 style="font-weight:600; margin:4px 0px; font-size: 18px; ">Web design and
                                        development</h4><span style="opacity: 0.8; font-size: 16px;">Regular License</span>
                                </td>
                                <td style="width: 12%; text-align: center;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data-weight: 600;">$400.00</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style="font-weight: 600;">2</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data font-weight: 600;opacity: 0.9;">$800.00</span>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-dataopacity: 0.8;font-weight: 600;">3</span>
                                </td>
                                <td style="padding: 16px;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <h4 style="font-weight:600; margin:4px 0px; font-size: 18px; ">Logo & Brand design</h4>
                                    <span style="opacity: 0.8; font-size: 16px;">Regular License</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data-weight: 600;">$240.00</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style="font-weight: 600;">2</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data font-weight: 600;opacity: 0.9;">$4800.00</span>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-dataopacity: 0.8;font-weight: 600;">4</span>
                                </td>
                                <td style="padding: 16px;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <h4 style="font-weight:600; margin:4px 0px; font-size: 18px; ">Stationary Design</h4>
                                    <span style="opacity: 0.8; font-size: 16px;">Regular License</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center;border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data-weight: 600;">$100.00</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style="font-weight: 600;">1</span>
                                </td>
                                <td
                                    style="width: 12%; text-align: center; border-bottom: 1px solid rgba(82, 82, 108, 0.2);">
                                    <span style=".invoice-table-data font-weight: 600;opacity: 0.9;">$100.00</span>
                                </td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td style="text-align: center; padding:35px 0 18px;"> <span
                                        style="opacity: 0.8;font-weight: 600;">Subtotal </span></td>
                                <td style=" text-align: center; display: block; .invoice-table-data:35px 0 18px;"> <span
                                        style=" font-weight: 600;opacity: 0.9;">$6100.00</span></td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td style="width: 12%; text-align: center;"> <span
                                        style="opacity: 0.8;font-weight: 600;">VAT / Tax 15% </span></td>
                                <td style=" text-align: center; display:block; padding-.invoice-table-data: 18px;"> <span
                                        style=" font-weight: 600;opacity: 0.9;">$50.00</span></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td> </td>
                                <td style="width: 12%; text-align: center;"> <span
                                        style="opacity: 0.8;font-weight: 600;">Discount </span></td>
                                <td style="text-align: center; display: block; .invoice-table-data-bottom: 18px;"> <span
                                        style=" font-weight: 600;opacity: 0.9;">$30.00</span></td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                                <td></td>
                                <td style="width: 12%; text-align: center;"> <span
                                        style="opacity: 0.8;font-weight: 600;">Total Due </span></td>
                                <td style="text-align: center;"> <span
                                        style=".invoice-table-datacolor: #ffffff; font-weight: 600;opacity: 0.9;background: #52526C; padding: 12px 37px; margin-top: 0px; display: block;">$6120.00</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr style="width: 100%; display: flex; justify-content: space-between; margin-top: 36px;">
                <td> <img src="{{ asset('assets/images/email-template/invoice-3/sign.png') }}" alt="sign"><span
                        style="display: block;font-size: 18px;font-weight: 600;">Laurine T. Ebbert</span><span
                        style=" display: block; font-size: 14px; padding-top: 5px;">( Designer )</span></td>
                <td> <span style="display: flex; justify-content: end; gap: 15px;"> <a
                            style="background: #006666 ; color:rgba(255, 255, 255, 1); border-radius: 10px;padding: 18px 27px;font-size: 16px;font-weight: 600;outline: 0;border: 0; text-decoration: none;"
                            href="#!" onclick="window.print();">Print Invoice<i class="icon-arrow-right"
                                style="font-size:13px;font-weight:bold; margin-left: 10px;"></i></a><a
                            style="background: rgba(0, 102, 102, 0.2);color: #006666 ; ;border-radius: 10px;padding: 18px 27px;font-size: 16px;font-weight: 600;outline: 0;border: 0; text-decoration: none;"
                            href="{{ route('admin.invoice_5') }}" download="">Download<i class="icon-arrow-right"
                                style="font-size:13px;font-weight:bold; margin-left: 10px;"> </i></a></span></td>
            </tr>
        </tbody>
    </table>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <!-- calendar js-->
    <script src="{{ asset('assets/js/print.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
@endsection
