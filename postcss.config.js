// Tailwind v4 is applied via the @tailwindcss/vite plugin (see vite.config.js),
// not a classic PostCSS pipeline. This empty config exists only to stop
// postcss-load-config's upward directory search from reaching the frontend's
// Tailwind v3 postcss.config.js one level up, which breaks this build.
export default {
  plugins: {},
};
