<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('http');

test('legal translations exist in German', function () {
    app()->setLocale('de');
    $keys = ['impressum', 'datenschutz', 'nutzung', 'regeln', 'faq', 'kontakt'];

    foreach ($keys as $key) {
        expect(__("ui.legal.{$key}.title"))->not->toBeEmpty()
            ->and(__("ui.legal.{$key}.body"))->not->toBeEmpty();
    }
});

test('legal translations exist in English', function () {
    app()->setLocale('en');
    $keys = ['impressum', 'datenschutz', 'nutzung', 'regeln', 'faq', 'kontakt'];

    foreach ($keys as $key) {
        expect(__("ui.legal.{$key}.title"))->not->toBeEmpty()
            ->and(__("ui.legal.{$key}.body"))->not->toBeEmpty();
    }
});

test('impressum has correct German title', function () {
    app()->setLocale('de');
    expect(__('ui.legal.impressum.title'))->toBe('Impressum');
});

test('impressum has correct English title', function () {
    app()->setLocale('en');
    expect(__('ui.legal.impressum.title'))->toBe('Imprint');
});

test('impressum body contains ECG legal notice in German', function () {
    app()->setLocale('de');
    expect(__('ui.legal.impressum.body'))->toContain('ECG');
});

test('impressum body contains ECG legal notice in English', function () {
    app()->setLocale('en');
    expect(__('ui.legal.impressum.body'))->toContain('ECG');
});

test('home page injects __LEGAL__ window variable', function () {
    $response = $this->get('/');

    $response->assertOk()->assertSee('window.__LEGAL__', false);
});

test('home page __LEGAL__ contains impressum key', function () {
    $response = $this->get('/');

    $response->assertOk()->assertSee('"impressum"', false);
});
