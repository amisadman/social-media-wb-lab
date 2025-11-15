# Design Docs & Brand Guidelines

This file documents the visual identity and design choices for Catgram so contributors and designers can keep UI consistent.

## Brand overview

- Name: Catgram
- Tone: Friendly, playful, modern

## Colors

Primary palette used in the app (defined in `app/Views/layout.php`):

- `--cat-primary`: #8B5CF6 (Violet) — primary accent for CTAs and links
- `--cat-primary-dark`: #7C3AED — hover/darker state for primary
- `--cat-secondary`: #FDE047 (Yellow) — mascot accents and highlights
- `--cat-light`: #FFFFFF (White) — card backgrounds
- `--cat-dark`: #1F2937 (Dark slate) — primary text color
- `--cat-bg`: #F9FAFB (Light gray) — page background

Use CSS variables where possible. The main stylesheet `public/assets/style.css` mirrors these variables for fallbacks.

## Typography

- UI font: `Inter` (loaded via Google Fonts in `layout.php`)
- Display: Use bold weights for headings and `Inter` for body copy. In some files `Barriecito` is available for logo/brand wordmark.

## Spacing & layout

- The site uses a centered `container` with `max-width: 1100px` for content.
- Standard card element: `.smooth-card` (border-radius `1.5rem`, soft shadow).
- Buttons: `btn-primary` for filled CTAs, with rounded corners and subtle shadow.

## Components

- Buttons

  - Primary: `btn-primary` (use `var(--cat-primary)` background)
  - Secondary: border-only with `var(--cat-primary)` text

- Inputs

  - Use `rounded-lg`, `border-gray-300`, and focus ring `focus:ring-2 focus:ring-[var(--cat-primary)]`.

- Cards
  - Use `.smooth-card` for content blocks (posts, forms). If you want a flatter layout, remove `smooth-card`.

## Icons & Images

- Logo: SVG in header (animated ear). Keep the animation subtle. The logo should be provided as a source file under `public/assets/logo.*` for consistency.
- Post images: saved in `public/uploads/` and shown with `object-cover` and `rounded-lg`.

## Tailwind / DaisyUI notes

- The project currently uses Tailwind via CDN and DaisyUI prebuilt CSS for components. For customization, migrate to a Tailwind build (Vite + `tailwindcss` + `daisyui`) and define a theme in `tailwind.config.js` referencing the brand variables.

## Accessibility

- Ensure color contrast for text over backgrounds meets WCAG AA where possible (especially primary buttons and text on primary backgrounds).
- Buttons and inputs should have focus styles (we use focus rings).
- Images should include `alt` attributes.

## Example CSS snippet (use in components or `style.css`)

```css
:root {
  --cat-primary: #8b5cf6;
  --cat-primary-dark: #7c3aed;
  --cat-secondary: #fde047;
}

.btn-primary {
  background: var(--cat-primary);
  color: #fff;
}
.btn-primary:hover {
  background: var(--cat-primary-dark);
}
```

## Where to make UI changes

- `app/Views/layout.php` — header, global CSS variables; primary place to keep brand tokens in sync.
- `public/assets/style.css` — fallback styles and small utilities used across views.
