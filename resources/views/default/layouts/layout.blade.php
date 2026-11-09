<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="{{ $appearance['effective'] }}"
    data-appearance="{{ $appearance['choice'] }}">
<!--suppress HtmlRequiredTitleElement -->
<head>
    @include('default.layouts.partials.head', [
        'themeColor' => $themeManifest['theme_color'],
        'stylesheet' => 'resources/scss/app.scss',
    ])
    @stack('head')
</head>
@php($wideContent = $__env->hasSection('content_full_width'))
<body @class(['theme-bastion', 'theme-bastion-home' => $wideContent])>
    <a class="bastion-skip visually-hidden-focusable"
        href="#main-content">{{ __('site.home.skip_to_content') }}</a>
    @include('default.layouts.partials.header', [
        'showBrandLogo' => false,
        'showMobileMenuLabels' => true,
    ])
    <main id="main-content" tabindex="-1"
        @class(['site-content main-content', 'site-content-wide' => $wideContent, 'container' => !$wideContent])>
        <div @class(['container bastion-page-before' => $wideContent])>
            @include('partials.feedback.alerts')
            <x-extensions.slot name="site.page.before" />
        </div>
        @yield('content')
        <div @class(['container' => $wideContent])>
            <x-extensions.slot name="site.page.after" />
        </div>
    </main>
    @include('default.layouts.partials.footer')
    @stack('scripts')
</body>
</html>
