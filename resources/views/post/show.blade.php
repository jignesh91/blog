@extends('layouts.app')
@section('content')
  @if($post)
    {{ $post->title }}
    @if(!Auth::guest() && ($post->author_id == Auth::user()->id))
      <button class="btn" style="float: right"><a href="{{ url('edit/'.$post->slug)}}">Edit Post</a></button>
    @endif
  @else
    Page does not exist
  @endif
<p>{{ $post->created_at->format('M d,Y \a\t h:i a') }} By <a href="{{ url('/user/'.$post->author_id)}}">{{ $post->author->name }}</a></p>
@if($post)
  <div>
    {!! $post->description !!}
    <?php
                        if($post->id >0)
                            $img = asset('/uploads/post/').'/'.$post->image;
                        else
                            $img = 'http://www.urbanui.com';
                    ?>
    <img src="{{$img}}" width="100">
  </div>    
@endif
@endsection