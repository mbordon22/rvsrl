<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>N° de Recibo Próximo<span> *</span></label>
                <input class="form-control" type="text" name="n_recibo_proximo" id="n_recibo_proximo"
                    value="{{ isset($parametros->n_recibo_proximo) ? $parametros->n_recibo_proximo : old('n_recibo_proximo') }}"
                    placeholder="Ingrese el n° del recibo proximo">
                @error('n_recibo_proximo')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        {{-- <div class="col-sm-6">
            <div class="mb-3">
                <label>Comprobante de Egreso Próximo<span> *</span></label>
                <input class="form-control" type="text" name="comprobante_egreso_proximo"
                    value="{{ isset($parametros->comprobante_egreso_proximo) ? $parametros->comprobante_egreso_proximo : old('comprobante_egreso_proximo') }}"
                    placeholder="Ingrese el comprobante de egreso proximo">
                @error('comprobante_egreso_proximo')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="col">
            <div class="text-end">
                <button type="submit" class="btn btn-success">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
