@extends('errors::minimal')
@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))

@if(app()->bound('sentry') && app('sentry')->getLastEventId())
    <script src="https://browser.sentry-cdn.com/5.6.3/bundle.min.js" integrity="sha384-/Cqa/8kaWn7emdqIBLk3AkFMAHBk0LObErtMhO+hr52CntkaurEnihPmqYj3uJho" crossorigin="anonymous"></script>
    <script>
        Sentry.init({ dsn: '{{ config('sentry.dsn') }}' });
        Sentry.showReportDialog({ eventId: '{{ app('sentry')->getLastEventId() }}' });
    </script>
@endif
