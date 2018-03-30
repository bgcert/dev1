<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
            	<li><a class="nav-link" href="/e">events</a></li>
    			<li><a class="nav-link" href="/v">venues</a></li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                    <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                @else
                    <li class="nav-item dropdown">
                    	@if(Auth::user()->role_id == 3)
                    	<li><a class="nav-link" href="/admin/activity">Admin</a></li>
                    	@endif
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        	<a class="dropdown-item" href="/users/">
                               Account
                            </a>
                            @if(isset(Auth::user()->company))
                        	<a class="dropdown-item" href="/dashboard">
                               Dashboard
                            </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<hr>

<div class="ui container">
	<div class="ui menu">
		<a class="active item">
			Seminari 365
		</a>
		<a class="item">
			Обучения
		</a>
		<div class="right menu">
			<div class="ui dropdown item">
				Потребител <i class="dropdown icon"></i>
				<div class="menu">
					<a class="item">English</a>
					<a class="item">Russian</a>
					<a class="item">Spanish</a>
				</div>
			</div>
			<div class="item">
				<div class="ui primary button">Регистрация</div>
			</div>
		</div>
	</div>
</div>
