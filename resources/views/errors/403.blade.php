@extends('errors.layout')

@section('code', '403')
@section('title', 'Access Denied')
@section('message', $exception?->getMessage() ?: 'You do not have permission to access this page. If you believe this is a mistake, please contact your administrator.')
