@extends('layouts.invoice.master')

@section('css')
@endsection

@section('style')
    <style type="text/css">
        body {
            font-family: 'Montserrat', sans-serif;
            display: block;
            color: #000248;
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
                        <li class="breadcrumb-item active">Invoice-2</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <table style="width:1160px;margin:0 auto;">
            <tbody>
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td><img class="img-fluid for-light"
                                            src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="logo-light"><img
                                            class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo.png') }}"
                                            alt="logo-dark">
                                        <address style="opacity: 0.8; width: 40%; margin-top: 10px; font-style:normal;">
                                            <span style="font-size: 18px; line-height: 1.5; font-weight: 500;">
                                                1982 Harvest Lane New York, NY12210
                                                United State </span></address>
                                    </td>
                                    <td style="opacity: 0.8; text-align:end;"><span
                                            style="display:block; line-height: 1.5; font-size:18px; font-weight:500;">Email
                                            : Riho@themesforest.com</span><span
                                            style="display:block; line-height: 1.5; font-size:18px; font-weight:500;">Website:
                                            www.Rihothemes.com</span><span
                                            style="display:block; line-height: 1.5; font-size:18px; font-weight:500;">Contact
                                            No : (316) 555-0116</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="width:100%;">
                            <tbody>
                                <tr
                                    style="display:flex;justify-content:space-between;border-top: 1px solid rgba(82, 82, 108, 0.3);border-bottom: 1px solid rgba(82, 82, 108, 0.3);padding: 25px 0;">
                                    <td style="display:flex;align-items:center; gap: 6px;"> <span
                                            style="opacity: 0.8; font-size: 18px; font-weight: 500;">Invoice No.</span>
                                        <h4 style="margin:0;font-weight:400; font-size: 18px;">#VL25000365</h4>
                                    </td>
                                    <td style="display:flex;align-items:center; gap: 6px;"> <span
                                            style="opacity: 0.8; font-size: 18px; font-weight: 500;">Date : </span>
                                        <h4 style="margin:0;font-weight:400; font-size: 18px;">09/03/2024</h4>
                                    </td>
                                    <td style="display:flex;align-items:center; gap: 6px;"> <span
                                            style="opacity: 0.8; font-size: 18px; font-weight: 500;">Payment Status :</span>
                                        <h4
                                            style="margin:0;font-weight:400; font-size: 18px;padding:6px 18px; background-color:rgba(84, 186, 74, 0.1);color:#54BA4A; border-radius: 5px;">
                                            Paid</h4>
                                    </td>
                                    <td style="display:flex;align-items:center; gap: 6px;"> <span
                                            style="opacity: 0.8; font-size: 18px; font-weight: 500;">Total Amount :</span>
                                        <h4 style="margin:0;font-weight:500; font-size: 18px;">$26,410.00</h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tbody>
                                <tr style="padding: 28px 0; display:block;">
                                    <td Style="width:63%;"><span
                                            style="opacity: 0.8; font-size: 16px; font-weight: 500;">BILLING ADDRESS</span>
                                        <h4 style="font-weight:400; margin: 12px 0 6px 0; font-size: 18px;">Brooklyn Simmons
                                        </h4><span
                                            style="width: 54%; display:block; line-height: 1.5; opacity: 0.8; font-size: 18px; font-weight: 400;">2118
                                            Thornridge Cir. Syracuse, Connecticut 35624, United State</span><span
                                            style="line-height:2.6; opacity: 0.8; font-size: 18px; font-weight: 400;">Phone
                                            : (239) 555-0108</span>
                                    </td>
                                    <td><span style="opacity: 0.8;font-size: 16px; font-weight: 500;">SHIPPING
                                            ADDRESS</span>
                                        <h4 style="font-weight:400; margin: 12px 0 6px 0; font-size: 18px;">Cameron
                                            Williamson</h4><span
                                            style="width: 95%; display:block; line-height: 1.5; opacity: 0.8; font-size: 18px; font-weight: 400;">2972
                                            Westheimer Rd. Santa Ana, Illinois 85486 </span><span
                                            style="line-height:2.6; opacity: 0.8; font-size: 18px; font-weight: 400;">Phone
                                            : (219) 555-0114 </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table
                            style="width: 100%;border-collapse: separate;border-spacing: 0;border: 1px solid rgba(82, 82, 108, 0.1)">
                            <thead>
                                <tr
                                    style="background:#006666;border-radius: 8px;overflow: hidden;box-shadow: 0px 10.9412px 10.9412px rgba(82, 77, 141, 0.04), 0px 9.51387px 7.6111px rgba(82, 77, 141, 0.06), 0px 5.05275px 4.0422px rgba(82, 77, 141, 0.0484671);border-radius: 5.47059px;">
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Products</span></th>
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Quantity</span></th>
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Price</span></th>
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Unit</span></th>
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Vat %</span></th>
                                    <th style="padding: 18px 15px;text-align: left"><span
                                            style="color: #fff; font-size: 18px;">Total</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="invoice-table-data" style="box-shadow: 0px 1px 0px 0px rgba(82, 82, 108, 0.15);">
                                    <td style="padding: 18px 15px;display:flex;align-items: center;gap: 10px;"><span
                                            style="width: 54px; height: 51px; background-color:#F5F6F9; display: flex; justify-content: center;align-items: center; border-radius: 5px;"><img
                                                src="{{ asset('assets/images/ecommerce/product-categories/laptop.png') }}"
                                                alt="laptop" style="height:29px;"></span>
                                        <ul style="padding: 0;margin: 0;list-style: none;">
                                            <li>
                                                <h4 style="font-weight:400; margin:4px 0px; font-size: 18px;">Apple Desktop
                                                </h4><span style="opacity: 0.8; font-size: 14px;">#XDG-6437</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">2</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span style="font-size: 18px;">$100</span>
                                    </td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">Hour(s)</span></td>
                                    <td style="padding: 18px 15px; width: 10%;"> <span style="font-size: 18px;">0</span>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">$200</span></td>
                                </tr>
                                <tr style="box-shadow: 0px 1px 0px 0px rgba(82, 82, 108, 0.15);">
                                    <td style="padding: 18px 15px;display:flex;align-items: center;gap: 10px;"><span
                                            style="width: 54px; height: 51px;background-color:#F5F6F9; display: flex; justify-content: center;align-items: center; border-radius: 5px;"><img
                                                src="{{ asset('assets/images/ecommerce/product-categories/watch.png') }}"
                                                alt="laptop" style="height:29px;"></span>
                                        <div>
                                            <h4 style="font-weight:400; margin:4px 0px; font-size: 18px;">Smart Watch</h4>
                                            <span style="opacity: 0.8; font-size: 14px;">#XDG-6437</span>
                                        </div>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">1</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span style="font-size: 18px;">$200</span>
                                    </td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">Hour(s)</span></td>
                                    <td style="padding: 18px 15px; width: 10%;"> <span style="font-size: 18px;">0</span>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">$200 </span></td>
                                </tr>
                                <tr class="invoice-table-data"
                                    style="box-shadow: 0px 1px 0px 0px rgba(82, 82, 108, 0.15);">
                                    <td style="padding: 18px 15px;display:flex;align-items: center;gap: 10px;"><span
                                            style="width: 54px; height: 51px; background-color:#F5F6F9; display: flex; justify-content: center;align-items: center; border-radius: 5px;"><img
                                                src="{{ asset('assets/images/ecommerce/product-categories/phone.png') }}"
                                                alt="laptop" style="height:29px;"></span>
                                        <div>
                                            <h4 style="font-weight:400; margin:4px 0px; font-size: 18px;">Apple iphone 13
                                                Pro</h4><span style="opacity: 0.8; font-size: 14px;">#XDG-6437</span>
                                        </div>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">1</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">$10,000</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">Hour(s)</span></td>
                                    <td style="padding: 18px 15px; width: 10%;"> <span style="font-size: 18px;">0 </span>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">$10,000</span></td>
                                </tr>
                                <tr>
                                    <td style="padding: 18px 15px;display:flex;align-items: center;gap: 10px;"><span
                                            style="width: 54px; height: 51px; background-color:#F5F6F9; display: flex; justify-content: center;align-items: center; border-radius: 5px;"><img
                                                src="{{ asset('assets/images/ecommerce/product-categories/headphone.png') }}"
                                                alt="laptop" style="height:29px;"></span>
                                        <div>
                                            <h4 style="font-weight:400; margin:4px 0px; font-size: 18px;">Wireless
                                                Headphone</h4><span style="opacity: 0.8; font-size: 14px;">#XDG-6437</span>
                                        </div>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">2</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">$8,000</span></td>
                                    <td style="padding: 18px 15px; width: 12%;"> <span
                                            style="font-size: 18px;">Hour(s)</span></td>
                                    <td style="padding: 18px 15px; width: 10%;"> <span style="font-size: 18px;">0</span>
                                    </td>
                                    <td style="padding: 18px 15px;"><span style="font-size: 18px;">$16,000</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="float: right;">
                            <tfoot>
                                <tr>
                                    <td style="padding: 5px 24px 5px 0; padding-top: 15px; text-align: end;"> <span
                                            style=" font-size: 18px; font-weight: 400;">Subtotal </span><span
                                            style="margin-left: 8px; font-size: 18px;">:</span></td>
                                    <td style="padding: 5px 0;text-align: left;padding-top: 15px;"><span
                                            style="font-size: 18px;">$26,400.00</span></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 24px 5px 0; text-align: end;"> <span
                                            style=" font-size: 18px; font-weight: 400;">VAT (0%) </span><span
                                            style="margin-left: 8px; font-size: 18px;">:</span></td>
                                    <td style="padding: 5px 0;text-align: left;padding-top: 0;"><span
                                            style="font-size: 18px;">$0.00</span></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 24px 5px 0; text-align: end;"> <span
                                            style=" font-size: 18px; font-weight: 400;">Shipping Rate</span><span
                                            style="margin-left: 8px; font-size: 18px;">:</span></td>
                                    <td style="padding: 5px 0;text-align: left;padding-top: 0;"><span
                                            style="font-size: 18px;">$10.00</span></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 24px 22px 0;"> <span
                                            style="font-weight: 600; font-size: 20px; ">Total Amount</span><span
                                            style="margin-left: 8px;">:</span></td>
                                    <td style="padding: 12px 24px 22px 0;;text-align: right"><span
                                            style="font-weight: 500; font-size: 20px; color: #006666 ;">$26,410.00</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td> <span
                            style="display:block;background: rgba(82, 82, 108, 0.3);height: 1px;width: 100%;margin-bottom:24px;"></span>
                    </td>
                </tr>
                <tr>
                    <td><span style="display: flex; justify-content: end; gap: 15px;"><a
                                style="background: #006666 ; color:rgba(255, 255, 255, 1);border-radius: 10px;padding: 18px 27px;font-size: 16px;font-weight: 600;outline: 0;border: 0; text-decoration: none;"
                                href="#!" onclick="window.print();">Print Invoice<i class="icon-arrow-right"
                                    style="font-size:13px;font-weight:bold; margin-left: 10px;"></i></a><a
                                style="background: rgba(0, 102, 102, 0.2);color: #006666 ;border-radius: 10px;padding: 18px 27px;font-size: 16px;font-weight: 600;outline: 0;border: 0; text-decoration: none;"
                                href="{{ route('admin.invoice_2') }}" download="">Download<i class="icon-arrow-right"
                                    style="font-size:13px;font-weight:bold; margin-left: 10px;"> </i></a></span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <!-- calendar js-->
    <script src="{{ asset('assets/js/print.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
@endsection
