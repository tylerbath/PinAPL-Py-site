@php
	/* Provided
	$name 	The name of the parameter
	*/

	$type = $parameter['type'] ?? 'text';
	$label = $parameter['label'] ?? $name;
	$placeholder = $parameter['default'];
	$helpText = $parameter['help_text'];
	$rules = $parameter['rules'];
	$options = $parameter['options'] ?? [];
	$requiredText = !empty($required) ? "required" : "";

	if ($name == "LibFilename"){
		$options = [];
		foreach (config('pinapl_config.libraries') as $filename => $displayName) {
			$options[$filename] = $displayName;
		} 
		$options['custom'] = 'Upload a Custom Library File';
	}

@endphp

<div class="column">
	<label for="#{{$name}}-input">{{ $label }}</label>
	@if ($type == 'select')
		<select name="{{ $name }}" id="{{$name}}-input" {{ $requiredText }}>
			@foreach ($options as $value => $displayName)
				<option value="{{ $value }}">{{ $displayName }}</option>
			@endforeach
		</select>
	@else
		<input id="{{$name}}-input" type="{{$type}}" name="{{$name}}" placeholder="{{$placeholder}}" title="{{$helpText}}" {{ $requiredText }}>
	@endif
</div>