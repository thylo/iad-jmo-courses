/**
 * SVGO — for the diagrams in media/diagrams/.
 *
 * They come out of Two.js, which exports what a plotter needs and not what a
 * web page needs: seventeen decimals on every coordinate, the drawing's pixel
 * size baked into width/height, and pure black hard-coded into every fill. One
 * of those diagrams weighs 168 kB, and it is invisible on a dark background.
 *
 * So this config does three jobs beyond the usual minification.
 */
export default {
  multipass: true,
  js2svg: { indent: 0, pretty: false },
  plugins: [
    {
      name: 'preset-default',
      params: {
        overrides: {
          // The viewBox is the only size a diagram should declare — see
          // removeDimensions below. preset-default drops it when width and
          // height agree with it, which is exactly our case.
          removeViewBox: false,

          // A hand-drawn stroke on an 800-unit canvas: one decimal is already
          // finer than a screen can show. This is where the weight goes.
          cleanupNumericValues: { floatPrecision: 1 },
          convertPathData: { floatPrecision: 1 },
          convertTransform: { floatPrecision: 1 },
        },
      },
    },

    // Ink follows the text. The site has a light and a dark paper, and a
    // diagram filled with hsl(0,0%,0%) disappears on the second one.
    { name: 'convertColors', params: { currentColor: true } },

    // width/height in pixels would freeze the drawing at its export size.
    // The viewBox survives, so the figure scales to the column it is in.
    'removeDimensions',

    // What Two.js leaves behind: a version attribute nobody reads, and an
    // inline style that decides display and overflow — both of which belong
    // to the stylesheet, not to the file.
    { name: 'removeAttrs', params: { attrs: ['svg:version', 'svg:style'] } },
  ],
};
