                <div class="media user-media">
                    <a class="user-link" href="{!! URL::route('users.profile') !!}">
                        <span class="fa fa-user fa-4x user-img"></span>
                    </a>
                    <div class="media-body">
                        <h5 class="media-heading">{!! $user->first_name . ' ' . $user->last_name !!}</h5>
                        <ul class="list-unstyled user-info">
                            @foreach( $user->roles as $role )
                                <li>{{  $role->name }}</li>
                            @endforeach
                            <li style="font-size: 12px;">Logged in since
                                <br>
                                <small> {!! $logged_in_for !!}</small>
                            </li>
                        </ul>
                    </div>
                </div>

                <ul id="menu" class="collapse">
                    <li class="nav-header">Menu</li>
                    {{--<li class="nav-divider"></li>--}}
                    @if ( \Kjamesy\Cms\Models\User::canManageContent($user) )
                        <li class="{!! $active=='index' ? 'active' : '' !!}">
                            <a href="{!! URL::route('admin') !!}"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a>
                        </li>
                        <li class="{!! $activeParent=='pages' ? 'active' : '' !!}">
                            <a href=""><i class="fa fa-folder-open"></i>&nbsp;Pages <span class="fa arrow"></span> </a>
                            <ul>
                                <li class="{!! $active=='allpages' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('pages.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Pages
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! $activeParent=='posts' ? 'active' : '' !!}">
                            <a href="javascript:;"><i class="fa fa-quote-left"></i>&nbsp;Posts <span class="fa arrow"></span> </a>
                            <ul>
                                <li class="{!! $active=='allposts' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('posts.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Posts
                                    </a>
                                </li>
                                <li class="{!! $active=='categories' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('categories.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;Categories
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! $activeParent=='locales' ? 'active' : '' !!}">
                            <a href="javascript:;"><i class="fa fa-language"></i>&nbsp;Languages <span class="fa arrow"></span> </a>
                            <ul>
                                <li class="{!! $active=='alllocales' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('locales.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Languages
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! $activeParent=='galleries' ? 'active' : '' !!}">
                            <a href="javascript:;"><i class="fa fa-picture-o"></i>&nbsp;Galleries <span class="fa arrow"></span> </a>
                            <ul>
                                <li class="{!! $active=='allgalleries' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('galleries.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Galleries
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{!! $activeParent=='events' ? 'active' : '' !!}">
                            <a href="javascript:;"><i class="fa fa-calendar"></i>&nbsp;Events <span class="fa arrow"></span> </a>
                            <ul>
                                <li class="{!! $active=='allevents' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('events.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Events
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if ( Config::get('cms::config')['has_extensions'] )
                            @foreach ( Config::get('cms::config')['cms_extensions'] as $key => $extension )
                                <li class="{!! $activeParent == strtolower($key) ? 'active' : '' !!}">
                                    <a href="javascript:;"><i class="{!! $extension['font-awesome-class'] !!}"></i>&nbsp;{!! Str::title($key) !!} <span class="fa arrow"></span> </a>
                                    <ul>
                                        @foreach ( $extension['children'] as $child )
                                            <li class="{!! $active == Str::slug($child['name']) ? 'active' : '' !!}">
                                                <a href="{!! URL::route($child['route-name']) !!}">
                                                    <i class="fa fa-angle-right"></i>&nbsp; {!! $child['name'] !!}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        @endif
                    @endif
                    <li class="{!! $activeParent=='users' ? 'active' : '' !!}">
                        <a href="javascript:;"><i class="fa fa-user"></i>&nbsp;Users <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{!! $active=='profile' ? 'active' : '' !!}">
                                <a href="{!!URL::route('users.profile') !!}">
                                    <i class="fa fa-angle-right"></i>&nbsp;Your Profile
                                </a>
                            </li>
                            @if ( $isAdmin )
                                <li class="{!! $active=='allusers' ? 'active' : '' !!}">
                                    <a href="{!! URL::route('users.landing') !!}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Users
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul><!-- /#menu -->