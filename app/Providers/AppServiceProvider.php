<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('backend.master', function ($view) {
            $siteSetting = \App\Models\SiteSetting::first();

            $themeStyle = $siteSetting?->theme_style === 'dark' ? 'dark' : 'light';
            $direction = $siteSetting?->direction === 'rtl' ? 'rtl' : 'ltr';
            $navigationStyle = $siteSetting?->navigation_style === 'horizontal' ? 'horizontal' : 'vertical';

            $navStyleChoices = ['menu-click', 'menu-hover', 'icon-click', 'icon-hover'];
            $verticalStyleChoices = ['default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'];
            $navigationMenuStyles = $siteSetting?->navigation_menu_styles;
            $navStyleAttr = in_array($navigationMenuStyles, $navStyleChoices, true) ? $navigationMenuStyles : 'menu-click';
            $verticalStyleAttr = in_array($navigationMenuStyles, $verticalStyleChoices, true) ? $navigationMenuStyles : 'overlay';

            $pageStyles = in_array($siteSetting?->page_styles, ['regular', 'classic', 'modern'], true) ? $siteSetting->page_styles : 'regular';
            $layoutWidth = $siteSetting?->layout_width === 'boxed' ? 'boxed' : 'fullwidth';
            $menuPositions = $siteSetting?->menu_positions === 'scrollable' ? 'scrollable' : 'fixed';
            $headerPositions = $siteSetting?->header_positions === 'scrollable' ? 'scrollable' : 'fixed';
            $pageLoader = $siteSetting?->page_loader === 'enable' ? 'enable' : 'disable';
            $menuColors = in_array($siteSetting?->menu_colors, ['light', 'dark', 'color', 'gradient', 'transparent'], true) ? $siteSetting->menu_colors : 'light';
            $headerColors = in_array($siteSetting?->header_colors, ['light', 'dark', 'color', 'gradient', 'transparent'], true) ? $siteSetting->header_colors : 'light';

            $themeBootstrap = [
                'theme_style' => $themeStyle,
                'themeStyle' => $themeStyle,
                'direction' => $direction,
                'navigation_style' => $navigationStyle,
                'navigationStyle' => $navigationStyle,
                'navigation_menu_styles' => $navigationMenuStyles,
                'page_styles' => $pageStyles,
                'pageStyles' => $pageStyles,
                'layout_width' => $layoutWidth,
                'layoutWidth' => $layoutWidth,
                'menu_positions' => $menuPositions,
                'menuPositions' => $menuPositions,
                'header_positions' => $headerPositions,
                'headerPositions' => $headerPositions,
                'page_loader' => $pageLoader,
                'pageLoader' => $pageLoader,
                'menu_colors' => $menuColors,
                'menuColors' => $menuColors,
                'header_colors' => $headerColors,
                'headerColors' => $headerColors,
                'theme_primary_code' => $siteSetting?->theme_primary_code,
                'theme_bg_color_code' => $siteSetting?->theme_bg_color_code,
                'menu_bg_img' => $siteSetting?->menu_bg_img,
                'navStyleAttr' => $navStyleAttr,
                'siteSetting' => $siteSetting,
                'verticalStyleAttr' => $verticalStyleAttr,
                'menuBgImg' => in_array($siteSetting?->menu_bg_img, ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'], true) ? $siteSetting->menu_bg_img : null,
            ];
            $themeBootstrap['themeBootstrap'] = $themeBootstrap;

//            $menuBgImg = in_array($siteSetting?->menu_bg_img, ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'], true) ? $siteSetting->menu_bg_img : null;
            $view->with($themeBootstrap);
        });
    }
}
