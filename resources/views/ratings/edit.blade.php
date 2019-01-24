@extends('layouts.master')
@extends('layouts.validator')


@section('title', 'Bewertung bearbeiten')


@section('class', 'ingredient form')
@section('content')
    {!! Form::open(['url' => 'ratings/edit/' . $rating->id]) !!}
        <div>
            {!! Form::label('Kriterium') !!}
            {!! Form::select('rating_criterion_id', $ratingCriteria, $rating->rating_criterion_id) !!}
        </div>

        <div>
            {!! Form::label('Kommentar') !!}
            {!! Form::textarea('comment', $rating->comment, ['maxlength' => 16777215, 'required']) !!}
        </div>

        <div>
            {!! Form::submit('Bewertung hinzufügen') !!}
        </div>
    {!! Form::close() !!}
@stop
