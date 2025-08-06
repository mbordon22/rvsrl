@extends('layouts.simple.master')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/dropzone.css') }}">
@endsection

@section('main_content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Parametros Contables</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Parametros Contables</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Parametros Contables</h4>
                </div>
                <div class="card-body">
                    <div class="form theme-form">
                        <form class="row g-3 custom-input" id="parametrosForm"
                            action="{{ route('admin.gestion-contable.parametros.update', $parametros->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.gestion_contable.parametros.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- calendar js-->
<script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.es.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}"></script>
<script>
    var cleave = new Cleave("#n_recibo_proximo", {
        delimiters: ["-", "-"],
        blocks: [1, 5, 8],
        uppercase: true,
        numericOnly: false // permitimos letras en el primer bloque
    });

    document.getElementById('n_recibo_proximo').addEventListener('input', function(e) {
        let val = e.target.value.toUpperCase();
        let match = val.match(/^([ABCX])-(\d{0,5})-(\d{0,8})$/);
        if (!match) {
            // Si no coincide, intenta limpiar la entrada
            let parts = val.split('-');
            let first = parts[0] ? parts[0].replace(/[^ABCX]/g, '').charAt(0) || '' : '';
            let second = parts[1] ? parts[1].replace(/\D/g, '') : '';
            let third = parts[2] ? parts[2].replace(/\D/g, '') : '';
            let newVal = first;
            if (second.length > 0) newVal += '-' + second;
            if (third.length > 0) newVal += '-' + third;
            e.target.value = newVal;
        }
    });
</script>
@endsection