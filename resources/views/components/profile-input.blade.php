@props(['name', 'label', 'value' => null, 'type' => 'text', 'required' => false, 'options' => [], 'hint' => null])

@php
    $fieldValue = $attributes->has('readonly') ? $value : old($name, $value);
    $control = 'min-h-11 w-full rounded-lg border border-slate-500 bg-white px-3 py-2 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-slate-900 dark:text-white dark:focus-visible:outline-teal-400';
    $description = trim(($hint ? $name.'-hint ' : '').($errors->has($name) ? $name.'-error' : ''));
@endphp

<div class="min-w-0">
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">
        {{ $label }} @if($required)<span aria-hidden="true">*</span>@endif
    </label>
    @if($options)
        <select id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->class($control) }}
            @if($description) aria-describedby="{{ $description }}" @endif @error($name) aria-invalid="true" @enderror>
            <option value="">Pilih {{ mb_strtolower($label) }}</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @selected($fieldValue === $key)>{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" @if($type !== 'password') value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}" @endif
            @required($required) {{ $attributes->class($control) }}
            @if($description) aria-describedby="{{ $description }}" @endif @error($name) aria-invalid="true" @enderror>
    @endif
    @if($hint)
        <p id="{{ $name }}-hint" class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-rose-800 dark:text-rose-300">{{ $message }}</p>
    @enderror
</div>
