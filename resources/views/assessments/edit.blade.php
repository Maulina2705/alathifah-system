@extends('layouts.app', ['title' => 'Input Nilai - ' . $assessment->student->name])

@section('content')
    <livewire:score-input :assessmentId="$assessment->id" />
@endsection
