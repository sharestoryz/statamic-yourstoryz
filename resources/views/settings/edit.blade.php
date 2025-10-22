@extends('statamic::layout')

@section('title', __('YourStoryz config'))

@section('content')
    <publish-form
        :title="__('YourStoryz config')"
        method="patch"
        action="{{ cp_route('yourstoryz.update') }}"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>
@endsection