@php
    $siteSetting = \App\Models\SiteSetting::first();

    $themeStyle = $siteSetting?->theme_style === 'dark' ? 'dark' : 'light';
    $direction = $siteSetting?->direction === 'rtl' ? 'rtl' : 'ltr';
    $navigationStyle = $siteSetting?->navigation_style === 'vertical' ? 'vertical' : 'horizontal';

    $navStyleChoices = ['menu-click', 'menu-hover', 'icon-click', 'icon-hover'];
    $verticalStyleChoices = ['default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'];
    $navigationMenuStyles = $siteSetting?->navigation_menu_styles;
    $navStyleAttr = in_array($navigationMenuStyles, $navStyleChoices, true) ? $navigationMenuStyles : 'menu-hover';
    $verticalStyleAttr = in_array($navigationMenuStyles, $verticalStyleChoices, true) ? $navigationMenuStyles : null;

    $pageStyles = in_array($siteSetting?->page_styles, ['regular', 'classic', 'modern'], true) ? $siteSetting->page_styles : 'regular';
    $layoutWidth = $siteSetting?->layout_width === 'boxed' ? 'boxed' : 'fullwidth';
    $menuPositions = $siteSetting?->menu_positions === 'scrollable' ? 'scrollable' : 'fixed';
    $headerPositions = $siteSetting?->header_positions === 'scrollable' ? 'scrollable' : 'fixed';
    $pageLoader = $siteSetting?->page_loader === 'enable' ? 'enable' : 'disable';
    $menuColors = in_array($siteSetting?->menu_colors, ['light', 'dark', 'color', 'gradient', 'transparent'], true) ? $siteSetting->menu_colors : 'light';
    $headerColors = in_array($siteSetting?->header_colors, ['light', 'dark', 'color', 'gradient', 'transparent'], true) ? $siteSetting->header_colors : 'light';

    $themeBootstrap = [
        'theme_style' => $themeStyle,
        'direction' => $direction,
        'navigation_style' => $navigationStyle,
        'navigation_menu_styles' => $navigationMenuStyles,
        'page_styles' => $pageStyles,
        'layout_width' => $layoutWidth,
        'menu_positions' => $menuPositions,
        'header_positions' => $headerPositions,
        'page_loader' => $pageLoader,
        'menu_colors' => $menuColors,
        'header_colors' => $headerColors,
        'theme_primary_code' => $siteSetting?->theme_primary_code,
        'theme_bg_color_code' => $siteSetting?->theme_bg_color_code,
        'menu_bg_img' => $siteSetting?->menu_bg_img,
    ];

    $menuBgImg = in_array($siteSetting?->menu_bg_img, ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'], true) ? $siteSetting->menu_bg_img : null;
@endphp

<!DOCTYPE html>
<html lang="en" dir="{{ $direction }}" data-nav-layout="{{ $navigationStyle }}" data-nav-style="{{ $navStyleAttr }}" @if($verticalStyleAttr) data-vertical-style="{{ $verticalStyleAttr }}" @endif data-page-style="{{ $pageStyles }}" data-width="{{ $layoutWidth }}" data-menu-position="{{ $menuPositions }}" data-header-position="{{ $headerPositions }}" data-theme-mode="{{ $themeStyle }}" data-header-styles="{{ $headerColors }}" data-menu-styles="{{ $menuColors }}" data-toggled="close" @if($menuBgImg) data-bg-img="{{ $menuBgImg }}" @endif loader="{{ $pageLoader }}">


<!-- Mirrored from laravelui.spruko.com/valex/index by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 31 Jan 2026 01:10:49 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Laravel Bootstrap Responsive Admin Web Dashboard Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="laravel, framework laravel, laravel template, admin, laravel dashboard, template dashboard, admin dashboard ui, bootstrap dashboard, laravel framework, vite laravel, bootstrap 5 templates, laravel admin panel, laravel tailwind, admin panel, template admin, bootstrap admin panel.">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- TITLE -->
    <title> Valex - @yield('title') </title>

    @if($siteSetting)
        <script>
            (function () {
                const theme = @json($themeBootstrap);
                const navStyleValues = ['menu-click', 'menu-hover', 'icon-click', 'icon-hover'];
                const verticalStyleValues = ['default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'];

                function setBooleanKey(key, enabled) {
                    if (enabled) {
                        localStorage.setItem(key, 'true');
                    } else {
                        localStorage.removeItem(key);
                    }
                }

                setBooleanKey('valexdarktheme', theme.theme_style === 'dark');

                if (theme.direction === 'rtl') {
                    setBooleanKey('valexrtl', true);
                    localStorage.removeItem('valexltr');
                } else {
                    setBooleanKey('valexltr', true);
                    localStorage.removeItem('valexrtl');
                }

                if (theme.navigation_style === 'horizontal') {
                    localStorage.setItem('valexlayout', 'horizontal');
                } else {
                    localStorage.removeItem('valexlayout');
                }

                if (navStyleValues.includes(theme.navigation_menu_styles)) {
                    localStorage.setItem('valexnavstyles', theme.navigation_menu_styles);
                    localStorage.removeItem('valexverticalstyles');
                } else if (verticalStyleValues.includes(theme.navigation_menu_styles)) {
                    localStorage.setItem('valexverticalstyles', theme.navigation_menu_styles);
                    localStorage.removeItem('valexnavstyles');
                } else {
                    localStorage.removeItem('valexnavstyles');
                    localStorage.removeItem('valexverticalstyles');
                }

                localStorage.removeItem('valexregular');
                localStorage.removeItem('valexclassic');
                localStorage.removeItem('valexmodern');
                if (theme.page_styles === 'classic') {
                    setBooleanKey('valexclassic', true);
                } else if (theme.page_styles === 'modern') {
                    setBooleanKey('valexmodern', true);
                } else {
                    setBooleanKey('valexregular', true);
                }

                if (theme.layout_width === 'boxed') {
                    setBooleanKey('valexboxed', true);
                    localStorage.removeItem('valexfullwidth');
                } else {
                    setBooleanKey('valexfullwidth', true);
                    localStorage.removeItem('valexboxed');
                }

                setBooleanKey('valexMenufixed', theme.menu_positions === 'fixed');
                setBooleanKey('valexMenuscrollable', theme.menu_positions === 'scrollable');
                setBooleanKey('valexHeaderfixed', theme.header_positions === 'fixed');
                setBooleanKey('valexHeaderscrollable', theme.header_positions === 'scrollable');

                localStorage.setItem('loaderEnable', theme.page_loader === 'enable' ? 'true' : 'false');
                localStorage.setItem('valexMenu', theme.menu_colors);
                localStorage.setItem('valexHeader', theme.header_colors);

                if (theme.theme_primary_code) {
                    localStorage.setItem('primaryRGB', String(theme.theme_primary_code).replace(/\s+/g, ''));
                } else {
                    localStorage.removeItem('primaryRGB');
                }

                if (theme.theme_bg_color_code) {
                    var bgParts = String(theme.theme_bg_color_code).replace(/\s+/g, '').split(',').map(Number);
                    localStorage.setItem('bodyBgRGB', bgParts[0] + ', ' + bgParts[1] + ', ' + bgParts[2]);
                    localStorage.setItem('bodylightRGB', (bgParts[0]+14) + ', ' + (bgParts[1]+14) + ', ' + (bgParts[2]+14));
                } else {
                    localStorage.removeItem('bodyBgRGB');
                    localStorage.removeItem('bodylightRGB');
                }

                if (theme.menu_bg_img) {
                    localStorage.setItem('bgimg', theme.menu_bg_img);
                } else {
                    localStorage.removeItem('bgimg');
                }
            })();
        </script>
    @endif

    @if($siteSetting?->theme_bg_color_code)
        @php
            $bgParts = array_map('intval', explode(',', $siteSetting->theme_bg_color_code));
            $bgLightParts = [($bgParts[0] ?? 0) + 14, ($bgParts[1] ?? 0) + 14, ($bgParts[2] ?? 0) + 14];
        @endphp
        <style>
            html {
                --body-bg-rgb: {{ $bgParts[0] }}, {{ $bgParts[1] }}, {{ $bgParts[2] }};
                --body-bg-rgb2: {{ $bgLightParts[0] }}, {{ $bgLightParts[1] }}, {{ $bgLightParts[2] }};
                --light-rgb: {{ $bgLightParts[0] }}, {{ $bgLightParts[1] }}, {{ $bgLightParts[2] }};
                --form-control-bg: rgb({{ $bgLightParts[0] }}, {{ $bgLightParts[1] }}, {{ $bgLightParts[2] }});
                --input-border: rgba(255,255,255,0.1);
            }
        </style>
    @endif

    @include('backend.includes.assets.style')
</head>

<body class="">

<!-- Switcher -->
@include('backend.includes.switcher')
<!-- End switcher -->

<!-- Loader -->
<div id="loader" >
    <img src="{{ asset('/') }}backend/build/assets/images/media/loader.svg" alt="">
</div>
<!-- Loader -->

<div class="page">

    <!-- Main-Header -->
    @include('backend.includes.header')
    <!-- End Main-Header -->

    <!-- Country-selector modal -->
    <!-- Start::Off-canvas sidebar-->
{{--    @include('backend.includes.notification-right-side')--}}
    <!-- End::Off-canvas sidebar-->

    <!-- End Country-selector modal -->

    <!--Main-Sidebar-->
    @include('backend.includes.menu')

    <!-- End Main-Sidebar-->

    <!-- Start::app-content -->
    <div class="main-content app-content">
        @yield('body')
    </div>
    <!-- End::content  -->

    <!-- Footer opened -->
    @include('backend.includes.footer')
    <!-- End Footer -->



</div>

<!-- Modals -->
@yield('modal')

<!-- SCRIPTS -->
<!-- Scroll To Top -->
<div class="scrollToTop">
    <span class="arrow"><i class="las la-angle-double-up"></i></span>
</div>
<div id="responsive-overlay"></div>
<!-- Scroll To Top -->

@include('backend.includes.assets.script')

<script>
    (function () {
        const switcherCanvas = document.getElementById('switcher-canvas');
        if (!switcherCanvas) {
            return;
        }

        const saveUrl = @json(route('site-settings.theme'));
        const html = document.documentElement;
        const allowed = {
            theme_style: ['light', 'dark'],
            direction: ['ltr', 'rtl'],
            navigation_style: ['horizontal', 'vertical'],
            navigation_menu_styles: ['menu-click', 'menu-hover', 'icon-click', 'icon-hover', 'default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'],
            page_styles: ['regular', 'classic', 'modern'],
            layout_width: ['fullwidth', 'boxed'],
            menu_positions: ['fixed', 'scrollable'],
            header_positions: ['fixed', 'scrollable'],
            page_loader: ['enable', 'disable'],
            menu_colors: ['light', 'dark', 'color', 'gradient', 'transparent'],
        };

        const presetMap = {
            'switcher-primary': 'preset-1',
            'switcher-primary1': 'preset-2',
            'switcher-primary2': 'preset-3',
            'switcher-primary3': 'preset-4',
            'switcher-primary4': 'preset-5',
        };

        const bgPresetMap = {
            'switcher-background': 'preset-1',
            'switcher-background1': 'preset-2',
            'switcher-background2': 'preset-3',
            'switcher-background3': 'preset-4',
            'switcher-background4': 'preset-5',
        };

        let saveTimer = null;
        let lastPayloadHash = null;
        let interacted = false;

        function pickAllowed(value, options, fallback) {
            return options.includes(value) ? value : fallback;
        }

        function normalizeRgb(value) {
            if (!value || typeof value !== 'string') {
                return null;
            }
            const compact = value.replace(/\s+/g, '');
            return /^\d{1,3},\d{1,3},\d{1,3}$/.test(compact) ? compact : null;
        }

        function detectThemePrimary(primaryCode) {
            for (const [id, key] of Object.entries(presetMap)) {
                const el = document.getElementById(id);
                if (el && el.checked) {
                    return key;
                }
            }

            return primaryCode ? 'custom' : null;
        }

        function detectThemeBg(bgCode) {
            for (const [id, key] of Object.entries(bgPresetMap)) {
                const el = document.getElementById(id);
                if (el && el.checked) {
                    return key;
                }
            }

            return bgCode ? 'custom' : null;
        }

        function buildPayload() {
            const navigationStyle = pickAllowed(html.getAttribute('data-nav-layout'), allowed.navigation_style, 'horizontal');
            const navStyle = html.getAttribute('data-nav-style');
            const verticalStyle = html.getAttribute('data-vertical-style');
            const primaryCode = normalizeRgb(localStorage.getItem('primaryRGB'));
            const bgCode = normalizeRgb(localStorage.getItem('bodyBgRGB'));

            let navigationMenuStyles = verticalStyle || navStyle || 'menu-hover';
            if (!allowed.navigation_menu_styles.includes(navigationMenuStyles)) {
                navigationMenuStyles = navigationStyle === 'vertical' ? 'default' : 'menu-hover';
            }

            const pageStyleAttr = html.getAttribute('data-page-style');
            const pageStyles = pickAllowed(
                pageStyleAttr,
                allowed.page_styles,
                localStorage.getItem('valexclassic') ? 'classic' : (localStorage.getItem('valexmodern') ? 'modern' : 'regular')
            );

            const widthAttr = html.getAttribute('data-width');
            const layoutWidth = pickAllowed(widthAttr, allowed.layout_width, localStorage.getItem('valexboxed') ? 'boxed' : 'fullwidth');

            const menuPositionAttr = html.getAttribute('data-menu-position');
            const menuPositions = pickAllowed(menuPositionAttr, allowed.menu_positions, localStorage.getItem('valexMenuscrollable') ? 'scrollable' : 'fixed');

            const headerPositionAttr = html.getAttribute('data-header-position');
            const headerPositions = pickAllowed(headerPositionAttr, allowed.header_positions, localStorage.getItem('valexHeaderscrollable') ? 'scrollable' : 'fixed');

            const menuColors = pickAllowed(html.getAttribute('data-menu-styles'), allowed.menu_colors, 'light');
            const headerColors = pickAllowed(html.getAttribute('data-header-styles'), allowed.menu_colors, 'light');

            const payload = {
                theme_style: pickAllowed(html.getAttribute('data-theme-mode'), allowed.theme_style, 'light'),
                direction: pickAllowed(html.getAttribute('dir'), allowed.direction, 'ltr'),
                navigation_style: navigationStyle,
                navigation_menu_styles: navigationMenuStyles,
                page_styles: pageStyles,
                layout_width: layoutWidth,
                menu_positions: menuPositions,
                header_positions: headerPositions,
                page_loader: (html.getAttribute('loader') === 'enable' || localStorage.loaderEnable === 'true') ? 'enable' : 'disable',
                menu_colors: menuColors,
                menu_color_code: ['color', 'gradient'].includes(menuColors) ? primaryCode : null,
                header_colors: headerColors,
                header_color_code: ['color', 'gradient'].includes(headerColors) ? primaryCode : null,
                theme_primary: detectThemePrimary(primaryCode),
                theme_primary_code: primaryCode,
                theme_bg_color: detectThemeBg(bgCode),
                theme_bg_color_code: bgCode,
                menu_bg_img: html.getAttribute('data-bg-img') || null,
            };

            return payload;
        }

        function saveThemeSettings() {
            const payload = buildPayload();
            const payloadHash = JSON.stringify(payload);

            if (payloadHash === lastPayloadHash) {
                return;
            }

            lastPayloadHash = payloadHash;

            $.ajax({
                url: saveUrl,
                type: 'POST',
                data: payload
            }).fail(function (xhr) {
                console.error('Theme settings save failed.', xhr?.responseJSON || xhr?.statusText || xhr);
            });
        }

        function queueSave() {
            if (!interacted) {
                return;
            }

            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveThemeSettings, 400);
        }

        switcherCanvas.addEventListener('click', function () {
            interacted = true;
            setTimeout(queueSave, 120);
        });

        switcherCanvas.addEventListener('change', function () {
            interacted = true;
            queueSave();
        });

        const observer = new MutationObserver(function () {
            queueSave();
        });

        observer.observe(html, {
            attributes: true,
            attributeFilter: [
                'dir',
                'style',
                'loader',
                'data-theme-mode',
                'data-nav-layout',
                'data-nav-style',
                'data-vertical-style',
                'data-page-style',
                'data-width',
                'data-menu-position',
                'data-header-position',
                'data-menu-styles',
                'data-header-styles',
                'data-bg-img',
            ],
        });
    })();
</script>
</body>


</html>
