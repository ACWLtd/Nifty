                <div class="media user-media">
                    <a class="user-link" href="{{ URL::route('users.profile') }}">
                        <span class="fa fa-user fa-4x img-thumbnail user-img text-danger"></span>
                    </a>
                    <div class="media-body">
                        <h5 class="media-heading">{{ $user->first_name . ' ' . $user->last_name }}</h5>
                        <ul class="list-unstyled user-info">
                            <li><span class="label label-danger">{{ $user->getGroups()[0]->name; }}</span></li>
                            <li>You logged in :
                                <br>
                                <small><i class="fa fa-calendar"></i>&nbsp; {{ $logged_in_for }}</small>
                            </li>
                        </ul>
                    </div>
                </div>

                <ul id="menu" class="collapse">
                    <li class="nav-header">Menu</li>
                    <li class="nav-divider"></li>
                    <li class="{{ $active=='index' ? 'active' : '' }}">
                        <a href="{{ URL::route('admin') }}"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a>
                    </li>
                    <li class="{{ $activeParent=='pages' ? 'active' : '' }}">
                        <a href=""><i class="fa fa-folder-open"></i>&nbsp;Pages <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{{ $active=='allpages' ? 'active' : '' }}">
                                <a href="{{ URL::route('pages.landing') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;All Pages
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $activeParent=='posts' ? 'active' : '' }}">
                        <a href="javascript:;"><i class="fa fa-quote-left"></i>&nbsp;Posts <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{{ $active=='allposts' ? 'active' : '' }}">
                                <a href="{{ URL::route('posts.landing') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;All Posts
                                </a>
                            </li>
                            <li class="{{ $active=='categories' ? 'active' : '' }}">
                                <a href="{{ URL::route('categories.landing') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;Categories
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $activeParent=='locales' ? 'active' : '' }}">
                        <a href="javascript:;"><i class="fa fa-language"></i>&nbsp;Languages <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{{ $active=='alllocales' ? 'active' : '' }}">
                                <a href="{{ URL::route('locales.landing') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;All Languages
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $activeParent=='events' ? 'active' : '' }}">
                        <a href="javascript:;"><i class="fa fa-calendar"></i>&nbsp;Events <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{{ $active=='allevents' ? 'active' : '' }}">
                                <a href="{{ URL::route('events.landing') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;All Events
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $activeParent=='users' ? 'active' : '' }}">
                        <a href="javascript:;"><i class="fa fa-user"></i>&nbsp;Users <span class="fa arrow"></span> </a>
                        <ul>
                            <li class="{{ $active=='profile' ? 'active' : '' }}">
                                <a href="{{URL::route('users.profile') }}">
                                    <i class="fa fa-angle-right"></i>&nbsp;Your Profile
                                </a>
                            </li>
                            @if ( $isAdmin )
                                <li class="{{ $active=='allusers' ? 'active' : '' }}">
                                    <a href="{{ URL::route('users.landing') }}">
                                        <i class="fa fa-angle-right"></i>&nbsp;All Users
                                    </a>
                                </li>
                                {{--<li class="{{ $active=='createuser' ? 'active' : '' }}">--}}
                                    {{--<a href="{{ URL::route('sentinel.users.create') }}">--}}
                                        {{--<i class="fa fa-angle-right"></i>&nbsp;New User--}}
                                    {{--</a>--}}
                                {{--</li>--}}

                            @endif
                        </ul>
                    </li>
                </ul><!-- /#menu -->