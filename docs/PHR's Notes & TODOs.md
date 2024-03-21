# PHR's Suggestions, TODOs, & Web-Dev checklist for Aural Alliance:

## [AA Homepage](AuralAlliance.page)
- Fix Social icons overlapping
- HTTPS?
- more punk/socialist/DIY aesthetics?

## [Furry Music Database](https://furrymusicians.info/)
- single stack for "Explore", "Sign Up" buttons when viewing Mobile / Portrait?

## --Both--
### Speed optimizations:
  - convert graphics to **AVIF** (8bit for max. compat.?) and/or **SVG**
  - convert bg video to **AV1**?
  - check CPU/GPU footprint of AV1 & AVIF before the above
  - Spritesheets for images?  Sprites / fonts for icons?
  - `jpegoptim.exe` & `optipng` for lossless JPEG & PNG minifying
  - deliver minified files (HTML, CSS, JS, etc.)
  - server-side automatic minification
  - consult **Lighthouse speed reports** (generated via Chromium)
### Security (not my forte; please verify with expert)
  - encrypt entire database (no plaintext)
  - salting & hashing
  - Let's Encrypt certificates?
### UI/UX
  - CSS animations?
