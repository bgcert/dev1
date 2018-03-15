@extends('layouts.app')

@section('content')
<div class="container">
    <h2>events</h2>
    <table class="table table-bordered">
    @foreach($events as $e)
    
    	<tr>
			<td>#ID</td>
			<td>{{ $e->id }}</td>
			
		</tr>
		<tr>
			<td>Title</td>
			<td><a href="/event/{{ $e->id }}">{{ $e->theme->title }}</a></td>
			
		</tr>
		<tr>
			<td>cover</td>
			<td><img src="{{ $e->cover }}" style="width: 200px;"></td>
		</tr>
		<tr>
			<td>Body (from theme)</td>
			<td>{{ $e->theme->body }}</td>
		</tr>
		<tr>
			<td>Begin at:</td>
			<td>{{ $e->begin_at }}</td>
		</tr>
		<tr>
			<td>Test</td>
			<td>{{ $e->test }}</td>
		</tr>
		<tr>
			<td>Organization</td>
			<td><a href="/c/{{ $e->theme->company->slug }}">{{ $e->theme->company->name }}</a></td>
		</tr>
		<tr>
			<td></td>
			<td><a href="#" class="btn btn-primary">Request</a></td>
		</tr>
		<tr>
			<td colspan="2"></td>
		</tr>
	
    @endforeach
    </table>
</div>
@endsection