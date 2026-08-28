---
title: Matinée
description: A Filament form field that turns a video URL into a stored oEmbed payload you can render anywhere.
---

# Matinée

Matinée is an oEmbed and video field for Filament Panel and Form Builders. A user pastes a video URL, Matinée recognises the provider, converts the URL into an embeddable one, and stores the result — along with the dimensions and any provider options — as a single JSON value on your model.

It ships with providers for YouTube and Vimeo, and you can add your own for any service that has an embed URL format.

## What it gives you

- A single field that captures a video URL, its embed URL, width, height, a responsive toggle, and per-provider options such as YouTube's `controls` and `start`.
- Inline validation: a URL that no provider recognises is rejected before the form is saved.
- An optional in-form preview, so an editor can confirm they pasted the right video.
- A Blade component for rendering the stored video on the front end.

## Compatibility

| Package version | Filament version |
|-----------------|------------------|
| 1.x             | 3.x              |
| 2.x             | 4.x              |
| 3.x             | 4.x & 5.x        |

Matinée requires PHP 8.2 or later.

## Where to go next

- [Installation](installation.md) — install the package, register its styles, and prepare your model.
- [Usage](usage.md) — add the field to a form and render the stored video.
- [Providers](providers.md) — the built-in providers, and how to write your own.
