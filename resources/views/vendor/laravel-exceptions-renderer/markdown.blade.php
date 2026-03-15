# {{ $exception->class() }} - {!! $exception->title() !!}

{!! $exception->message() !!}

PHP {{ PHP_VERSION }}
Laravel {{ app()->version() }}
{{ $exception->request()->httpHost() }}

## Stack Trace

@php
    $frames = $exception->frames();
    $idx = 0;
    foreach ($frames as $f) {
        echo $idx . ' - ' . e($f->file()) . ':' . e($f->line()) . "\n";
        $idx++;
    }
@endphp

## Request

{{ $exception->request()->method() }} {{ \Illuminate\Support\Str::start($exception->request()->path(), '/') }}

## Headers

@php
    $requestHeaders = $exception->requestHeaders();
@endphp
@if(count($requestHeaders) > 0)
@php
    foreach ($requestHeaders as $hk => $hv) {
        echo "* **" . e($hk) . "**: " . e($hv) . "\n";
    }
@endphp
@else
No header data available.
@endif

## Route Context

@php
    $routeContext = $exception->applicationRouteContext();
@endphp
@if(count($routeContext) > 0)
@php
    foreach ($routeContext as $rn => $rv) {
        echo e($rn) . ": " . e($rv) . "\n";
    }
@endphp
@else
No routing data available.
@endif

## Route Parameters

@php
    $routeParametersContext = $exception->applicationRouteParametersContext();
@endphp
@if ($routeParametersContext)
{!! $routeParametersContext !!}
@else
No route parameter data available.
@endif

## Database Queries

@php
    $queries = $exception->applicationQueries();
@endphp
@if(count($queries) > 0)
@php
    foreach ($queries as $q) {
        $conn = $q['connectionName'] ?? '';
        $sql = $q['sql'] ?? '';
        $time = $q['time'] ?? 0;
        echo "* " . e($conn) . " - " . e($sql) . " (" . e($time) . " ms)\n";
    }
@endphp
@else
No database queries detected.
@endif
