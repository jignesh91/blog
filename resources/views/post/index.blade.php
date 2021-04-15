@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $module_title }}
                    <div class="card-body float-right">
                    @if($btnAdd)
                    <a class="btn btn-gray pull-right" href="{{$add_url}}">{{$addBtnName}}</a>
                    @endif
                    </div>
                </div>

                <div class="card-body">
                    @if ( !$posts->count() )
                    There is no post till now. write a new post now!!!
                    @else
                    <div class="">
                      @foreach( $posts as $post )
                      <div class="list-group">
                        <div class="list-group-item">
                          <h3><a href="{{ url('/') }}">{{ $post->title }}</a>
                            @if(!Auth::guest() && ($post->author_id == Auth::user()->id))
                              <button class="btn" style="float: right"><a href="{{ route('post.edit',['post' => $post->id])}}">Edit Post</a></button>
                              <button class="btn" style="float: right"><a data-id="{{ $post->id }}" href="{{url('post-delete/'.$post->id)}}"  title="Delete">Delete Post</a></button>
                            @endif
                          </h3>
                          <p>{{ $post->created_at->format('M d,Y \a\t h:i a') }} By <a href="{{ url('/user/'.$post->author_id)}}">{{ $post->author->name }}</a></p>
                        </div>
                        <div class="list-group-item">
                          <article>
                            <?php
                        if($post->id >0)
                            $img = asset('/uploads/post/').'/'.$post->image;
                        else
                            $img = 'http://www.urbanui.com';
                    ?>
    <img src="{{$img}}" width="100">
                            {!! Str::limit($post->description, $limit = 100, $end = '....... <a href='.url("post-view/".$post->id).'>Read More</a>') !!}
                          </article>
                        </div>
                      </div>
                      @endforeach
                      {!! $posts->render() !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection