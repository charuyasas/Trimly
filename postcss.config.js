export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
        // CSS optimization and minification
        ...(process.env.NODE_ENV === 'production' ? {
            cssnano: {
                preset: ['default', {
                    discardComments: {
                        removeAll: true,
                    },
                    normalizeWhitespace: true,
                    minifyFontValues: true,
                    minifySelectors: true,
                }],
            },
        } : {}),
    },
};
