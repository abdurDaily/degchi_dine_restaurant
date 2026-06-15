@php
    $s1 = request()->segment(1);
    $s2 = request()->segment(2);
    $s3 = request()->segment(3);

    $pageTitle = str()->headline($s1);
    $menus = [
        ['title'=> __('Dashboard') ,'link'=>url('dashboard')],
        ['title'=> __($pageTitle),'link'=>url($s1)],
    ];

    if(!is_numeric($s2) && !empty($s2))
    $menus[] = ['title' => str()->headline($s2), 'link' => 'javascript: void(0);'];

    if(!is_numeric($s3) && !empty($s3))
    $menus[] = ['title' => str()->headline($s3), 'link' => 'javascript: void(0);'];

@endphp

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ str()->plural($pageTitle) }} @if(!is_numeric($s2)) {{$s2}} @endif</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach ($menus as $menu)
                    <li class="breadcrumb-item active"><a href="{{$menu['link']}}">{{$menu['title']}}</a></li>
                    @endforeach
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->