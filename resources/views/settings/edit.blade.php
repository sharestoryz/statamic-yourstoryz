@extends('statamic::layout')

@section('title', __('Edit YourStoryz settings'))

@section('content')
    <publish-form
        title="{{ __('Edit YourStoryz settings') }}"
        action="{{ cp_route('yourstoryz.settings.update') }}"
        method="patch"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    />
@endsection