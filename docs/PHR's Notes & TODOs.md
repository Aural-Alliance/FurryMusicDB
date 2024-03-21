# 📝 PHR's Suggestions, TODOs, & Web-Dev checklist:

## [Aural Alliance Homepage](AuralAlliance.page)
- Fix Social icons overlapping in header
- enable HTTPS?
- more punk/socialist/DIY aesthetics?

## [Furry Music Database](https://furrymusicians.info/)
- single stack for "Explore", "Sign Up" buttons when viewing Mobile / Portrait?

## Both Sites

### Speed optimizations:
- convert graphics to **AVIF** (8bit for max. compat.?) and/or **SVG**
- replace **flat-colour** graphics, banners & elements with **SVGs or CSS** (e.g. Pride Flags 🏳‍🌈)
- convert videos to **AV1**?
	- use `fast-decode` option when transcoding)
- check CPU/GPU footprint of AV1 & AVIF before the above
	- ❗ filesize highest priority; bytes transferred costs money, esp. end-users on Mobile
- Spritesheets for images?  Sprites / fonts for icons?
- `jpegoptim.exe` & `optipng` for lossless JPEG & PNG minifying
- deliver **minified files** (HTML, CSS, JS, etc.)
- server-side automatic minification
- consult **Lighthouse speed reports** (generated via Chromium)

### Security (not my forte; please verify with expert)
- encrypt entire database (no plaintext)
- salting & hashing
- Let's Encrypt certificates?

### UI/UX & Accessibility
- CSS animations?
#### Fonts:
- [Atkinson Hyperlegible](https://brailleinstitute.org/freefont) (sans-serif made by Braille Institute)
- [Hack](https://sourcefoundry.org/hack/) (Consolas-esque monospace)
