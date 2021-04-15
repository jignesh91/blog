@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $title }}
                    </div>
                </div>

                <div class="card-body">
                  {!! Form::model($formObj,['method' => $method,'files' => true, 'route' => [$action_url,$action_params],'class' => 'form form-group', 'id' => 'main-frm']) !!}

                  <input type="hidden" name="_token" value="{{ csrf_token() }}">

                  <div class="form-group">
                    {!! Form::text('title',null,['class' => 'form-control', 'data-required' => true,'placeholder'=>'Enter Title']) !!}

                  </div>

                  <div class="form-group">
                    {!! Form::textarea('description',null,['class' => 'form-control', 'data-required' => true,'rows'=>5]) !!}

                  </div>
                  <div class="form-group">
                    <?php
                        if($formObj->id >0)
                            $img = asset('/uploads/post/').'/'.$formObj->image;
                        else
                            $img = 'http://www.urbanui.com';
                    ?>
                  <input type="file"  data-default-file="{{ $img }}" name="image" accept="image/*" id="imgValidate">
                   <?php
                        if($formObj->id >0){
                            ?>
                            <img src="{{ $img }}" width="50">
                        <?php }?>
                  
                  </div>
                  <div class="form-group">
                    <div id="correction-div">
                            
                        </div>
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-success waves-effect waves-light" id="add-correction-fields">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                  </div>

                  <div class="form-group">

                    <input type="submit" value="{{ $buttonText}}" class="btn btn-gray pull-right" id="submit_id" />

                </div>
                <div>
                  
                </div>

            {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-growl/1.0.0/jquery.bootstrap-growl.min.js" integrity="sha512-pBoUgBw+mK85IYWlMTSeBQ0Djx3u23anXFNQfBiIm2D8MbVT9lr+IxUccP8AMMQ6LCvgnlhUCK3ZCThaBCr8Ng==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-growl/1.0.0/jquery.bootstrap-growl.js" integrity="sha512-4fpPq5LCcSAofCKmaM58RSbjpVRUqjx8nKAaBQVFay4MRo7FLafbt6bUNUfUbTZcSMzRNxZuVj3shwHA6ZeiOQ==" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#main-frm').submit(function () {

            if (true)
            {
                $('#submit_id').attr('disabled',true);
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function (result)
                    {
                        if (result.status == 1)
                        {
                            $.bootstrapGrowl(result.msg, {type: 'success', delay: 4000});

                            window.location = '{{ $list_url }}';    
                        }
                        else
                        {
                            $.bootstrapGrowl(result.msg, {type: 'danger', delay: 4000});
                            
                        }
                        $('#submit_id').attr('disabled',false);
                    },
                    error: function (error) {
                        $.bootstrapGrowl("Internal server error !", {type: 'danger', delay: 4000});
                        
                    }
                });
            }
            return false;
        });
    });

    var tags_label = '<?php echo __("Tags") ?>';
    
    var posts = `<?php echo (!empty($posts))?json_encode($posts):''; ?>`;
    posts = $.parseJSON(posts);


    $( document ).ready(function() {
        if (posts.length !== 0){
            $.each(posts, function (key, val){
                add_correction_field(val.tags);
            });
        }else{
            add_correction_field();
        }
    });
    

$(document).on('click',"#add-correction-fields", function(){
    add_correction_field();
});

function add_correction_field(origin_value = '', destination_value = '', correction_val = ''){
    var correction_fields = '';
    var value = '';
    $.each(posts, function (key, val){
                  if(origin_value == val.id){
                      value += 'value="'+val.tags+'"';
                  }else{
                      value += 'value="'+val.tags+'"';
                  }
                  
              });
    correction_fields =
    '<div class="row">'+
        '<div class="col-md-3">'+
            '<div class="form-group">'+
                '<label>'+tags_label+'</label>'+
                '<input name="tags[]" class="form-control" '+value+' required>'+
            '</div>'+
        '</div>'+
        '<div class="col-md-2">'+
        '<div class="form-group">'+
            '<button type="button" class="btn btn-danger waves-effect waves-light remove-correction-row">'+
                '<i class="fa fa-close"></i>'+
            '</button>'+
        '</div>'+
    '</div>';

    $( "#correction-div" ).append( correction_fields );
}

$(document).on('click','.remove-correction-row',function(){
    $(this).closest(".row").remove();
});
</script>
@endsection