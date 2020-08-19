<nav-vue class="navbar navbar-expand-md navbar-light bg-white shadow-sm"
    inline-template>

    <div class="container" ref="container">
        <a class="navbar-brand"
            :class="currentWidth < 768 ? 'flex' : ''"
            href="{{ url('/') }}">

            {{ config('app.name', 'Laravel') }}

        </a>
        <button class="navbar-toggler"
            ref="navbarToggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      瀏覽文章
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('thread.index') }}">所有文章</a>
                        <a class="dropdown-item" href="{{ route('thread.index', ['popular' => 1]) }}">熱門文章</a>
                        <a class="dropdown-item" href="{{ route('thread.index', ['unanswered' => 1]) }}">未回覆文章</a>
                        @if (auth()->check())
                            <a class="dropdown-item" href="{{ route('thread.index', ['by' => auth()->user()->name]) }}">我的文章</a>
                        @endif
                    </div>
                  </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('thread.create') }}">發表文章</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      文章類型
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach($channels as $channel)
                            <a class="dropdown-item" href="{{ route('thread.indexWithChannel', ['channel' => $channel->slug]) }}">{{ $channel->slug }}</a>
                        @endforeach
                    </div>
                  </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto"
                :class="currentWidth < 768 ? '' :  'align-items-center'">

                {{-- search bar --}}
                <li class="nav-item mr-5">
                    <div class="level" ref="searchBar">
                        <search-modal @resize="checkWidth"></search-modal>
                    </div>
                </li>


                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('登入') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('註冊') }}</a>
                        </li>
                    @endif
                @else

                    <user-notification></user-notification>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="{{ route('profile.show', auth()->user()) }}">
                                個人檔案
                            </a>

                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('登出') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav-vue>
