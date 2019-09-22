@php
if ( !isset($input_attributes) ) {
$input_attributes = [];
}
if ( !isset($input_attributes['class'])){
$input_attributes['class'] = 'form-control input-'.$name;
}

@endphp

<div class="{{ $groupClass ?? 'form-group' }}">
    <label for="{{$name}}" class="label-{{$name}}">{!! $label ?? $helper->t($name) !!}</label>

    @if ( !isset($type) || $type == 'text')

    {!! Form::text($name, null, $input_attributes); !!}

    @elseif ( $type == 'textarea')

    {!! Form::textarea($name, null, $input_attributes); !!}

    @elseif ( $type == 'email')

    {!! Form::email($name, null, $input_attributes); !!}

    @elseif ( $type == 'number')

    {!! Form::number($name, null, $input_attributes); !!}

    @elseif ( $type == 'file')

    {{-- {!! Form::file($name, $input_attributes); !!} --}}

    <div class="input-group mb-3 input-group-{{$name}}">
        <div class="input-group-prepend">
            <span class="input-group-text text-secondary" ><i class="fas fa-upload"></i></span>
        </div>
        <div class="custom-file">
            <input type="file"
            name="{{$name}}"
            class="custom-file-input {{$input_attributes['class'] ?? ''}}"
            accept="{{$input_attributes['accept'] ?? '*'}}"
            id="input-{{$name}}"
            onchange="$(this).next().after().text($(this).val().split('\\').slice(-1)[0])">
            <label class="custom-file-label" for="input-{{$name}}">{{$helper->t('upload,file') }}</label>
        </div>
    </div>

    <input name='{{$name}}_id' type="hidden" class='input-{{$name}}_id'>

    <div class="form-group upload-img" id="preview-{{$name}}">
    @if ( isset($file_data) && property_exists($file_data, $name) && isset($file_data->$name) )
        <a href="{{$helper->imageUrl($file_data->$name, 'original','original') }}" target="_blank">
            <img class="img-fluid img-thumbnail p-1"
                src="{{$helper->imageUrl($file_data->$name, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}"
                style="max-height:{{$helper->s('file.small_image_height')}}px;" /></a>

        {{$helper->t('delete,image') }}: {!! Form::checkbox($name . '_delete', 1, $checked ?? false); !!}
    @endif
    </div>

    @elseif ( $type == 'checkbox')

    {!! Form::checkbox($name, 1, $checked ?? false); !!}

    @elseif ( $type == 'select')

    {!! Form::select($name, $options, null, $input_attributes); !!}

    @elseif ( $type == 'date')

    {!! Form::date($name, $value ?? null, $input_attributes); !!}

    @endif
</div>
