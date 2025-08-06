<!-- Page Header Start-->
<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative">
                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"
                            placeholder="Search Riho .." name="q" title="" autofocus>
                        <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...
                            </span></div><i class="close-search" data-feather="x"></i>
                    </div>
                    <div class="Typeahead-menu"> </div>
                </div>
            </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"> <a href="{{ route('admin.dashboard') }}"><img class="img-fluid for-light"
                        src="{{ asset('assets/images/logo/logo_sidebar.jpg') }}" style="height: 50px" alt="logo-light"><img
                        class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_sidebar.jpg') }}" style="height: 50px" alt="logo-dark"></a>
            </div>
            <div class="toggle-sidebar"> <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
            </div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                {{-- <li class="d-md-block d-none">
                    <div class="form search-form mb-0">
                        <div class="input-group"><span class="input-icon">
                                <svg>
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#search-header') }}"></use>
                                </svg>
                                <input class="w-100" type="search" placeholder="Buscar"></span></div>
                    </div>
                </li> --}}
                {{-- <li class="d-md-none d-block">
                    <div class="form search-form mb-0">
                        <div class="input-group"> <span class="input-show">
                                <svg id="searchIcon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#search-header') }}"></use>
                                </svg>
                                <div id="searchInput">
                                    <input type="search" placeholder="Buscar">
                                </div>
                            </span></div>
                    </div>
                </li> --}}
                {{-- <li>
                    <div class="mode"><i class="moon" data-feather="moon"> </i></div>
                </li>
                <li class="onhover-dropdown notification-down">
                    <div class="notification-box">
                        <svg>
                            <use href="{{ asset('assets/svg/icon-sprite.svg#notification-header') }}"></use>
                        </svg><span class="badge rounded-pill badge-secondary">4 </span>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <div class="card mb-0">
                            <div class="card-header">
                                <div class="common-space">
                                    <h4 class="text-start f-w-600">Notitications</h4>
                                    <div><span>4 New</span></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="notitications-bar">
                                    <ul class="nav nav-pills nav-primary p-0" id="pills-tab" role="tablist">
                                        <li class="nav-item p-0"> <a class="nav-link active" id="pills-aboutus-tab"
                                                data-bs-toggle="pill" href="#pills-aboutus" role="tab"
                                                aria-controls="pills-aboutus" aria-selected="true">All(3)</a>
                                        </li>
                                        <li class="nav-item p-0"> <a class="nav-link" id="pills-blog-tab"
                                                data-bs-toggle="pill" href="#pills-blog" role="tab"
                                                aria-controls="pills-blog" aria-selected="false">
                                                Messages</a></li>
                                        <li class="nav-item p-0"> <a class="nav-link" id="pills-contactus-tab"
                                                data-bs-toggle="pill" href="#pills-contactus" role="tab"
                                                aria-controls="pills-contactus" aria-selected="false">
                                                Cart </a></li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade" id="pills-blog" role="tabpanel"
                                            aria-labelledby="pills-blog-tab">
                                            <div class="notification-card">
                                                <ul>
                                                    <li
                                                        class="notification d-flex w-100 justify-content-between align-items-center">
                                                        <div
                                                            class="d-flex w-100 notification-data justify-content-center align-items-center gap-2">
                                                            <div class="user-alerts flex-shrink-0"><img
                                                                    class="rounded-circle img-fluid img-40"
                                                                    src="{{ asset('assets/images/dashboard/user/3.jpg') }}"
                                                                    alt="user" /></div>
                                                            <div class="flex-grow-1">
                                                                <div class="common-space user-id w-100">
                                                                    <div class="common-space w-100"> <a
                                                                            class="f-w-500 f-12" href="#">Robert
                                                                            D. Hambly</a></div>
                                                                </div>
                                                                <div><span class="f-w-500 f-light f-12">Hello
                                                                        Miss...😊</span></div>
                                                            </div><span class="f-light f-w-500 f-12">44
                                                                sec</span>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-contactus" role="tabpanel"
                                            aria-labelledby="pills-contactus-tab">
                                            <div class="cart-dropdown mt-4">
                                                <ul>

                                                    <li class="pr-0 pl-0 pb-3 pt-3">
                                                        <div class="media"><img class="img-fluid b-r-5 me-3 img-60"
                                                                src="{{ asset('assets/images/other-images/receiver-img.jpg') }}"
                                                                alt="">
                                                            <div class="media-body"><a class="f-light f-w-500"
                                                                    href="#">Men Cotton
                                                                    Blend Blue T-Shirt</a>
                                                                <div class="qty-box">
                                                                    <div class="input-group"> <span
                                                                            class="input-group-prepend">
                                                                            <button class="btn quantity-left-minus"
                                                                                type="button" data-type="minus"
                                                                                data-field="">- </button></span>
                                                                        <input class="form-control input-number"
                                                                            type="text" name="quantity" value="1"><span
                                                                            class="input-group-prepend">
                                                                            <button class="btn quantity-right-plus"
                                                                                type="button" data-type="plus"
                                                                                data-field="">+</button></span>
                                                                    </div>
                                                                </div>
                                                                <h6 class="font-primary">$695.00</h6>
                                                            </div>
                                                            <div class="close-circle"><a class="bg-danger" href="#"><i
                                                                        data-feather="x"></i></a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="mb-3 total"><a href="#">
                                                            <h6 class="mb-0">
                                                                Order Total :<span class="f-right">$1195.00
                                                                </span></h6>
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-footer pb-0 pr-0 pl-0">
                                            <div class="text-center"> <a class="f-w-700" href="#">
                                                    <button class="btn btn-primary" type="button"
                                                        title="btn btn-primary">Check all</button></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                <li class="profile-nav onhover-dropdown">
                    <div class="media profile-media">
                        @php
                            $media = auth()?->user()?->getFirstMedia('image');
                        @endphp
                        @if ($media)
                            <img src="{{ $media->getUrl() }}" alt="Imagen Perfil" class="b-r-10 img-40 rounded-circle">
                        @else
                            <img src="{{ asset('assets/images/dashboard/profile.png') }}" alt="Imagen Perfil"  class="b-r-10 img-40 rounded-circle">
                        @endif
                        <div class="media-body d-xxl-block d-none box-col-none">
                            <div class="d-flex align-items-center gap-2"> <span>{{ ucfirst(auth()?->user()?->first_name)
                                    }} </span><i class="middle fa fa-angle-down"> </i></div>
                            <p class="mb-0 font-roboto">{{ auth()?->user()->role->name }}</p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ route('admin.user.edit-profile',auth()->user()->role->name) }}"><i
                                    data-feather="user"></i><span>Mi Perfil</span></a>
                        </li>
                        {{-- <li><a href="#"><i data-feather="mail"></i><span>Inbox</span></a></li>
                        <li> <a href="#"> <i data-feather="settings"></i><span>Settings</span></a></li> --}}
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="btn btn-pill btn-outline-primary btn-sm">
                                Log Out
                            </a>
                            <form action="{{route('logout')}}" method="POST" class="d-none" id="logout-form">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">                        
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details"> 
            <div class="ProfileCard-realName">name</div>
            </div> 
            </div>
          </script>
        <script class="empty-template" type="text/x-handlebars-template">
            <div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div>
        </script>
    </div>
</div>
<!-- Page Header Ends-->