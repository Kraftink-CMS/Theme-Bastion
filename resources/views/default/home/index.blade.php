@extends('default.layouts.layout')
@use('App\Kraft\SiteDates')
@use('App\Kraft\SocialLinks')
@section('content_full_width', true)

@section('content')
    <div class="bastion-home">
        <div class="container">
            <x-extensions.slot name="site.home.before" :context="['news' => $news]" />
        </div>
        <section class="bastion-hero" aria-labelledby="bastion-title">
            <div class="bastion-hero-media" aria-hidden="true">
                <img src="{{ route('site.extensions.assets.show', ['id' => $themeManifest['id'], 'asset' => 'images/relay.webp']) }}"
                    alt="" width="1672" height="941" fetchpriority="high">
            </div>
            <div class="bastion-hero-inner container">
                <div class="bastion-hero-content">
                    <h1 id="bastion-title">
                        @if ($logo = $publicSite['logo'])
                            <img class="bastion-hero-logo" src="{{ route('site.media.show', $logo) }}"
                                width="{{ $logo->width }}" height="{{ $logo->height }}"
                                alt="{{ $publicSite['homeTitle'] ?: $siteName }}">
                        @else
                            <span class="bastion-hero-title">{{ $publicSite['homeTitle'] ?: $siteName }}</span>
                        @endif
                    </h1>
                    <p class="bastion-hero-description">
                        {{ $publicSite['homeDescription'] ?: $publicSite['description'] ?: __('theme::messages.home.welcome.description') }}
                    </p>
                    <div class="bastion-hero-actions actions">
                        @if ($publicSite['communityAddress'])
                            <a class="btn btn-primary" href="#community-connection">
                                {{ __('theme::messages.home.welcome.join') }} <i class="bi bi-arrow-right"
                                    aria-hidden="true"></i></a>
                        @elseif (auth()->check())
                            <a class="btn btn-primary" href="{{ route('account.index') }}">
                                {{ __('site.auth.account.title') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @elseif ($capabilities->canRegister)
                            <a class="btn btn-primary" href="{{ route('register') }}">
                                {{ __('site.home.join_us') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @else
                            <a class="btn btn-primary" href="{{ route('login') }}">
                                {{ __('common.labels.login') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @endif
                        @if ($capabilities->hasNews)
                            <a class="btn btn-outline-primary" href="#news">
                                {{ __('theme::messages.home.news.title') }} <i class="bi bi-arrow-down"
                                    aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="bastion-home-content container">
            @include('default.home.partials.slider')
            @include('default.home.partials.welcome')

            <div class="bastion-station-grid">
                @if ($capabilities->hasNews)
                    <section class="bastion-journal" id="news" aria-labelledby="news-heading">
                        <div class="bastion-panel-heading">
                            <p class="bastion-panel-index"><span aria-hidden="true">01 /</span>
                                {{ __('theme::messages.home.news.kicker') }}</p>
                            <h2 id="news-heading">{{ __('theme::messages.home.news.title') }}</h2>
                            <a class="bastion-panel-link" href="{{ route('site.news.index') }}">
                                {{ __('site.home.all_news') }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        </div>
                        <div id="home-news-list" data-ajax-pagination tabindex="-1">
                            <div class="bastion-news-grid">
                                @foreach ($news as $entry)
                                    @php
                                        $cover = $entry->coverAsset();
                                    @endphp
                                    <article class="bastion-news-tile">
                                        <a class="bastion-news-tile-link" href="{{ $entry->url() }}">
                                            @if ($cover)
                                                <img class="bastion-news-tile-image" src="{{ $entry->coverUrl() }}"
                                                    alt="" width="{{ $cover->width }}"
                                                    height="{{ $cover->height }}" loading="lazy">
                                            @endif
                                            <div class="bastion-news-tile-copy">
                                                <p class="bastion-news-tile-meta">
                                                    <time datetime="{{ $entry->published_at->toAtomString() }}">
                                                        {{ app(SiteDates::class)->format($entry->published_at, 'd F Y') }}</time>
                                                    @if ($entry->is_pinned)
                                                        <span>{{ __('site.content.pinned') }}</span>
                                                    @endif
                                                </p>
                                                <h3>{{ $entry->title }}</h3>
                                            </div>
                                            <i class="bastion-news-tile-arrow bi bi-arrow-up-right" aria-hidden="true"></i>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                            @if ($news->hasPages())
                                <div class="mt-4">{{ $news->links() }}</div>
                            @endif
                        </div>
                    </section>
                @endif

                @php
                    $accountShortcut = auth()->check()
                        ? [
                            'url' => route('account.index'),
                            'label' => __('site.auth.account.title'),
                            'icon' => 'person-circle',
                        ]
                        : [
                            'url' => route('login'),
                            'label' => __('common.labels.login'),
                            'icon' => 'box-arrow-in-right',
                        ];
                    $shortcuts = [$accountShortcut];
                    if (auth()->guest() && $capabilities->canRegister) {
                        $shortcuts[] = [
                            'url' => route('register'),
                            'label' => __('site.auth.register.submit'),
                            'icon' => 'person-plus',
                        ];
                    }
                    $navigationShortcuts = $capabilities->hasCustomHeader
                        ? $headerLinks
                        : $publicSite['extensionHeaderLinks'];
                    $excludedShortcuts = [url('/'), route('site.news.index')];
                    $shortcuts = collect([...$shortcuts, ...$navigationShortcuts])
                        ->reject(fn(array $shortcut) => in_array($shortcut['url'], $excludedShortcuts, true))
                        ->unique('url')
                        ->take(5);
                    $socialLinks = app(SocialLinks::class)->visible();
                @endphp
                <aside class="bastion-community-grid" aria-label="{{ __('site.home.useful_links') }}">
                    <section class="bastion-community" aria-labelledby="community-heading">
                        <div class="bastion-panel-heading">
                            <p class="bastion-panel-index"><span
                                    aria-hidden="true">{{ $capabilities->hasNews ? '02' : '01' }} /</span>
                                {{ __('theme::messages.home.community.kicker') }}</p>
                            <h2 id="community-heading">{{ __('theme::messages.home.community.title') }}</h2>
                        </div>
                        <ul class="bastion-shortcuts">
                            @foreach ($shortcuts as $shortcut)
                                <li>
                                    <a href="{{ $shortcut['url'] }}"
                                        @if ($shortcut['new_tab'] ?? false) target="_blank" rel="noopener noreferrer" @endif>
                                        <i class="bi bi-{{ $shortcut['icon'] ?? 'arrow-up-right-square' }}"
                                            aria-hidden="true"></i>
                                        <span>{{ $shortcut['label'] }}
                                            @if ($shortcut['new_tab'] ?? false)
                                                <span class="visually-hidden">{{ __('admin.social.new_tab') }}</span>
                                            @endif
                                        </span>
                                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                    @include('default.home.partials.community-connection')
                    @if ($socialLinks)
                        <section class="bastion-social" aria-labelledby="bastion-social-heading">
                            <div class="bastion-panel-heading">
                                <p class="bastion-panel-index"><span
                                        aria-hidden="true">{{ $capabilities->hasNews ? '03' : '02' }} /</span>
                                    {{ __('theme::messages.home.social.kicker') }}</p>
                                <h2 id="bastion-social-heading">{{ __('admin.social.title') }}</h2>
                            </div>
                            <ul class="bastion-shortcuts">
                                @foreach ($socialLinks as $socialLink)
                                    <li>
                                        <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer me">
                                            <i class="bi bi-{{ $socialLink['icon'] }}" aria-hidden="true"></i>
                                            <span>{{ $socialLink['title'] }}<span
                                                    class="visually-hidden">{{ __('admin.social.new_tab') }}</span></span>
                                            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </aside>
            </div>
            <x-widgets.zone name="home" :context="['news' => $news]" />
            <x-extensions.slot name="site.home.after" :context="['news' => $news]" />
        </div>
    </div>
@endsection
