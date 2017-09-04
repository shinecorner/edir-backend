<div class="mobile-menu-left-overlay"></div>
<nav class="side-menu">
    <ul class="side-menu-list">
        <li class="grey">
            <a href="{!! route('dashboard') !!}" class="label-right">
                <i class="font-icon font-icon-home"></i>
                <span class="lbl">Dashboard</span>
            </a>
        </li>

		<li class="grey">
			<a href="{!! route('directory') !!}" {!! request()->is('directory/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-revers"></i>
				<span class="lbl">Directories</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('audit') !!}" {!! request()->is('audit*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-editor-list"></i>
				<span class="lbl">Audit</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('company') !!}" {!! request()->is('firmen/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-build"></i>
				<span class="lbl">Firmen</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('event') !!}" {!! request()->is('event/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-pin-2"></i>
				<span class="lbl">Events</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('deal') !!}" {!! request()->is('deal/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-wallet"></i>
				<span class="lbl">Deals</span>
			</a>
		</li>
		
		<li class="grey">
			<a href="{!! route('rating') !!}" {!! request()->is('ratings/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-star"></i>
				<span class="lbl">Ratings</span>
			</a>
		</li>

		<li class="grey">
			<a href="{!! route('blog') !!}" {!! request()->is('blog/*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-notebook"></i>
				<span class="lbl">Blog</span>
			</a>
		</li>

        <li class="grey">
			<a href="{!! route('user') !!}" {!! request()->is('benutzer/*') ? 'class="active"' : '' !!}>
                <i class="font-icon font-icon-user"></i>
                <span class="lbl">Mitarbeiter</span>
            </a>
        </li>

		<li class="grey">
			<a href="{!! route('messages') !!}" {!! request()->is('messages*') ? 'class="active"' : '' !!}>
				<i class="font-icon font-icon-help"></i>
				<span class="lbl">Support
					@if(auth()->user()->newThreadsCount())
						<span class="label label-danger">{{ auth()->user()->newThreadsCount() }}</span>
					@endif
				</span>
			</a>
		</li>

		<li class="grey with-sub {!! request()->is('kategorien/*') ? 'opened' : '' !!}">
			<span>
				<i class="font-icon font-icon font-icon-widget"></i>
				<span class="lbl">Kategorien</span>
			</span>
			<ul {!! request()->is('kategorien/*') ? 'style="display:block;"' : '' !!}>
				<li>
					<a href="{!! route('category.primary') !!}" {!! request()->is('kategorien/hauptkategorien*') ? 'class="active"' : '' !!}>
						<span class="lbl">Hauptkategorien</span>
					</a>
				</li>
				<li>
					<a href="{!! route('category.secondary') !!}" {!! request()->is('kategorien/unterkategorien*') ? 'class="active"' : '' !!}>
						<span class="lbl">Unterkategorien</span>
					</a>
				</li>
				<li>
					<a href="{!! route('category.event') !!}" {!! request()->is('kategorien/eventkategorien*') ? 'class="active"' : '' !!}>
						<span class="lbl">Eventkategorien</span>
					</a>
				</li>
				<li>
					<a href="{!! route('category.deal') !!}" {!! request()->is('kategorien/dealkategorien*') ? 'class="active"' : '' !!}>
						<span class="lbl">Dealkategorien</span>
					</a>
				</li>
			</ul>
		</li>
    </ul>
</nav>