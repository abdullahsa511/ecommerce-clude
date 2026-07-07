# Image delivery optimization

## 1. WebP (modern format) – active

- **Nginx** (see `nginx.conf`): For requests to `.jpg`/`.jpeg`/`.png`, if the browser sends `Accept: image/webp` and a `.webp` variant exists at the same path (e.g. `image.jpg.webp`), that variant is served. Saves a lot of bytes vs JPG/PNG.
- **Generate WebP variants** (one-time or after adding new images):
  ```bash
  ./scripts/generate-webp.sh
  ```
  Uses `cwebp` (libwebp) or ImageMagick `convert`. Optional: `WEBP_QUALITY=85 ./scripts/generate-webp.sh`, `DRY_RUN=1 ./scripts/generate-webp.sh` to preview.

## 2. Responsive images (right size for display)

Lighthouse reports “image larger than displayed” when the file is much bigger than the displayed size (e.g. 876×657 served for 566×233). Fix by serving a smaller file for that context.

**Option A – Backend/templates:** Output `<img>` with `srcset` and `sizes` so the browser requests an appropriate width:

```html
<img
  src="/media/Projects/banner/example.jpg"
  srcset="/media/Projects/banner/example-566w.jpg 566w,
          /media/Projects/banner/example-349w.jpg 349w"
  sizes="(max-width: 768px) 349px, 566px"
  alt="..."
  loading="lazy"
/>
```

You need generated resized files (e.g. `example-566w.jpg`, `example-349w.jpg`) from your upload/build pipeline or a script.

**Option B – Picture element (WebP + fallback):**

```html
<picture>
  <source type="image/webp" srcset="/media/.../image.jpg.webp" />
  <img src="/media/.../image.jpg" alt="..." loading="lazy" />
</picture>
```

With the current nginx setup, the same URL can be requested and nginx will serve the `.webp` when the browser sends `Accept: image/webp`, so a single `<img src="...">` is enough for WebP. Use `<picture>` only if you need different art (e.g. crop) per format.

## 3. Lazy loading

Add `loading="lazy"` to images below the fold so they load after initial paint. Already used in many templates; add where missing for off-screen images.

## Summary

| Action                         | Est. savings | How |
|--------------------------------|-------------|-----|
| Serve WebP when available      | Large       | Run `./scripts/generate-webp.sh`; nginx already configured |
| Serve correctly sized images   | Large       | Add `srcset`/`sizes` and resized files in backend/templates |
| Lazy-load below-the-fold images| Perceived   | `loading="lazy"` on non-LCP images |
