@extends('dashboard.layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
           {{ trans('admin.admin') }} <i class="fa fa-angle-right margin-separator"></i> {{ trans('misc.purchases') }} ({{$data->total()}})
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

        	<div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">
                  		{{ trans('misc.purchases') }}
                  	</h3>
                </div><!-- /.box-header -->

                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
               <tbody>

               	@if( $data->total() !=  0 && $data->count() != 0 )
                   <tr>
                      <th class="active">ID</th>
                      <th class="active">{{ trans('misc.thumbnail') }}</th>
                      <th class="active">{{ trans('admin.title') }}</th>
                      <th class="active">{{ trans('misc.price') }}</th>
                      <th class="active">{{ trans('admin.date') }}</th>
                      <th class="active">{{ trans('admin.actions') }}</th>
                    </tr><!-- /.TR -->


                  @foreach( $data as $purchase )

                    @php

                    if(null !== $purchase->images()) {

                      $image_photo = asset('public/uploads/thumbnail').'/'.$purchase->images()->thumbnail;
                      $image_title = $purchase->images()->title;
                      $image_url   = url('photo', $purchase->images()->id);


                    } else {
                      $image_photo = null;
                      $image_title = trans('misc.not_available');
                      $image_url   = 'javascript:void(0);';

                    }

                    @endphp

                    <tr>
                      <td>{{ $purchase->id }}</td>
                      <td><img src="{{$image_photo}}" width="50" onerror="" /></td>
                      <td><a href="{{ $image_url }}" title="{{$image_title}}" target="_blank">{{ str_limit($image_title, 25, '...') }} <i class="fa fa-external-link-square"></i></a></td>
                      <td>{{ App\Helper::amountFormat($purchase->price) }}</td>
                      <td>{{ date('d M, Y', strtotime($purchase->date)) }}</td>
                      <td><a href="{{ $image_url }}" class="btn btn-success btn-sm padding-btn" target="_blank">
                         	{{ trans('misc.download') }} <i class="fa fa-external-link-square"></i>
                         	</a></td>
                    </tr><!-- /.TR -->
                    @endforeach

                    @else
                    <hr />
                    	<h3 class="text-center no-found">{{ trans('misc.no_results_found') }}</h3>

                    @endif

                  </tbody>

                  </table>

                </div><!-- /.box-body -->
              </div><!-- /.box -->
              @if( $data->lastPage() > 1 )
             {{ $data->links() }}
             @endif
            </div>
          </div>

          <!-- Your Page Content Here -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection
